<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetLocation;
use App\Models\AssetUom;
use App\Models\AssetVendor;
use App\Models\DeletedAsset;
use App\Models\TransferAsset;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Milon\Barcode\DNS1D;

class AssetController extends Controller
{

  private function nextAssetSequence(string $assetCategory): int
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

    $codes = Asset::withTrashed()
      ->where('asset_category', $assetCategory)
      ->whereNotNull('asset_code')
      ->pluck('asset_code');
    foreach ($codes as $code) {
      $seq = $extractSeq($code);
      if ($seq !== null && $seq > $max) {
        $max = $seq;
      }
    }

    $deletedCodes = DeletedAsset::query()
      ->where('asset_category', $assetCategory)
      ->whereNotNull('asset_code')
      ->pluck('asset_code');
    foreach ($deletedCodes as $code) {
      $seq = $extractSeq($code);
      if ($seq !== null && $seq > $max) {
        $max = $seq;
      }
    }

    $transferCodes = TransferAsset::query()
      ->where('asset_category', $assetCategory)
      ->whereNotNull('asset_code')
      ->pluck('asset_code');
    foreach ($transferCodes as $code) {
      $seq = $extractSeq($code);
      if ($seq !== null && $seq > $max) {
        $max = $seq;
      }
    }

    return $max + 1;
  }

  public function transfer(Request $request)
  {
    // Outbound page is for Jababeka by default
    $location = $request->query('location', 'Jababeka');

    // Assets to show in modal list: exclude assets that currently have an active outbound request
    $activeTransferIds = TransferAsset::query()
      ->whereNull('cancelled_at')
      ->whereNull('received_at')
      ->pluck('asset_id')
      ->filter()
      ->all();

    $assets = Asset::query()
      ->when(!empty($location), fn($q) => $q->where('asset_location', $location))
      ->when(!empty($activeTransferIds), fn($q) => $q->whereNotIn('id', $activeTransferIds))
      ->orderByDesc('id')
      ->get();

    // Transfers table: show only active outbound requests (not cancelled, not received)
    $transfers = TransferAsset::query()
      ->whereNull('cancelled_at')
      ->whereNull('received_at')
      ->where(function ($q) {
        $q->whereNull('status')->orWhere('status', 'OUT_REQUESTED');
      })
      ->when(!empty($location), fn($q) => $q->where('from_location', $location))
      ->orderByDesc('id')
      ->get();

    return view('pages.admin.asset.asset_transfer', compact('assets', 'transfers'));
  }

  public function modalList()
  {
    $transferredIds = TransferAsset::query()
      ->whereNull('cancelled_at')
      ->whereNull('received_at')
      ->pluck('asset_id')
      ->filter()
      ->all();

    $assets = Asset::query()
      ->when(!empty($transferredIds), fn($q) => $q->whereNotIn('id', $transferredIds))
      ->orderByDesc('id')
      ->get();

    return response()->json($assets);
  }

  public function transferList()
  {
    $transfers = TransferAsset::query()
      ->whereNull('cancelled_at')
      ->whereNull('received_at')
      ->where(function ($q) {
        $q->whereNull('status')->orWhere('status', 'OUT_REQUESTED');
      })
      ->orderByDesc('id')
      ->get();
    return response()->json($transfers);
  }

  public function saveTransfer(Request $request)
  {
    $validated = $request->validate([
      'selected_ids' => ['required', 'string'],
    ]);

    $ids = collect(explode(',', $validated['selected_ids']))
      ->map(fn($v) => trim($v))
      ->filter(fn($v) => $v !== '')
      ->map(fn($v) => (int) $v)
      ->filter(fn($v) => $v > 0)
      ->unique()
      ->values();

    if ($ids->isEmpty()) {
      return redirect()->route('admin.assets.transfer')->with('error', 'Tidak ada asset yang dipilih.');
    }

    DB::beginTransaction();
    try {
      // Prevent creating duplicate active outbound request for the same asset
      $alreadyTransferred = TransferAsset::query()
        ->whereIn('asset_id', $ids)
        ->whereNull('cancelled_at')
        ->whereNull('received_at')
        ->pluck('asset_id')
        ->all();

      $idsToTransfer = $ids->diff($alreadyTransferred)->values();

      if ($idsToTransfer->isEmpty()) {
        DB::rollBack();
        return redirect()->route('admin.assets.transfer')->with('error', 'Asset yang dipilih sudah pernah diajukan pindah.');
      }

      $assets = Asset::query()->whereIn('id', $idsToTransfer)->get();

      foreach ($assets as $asset) {
        $fromLocation = $asset->asset_location;
        $toLocation = $fromLocation === 'Jababeka' ? 'Karawang' : null;

        if ($fromLocation !== 'Jababeka') {
          continue;
        }

        TransferAsset::create([
          'asset_id' => $asset->id,
          'asset_code' => $asset->asset_code,
          'asset_name' => $asset->asset_name,
          'asset_category' => $asset->asset_category,
          // Keep legacy column for display/backward compat; from/to are used for filtering & audit
          'asset_location' => $asset->asset_location,
          'from_location' => $fromLocation,
          'to_location' => $toLocation,
          'person_in_charge' => $asset->person_in_charge,
          'asset_status' => $asset->asset_status,
          'purchase_date' => $asset->purchase_date,
          'price' => $asset->price,
          'asset_condition' => $asset->asset_condition,
          'ownership_status' => $asset->ownership_status,
          'description' => $asset->description,
          'last_updated' => $asset->last_updated,
          'transferred_at' => now(),
          'asset_payload' => $asset->toArray(),

          'status' => 'OUT_REQUESTED',
          'requested_by' => Auth::id(),
          'requested_at' => now(),
        ]);
      }

      DB::commit();
      return redirect()->route('admin.assets.transfer')->with('success', 'Asset berhasil diajukan pindah.');
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('saveTransfer error: ' . $e->getMessage());
      return redirect()->route('admin.assets.transfer')->with('error', 'Gagal menyimpan pengajuan pindah asset.');
    }
  }

  public function cancelTransfer(Request $request)
  {
    $validated = $request->validate([
      'selected_transfer_ids' => ['required', 'string'],
    ]);

    $transferIds = collect(explode(',', $validated['selected_transfer_ids']))
      ->map(fn($v) => trim($v))
      ->filter(fn($v) => $v !== '')
      ->map(fn($v) => (int) $v)
      ->filter(fn($v) => $v > 0)
      ->unique()
      ->values();

    if ($transferIds->isEmpty()) {
      return redirect()->route('admin.assets.transfer')->with('error', 'Tidak ada data transfer yang dipilih.');
    }

    DB::beginTransaction();
    try {
      // Lock selected transfers to avoid race with scan
      $transfers = TransferAsset::query()
        ->whereIn('id', $transferIds)
        ->lockForUpdate()
        ->get();

      $hasConflict = false;
      foreach ($transfers as $transfer) {
        $isReceived = !empty($transfer->received_at) || ($transfer->status === 'RECEIVED');
        if ($isReceived) {
          $hasConflict = true;
          break;
        }
      }

      if ($hasConflict) {
        DB::rollBack();
        $message = 'Tidak bisa dibatalkan karena sudah discan/diterima.';
        if ($request->expectsJson()) {
          return response()->json(['message' => $message], 409);
        }
        return redirect()->route('admin.assets.transfer')->with('error', $message)->setStatusCode(409);
      }

      $cancelledCount = 0;
      foreach ($transfers as $transfer) {
        $isAlreadyCancelled = !empty($transfer->cancelled_at) || ($transfer->status === 'CANCELLED');
        if ($isAlreadyCancelled) {
          continue;
        }

        // Cancel only OPEN/REQUESTED transfers
        $isOpen = empty($transfer->status) || ($transfer->status === 'OUT_REQUESTED');
        if (!$isOpen) {
          continue;
        }

        $transfer->update([
          'status' => 'CANCELLED',
          'cancelled_by' => Auth::id(),
          'cancelled_at' => now(),
        ]);
        $cancelledCount++;
      }

      DB::commit();
      if ($cancelledCount === 0) {
        return redirect()->route('admin.assets.transfer')->with('error', 'Tidak ada pengajuan yang bisa dibatalkan.');
      }

      return redirect()->route('admin.assets.transfer')->with('success', 'Pengajuan pindah berhasil dibatalkan.');
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('cancelTransfer error: ' . $e->getMessage());
      return redirect()->route('admin.assets.transfer')->with('error', 'Gagal membatalkan pengajuan pindah asset.');
    }
  }

  public function printSelectedBarcode(Request $request)
  {
    $ids = explode(',', $request->input('selected_ids', ''));
    $assets = Asset::whereIn('id', $ids)->get();
    return view('pages.admin.asset.asset_barcode_print', compact('assets'));
  }
  // Serve barcode image as PNG for asset_code
  public function barcodeImage($code)
  {
    try {
      // Generate barcode using milon/barcode library
      $barcode = new DNS1D();
      $barcodeBase64 = $barcode->getBarcodePNG($code, 'C128', 2, 60, [0, 0, 0], false);

      // Check if result is base64 encoded
      if (!empty($barcodeBase64)) {
        // If it contains data URI prefix, strip it
        if (strpos($barcodeBase64, 'data:image') === 0) {
          // Extract just the base64 part after comma
          $barcodeBase64 = explode(',', $barcodeBase64)[1] ?? $barcodeBase64;
        }

        // Decode base64 to binary
        $barcodeImage = base64_decode($barcodeBase64, true);

        if ($barcodeImage === false) {
          throw new \Exception('Failed to decode barcode base64');
        }

        return response($barcodeImage, 200)
          ->header('Content-Type', 'image/png')
          ->header('Cache-Control', 'public, max-age=3600');
      } else {
        throw new \Exception('Barcode generation returned empty result');
      }
    } catch (\Exception $e) {
      Log::error('Barcode generation error: ' . $e->getMessage() . ' for code: ' . $code);
      abort(500, 'Barcode generation failed: ' . $e->getMessage());
    }
  }

  public function printBarcode($id)
  {
    $asset = Asset::findOrFail($id);
    return view('pages.admin.asset.asset_barcode_print', compact('asset'));
  }
  public function __construct()
  {
    date_default_timezone_set('Asia/Jakarta');
  }
  public function edit($id)
  {
    return redirect()->route('admin.assets.index', ['edit' => $id]);
  }

  public function index(Request $request)
  {
    $location = $request->query('location');

    $assetCategories = AssetCategory::query()->where('is_active', true)->orderBy('code')->get();
    $assetLocations = AssetLocation::query()->where('is_active', true)->orderBy('name')->get();
    $assetUoms = AssetUom::query()->where('is_active', true)->orderBy('name')->get();
    $assetVendors = AssetVendor::query()->where('is_active', true)->orderBy('name')->get();
    $departments = Department::query()->orderBy('name')->get();
    $employees = Employee::query()->orderBy('name')->get();

    $activeTransferIds = TransferAsset::query()
      ->whereNull('cancelled_at')
      ->whereNull('received_at')
      ->pluck('asset_id')
      ->filter()
      ->all();

    $assets = Asset::query()
      ->when(!empty($location), fn($q) => $q->where('asset_location', $location))
      ->when(!empty($activeTransferIds), fn($q) => $q->whereNotIn('id', $activeTransferIds))
      ->orderByDesc('id')
      ->paginate(10)
      ->withQueryString();
    return view('pages.admin.asset.asset_pt', compact('assets', 'assetCategories', 'assetLocations', 'assetUoms', 'assetVendors', 'departments', 'employees'));
  }

  public function jababeka()
  {
    return redirect()->route('admin.assets.index', ['location' => 'Jababeka']);
  }

  public function karawang()
  {
    return redirect()->route('admin.assets.index', ['location' => 'Karawang']);
  }

  public function in()
  {
    // Show recent inbound scans (received) for quick audit
    $recentReceipts = TransferAsset::query()
      ->whereNotNull('received_at')
      ->orderByDesc('received_at')
      ->limit(50)
      ->get();

    return view('pages.admin.asset.asset_in', compact('recentReceipts'));
  }

  public function scanIn(Request $request)
  {
    $validated = $request->validate([
      'asset_code' => ['required', 'string'],
    ]);

    $assetCode = trim($validated['asset_code']);

    DB::beginTransaction();
    try {
      // 1) Try to receive an OPEN outbound request (lock row to avoid race with cancel)
      $transfer = TransferAsset::query()
        ->where('asset_code', $assetCode)
        ->where('from_location', 'Jababeka')
        ->where('to_location', 'Karawang')
        ->whereNull('cancelled_at')
        ->whereNull('received_at')
        ->where(function ($q) {
          $q->whereNull('status')->orWhere('status', 'OUT_REQUESTED');
        })
        ->lockForUpdate()
        ->first();

      if ($transfer) {
        $now = now();

        // Mark received (idempotent at DB level: only open rows reach here)
        $transfer->update([
          'status' => 'RECEIVED',
          'received_by' => Auth::id(),
          'received_at' => $now,
          // Keep legacy location column consistent for display
          'asset_location' => 'Karawang',
          'last_updated' => $now,
        ]);

        // Update current state in assets table
        Asset::query()
          ->where('id', $transfer->asset_id)
          ->update([
            'asset_location' => 'Karawang',
            'last_updated' => $now,
          ]);

        Log::info('Asset received via scan', [
          'asset_code' => $assetCode,
          'transfer_id' => $transfer->id,
          'asset_id' => $transfer->asset_id,
          'from_location' => $transfer->from_location,
          'to_location' => $transfer->to_location,
          'received_by' => Auth::id(),
          'received_at' => $now->toDateTimeString(),
        ]);

        DB::commit();
        return redirect()->route('admin.assets.in')->with('success', 'Asset berhasil diterima (scan OK).');
      }

      // 2) Idempotent: if already received, return success without changing anything
      $alreadyReceived = TransferAsset::query()
        ->where('asset_code', $assetCode)
        ->where('from_location', 'Jababeka')
        ->where('to_location', 'Karawang')
        ->where(function ($q) {
          $q->whereNotNull('received_at')->orWhere('status', 'RECEIVED');
        })
        ->orderByDesc('received_at')
        ->first();

      if ($alreadyReceived) {
        DB::commit();
        return redirect()->route('admin.assets.in')->with('success', 'Barcode sudah pernah diterima sebelumnya.');
      }

      // 3) If cancelled, reject with a clear error
      $cancelled = TransferAsset::query()
        ->where('asset_code', $assetCode)
        ->where('from_location', 'Jababeka')
        ->where('to_location', 'Karawang')
        ->where(function ($q) {
          $q->whereNotNull('cancelled_at')->orWhere('status', 'CANCELLED');
        })
        ->orderByDesc('cancelled_at')
        ->first();

      if ($cancelled) {
        DB::commit();
        $message = 'Tidak bisa menerima asset karena pengajuan sudah dibatalkan.';
        if ($request->expectsJson()) {
          return response()->json(['message' => $message], 409);
        }
        return redirect()->route('admin.assets.in')->with('error', $message)->setStatusCode(409);
      }

      DB::rollBack();
      return redirect()->route('admin.assets.in')->with('error', 'Barcode tidak valid / tidak ada di daftar pengajuan Asset Keluar.');
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('scanIn error: ' . $e->getMessage());
      return redirect()->route('admin.assets.in')->with('error', 'Gagal memproses scan asset masuk.');
    }
  }

  public function create()
  {
    return redirect()->route('admin.assets.index', ['open' => 'full']);
  }

  public function store(Request $request)
  {
    // Ensure empty select values don't break validation (some setups may not convert '' to null)
    foreach (['satuan', 'vendor_supplier', 'department_id', 'person_in_charge_employee_id'] as $key) {
      if ($request->has($key) && $request->input($key) === '') {
        $request->merge([$key => null]);
      }
    }

    $validated = $request->validate([
      // 'asset_code' => 'required|unique:m_igi_asset,asset_code', // di-generate otomatis
      'asset_name' => 'required',
      'asset_category' => [
        'required',
        'string',
        Rule::exists('m_igi_asset_categories', 'code')->where(fn($q) => $q->where('is_active', true)),
      ],
      'brand_type_model' => 'nullable',
      'serial_number' => 'nullable',
      'description' => 'nullable',
      'purchase_date' => 'nullable|date',
      'price' => 'nullable|numeric',
      'qty' => 'nullable|integer|min:0',
      'satuan' => [
        'nullable',
        'string',
        'max:50',
        Rule::exists('m_igi_asset_uoms', 'name')->where(fn($q) => $q->where('is_active', true)),
      ],
      'vendor_supplier' => [
        'nullable',
        'string',
        'max:100',
        Rule::exists('m_igi_asset_vendors', 'name')->where(fn($q) => $q->where('is_active', true)),
      ],
      'invoice_number' => 'nullable',
      'asset_location' => [
        'required',
        'string',
        Rule::exists('m_igi_asset_locations', 'name')->where(fn($q) => $q->where('is_active', true)),
      ],
      'department_id' => ['nullable', 'integer', Rule::exists('m_igi_departments', 'id')],
      'person_in_charge_employee_id' => ['nullable', 'integer', Rule::exists('m_igi_employees', 'id')],
      'department' => ['nullable', 'string', 'max:255'],
      'person_in_charge' => ['nullable', 'string', 'max:255'],
      'ownership_status' => 'nullable',
      'asset_condition' => 'nullable',
      'asset_status' => 'nullable',
      'start_use_date' => 'nullable|date',
      'warranty_status' => 'nullable',
      'warranty_end_date' => 'nullable|date',
      'input_by' => 'nullable',
      'notes' => 'nullable',
      'image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
      'image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
      'image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if (!empty($validated['department_id'])) {
      $dept = Department::query()->find((int) $validated['department_id']);
      if ($dept) {
        $validated['department'] = $dept->name;
      }
    }
    if (!empty($validated['person_in_charge_employee_id'])) {
      $emp = Employee::query()->find((int) $validated['person_in_charge_employee_id']);
      if ($emp) {
        $validated['person_in_charge'] = $emp->name;
      }
    }

    // Normalize empty strings from select fields
    foreach (['satuan', 'vendor_supplier', 'department', 'person_in_charge'] as $key) {
      if (array_key_exists($key, $validated) && trim((string) $validated[$key]) === '') {
        $validated[$key] = null;
      }
    }
    // Handle file uploads
    foreach (['image_1', 'image_2', 'image_3'] as $imgField) {
      if ($request->hasFile($imgField)) {
        $file = $request->file($imgField);
        $filename = uniqid($imgField . '_') . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('assets'), $filename);
        $validated[$imgField] = 'assets/' . $filename;
      }
    }

    // Generate asset_code otomatis
    $catMap = [
      'IT' => 'IT',
      'Vehicle' => 'VH',
      'Machine' => 'MC',
      'Furniture' => 'FR',
      'Other' => 'OT',
    ];
    $locMap = [
      'Jababeka' => '01',
      'Karawang' => '02',
    ];
    $catPrefix = AssetCategory::query()->where('code', $validated['asset_category'])->value('asset_code_prefix');
    $locPrefix = AssetLocation::query()->where('name', $validated['asset_location'])->value('asset_code_prefix');
    $cat = $catPrefix ?: ($catMap[$validated['asset_category']] ?? 'XX');
    $loc = $locPrefix ?: ($locMap[$validated['asset_location']] ?? '00');
    $dateSource = $validated['start_use_date'] ?? ($validated['purchase_date'] ?? now()->toDateString());
    $date = date('Ymd', strtotime($dateSource));
    // Hitung urutan per kategori berdasarkan nomor terakhir (max) + 1
    $nextSeq = $this->nextAssetSequence($validated['asset_category']);
    $urut = str_pad($nextSeq, 6, '0', STR_PAD_LEFT);
    $asset_code = "IGI-$cat-$loc-$date-$urut";
    $validated['asset_code'] = $asset_code;
    $validated['input_date'] = now();
    $validated['last_updated'] = now();
    Asset::create($validated);
    return redirect()->route('admin.assets.index')->with('success', 'Asset berhasil ditambahkan.');
  }

  public function show($id)
  {
    $asset = Asset::findOrFail($id);
    // Ensure photo path is set for the view (if photo column exists)
    // If photo is not set, the Blade will fallback to no-image.png
    return view('pages.admin.asset.asset_detail', compact('asset'));
  }

  public function json($id)
  {
    $asset = Asset::withTrashed()->findOrFail($id);

    return response()->json([
      'id' => $asset->id,
      'asset_code' => $asset->asset_code,
      'asset_name' => $asset->asset_name,
      'asset_category' => $asset->asset_category,
      'asset_location' => $asset->asset_location,
      'qty' => $asset->qty,
      'satuan' => $asset->satuan,
      'vendor_supplier' => $asset->vendor_supplier,
      'invoice_number' => $asset->invoice_number,
      'brand_type_model' => $asset->brand_type_model,
      'serial_number' => $asset->serial_number,
      'description' => $asset->description,
      'purchase_date' => $asset->purchase_date,
      'price' => $asset->price,
      'department_id' => $asset->department_id,
      'department' => $asset->department,
      'person_in_charge_employee_id' => $asset->person_in_charge_employee_id,
      'person_in_charge' => $asset->person_in_charge,
      'ownership_status' => $asset->ownership_status,
      'asset_condition' => $asset->asset_condition,
      'asset_status' => $asset->asset_status,
      'start_use_date' => $asset->start_use_date,
      'warranty_status' => $asset->warranty_status,
      'warranty_end_date' => $asset->warranty_end_date,
      'notes' => $asset->notes,
      'image_1' => $asset->image_1,
      'image_2' => $asset->image_2,
      'image_3' => $asset->image_3,
      'image_1_url' => !empty($asset->image_1) ? asset($asset->image_1) : null,
      'image_2_url' => !empty($asset->image_2) ? asset($asset->image_2) : null,
      'image_3_url' => !empty($asset->image_3) ? asset($asset->image_3) : null,
    ]);
  }

  public function update(Request $request, $id)
  {
    $asset = Asset::findOrFail($id);

    foreach (['satuan', 'vendor_supplier', 'department_id', 'person_in_charge_employee_id'] as $key) {
      if ($request->has($key) && $request->input($key) === '') {
        $request->merge([$key => null]);
      }
    }

    $validated = $request->validate([
      // 'asset_code' => 'required|unique:m_igi_asset,asset_code,' . $id, // di-generate otomatis
      'asset_name' => 'required',
      'asset_category' => [
        'required',
        'string',
        Rule::exists('m_igi_asset_categories', 'code')->where(fn($q) => $q->where('is_active', true)),
      ],
      'brand_type_model' => 'nullable',
      'serial_number' => 'nullable',
      'description' => 'nullable',
      'purchase_date' => 'nullable|date',
      'price' => 'nullable|numeric',
      'qty' => 'nullable|integer|min:0',
      'satuan' => [
        'nullable',
        'string',
        'max:50',
        Rule::exists('m_igi_asset_uoms', 'name')->where(fn($q) => $q->where('is_active', true)),
      ],
      'vendor_supplier' => [
        'nullable',
        'string',
        'max:100',
        Rule::exists('m_igi_asset_vendors', 'name')->where(fn($q) => $q->where('is_active', true)),
      ],
      'invoice_number' => 'nullable',
      'asset_location' => [
        'required',
        'string',
        Rule::exists('m_igi_asset_locations', 'name')->where(fn($q) => $q->where('is_active', true)),
      ],
      'department_id' => ['nullable', 'integer', Rule::exists('m_igi_departments', 'id')],
      'person_in_charge_employee_id' => ['nullable', 'integer', Rule::exists('m_igi_employees', 'id')],
      'department' => ['nullable', 'string', 'max:255'],
      'person_in_charge' => ['nullable', 'string', 'max:255'],
      'ownership_status' => 'nullable',
      'asset_condition' => 'nullable',
      'asset_status' => 'nullable',
      'start_use_date' => 'nullable|date',
      'warranty_status' => 'nullable',
      'warranty_end_date' => 'nullable|date',
      'input_by' => 'nullable',
      'notes' => 'nullable',
      'image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
      'image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
      'image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if (!empty($validated['department_id'])) {
      $dept = Department::query()->find((int) $validated['department_id']);
      if ($dept) {
        $validated['department'] = $dept->name;
      }
    }
    if (!empty($validated['person_in_charge_employee_id'])) {
      $emp = Employee::query()->find((int) $validated['person_in_charge_employee_id']);
      if ($emp) {
        $validated['person_in_charge'] = $emp->name;
      }
    }

    foreach (['satuan', 'vendor_supplier', 'department', 'person_in_charge'] as $key) {
      if (array_key_exists($key, $validated) && trim((string) $validated[$key]) === '') {
        $validated[$key] = null;
      }
    }
    // Handle file uploads
    foreach (['image_1', 'image_2', 'image_3'] as $imgField) {
      if ($request->hasFile($imgField)) {
        $file = $request->file($imgField);
        $filename = uniqid($imgField . '_') . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('assets'), $filename);
        $validated[$imgField] = 'assets/' . $filename;
      } else {
        // If not uploading a new file, keep the old value
        $validated[$imgField] = $asset[$imgField];
      }
    }

    // Generate asset_code otomatis hanya jika kategori/lokasi/tanggal berubah
    $catMap = [
      'IT' => 'IT',
      'Vehicle' => 'VH',
      'Machine' => 'MC',
      'Furniture' => 'FR',
      'Other' => 'OT',
    ];
    $locMap = [
      'Jababeka' => '01',
      'Karawang' => '02',
    ];

    // start_use_date boleh diubah untuk stok tanpa mengubah kode.
    $shouldRegenerateCode =
      ($asset->asset_category !== ($validated['asset_category'] ?? null)) ||
      ($asset->asset_location !== ($validated['asset_location'] ?? null));

    if ($shouldRegenerateCode) {
      $catPrefix = AssetCategory::query()->where('code', $validated['asset_category'])->value('asset_code_prefix');
      $locPrefix = AssetLocation::query()->where('name', $validated['asset_location'])->value('asset_code_prefix');
      $cat = $catPrefix ?: ($catMap[$validated['asset_category']] ?? 'XX');
      $loc = $locPrefix ?: ($locMap[$validated['asset_location']] ?? '00');
      $dateSource = $validated['start_use_date'] ?? ($asset->start_use_date ?? ($validated['purchase_date'] ?? ($asset->purchase_date ?? now()->toDateString())));
      $date = date('Ymd', strtotime($dateSource));

      $nextSeq = $this->nextAssetSequence($validated['asset_category']);
      $urut = str_pad($nextSeq, 6, '0', STR_PAD_LEFT);
      $asset_code = "IGI-$cat-$loc-$date-$urut";
      $validated['asset_code'] = $asset_code;
    } else {
      unset($validated['asset_code']);
    }

    $validated['last_updated'] = now();
    $asset->update($validated);
    return redirect()->route('admin.assets.index')->with('success', 'Asset berhasil diperbarui.');
  }

  public function destroy($id)
  {
    $asset = Asset::findOrFail($id);
    $userId = Auth::id();
    // Copy data ke deleted_asset
    $deletedData = $asset->toArray();
    $deletedData['asset_id'] = $asset->id;
    $deletedData['deleted_at'] = now();
    $deletedData['deleted_by'] = $userId;
    unset($deletedData['id']);
    DeletedAsset::create($deletedData);
    $asset->delete();
    return redirect()->route('admin.assets.index')->with('success', 'Asset berhasil dihapus dan dipindahkan ke riwayat.');
  }

  public function historyDelete(Request $request)
  {
    $search = $request->input('search');
    $query = DeletedAsset::query();
    if ($search) {
      $query->where(function ($q) use ($search) {
        $q->where('asset_code', 'like', "%$search%")
          ->orWhere('asset_name', 'like', "%$search%")
          ->orWhere('asset_category', 'like', "%$search%")
          ->orWhere('department', 'like', "%$search%")
          ->orWhere('person_in_charge', 'like', "%$search%")
          ->orWhere('notes', 'like', "%$search%")
        ;
      });
    }
    $deletedAssets = $query->orderByDesc('deleted_at')->paginate(10);
    return view('pages.admin.asset.asset_history_delete', compact('deletedAssets', 'search'));
  }

  public function restore($id)
  {
    $deletedAsset = DeletedAsset::findOrFail($id);
    // Hapus data asset dengan asset_code yang sama jika ada (jaga-jaga restore berulang)
    // Hapus semua data (termasuk soft deleted) dengan asset_code yang sama
    Asset::withTrashed()->where('asset_code', $deletedAsset->asset_code)->forceDelete();
    // Kembalikan ke tabel asset
    $assetData = $deletedAsset->toArray();
    unset($assetData['id'], $assetData['deleted_at'], $assetData['deleted_by'], $assetData['created_at'], $assetData['updated_at']);
    Asset::create($assetData);
    $deletedAsset->delete();
    return redirect()->route('admin.assets.historyDelete')->with('success', 'Asset berhasil direstore.');
  }
}
