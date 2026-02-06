<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;
use App\Models\AssetCategory;
use App\Models\AssetLocation;
use App\Models\AssetUom;
use App\Models\AssetVendor;
use App\Models\Asset;
use App\Models\Department;
use App\Models\Employee;
use App\Models\AccountAccessLog;
use App\Models\Document;
use App\Models\DocumentReminderLog;
use Illuminate\Support\Facades\Mail;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduler: prune low-value audit logs daily (keep last 50 per account).
Schedule::command('accounts:audit-prune --keep=50')
    ->dailyAt('01:30')
    ->withoutOverlapping();

// Scheduler: documents maintenance (status + reminder)
Schedule::command('documents:sync-status')
    ->dailyAt('01:40')
    ->withoutOverlapping();

Schedule::command('documents:send-reminders')
    ->dailyAt('07:30')
    ->withoutOverlapping();

Artisan::command('documents:sync-status', function () {
    $today = now()->toDateString();

    $count = Document::query()
        ->whereIn('status', ['Active', 'Draft'])
        ->whereHas('contractTerms', function ($q) use ($today) {
            $q->whereNotNull('end_date')->where('end_date', '<', $today);
        })
        ->count();

    if ($count <= 0) {
        $this->info('Tidak ada dokumen yang perlu diubah menjadi Expired.');
        return self::SUCCESS;
    }

    $updated = Document::query()
        ->whereIn('status', ['Active', 'Draft'])
        ->whereHas('contractTerms', function ($q) use ($today) {
            $q->whereNotNull('end_date')->where('end_date', '<', $today);
        })
        ->update([
            'status' => 'Expired',
            'updated_at' => now(),
        ]);

    $this->info('Selesai. Dokumen diubah menjadi Expired: ' . (int) $updated);
    return self::SUCCESS;
})->purpose('Update status dokumen otomatis: Active/Draft -> Expired jika lewat end_date.');

Artisan::command('documents:send-reminders', function () {
    $daysList = [90, 60, 30, 14, 7];
    $sent = 0;

    foreach ($daysList as $daysBefore) {
        $targetDate = now()->addDays($daysBefore)->toDateString();

        $docs = Document::query()
            ->with(['vendor', 'contractTerms', 'picUser', 'creator'])
            ->whereIn('status', ['Active', 'Draft'])
            ->whereHas('contractTerms', function ($q) use ($targetDate) {
                $q->whereNotNull('end_date')->where('end_date', $targetDate);
            })
            ->get();

        foreach ($docs as $doc) {
            $recipients = collect([
                $doc->picUser,
                $doc->creator,
            ])
                ->filter()
                ->unique('id')
                ->values();

            foreach ($recipients as $u) {
                $exists = DocumentReminderLog::query()
                    ->where('document_id', $doc->document_id)
                    ->where('days_before', $daysBefore)
                    ->where('user_id', (int) $u->id)
                    ->exists();

                if ($exists) {
                    continue;
                }

                DocumentReminderLog::query()->create([
                    'document_id' => $doc->document_id,
                    'days_before' => (int) $daysBefore,
                    'user_id' => (int) $u->id,
                    'sent_at' => now(),
                ]);

                $email = (string) ($u->email ?? '');
                if ($email !== '') {
                    try {
                        $subject = '[Archived Berkas] Reminder: ' . $doc->document_title;
                        $body = "Dokumen akan berakhir dalam {$daysBefore} hari.\n\n" .
                            'Judul: ' . $doc->document_title . "\n" .
                            'Nomor: ' . ($doc->document_number ?? '-') . "\n" .
                            'Vendor: ' . ($doc->vendor?->name ?? '-') . "\n" .
                            'End Date: ' . ($doc->contractTerms?->end_date?->format('Y-m-d') ?? '-') . "\n";

                        Mail::raw($body, function ($m) use ($email, $subject) {
                            $m->to($email)->subject($subject);
                        });
                    } catch (\Throwable $e) {
                        // Do not fail the whole scheduler if mail is not configured.
                        $this->warn('Gagal kirim email ke ' . $email . ' (mail belum dikonfigurasi?).');
                    }
                }

                $sent++;
            }
        }
    }

    $this->info('Selesai. Reminder log baru dibuat: ' . (int) $sent);
    return self::SUCCESS;
})->purpose('Kirim reminder (dan catat) sebelum end_date: 90/60/30/14/7 hari.');

