<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;
use App\Models\UniformLot;
use App\Models\UniformMovement;
use App\Models\UniformItem;
use App\Models\UniformSize;
use App\Models\UniformItemName;
use App\Models\UniformCategory;
use App\Models\UniformColor;
use App\Models\UniformUom;
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

Artisan::command('uniforms:backfill-lots {--dry-run : Hanya simulasi, tidak menyimpan perubahan}', function () {
    $dryRun = (bool) $this->option('dry-run');

    $baseQuery = UniformMovement::query()
        ->whereNull('lot_id')
        ->where('movement_type', 'IN')
        ->whereNotNull('lot_number')
        ->where('lot_number', '!=', '')
        ->where('qty_change', '>', 0);

    $count = (int) $baseQuery->count();
    if ($count <= 0) {
        $this->info('Tidak ada movement stok masuk yang perlu di-backfill (lot_id sudah terisi atau tidak ada data).');
        return self::SUCCESS;
    }

    $this->info(($dryRun ? '[DRY RUN] ' : '') . "Menemukan {$count} movement stok masuk tanpa lot_id. Memproses...");

    $stats = [
        'movements_linked' => 0,
        'lots_created' => 0,
        'lots_updated' => 0,
    ];

    DB::transaction(function () use ($baseQuery, $dryRun, &$stats) {
        $baseQuery
            ->orderBy('id')
            ->chunkById(200, function ($movements) use ($dryRun, &$stats) {
                foreach ($movements as $m) {
                    $lot = UniformLot::query()
                        ->where('uniform_item_id', (int) $m->uniform_item_id)
                        ->where('lot_number', (string) $m->lot_number)
                        ->lockForUpdate()
                        ->first();

                    if (!$lot) {
                        $stats['lots_created']++;
                        if (!$dryRun) {
                            $lot = UniformLot::query()->create([
                                'uniform_item_id' => (int) $m->uniform_item_id,
                                'lot_number' => (string) $m->lot_number,
                                'qty_in' => (int) $m->qty_change,
                                'remaining_qty' => (int) $m->qty_change,
                                'expired_at' => $m->expired_at,
                                'received_at' => $m->performed_at,
                                'received_by' => $m->performed_by,
                                'notes' => 'Backfill dari movement stok masuk #' . $m->id,
                            ]);
                        }
                    } else {
                        $stats['lots_updated']++;
                        if (!$dryRun) {
                            $lot->qty_in = (int) $lot->qty_in + (int) $m->qty_change;
                            $lot->remaining_qty = (int) $lot->remaining_qty + (int) $m->qty_change;
                            if (empty($lot->expired_at) && !empty($m->expired_at)) {
                                $lot->expired_at = $m->expired_at;
                            }
                            if (empty($lot->received_at) && !empty($m->performed_at)) {
                                $lot->received_at = $m->performed_at;
                            }
                            if (empty($lot->received_by) && !empty($m->performed_by)) {
                                $lot->received_by = $m->performed_by;
                            }
                            $lot->save();
                        }
                    }

                    $stats['movements_linked']++;
                    if (!$dryRun) {
                        $m->update([
                            'lot_id' => $lot?->id,
                            'reference_doc' => $m->reference_doc ?: (string) $m->lot_number,
                        ]);
                    }
                }
            });
    });

    $this->info('Selesai. Ringkasan:');
    $this->line('- Movement di-link: ' . $stats['movements_linked']);
    $this->line('- Lot dibuat: ' . $stats['lots_created']);
    $this->line('- Lot di-update (ditambah qty): ' . $stats['lots_updated']);

    if ($dryRun) {
        $this->warn('DRY RUN: Tidak ada perubahan disimpan. Jalankan tanpa --dry-run untuk menerapkan.');
    }

    return self::SUCCESS;
})->purpose('Backfill lot dari movement stok masuk (legacy) yang lot_id masih NULL.');

