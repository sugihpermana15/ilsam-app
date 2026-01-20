<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UniformIssue;
use App\Models\UniformItem;
use App\Models\UniformLot;
use App\Models\UniformMovement;
use App\Models\UniformIssueLot;
use App\Models\UniformAdjustmentRequest;
use App\Models\UniformWriteOffRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UniformController extends Controller
{
  private const MOVEMENT_IN = 'IN';
  private const MOVEMENT_OUT = 'OUT';
  private const MOVEMENT_RETURN = 'RETURN';
  private const MOVEMENT_ADJUSTMENT_IN = 'ADJUSTMENT_IN';
  private const MOVEMENT_ADJUSTMENT_OUT = 'ADJUSTMENT_OUT';
  private const MOVEMENT_WRITE_OFF = 'WRITE_OFF';
  private const MOVEMENT_REPLACEMENT = 'REPLACEMENT';

  private const ISSUE_STATUS_ISSUED = 'ISSUED';
  private const ISSUE_STATUS_RETURNED = 'RETURNED';
  private const ISSUE_STATUS_REPLACED = 'REPLACED';
  private const ISSUE_STATUS_DAMAGED = 'DAMAGED';

  private const APPROVAL_PENDING = 'PENDING';
  private const APPROVAL_APPROVED = 'APPROVED';
  private const APPROVAL_REJECTED = 'REJECTED';

  private function nextItemSequence(string $location): int
  {
    $extractSeq = static function (?string $code): ?int {
      if (empty($code)) {
        return null;
      }
      if (!preg_match('/-(\d{6})$/', $code, $m)) {
        return null;
      }
      return (int) $m[1];
    };

    $max = 0;

    $codes = UniformItem::query()
      ->where('location', $location)
      ->whereNotNull('item_code')
      ->pluck('item_code');

    foreach ($codes as $code) {
      $seq = $extractSeq($code);
      if ($seq !== null && $seq > $max) {
        $max = $seq;
      }
    }

    return $max + 1;
  }

  private function nextIssueSequence(string $location, string $locCode, string $date): int
  {
    $extractSeq = static function (?string $code): ?int {
      if (empty($code)) {
        return null;
      }
      if (!preg_match('/-(\d{6})$/', $code, $m)) {
        return null;
      }
      return (int) $m[1];
    };

    $max = 0;
    $prefix = "ISS-$locCode-$date-";

    $issueCodes = UniformIssue::query()
      ->whereNotNull('issue_code')
      ->where('issue_code', 'like', $prefix . '%')
      ->pluck('issue_code');

    foreach ($issueCodes as $code) {
      $seq = $extractSeq($code);
      if ($seq !== null && $seq > $max) {
        $max = $seq;
      }
    }

    return $max + 1;
  }

  private function locationCode(string $location): string
  {
    return match ($location) {
      'Jababeka' => 'JB',
      'Karawang' => 'KR',
      default => 'XX',
    };
  }

  private function nextLotSequence(int $uniformItemId, string $date): int
  {
    $extractSeq = static function (?string $code): ?int {
      if (empty($code)) {
        return null;
      }
      if (!preg_match('/-(\d{6})$/', $code, $m)) {
        return null;
      }
      return (int) $m[1];
    };

    $max = 0;
    $prefix = "LOT-U{$uniformItemId}-{$date}-";

    $lotNumbers = UniformLot::query()
      ->where('uniform_item_id', $uniformItemId)
      ->where('lot_number', 'like', $prefix . '%')
      ->pluck('lot_number');

    foreach ($lotNumbers as $code) {
      $seq = $extractSeq($code);
      if ($seq !== null && $seq > $max) {
        $max = $seq;
      }
    }

    return $max + 1;
  }

  private function nextNamedLotSequence(int $uniformItemId, string $date, string $prefixName): int
  {
    $extractSeq = static function (?string $code): ?int {
      if (empty($code)) {
        return null;
      }
      if (!preg_match('/-(\d{6})$/', $code, $m)) {
        return null;
      }
      return (int) $m[1];
    };

    $max = 0;
    $prefix = "$prefixName-U{$uniformItemId}-{$date}-";

    $lotNumbers = UniformLot::query()
      ->where('uniform_item_id', $uniformItemId)
      ->where('lot_number', 'like', $prefix . '%')
      ->pluck('lot_number');

    foreach ($lotNumbers as $code) {
      $seq = $extractSeq($code);
      if ($seq !== null && $seq > $max) {
        $max = $seq;
      }
    }

    return $max + 1;
  }

  public function master()
  {
    $items = UniformItem::query()->with('sizeMaster')->orderByDesc('id')->get();
    $sizes = \App\Models\UniformSize::query()->where('is_active', true)->orderBy('code')->get();
    $itemNames = \App\Models\UniformItemName::query()->where('is_active', true)->orderBy('name')->get();
    $categories = \App\Models\UniformCategory::query()->where('is_active', true)->orderBy('name')->get();
    $colors = \App\Models\UniformColor::query()->where('is_active', true)->orderBy('name')->get();
    $uoms = \App\Models\UniformUom::query()->where('is_active', true)->orderBy('code')->get();

    return view('pages.admin.stock.stock_uniform.uniform_master', compact('items', 'sizes', 'itemNames', 'categories', 'colors', 'uoms'));
  }

  public function storeItem(Request $request)
  {
    $request->merge([
      'color' => ($request->input('color') === '' ? null : $request->input('color')),
      'min_stock' => ($request->input('min_stock') === '' ? null : $request->input('min_stock')),
    ]);

    $validated = $request->validate([
      'item_name' => ['required', 'string', 'max:255', Rule::exists('m_igi_uniform_item_names', 'name')->where('is_active', true)],
      'category' => ['required', 'string', 'max:100', Rule::exists('m_igi_uniform_categories', 'name')->where('is_active', true)],
      'uniform_size_id' => ['required', 'integer', 'exists:m_igi_uniform_sizes,id'],
      'size' => ['nullable', 'string', 'max:50'],
      'color' => ['nullable', 'string', 'max:50', Rule::exists('m_igi_uniform_colors', 'name')->where('is_active', true)],
      'uom' => ['required', 'string', 'max:20', Rule::exists('m_igi_uniform_uoms', 'code')->where('is_active', true)],
      'location' => ['required', 'in:Jababeka,Karawang'],
      'min_stock' => ['nullable', 'integer', 'min:0'],
      'notes' => ['nullable', 'string'],
    ]);

    // Keep legacy string column `size` filled for compatibility.
    $size = \App\Models\UniformSize::query()->where('id', (int) $validated['uniform_size_id'])->first();
    $validated['size'] = $size?->code ?? ($validated['size'] ?? null);

    $locCode = $this->locationCode($validated['location']);
    $date = now()->format('Ymd');
    $nextSeq = $this->nextItemSequence($validated['location']);
    $seq = str_pad($nextSeq, 6, '0', STR_PAD_LEFT);

    $validated['item_code'] = "UNI-$locCode-$date-$seq";
    $validated['current_stock'] = 0;
    $validated['is_active'] = true;
    $validated['input_date'] = now();
    $validated['last_updated'] = now();

    UniformItem::create($validated);

    return redirect()->route('admin.uniforms.master')->with('success', 'Master seragam berhasil ditambahkan.');
  }

  public function updateItem(Request $request, $id)
  {
    $item = UniformItem::query()->where('id', (int) $id)->firstOrFail();

    $request->merge([
      'color' => ($request->input('color') === '' ? null : $request->input('color')),
      'min_stock' => ($request->input('min_stock') === '' ? null : $request->input('min_stock')),
    ]);

    $validated = $request->validate([
      'item_name' => ['required', 'string', 'max:255', Rule::exists('m_igi_uniform_item_names', 'name')->where('is_active', true)],
      'category' => ['required', 'string', 'max:100', Rule::exists('m_igi_uniform_categories', 'name')->where('is_active', true)],
      'color' => ['nullable', 'string', 'max:50', Rule::exists('m_igi_uniform_colors', 'name')->where('is_active', true)],
      'uom' => ['required', 'string', 'max:20', Rule::exists('m_igi_uniform_uoms', 'code')->where('is_active', true)],
      'min_stock' => ['nullable', 'integer', 'min:0'],
      'notes' => ['nullable', 'string'],
    ]);

    $item->update([
      'item_name' => $validated['item_name'],
      'category' => $validated['category'],
      'color' => $validated['color'] ?? null,
      'uom' => $validated['uom'],
      'min_stock' => $validated['min_stock'] ?? null,
      'notes' => $validated['notes'] ?? null,
      'last_updated' => now(),
    ]);

    return redirect()->route('admin.uniforms.master')->with('success', 'Master seragam berhasil diperbarui.');
  }

  public function toggleItemActive($id)
  {
    $item = UniformItem::findOrFail($id);
    $item->update([
      'is_active' => !$item->is_active,
      'last_updated' => now(),
    ]);

    return redirect()->route('admin.uniforms.master')->with('success', 'Status item berhasil diubah.');
  }

  public function stock()
  {
    $items = UniformItem::query()->with('sizeMaster')->where('is_active', true)->orderBy('item_name')->get();
    $recentMovements = UniformMovement::query()
      ->with(['item.sizeMaster', 'performedBy'])
      ->orderByDesc('performed_at')
      ->limit(100)
      ->get();

    return view('pages.admin.stock.stock_uniform.uniform_stock', compact('items', 'recentMovements'));
  }

  public function stockIn(Request $request)
  {
    $validated = $request->validate([
      'uniform_item_id' => ['required', 'integer', 'exists:m_igi_uniform_items,id'],
      'qty' => ['required', 'integer', 'min:1'],
      'lot_number' => ['nullable', 'string', 'max:100'],
      'expired_at' => ['nullable', 'date'],
      'notes' => ['nullable', 'string'],
    ]);

    DB::beginTransaction();
    try {
      $item = UniformItem::query()->where('id', $validated['uniform_item_id'])->lockForUpdate()->firstOrFail();

      $qty = (int) $validated['qty'];
      $receivedAt = now();
      $date = $receivedAt->format('Ymd');

      $lotNumber = $validated['lot_number'] ?? null;
      if (empty($lotNumber)) {
        $nextSeq = $this->nextLotSequence((int) $item->id, $date);
        $seq = str_pad((string) $nextSeq, 6, '0', STR_PAD_LEFT);
        $lotNumber = "LOT-U{$item->id}-{$date}-{$seq}";
      }

      $lot = UniformLot::query()->create([
        'uniform_item_id' => $item->id,
        'lot_number' => $lotNumber,
        'qty_in' => $qty,
        'remaining_qty' => $qty,
        'expired_at' => $validated['expired_at'] ?? null,
        'received_at' => $receivedAt,
        'received_by' => Auth::id(),
        'notes' => $validated['notes'] ?? null,
      ]);

      $item->update([
        'current_stock' => (int) $item->current_stock + $qty,
        'last_updated' => now(),
      ]);

      UniformMovement::create([
        'uniform_item_id' => $item->id,
        'lot_id' => $lot->id,
        'movement_type' => self::MOVEMENT_IN,
        'qty_change' => $qty,
        'lot_number' => $lotNumber,
        'reference_doc' => $lotNumber,
        'expired_at' => $validated['expired_at'] ?? null,
        'notes' => $validated['notes'] ?? null,
        'performed_by' => Auth::id(),
        'performed_at' => $receivedAt,
      ]);

      DB::commit();
      return redirect()->route('admin.uniforms.stock')->with('success', 'Stok masuk berhasil disimpan. Lot: ' . $lotNumber);
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('Uniform stockIn error: ' . $e->getMessage());
      return redirect()->route('admin.uniforms.stock')->with('error', 'Gagal menyimpan stok masuk.');
    }
  }

  public function distribution()
  {
    $items = UniformItem::query()->with('sizeMaster')->where('is_active', true)->orderBy('item_name')->get();
    $employees = Employee::query()
      ->leftJoin('m_igi_departments as d', 'm_igi_employees.department_id', '=', 'd.id')
      ->leftJoin('m_igi_positions as p', 'm_igi_employees.position_id', '=', 'p.id')
      ->select('m_igi_employees.*')
      ->with(['department', 'position'])
      ->orderBy('d.name')
      ->orderBy('p.name')
      ->orderBy('m_igi_employees.name')
      ->get();

    $recentIssues = UniformIssue::query()
      ->with(['item.sizeMaster', 'issuedToEmployee', 'issuedTo', 'issuedBy'])
      ->orderByDesc('issued_at')
      ->limit(100)
      ->get();

    return view('pages.admin.stock.stock_uniform.uniform_distribution', compact('items', 'employees', 'recentIssues'));
  }

  public function issue(Request $request)
  {
    $validated = $request->validate([
      'uniform_item_id' => ['required', 'integer', 'exists:m_igi_uniform_items,id'],
      'issued_to_employee_id' => ['required', 'integer', 'exists:m_igi_employees,id'],
      'qty' => ['required', 'integer', 'min:1'],
      'issued_at' => ['nullable', 'date'],
      'notes' => ['nullable', 'string'],
    ]);

    DB::beginTransaction();
    try {
      $item = UniformItem::query()->where('id', $validated['uniform_item_id'])->lockForUpdate()->firstOrFail();

      $qty = (int) $validated['qty'];
      if ((int) $item->current_stock < $qty) {
        DB::rollBack();
        return redirect()->route('admin.uniforms.distribution')->with('error', 'Stok tidak cukup untuk distribusi.');
      }

      $issueDate = !empty($validated['issued_at']) ? \Carbon\Carbon::parse($validated['issued_at']) : now();

      // FIFO allocation: oldest lots first, cannot allocate from expired lots.
      $remainingToAllocate = $qty;
      $allocated = [];

      $lots = UniformLot::query()
        ->where('uniform_item_id', $item->id)
        ->where('remaining_qty', '>', 0)
        ->where(function ($q) use ($issueDate) {
          $q->whereNull('expired_at')->orWhere('expired_at', '>=', $issueDate->toDateString());
        })
        ->orderBy('received_at')
        ->orderBy('id')
        ->lockForUpdate()
        ->get();

      foreach ($lots as $lot) {
        if ($remainingToAllocate <= 0) {
          break;
        }

        $available = (int) $lot->remaining_qty;
        if ($available <= 0) {
          continue;
        }

        $take = min($available, $remainingToAllocate);
        $lot->update(['remaining_qty' => $available - $take]);
        $allocated[] = ['lot_id' => $lot->id, 'qty' => $take];
        $remainingToAllocate -= $take;
      }

      if ($remainingToAllocate > 0) {
        DB::rollBack();
        return redirect()->route('admin.uniforms.distribution')->with('error', 'Stok lot tidak cukup / lot expired.');
      }

      $issueLocCode = $this->locationCode((string) $item->location);
      $date = $issueDate->format('Ymd');
      $nextSeq = $this->nextIssueSequence((string) $item->location, $issueLocCode, $date);
      $seq = str_pad($nextSeq, 6, '0', STR_PAD_LEFT);
      $issueCode = "ISS-$issueLocCode-$date-$seq";

      $issue = UniformIssue::create([
        'issue_code' => $issueCode,
        'uniform_item_id' => $item->id,
        'issued_to_user_id' => null,
        'issued_to_employee_id' => (int) $validated['issued_to_employee_id'],
        'qty' => $qty,
        'status' => self::ISSUE_STATUS_ISSUED,
        'notes' => $validated['notes'] ?? null,
        'issued_by' => Auth::id(),
        'issued_at' => $issueDate,
      ]);

      foreach ($allocated as $row) {
        $lot = UniformLot::query()->where('id', $row['lot_id'])->first();

        UniformIssueLot::query()->create([
          'issue_id' => $issue->id,
          'lot_id' => $row['lot_id'],
          'qty' => $row['qty'],
        ]);

        UniformMovement::create([
          'uniform_item_id' => $item->id,
          'issue_id' => $issue->id,
          'lot_id' => $row['lot_id'],
          'movement_type' => self::MOVEMENT_OUT,
          'qty_change' => - ((int) $row['qty']),
          'lot_number' => $lot?->lot_number,
          'reference_doc' => $issueCode,
          'notes' => 'Distribusi ke karyawan. ' . ($validated['notes'] ?? ''),
          'performed_by' => Auth::id(),
          'performed_at' => now(),
        ]);
      }

      $item->update([
        'current_stock' => (int) $item->current_stock - $qty,
        'last_updated' => now(),
      ]);

      DB::commit();
      return redirect()->route('admin.uniforms.distribution')->with('success', 'Distribusi seragam berhasil. Stok otomatis berkurang.');
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('Uniform issue error: ' . $e->getMessage());
      return redirect()->route('admin.uniforms.distribution')->with('error', 'Gagal melakukan distribusi seragam.');
    }
  }

  public function returnIssue(Request $request, UniformIssue $issue)
  {
    $request->validate([
      'notes' => ['nullable', 'string'],
    ]);

    DB::beginTransaction();
    try {
      $issue = UniformIssue::query()->where('id', $issue->id)->lockForUpdate()->firstOrFail();
      if ((string) $issue->status !== self::ISSUE_STATUS_ISSUED) {
        DB::rollBack();
        return back()->with('error', 'Issue tidak dapat diretur (status bukan ISSUED).');
      }

      $item = UniformItem::query()->where('id', $issue->uniform_item_id)->lockForUpdate()->firstOrFail();

      $breakdown = UniformIssueLot::query()
        ->where('issue_id', $issue->id)
        ->lockForUpdate()
        ->get();

      if ($breakdown->isEmpty()) {
        DB::rollBack();
        return back()->with('error', 'Issue lot breakdown tidak ditemukan.');
      }

      foreach ($breakdown as $row) {
        $lot = UniformLot::query()->where('id', $row->lot_id)->lockForUpdate()->first();
        if ($lot) {
          $lot->update([
            'remaining_qty' => (int) $lot->remaining_qty + (int) $row->qty,
          ]);

          UniformMovement::create([
            'uniform_item_id' => $item->id,
            'issue_id' => $issue->id,
            'lot_id' => $lot->id,
            'movement_type' => self::MOVEMENT_RETURN,
            'qty_change' => (int) $row->qty,
            'lot_number' => $lot->lot_number,
            'reference_doc' => $issue->issue_code,
            'notes' => 'Retur dari karyawan. ' . ($request->input('notes') ?? ''),
            'performed_by' => Auth::id(),
            'performed_at' => now(),
          ]);
        }
      }

      $issue->update([
        'status' => self::ISSUE_STATUS_RETURNED,
        'returned_at' => now(),
      ]);

      $item->update([
        'current_stock' => (int) $item->current_stock + (int) $issue->qty,
        'last_updated' => now(),
      ]);

      DB::commit();
      return back()->with('success', 'Retur berhasil diproses.');
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('Uniform return error: ' . $e->getMessage());
      return back()->with('error', 'Gagal memproses retur.');
    }
  }

  public function replaceIssue(Request $request, UniformIssue $issue)
  {
    $validated = $request->validate([
      'qty' => ['nullable', 'integer', 'min:1'],
      'reason' => ['required', 'string', 'max:255'],
      'notes' => ['nullable', 'string'],
      'issued_at' => ['nullable', 'date'],
      'return_old' => ['nullable', 'boolean'],
    ]);

    DB::beginTransaction();
    try {
      $issue = UniformIssue::query()->where('id', $issue->id)->lockForUpdate()->firstOrFail();
      if ((string) $issue->status !== self::ISSUE_STATUS_ISSUED) {
        DB::rollBack();
        return back()->with('error', 'Issue tidak dapat diganti (status bukan ISSUED).');
      }

      if (empty($issue->issued_to_employee_id)) {
        DB::rollBack();
        return back()->with('error', 'Issue ini tidak terkait employee master (issued_to_employee_id kosong).');
      }

      $replacementQty = (int) ($validated['qty'] ?? $issue->qty);
      if ($replacementQty <= 0) {
        DB::rollBack();
        return back()->with('error', 'Qty penggantian tidak valid.');
      }

      $item = UniformItem::query()->where('id', $issue->uniform_item_id)->lockForUpdate()->firstOrFail();
      $returnOld = $request->boolean('return_old');

      // Optional: exchange flow. Return the original issue back to stock first (per-lot), then issue replacement.
      if ($returnOld) {
        $breakdown = UniformIssueLot::query()
          ->where('issue_id', $issue->id)
          ->lockForUpdate()
          ->get();

        if ($breakdown->isEmpty()) {
          DB::rollBack();
          return back()->with('error', 'Issue lot breakdown tidak ditemukan untuk retur/tukar.');
        }

        foreach ($breakdown as $row) {
          $lot = UniformLot::query()->where('id', $row->lot_id)->lockForUpdate()->first();
          if (!$lot) {
            continue;
          }

          $lot->update([
            'remaining_qty' => (int) $lot->remaining_qty + (int) $row->qty,
          ]);

          UniformMovement::query()->create([
            'uniform_item_id' => (int) $item->id,
            'issue_id' => (int) $issue->id,
            'lot_id' => (int) $lot->id,
            'movement_type' => self::MOVEMENT_RETURN,
            'qty_change' => (int) $row->qty,
            'lot_number' => $lot->lot_number,
            'reference_doc' => $issue->issue_code,
            'notes' => 'Retur (tukar penggantian). Alasan: ' . ((string) $validated['reason']) . '. ' . ((string) ($validated['notes'] ?? '')),
            'performed_by' => Auth::id(),
            'performed_at' => now(),
          ]);
        }

        $item->update([
          'current_stock' => (int) $item->current_stock + (int) $issue->qty,
          'last_updated' => now(),
        ]);
      }

      if ((int) $item->current_stock < $replacementQty) {
        DB::rollBack();
        return back()->with('error', 'Stok tidak cukup untuk penggantian.');
      }

      $replacementDate = !empty($validated['issued_at']) ? \Carbon\Carbon::parse($validated['issued_at']) : now();

      // FIFO allocation: oldest lots first, cannot allocate from expired lots.
      $remainingToAllocate = $replacementQty;
      $allocated = [];

      $lots = UniformLot::query()
        ->where('uniform_item_id', $item->id)
        ->where('remaining_qty', '>', 0)
        ->where(function ($q) use ($replacementDate) {
          $q->whereNull('expired_at')->orWhere('expired_at', '>=', $replacementDate->toDateString());
        })
        ->orderBy('received_at')
        ->orderBy('id')
        ->lockForUpdate()
        ->get();

      foreach ($lots as $lot) {
        if ($remainingToAllocate <= 0) {
          break;
        }

        $available = (int) $lot->remaining_qty;
        if ($available <= 0) {
          continue;
        }

        $take = min($available, $remainingToAllocate);
        $lot->update(['remaining_qty' => $available - $take]);
        $allocated[] = ['lot_id' => $lot->id, 'qty' => $take];
        $remainingToAllocate -= $take;
      }

      if ($remainingToAllocate > 0) {
        DB::rollBack();
        return back()->with('error', 'Stok lot tidak cukup / lot kedaluwarsa untuk penggantian.');
      }

      $issueLocCode = $this->locationCode((string) $item->location);
      $date = $replacementDate->format('Ymd');
      $nextSeq = $this->nextIssueSequence((string) $item->location, $issueLocCode, $date);
      $seq = str_pad($nextSeq, 6, '0', STR_PAD_LEFT);
      $replacementIssueCode = "ISS-$issueLocCode-$date-$seq";

      $notes = trim((string) ($validated['notes'] ?? ''));
      $reason = trim((string) $validated['reason']);
      $combinedNotes = 'Penggantian untuk ' . ($issue->issue_code ?? ('Issue#' . $issue->id)) . '. Alasan: ' . $reason;
      if (!empty($notes)) {
        $combinedNotes .= '. ' . $notes;
      }

      $replacementIssue = UniformIssue::query()->create([
        'reference_issue_id' => (int) $issue->id,
        'issue_code' => $replacementIssueCode,
        'uniform_item_id' => (int) $item->id,
        'issued_to_user_id' => $issue->issued_to_user_id,
        'issued_to_employee_id' => (int) $issue->issued_to_employee_id,
        'qty' => $replacementQty,
        'status' => self::ISSUE_STATUS_ISSUED,
        'notes' => $combinedNotes,
        'issued_by' => Auth::id(),
        'issued_at' => $replacementDate,
      ]);

      foreach ($allocated as $row) {
        $lot = UniformLot::query()->where('id', $row['lot_id'])->first();

        UniformIssueLot::query()->create([
          'issue_id' => $replacementIssue->id,
          'lot_id' => $row['lot_id'],
          'qty' => $row['qty'],
        ]);

        UniformMovement::query()->create([
          'uniform_item_id' => (int) $item->id,
          'issue_id' => (int) $replacementIssue->id,
          'lot_id' => (int) $row['lot_id'],
          'movement_type' => self::MOVEMENT_REPLACEMENT,
          'qty_change' => - ((int) $row['qty']),
          'lot_number' => $lot?->lot_number,
          'reference_doc' => $replacementIssueCode,
          'notes' => $combinedNotes,
          'performed_by' => Auth::id(),
          'performed_at' => now(),
        ]);
      }

      $item->update([
        'current_stock' => (int) $item->current_stock - $replacementQty,
        'last_updated' => now(),
      ]);

      $issue->update([
        'status' => self::ISSUE_STATUS_REPLACED,
        'returned_at' => $returnOld ? now() : $issue->returned_at,
      ]);

      DB::commit();
      return back()->with('success', 'Penggantian berhasil. Issue lama ditandai DIGANTI dan stok berkurang sesuai FIFO lot.');
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('Uniform replaceIssue error: ' . $e->getMessage());
      return back()->with('error', 'Gagal memproses penggantian.');
    }
  }

  public function history()
  {
    $movements = UniformMovement::query()
      ->with(['item.sizeMaster', 'performedBy', 'issue.issuedToEmployee', 'issue.issuedTo'])
      ->orderByDesc('performed_at')
      ->limit(500)
      ->get();

    return view('pages.admin.stock.stock_uniform.uniform_history', compact('movements'));
  }

  public function adjustments()
  {
    $items = UniformItem::query()->with('sizeMaster')->where('is_active', true)->orderBy('item_name')->get();

    $lots = UniformLot::query()
      ->with('item.sizeMaster')
      ->where('remaining_qty', '>', 0)
      ->orderByDesc('received_at')
      ->limit(500)
      ->get();

    $pending = UniformAdjustmentRequest::query()
      ->with(['item.sizeMaster', 'lot', 'requestedBy', 'approvedBy'])
      ->where('approval_status', self::APPROVAL_PENDING)
      ->orderByDesc('requested_at')
      ->limit(200)
      ->get();

    $recent = UniformAdjustmentRequest::query()
      ->with(['item.sizeMaster', 'lot', 'requestedBy', 'approvedBy', 'approvedMovement'])
      ->where('approval_status', '!=', self::APPROVAL_PENDING)
      ->orderByDesc('approved_at')
      ->limit(200)
      ->get();

    return view('pages.admin.stock.stock_uniform.uniform_adjustments', compact('items', 'lots', 'pending', 'recent'));
  }

  public function storeAdjustment(Request $request)
  {
    $validated = $request->validate([
      'uniform_item_id' => ['required', 'integer', 'exists:m_igi_uniform_items,id'],
      'qty_change' => ['required', 'integer', 'not_in:0'],
      'lot_id' => ['nullable', 'integer', 'exists:m_igi_uniform_lots,id'],
      'reason' => ['required', 'string'],
    ]);

    $qtyChange = (int) $validated['qty_change'];
    $lotId = $validated['lot_id'] ?? null;

    // For audit clarity: ADJUSTMENT_IN always creates a dedicated adjustment lot.
    if ($qtyChange > 0 && !empty($lotId)) {
      return back()->with('error', 'Adjustment IN tidak boleh memilih lot (akan dibuat lot ADJ otomatis).');
    }

    if ($qtyChange < 0 && !empty($lotId)) {
      $lot = UniformLot::query()->where('id', $lotId)->first();
      if ($lot && (int) $lot->uniform_item_id !== (int) $validated['uniform_item_id']) {
        return back()->with('error', 'Lot tidak sesuai dengan item yang dipilih.');
      }
    }

    UniformAdjustmentRequest::query()->create([
      'uniform_item_id' => (int) $validated['uniform_item_id'],
      'lot_id' => $lotId ? (int) $lotId : null,
      'qty_change' => $qtyChange,
      'reason' => $validated['reason'],
      'approval_status' => self::APPROVAL_PENDING,
      'requested_by' => Auth::id(),
      'requested_at' => now(),
    ]);

    return back()->with('success', 'Permintaan penyesuaian berhasil dibuat (MENUNGGU).');
  }

  public function approveAdjustment(UniformAdjustmentRequest $adjustment)
  {
    DB::beginTransaction();
    try {
      $adjustment = UniformAdjustmentRequest::query()->where('id', $adjustment->id)->lockForUpdate()->firstOrFail();
      if ((string) $adjustment->approval_status !== self::APPROVAL_PENDING) {
        DB::rollBack();
        return back()->with('error', 'Request ini sudah diproses.');
      }

      $item = UniformItem::query()->where('id', $adjustment->uniform_item_id)->lockForUpdate()->firstOrFail();
      $qtyChange = (int) $adjustment->qty_change;
      $ref = 'UADJ-' . $adjustment->id;

      if ($qtyChange > 0) {
        $date = now()->format('Ymd');
        $seq = str_pad((string) $this->nextNamedLotSequence((int) $item->id, $date, 'ADJ'), 6, '0', STR_PAD_LEFT);
        $lotNumber = "ADJ-U{$item->id}-{$date}-{$seq}";

        $lot = UniformLot::query()->create([
          'uniform_item_id' => $item->id,
          'lot_number' => $lotNumber,
          'qty_in' => $qtyChange,
          'remaining_qty' => $qtyChange,
          'expired_at' => null,
          'received_at' => now(),
          'received_by' => Auth::id(),
          'notes' => 'Penyesuaian Masuk: ' . $adjustment->reason,
        ]);

        $movement = UniformMovement::query()->create([
          'uniform_item_id' => $item->id,
          'lot_id' => $lot->id,
          'movement_type' => self::MOVEMENT_ADJUSTMENT_IN,
          'qty_change' => $qtyChange,
          'lot_number' => $lotNumber,
          'reference_doc' => $ref,
          'notes' => 'Penyesuaian Masuk disetujui. ' . $adjustment->reason,
          'performed_by' => Auth::id(),
          'performed_at' => now(),
        ]);

        $item->update([
          'current_stock' => (int) $item->current_stock + $qtyChange,
          'last_updated' => now(),
        ]);

        $adjustment->update([
          'approval_status' => self::APPROVAL_APPROVED,
          'approved_by' => Auth::id(),
          'approved_at' => now(),
          'approved_movement_id' => $movement->id,
        ]);

        DB::commit();
        return back()->with('success', 'Penyesuaian masuk disetujui.');
      }

      // ADJUSTMENT_OUT
      $need = abs($qtyChange);
      if ($need <= 0) {
        DB::rollBack();
        return back()->with('error', 'Qty penyesuaian tidak valid.');
      }

      if ($adjustment->lot_id) {
        $lot = UniformLot::query()->where('id', $adjustment->lot_id)->lockForUpdate()->firstOrFail();
        if ((int) $lot->uniform_item_id !== (int) $item->id) {
          DB::rollBack();
          return back()->with('error', 'Lot tidak sesuai item.');
        }
        if ((int) $lot->remaining_qty < $need) {
          DB::rollBack();
          return back()->with('error', 'Sisa lot tidak cukup untuk penyesuaian keluar.');
        }
        if ((int) $item->current_stock < $need) {
          DB::rollBack();
          return back()->with('error', 'Current stock tidak cukup (cache). Jalankan rekonsiliasi stok.');
        }

        $lot->update(['remaining_qty' => (int) $lot->remaining_qty - $need]);
        $movement = UniformMovement::query()->create([
          'uniform_item_id' => $item->id,
          'lot_id' => $lot->id,
          'movement_type' => self::MOVEMENT_ADJUSTMENT_OUT,
          'qty_change' => -$need,
          'lot_number' => $lot->lot_number,
          'reference_doc' => $ref,
          'notes' => 'Penyesuaian Keluar disetujui. ' . $adjustment->reason,
          'performed_by' => Auth::id(),
          'performed_at' => now(),
        ]);

        $item->update([
          'current_stock' => (int) $item->current_stock - $need,
          'last_updated' => now(),
        ]);

        $adjustment->update([
          'approval_status' => self::APPROVAL_APPROVED,
          'approved_by' => Auth::id(),
          'approved_at' => now(),
          'approved_movement_id' => $movement->id,
        ]);

        DB::commit();
        return back()->with('success', 'Penyesuaian keluar disetujui (lot spesifik).');
      }

      // FIFO consume lots (non-expired).
      if ((int) $item->current_stock < $need) {
        DB::rollBack();
        return back()->with('error', 'Current stock tidak cukup (cache). Jalankan rekonsiliasi stok.');
      }
      $parent = UniformMovement::query()->create([
        'uniform_item_id' => $item->id,
        'movement_type' => self::MOVEMENT_ADJUSTMENT_OUT,
        'qty_change' => -$need,
        'reference_doc' => $ref,
        'notes' => 'Penyesuaian Keluar disetujui (FIFO). ' . $adjustment->reason,
        'performed_by' => Auth::id(),
        'performed_at' => now(),
      ]);

      $remaining = $need;
      $lots = UniformLot::query()
        ->where('uniform_item_id', $item->id)
        ->where('remaining_qty', '>', 0)
        ->where(function ($q) {
          $q->whereNull('expired_at')->orWhere('expired_at', '>=', now()->toDateString());
        })
        ->orderBy('received_at')
        ->orderBy('id')
        ->lockForUpdate()
        ->get();

      foreach ($lots as $lot) {
        if ($remaining <= 0) {
          break;
        }
        $available = (int) $lot->remaining_qty;
        if ($available <= 0) {
          continue;
        }
        $take = min($available, $remaining);
        $lot->update(['remaining_qty' => $available - $take]);

        UniformMovement::query()->create([
          'uniform_item_id' => $item->id,
          'lot_id' => $lot->id,
          'reference_movement_id' => $parent->id,
          'movement_type' => self::MOVEMENT_ADJUSTMENT_OUT,
          'qty_change' => -$take,
          'lot_number' => $lot->lot_number,
          'reference_doc' => $ref,
          'notes' => 'Penyesuaian Keluar disetujui (FIFO). ' . $adjustment->reason,
          'performed_by' => Auth::id(),
          'performed_at' => now(),
        ]);

        $remaining -= $take;
      }

      if ($remaining > 0) {
        DB::rollBack();
        return back()->with('error', 'Stok lot tidak cukup untuk penyesuaian keluar (FIFO).');
      }

      $item->update([
        'current_stock' => (int) $item->current_stock - $need,
        'last_updated' => now(),
      ]);

      $adjustment->update([
        'approval_status' => self::APPROVAL_APPROVED,
        'approved_by' => Auth::id(),
        'approved_at' => now(),
        'approved_movement_id' => $parent->id,
      ]);

      DB::commit();
      return back()->with('success', 'Penyesuaian keluar disetujui (FIFO).');
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('Uniform approveAdjustment error: ' . $e->getMessage());
      return back()->with('error', 'Gagal menyetujui penyesuaian.');
    }
  }

  public function rejectAdjustment(Request $request, UniformAdjustmentRequest $adjustment)
  {
    $validated = $request->validate([
      'rejection_reason' => ['required', 'string'],
    ]);

    DB::beginTransaction();
    try {
      $adjustment = UniformAdjustmentRequest::query()->where('id', $adjustment->id)->lockForUpdate()->firstOrFail();
      if ((string) $adjustment->approval_status !== self::APPROVAL_PENDING) {
        DB::rollBack();
        return back()->with('error', 'Request ini sudah diproses.');
      }

      $adjustment->update([
        'approval_status' => self::APPROVAL_REJECTED,
        'approved_by' => Auth::id(),
        'approved_at' => now(),
        'rejection_reason' => $validated['rejection_reason'],
      ]);

      DB::commit();
      return back()->with('success', 'Permintaan penyesuaian ditolak.');
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('Uniform rejectAdjustment error: ' . $e->getMessage());
      return back()->with('error', 'Gagal menolak penyesuaian.');
    }
  }

  public function writeOffs()
  {
    $lots = UniformLot::query()
      ->with('item.sizeMaster')
      ->where('remaining_qty', '>', 0)
      ->orderByDesc('received_at')
      ->limit(500)
      ->get();

    $pending = UniformWriteOffRequest::query()
      ->with(['item.sizeMaster', 'lot', 'requestedBy', 'approvedBy'])
      ->where('approval_status', self::APPROVAL_PENDING)
      ->orderByDesc('requested_at')
      ->limit(200)
      ->get();

    $recent = UniformWriteOffRequest::query()
      ->with(['item.sizeMaster', 'lot', 'requestedBy', 'approvedBy', 'approvedMovement'])
      ->where('approval_status', '!=', self::APPROVAL_PENDING)
      ->orderByDesc('approved_at')
      ->limit(200)
      ->get();

    return view('pages.admin.stock.stock_uniform.uniform_writeoffs', compact('lots', 'pending', 'recent'));
  }

  public function lots(Request $request)
  {
    $query = UniformLot::query()->with(['item.sizeMaster', 'receivedBy']);

    if ($request->filled('uniform_item_id')) {
      $query->where('uniform_item_id', (int) $request->input('uniform_item_id'));
    }

    if ($request->filled('location')) {
      $location = (string) $request->input('location');
      $query->whereHas('item', function ($q) use ($location) {
        $q->where('location', $location);
      });
    }

    $lots = $query->orderByDesc('received_at')->limit(1000)->get();
    $items = UniformItem::query()->with('sizeMaster')->orderBy('item_name')->get();

    return view('pages.admin.stock.stock_uniform.uniform_lots', compact('lots', 'items'));
  }

  public function reconcile()
  {
    $items = UniformItem::query()
      ->select([
        'm_igi_uniform_items.*',
        DB::raw('COALESCE(SUM(l.remaining_qty), 0) as lots_remaining_sum'),
      ])
      ->leftJoin('m_igi_uniform_lots as l', 'l.uniform_item_id', '=', 'm_igi_uniform_items.id')
      ->groupBy('m_igi_uniform_items.id')
      ->orderBy('m_igi_uniform_items.location')
      ->orderBy('m_igi_uniform_items.item_name')
      ->get();

    return view('pages.admin.stock.stock_uniform.uniform_reconcile', compact('items'));
  }

  public function reconcileCreateAdjustment(Request $request)
  {
    $validated = $request->validate([
      'uniform_item_id' => ['required', 'integer', 'exists:m_igi_uniform_items,id'],
      'diff' => ['required', 'integer', 'not_in:0'],
      'reason' => ['nullable', 'string'],
    ]);

    $diff = (int) $validated['diff'];

    UniformAdjustmentRequest::query()->create([
      'uniform_item_id' => (int) $validated['uniform_item_id'],
      'lot_id' => null,
      'qty_change' => $diff,
      'reason' => 'Rekonsiliasi: ' . ((string) ($validated['reason'] ?? 'Perbedaan current_stock vs total sisa lot.')),
      'approval_status' => self::APPROVAL_PENDING,
      'requested_by' => Auth::id(),
      'requested_at' => now(),
    ]);

    return redirect()->route('admin.uniforms.reconcile')->with('success', 'Permintaan penyesuaian (rekonsiliasi) berhasil dibuat (MENUNGGU).');
  }

  public function storeWriteOff(Request $request)
  {
    $validated = $request->validate([
      'lot_id' => ['required', 'integer', 'exists:m_igi_uniform_lots,id'],
      'qty' => ['required', 'integer', 'min:1'],
      'reason' => ['required', 'string'],
    ]);

    $lot = UniformLot::query()->where('id', $validated['lot_id'])->firstOrFail();
    if ((int) $lot->remaining_qty < (int) $validated['qty']) {
      return back()->with('error', 'Sisa lot tidak cukup untuk qty penghapusan yang diminta.');
    }

    UniformWriteOffRequest::query()->create([
      'uniform_item_id' => (int) $lot->uniform_item_id,
      'lot_id' => (int) $lot->id,
      'qty' => (int) $validated['qty'],
      'reason' => $validated['reason'],
      'approval_status' => self::APPROVAL_PENDING,
      'requested_by' => Auth::id(),
      'requested_at' => now(),
    ]);

    return back()->with('success', 'Permintaan penghapusan berhasil dibuat (MENUNGGU).');
  }

  public function approveWriteOff(UniformWriteOffRequest $writeoff)
  {
    DB::beginTransaction();
    try {
      $writeoff = UniformWriteOffRequest::query()->where('id', $writeoff->id)->lockForUpdate()->firstOrFail();
      if ((string) $writeoff->approval_status !== self::APPROVAL_PENDING) {
        DB::rollBack();
        return back()->with('error', 'Request ini sudah diproses.');
      }

      $lot = UniformLot::query()->where('id', $writeoff->lot_id)->lockForUpdate()->firstOrFail();
      $item = UniformItem::query()->where('id', $writeoff->uniform_item_id)->lockForUpdate()->firstOrFail();

      $qty = (int) $writeoff->qty;
      if ((int) $lot->remaining_qty < $qty) {
        DB::rollBack();
        return back()->with('error', 'Sisa lot tidak cukup untuk penghapusan.');
      }
      if ((int) $item->current_stock < $qty) {
        DB::rollBack();
        return back()->with('error', 'Current stock tidak cukup (cache). Jalankan rekonsiliasi stok.');
      }

      $lot->update(['remaining_qty' => (int) $lot->remaining_qty - $qty]);
      $item->update([
        'current_stock' => (int) $item->current_stock - $qty,
        'last_updated' => now(),
      ]);

      $ref = 'UWR-' . $writeoff->id;
      $movement = UniformMovement::query()->create([
        'uniform_item_id' => $item->id,
        'lot_id' => $lot->id,
        'movement_type' => self::MOVEMENT_WRITE_OFF,
        'qty_change' => -$qty,
        'lot_number' => $lot->lot_number,
        'reference_doc' => $ref,
        'notes' => 'Penghapusan disetujui. ' . $writeoff->reason,
        'performed_by' => Auth::id(),
        'performed_at' => now(),
      ]);

      $writeoff->update([
        'approval_status' => self::APPROVAL_APPROVED,
        'approved_by' => Auth::id(),
        'approved_at' => now(),
        'approved_movement_id' => $movement->id,
      ]);

      DB::commit();
      return back()->with('success', 'Penghapusan disetujui.');
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('Uniform approveWriteOff error: ' . $e->getMessage());
      return back()->with('error', 'Gagal menyetujui penghapusan.');
    }
  }

  public function rejectWriteOff(Request $request, UniformWriteOffRequest $writeoff)
  {
    $validated = $request->validate([
      'rejection_reason' => ['required', 'string'],
    ]);

    DB::beginTransaction();
    try {
      $writeoff = UniformWriteOffRequest::query()->where('id', $writeoff->id)->lockForUpdate()->firstOrFail();
      if ((string) $writeoff->approval_status !== self::APPROVAL_PENDING) {
        DB::rollBack();
        return back()->with('error', 'Request ini sudah diproses.');
      }

      $writeoff->update([
        'approval_status' => self::APPROVAL_REJECTED,
        'approved_by' => Auth::id(),
        'approved_at' => now(),
        'rejection_reason' => $validated['rejection_reason'],
      ]);

      DB::commit();
      return back()->with('success', 'Permintaan penghapusan ditolak.');
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('Uniform rejectWriteOff error: ' . $e->getMessage());
      return back()->with('error', 'Gagal menolak penghapusan.');
    }
  }
}