Artisan::command('accounts:audit-prune {--keep=50 : Simpan N log low-value terakhir per account}', function () {
    $keep = max(1, (int) $this->option('keep'));

    $lowValue = (array) config('accounts_audit.low_value_actions', ['ACCOUNT_DETAIL_VIEW']);
    $this->info('Pruning audit log low-value...');
    $this->line('- Aksi low-value: ' . implode(', ', $lowValue));
    $this->line('- Keep per account: ' . $keep);

    $accountIds = AccountAccessLog::query()
        ->whereNotNull('account_id')
        ->whereIn('action', $lowValue)
        ->select('account_id')
        ->distinct()
        ->pluck('account_id');

    $deletedTotal = 0;
    foreach ($accountIds as $accountId) {
        $cutoffId = AccountAccessLog::query()
            ->where('account_id', $accountId)
            ->whereIn('action', $lowValue)
            ->orderByDesc('id')
            ->skip($keep - 1)
            ->value('id');

        if (!$cutoffId) {
            continue;
        }

        $deleted = AccountAccessLog::query()
            ->where('account_id', $accountId)
            ->whereIn('action', $lowValue)
            ->where('id', '<', $cutoffId)
            ->delete();

        $deletedTotal += (int) $deleted;
    }

    $this->info('Selesai. Total log low-value dihapus: ' . $deletedTotal);
    return self::SUCCESS;
})->purpose('Pangkas audit log low-value (mis. view) agar per account hanya tersisa N terakhir.');