Artisan::command('uniforms:backfill-item-sizes {--dry-run : Hanya simulasi, tidak menyimpan perubahan}', function () {
    $dryRun = (bool) $this->option('dry-run');

    $q = UniformItem::query()
        ->whereNull('uniform_size_id')
        ->whereNotNull('size')
        ->where('size', '!=', '');

    $count = (int) $q->count();
    if ($count <= 0) {
        $this->info('Tidak ada item yang perlu di-backfill (uniform_size_id sudah terisi atau size kosong).');
        return self::SUCCESS;
    }

    $this->info(($dryRun ? '[DRY RUN] ' : '') . "Menemukan {$count} item dengan size tapi uniform_size_id kosong. Memproses...");

    $stats = [
        'items_updated' => 0,
        'sizes_created' => 0,
    ];

    DB::transaction(function () use ($q, $dryRun, &$stats) {
        $q->orderBy('id')->chunkById(200, function ($items) use ($dryRun, &$stats) {
            foreach ($items as $item) {
                $raw = trim((string) $item->size);
                if ($raw === '') {
                    continue;
                }

                $code = strtoupper(preg_replace('/\s+/', ' ', $raw));

                $size = UniformSize::query()->where('code', $code)->lockForUpdate()->first();
                if (!$size) {
                    $stats['sizes_created']++;
                    if (!$dryRun) {
                        $size = UniformSize::query()->create([
                            'code' => $code,
                            'name' => $raw,
                            'is_active' => true,
                        ]);
                    }
                }

                $stats['items_updated']++;
                if (!$dryRun) {
                    $item->update(['uniform_size_id' => $size?->id]);
                }
            }
        });
    });

    $this->info('Selesai. Ringkasan:');
    $this->line('- Item di-update: ' . $stats['items_updated']);
    $this->line('- Size dibuat: ' . $stats['sizes_created']);

    if ($dryRun) {
        $this->warn('DRY RUN: Tidak ada perubahan disimpan. Jalankan tanpa --dry-run untuk menerapkan.');
    }

    return self::SUCCESS;
})->purpose('Backfill uniform_size_id pada item dari kolom size (legacy).');