Artisan::command('assets:backfill-master-data {--dry-run : Hanya simulasi, tidak menyimpan perubahan}', function () {
    $dryRun = (bool) $this->option('dry-run');

    $this->info(($dryRun ? '[DRY RUN] ' : '') . 'Backfill master data asset dari data existing (m_igi_asset)...');

    $stats = [
        'categories_created' => 0,
        'categories_reactivated' => 0,
        'locations_created' => 0,
        'locations_reactivated' => 0,
        'uoms_created' => 0,
        'uoms_reactivated' => 0,
        'vendors_created' => 0,
        'vendors_reactivated' => 0,
    ];

    $seedCategories = [
        ['code' => 'IT', 'name' => 'IT', 'asset_code_prefix' => 'IT'],
        ['code' => 'Vehicle', 'name' => 'Vehicle', 'asset_code_prefix' => 'VH'],
        ['code' => 'Machine', 'name' => 'Machine', 'asset_code_prefix' => 'MC'],
        ['code' => 'Furniture', 'name' => 'Furniture', 'asset_code_prefix' => 'FR'],
        ['code' => 'Other', 'name' => 'Other', 'asset_code_prefix' => 'OT'],
    ];

    $seedLocations = [
        ['name' => 'Jababeka', 'asset_code_prefix' => '01'],
        ['name' => 'Karawang', 'asset_code_prefix' => '02'],
    ];

    $uoms = DB::table('m_igi_asset')
        ->whereNotNull('satuan')
        ->where('satuan', '!=', '')
        ->select('satuan')
        ->distinct()
        ->orderBy('satuan')
        ->pluck('satuan');

    $vendors = DB::table('m_igi_asset')
        ->whereNotNull('vendor_supplier')
        ->where('vendor_supplier', '!=', '')
        ->select('vendor_supplier')
        ->distinct()
        ->orderBy('vendor_supplier')
        ->pluck('vendor_supplier');

    DB::transaction(function () use ($dryRun, &$stats, $seedCategories, $seedLocations, $uoms, $vendors) {
        foreach ($seedCategories as $row) {
            $existing = AssetCategory::query()->where('code', $row['code'])->lockForUpdate()->first();
            if (!$existing) {
                $stats['categories_created']++;
                if (!$dryRun) {
                    AssetCategory::query()->create([
                        'code' => $row['code'],
                        'name' => $row['name'],
                        'asset_code_prefix' => $row['asset_code_prefix'],
                        'is_active' => true,
                    ]);
                }
            } elseif (!$existing->is_active) {
                $stats['categories_reactivated']++;
                if (!$dryRun) {
                    $existing->update(['is_active' => true]);
                }
            }
        }

        foreach ($seedLocations as $row) {
            $existing = AssetLocation::query()->where('name', $row['name'])->lockForUpdate()->first();
            if (!$existing) {
                $stats['locations_created']++;
                if (!$dryRun) {
                    AssetLocation::query()->create([
                        'name' => $row['name'],
                        'asset_code_prefix' => $row['asset_code_prefix'],
                        'is_active' => true,
                    ]);
                }
            } elseif (!$existing->is_active) {
                $stats['locations_reactivated']++;
                if (!$dryRun) {
                    $existing->update(['is_active' => true]);
                }
            }
        }

        foreach ($uoms as $raw) {
            $name = trim((string) $raw);
            if ($name === '') {
                continue;
            }
            $existing = AssetUom::query()->where('name', $name)->lockForUpdate()->first();
            if (!$existing) {
                $stats['uoms_created']++;
                if (!$dryRun) {
                    AssetUom::query()->create(['name' => $name, 'is_active' => true]);
                }
            } elseif (!$existing->is_active) {
                $stats['uoms_reactivated']++;
                if (!$dryRun) {
                    $existing->update(['is_active' => true]);
                }
            }
        }

        foreach ($vendors as $raw) {
            $name = trim((string) $raw);
            if ($name === '') {
                continue;
            }
            $existing = AssetVendor::query()->where('name', $name)->lockForUpdate()->first();
            if (!$existing) {
                $stats['vendors_created']++;
                if (!$dryRun) {
                    AssetVendor::query()->create(['name' => $name, 'is_active' => true]);
                }
            } elseif (!$existing->is_active) {
                $stats['vendors_reactivated']++;
                if (!$dryRun) {
                    $existing->update(['is_active' => true]);
                }
            }
        }
    });

    $this->info('Selesai. Ringkasan:');
    $this->line('- Kategori dibuat: ' . $stats['categories_created'] . ' | re-aktif: ' . $stats['categories_reactivated']);
    $this->line('- Lokasi dibuat: ' . $stats['locations_created'] . ' | re-aktif: ' . $stats['locations_reactivated']);
    $this->line('- Satuan dibuat: ' . $stats['uoms_created'] . ' | re-aktif: ' . $stats['uoms_reactivated']);
    $this->line('- Vendor dibuat: ' . $stats['vendors_created'] . ' | re-aktif: ' . $stats['vendors_reactivated']);

    if ($dryRun) {
        $this->warn('DRY RUN: Tidak ada perubahan disimpan. Jalankan tanpa --dry-run untuk menerapkan.');
    }

    return self::SUCCESS;
})->purpose('Backfill master data asset (kategori, lokasi, vendor, satuan) dari tabel m_igi_asset.');

Artisan::command('assets:backfill-relations {--dry-run : Hanya simulasi, tidak menyimpan perubahan}', function () {
    $dryRun = (bool) $this->option('dry-run');

    if (!Schema::hasColumn('m_igi_asset', 'department_id') || !Schema::hasColumn('m_igi_asset', 'person_in_charge_employee_id')) {
        $this->error('Kolom relasi belum ada. Jalankan `php artisan migrate` terlebih dahulu (department_id, person_in_charge_employee_id).');
        return self::FAILURE;
    }

    $normalize = static function (?string $value): string {
        $value = trim((string) $value);
        $value = preg_replace('/\s+/', ' ', $value);
        return mb_strtolower((string) $value);
    };

    $deptIndex = Department::query()
        ->select(['id', 'name'])
        ->get()
        ->mapWithKeys(fn($d) => [$normalize($d->name) => (int) $d->id]);

    $empByName = Employee::query()
        ->select(['id', 'name', 'no_id'])
        ->get()
        ->mapWithKeys(fn($e) => [$normalize($e->name) => (int) $e->id]);

    $empByNoId = Employee::query()
        ->select(['id', 'name', 'no_id'])
        ->get()
        ->mapWithKeys(fn($e) => [trim((string) $e->no_id) => (int) $e->id]);

    $q = Asset::query()->withTrashed()
        ->where(function ($qq) {
            $qq->whereNull('department_id')->orWhereNull('person_in_charge_employee_id');
        });

    $count = (int) $q->count();
    if ($count <= 0) {
        $this->info('Tidak ada asset yang perlu di-backfill relasi (semua sudah terisi).');
        return self::SUCCESS;
    }

    $this->info(($dryRun ? '[DRY RUN] ' : '') . "Menemukan {$count} asset yang relasinya belum lengkap. Memproses...");

    $stats = [
        'assets_scanned' => 0,
        'department_linked' => 0,
        'pic_linked' => 0,
        'dept_not_found' => 0,
        'pic_not_found' => 0,
    ];

    DB::transaction(function () use ($q, $dryRun, $normalize, $deptIndex, $empByName, $empByNoId, &$stats) {
        $q->orderBy('id')->chunkById(200, function ($assets) use ($dryRun, $normalize, $deptIndex, $empByName, $empByNoId, &$stats) {
            foreach ($assets as $asset) {
                $stats['assets_scanned']++;

                $updates = [];

                if (empty($asset->department_id)) {
                    $deptKey = $normalize($asset->department);
                    if ($deptKey !== '' && $deptIndex->has($deptKey)) {
                        $updates['department_id'] = (int) $deptIndex->get($deptKey);
                        $stats['department_linked']++;
                    } elseif ($deptKey !== '') {
                        $stats['dept_not_found']++;
                    }
                }

                if (empty($asset->person_in_charge_employee_id)) {
                    $picRaw = trim((string) $asset->person_in_charge);
                    $picKey = $normalize($picRaw);

                    $matchedEmpId = null;

                    // Try parse no_id prefix if present in the string (e.g. "EMP001 - John Doe")
                    if ($picRaw !== '' && preg_match('/^\s*([A-Za-z0-9\-_.]+)\s*[-|:]\s*(.+)\s*$/', $picRaw, $m)) {
                        $maybeNoId = trim((string) $m[1]);
                        if ($maybeNoId !== '' && $empByNoId->has($maybeNoId)) {
                            $matchedEmpId = (int) $empByNoId->get($maybeNoId);
                        } else {
                            $maybeNameKey = $normalize((string) $m[2]);
                            if ($maybeNameKey !== '' && $empByName->has($maybeNameKey)) {
                                $matchedEmpId = (int) $empByName->get($maybeNameKey);
                            }
                        }
                    }

                    // Fallback exact match by name
                    if ($matchedEmpId === null && $picKey !== '' && $empByName->has($picKey)) {
                        $matchedEmpId = (int) $empByName->get($picKey);
                    }

                    if ($matchedEmpId !== null) {
                        $updates['person_in_charge_employee_id'] = $matchedEmpId;
                        $stats['pic_linked']++;
                    } elseif ($picKey !== '') {
                        $stats['pic_not_found']++;
                    }
                }

                if (!empty($updates) && !$dryRun) {
                    $asset->update($updates);
                }
            }
        });
    });

    $this->info('Selesai. Ringkasan:');
    $this->line('- Asset discan: ' . $stats['assets_scanned']);
    $this->line('- Department ter-link: ' . $stats['department_linked']);
    $this->line('- PIC ter-link: ' . $stats['pic_linked']);
    $this->line('- Department tidak ditemukan: ' . $stats['dept_not_found']);
    $this->line('- PIC tidak ditemukan: ' . $stats['pic_not_found']);

    if ($dryRun) {
        $this->warn('DRY RUN: Tidak ada perubahan disimpan. Jalankan tanpa --dry-run untuk menerapkan.');
    }

    return self::SUCCESS;
})->purpose('Backfill department_id & person_in_charge_employee_id di asset dari kolom legacy department/person_in_charge.');