Artisan::command('uniforms:backfill-master-fields {--dry-run : Hanya simulasi, tidak menyimpan perubahan}', function () {
    $dryRun = (bool) $this->option('dry-run');

    $this->info(($dryRun ? '[DRY RUN] ' : '') . 'Backfill master data seragam dari data existing (m_igi_uniform_items)...');

    $stats = [
        'item_names_created' => 0,
        'item_names_reactivated' => 0,
        'categories_created' => 0,
        'categories_reactivated' => 0,
        'colors_created' => 0,
        'colors_reactivated' => 0,
        'uoms_created' => 0,
        'uoms_reactivated' => 0,
    ];

    $itemNames = DB::table('m_igi_uniform_items')
        ->whereNotNull('item_name')
        ->where('item_name', '!=', '')
        ->select('item_name')
        ->distinct()
        ->orderBy('item_name')
        ->pluck('item_name');

    $categories = DB::table('m_igi_uniform_items')
        ->whereNotNull('category')
        ->where('category', '!=', '')
        ->select('category')
        ->distinct()
        ->orderBy('category')
        ->pluck('category');

    $colors = DB::table('m_igi_uniform_items')
        ->whereNotNull('color')
        ->where('color', '!=', '')
        ->select('color')
        ->distinct()
        ->orderBy('color')
        ->pluck('color');

    $uoms = DB::table('m_igi_uniform_items')
        ->whereNotNull('uom')
        ->where('uom', '!=', '')
        ->select('uom')
        ->distinct()
        ->orderBy('uom')
        ->pluck('uom');

    DB::transaction(function () use ($dryRun, &$stats, $itemNames, $categories, $colors, $uoms) {
        foreach ($itemNames as $raw) {
            $name = trim((string) $raw);
            if ($name === '') {
                continue;
            }

            $existing = UniformItemName::query()->where('name', $name)->lockForUpdate()->first();
            if (!$existing) {
                $stats['item_names_created']++;
                if (!$dryRun) {
                    UniformItemName::query()->create(['name' => $name, 'is_active' => true]);
                }
                continue;
            }

            if (!$existing->is_active) {
                $stats['item_names_reactivated']++;
                if (!$dryRun) {
                    $existing->update(['is_active' => true]);
                }
            }
        }

        foreach ($categories as $raw) {
            $name = trim((string) $raw);
            if ($name === '') {
                continue;
            }

            $existing = UniformCategory::query()->where('name', $name)->lockForUpdate()->first();
            if (!$existing) {
                $stats['categories_created']++;
                if (!$dryRun) {
                    UniformCategory::query()->create(['name' => $name, 'is_active' => true]);
                }
                continue;
            }

            if (!$existing->is_active) {
                $stats['categories_reactivated']++;
                if (!$dryRun) {
                    $existing->update(['is_active' => true]);
                }
            }
        }

        foreach ($colors as $raw) {
            $name = trim((string) $raw);
            if ($name === '') {
                continue;
            }

            $existing = UniformColor::query()->where('name', $name)->lockForUpdate()->first();
            if (!$existing) {
                $stats['colors_created']++;
                if (!$dryRun) {
                    UniformColor::query()->create(['name' => $name, 'is_active' => true]);
                }
                continue;
            }

            if (!$existing->is_active) {
                $stats['colors_reactivated']++;
                if (!$dryRun) {
                    $existing->update(['is_active' => true]);
                }
            }
        }

        foreach ($uoms as $raw) {
            $code = trim((string) $raw);
            if ($code === '') {
                continue;
            }

            $existing = UniformUom::query()->where('code', $code)->lockForUpdate()->first();
            if (!$existing) {
                $stats['uoms_created']++;
                if (!$dryRun) {
                    UniformUom::query()->create(['code' => $code, 'name' => $code, 'is_active' => true]);
                }
                continue;
            }

            if (!$existing->is_active) {
                $stats['uoms_reactivated']++;
                if (!$dryRun) {
                    $existing->update(['is_active' => true]);
                }
            }
        }
    });

    $this->info('Selesai. Ringkasan:');
    $this->line('- Nama Item: dibuat ' . $stats['item_names_created'] . ', diaktifkan ulang ' . $stats['item_names_reactivated']);
    $this->line('- Kategori: dibuat ' . $stats['categories_created'] . ', diaktifkan ulang ' . $stats['categories_reactivated']);
    $this->line('- Warna: dibuat ' . $stats['colors_created'] . ', diaktifkan ulang ' . $stats['colors_reactivated']);
    $this->line('- UOM: dibuat ' . $stats['uoms_created'] . ', diaktifkan ulang ' . $stats['uoms_reactivated']);

    if ($dryRun) {
        $this->warn('DRY RUN: Tidak ada perubahan disimpan. Jalankan tanpa --dry-run untuk menerapkan.');
    }

    return self::SUCCESS;
})->purpose('Backfill master data (Nama Item/Kategori/Warna/UOM) dari nilai unik di master seragam existing.');

Artisan::command('uniforms:update-min-stock
  {from=100 : Nilai min_stock lama yang akan diganti}
  {to=5 : Nilai min_stock baru}
  {--dry-run : Hanya simulasi, tidak menyimpan perubahan}', function () {
    $dryRun = (bool) $this->option('dry-run');
    $from = (int) $this->argument('from');
    $to = (int) $this->argument('to');

    if ($from < 0 || $to < 0) {
        $this->error('Nilai from/to tidak boleh negatif.');
        return self::FAILURE;
    }

    if ($from === $to) {
        $this->info('from dan to sama, tidak ada perubahan.');
        return self::SUCCESS;
    }

    $q = UniformItem::query()->where('min_stock', $from);
    $count = (int) $q->count();

    if ($count <= 0) {
        $this->info("Tidak ada item seragam dengan min_stock={$from}.");
        return self::SUCCESS;
    }

    $this->info(($dryRun ? '[DRY RUN] ' : '') . "Menemukan {$count} item seragam dengan min_stock={$from}. Mengubah ke {$to}...");

    if (!$dryRun) {
        DB::transaction(function () use ($q, $to) {
            $q->update(['min_stock' => $to]);
        });
    }

    $this->info('Selesai.');
    if ($dryRun) {
        $this->warn('DRY RUN: Tidak ada perubahan disimpan. Jalankan tanpa --dry-run untuk menerapkan.');
    }

    return self::SUCCESS;
})->purpose('Ubah min_stock existing (contoh: 100 -> 5) untuk item seragam.');

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
