<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\StockItem;
use App\Models\StockTransaction;
use App\Models\StockNotification;
use App\Models\StockTransferRequest;
use App\Services\StockService;
use App\Services\StockTransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class StockController extends Controller
{
    private const SITES = [
        'jababeka' => 'Jababeka',
        'karawang' => 'Karawang',
    ];

    public function __construct(
        private readonly StockService $stock,
        private readonly StockTransferService $transfers,
    )
    {
    }

    public function masterIndex(Request $request): View
    {
        return view('pages.admin.stock.' . $this->category($request) . '.master.index', [
            'category' => $this->category($request),
        ]);
    }

    public function masterJson(Request $request, string $category, string $stockItem): JsonResponse
    {
        $item = $this->item($request, $stockItem);

        return response()->json([
            'id' => $item->id,
            'code' => $item->code,
            'name' => $item->name,
            'unit' => $item->unit,
            'unit_price' => $item->unit_price,
            'image_url' => $this->imageUrl($request, $item->image_path),
            'current_stock' => $item->current_stock,
            'stock_by_site' => $item->siteBalances()->pluck('on_hand_qty', 'site_code'),
            'low_stock_threshold' => $item->low_stock_threshold,
            'is_active' => $item->is_active,
        ]);
    }

    public function masterDatatable(Request $request): JsonResponse
    {
        $category = $this->category($request);
        $draw = (int) $request->input('draw', 0);
        $start = max(0, (int) $request->input('start', 0));
        $length = max(1, min((int) $request->input('length', 10), 200));
        $search = trim((string) data_get($request->all(), 'search.value', ''));
        $query = StockItem::query()->with('siteBalances')->where('category', $category);
        $total = (clone $query)->count();

        if ($search !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';
            $query->where('name', 'like', $like);
        }

        $filtered = (clone $query)->count();
        $rows = $query->orderByDesc('is_active')->orderBy('name')->skip($start)->take($length)->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $rows->map(fn (StockItem $item) => [
                'id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'unit' => $item->unit,
                'unit_price' => $item->unit_price,
                'current_stock' => $item->current_stock,
                'stock_jababeka' => (int) ($item->siteBalances->firstWhere('site_code', 'jababeka')?->on_hand_qty ?? 0),
                'stock_karawang' => (int) ($item->siteBalances->firstWhere('site_code', 'karawang')?->on_hand_qty ?? 0),
                'image_url' => $this->imageUrl($request, $item->image_path),
                'is_active' => $item->is_active,
            ])->values(),
        ]);
    }

    public function masterTemplate(Request $request)
    {
        $category = $this->category($request);
        return response()->streamDownload(function () use ($category): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'unit', 'unit_price', 'low_stock_threshold', 'is_active']);
            fputcsv($handle, ['Pulpen', 'pcs', 25000, 5, 1]);
            fclose($handle);
        }, 'template-master-' . $category . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function masterExport(Request $request)
    {
        $category = $this->category($request);
        $items = StockItem::query()->with('siteBalances')->where('category', $category)->orderBy('name')->get();
        return response()->streamDownload(function () use ($items): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['code', 'name', 'unit', 'unit_price', 'low_stock_threshold', 'jababeka_stock', 'karawang_stock', 'is_active']);
            foreach ($items as $item) {
                fputcsv($handle, [$item->code, $item->name, $item->unit, $item->unit_price, $item->low_stock_threshold, $item->siteBalances->firstWhere('site_code', 'jababeka')?->on_hand_qty ?? 0, $item->siteBalances->firstWhere('site_code', 'karawang')?->on_hand_qty ?? 0, $item->is_active ? 1 : 0]);
            }
            fclose($handle);
        }, 'master-stock-' . $category . '-' . now()->format('Ymd_His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function masterImport(Request $request): RedirectResponse
    {
        $category = $this->category($request);
        $request->validate(['import_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120']]);
        $handle = fopen($request->file('import_file')->getRealPath(), 'r');
        $headers = array_map(fn ($value) => strtolower(trim((string) $value)), fgetcsv($handle) ?: []);
        $required = ['name', 'unit', 'unit_price', 'low_stock_threshold', 'is_active'];
        if (array_diff($required, $headers)) {
            return back()->with('error', 'Kolom CSV wajib: name, unit, unit_price, low_stock_threshold, is_active.');
        }
        $count = 0;
        DB::transaction(function () use ($handle, $headers, $category, &$count): void {
            while (($row = fgetcsv($handle)) !== false) {
                if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) continue;
                $data = array_combine($headers, array_pad($row, count($headers), null));
                $name = trim((string) ($data['name'] ?? ''));
                if ($name === '') continue;
                $item = StockItem::query()->firstOrNew(['category' => $category, 'name' => $name]);

                $item->fill([
                    'code' => $item->code ?: $this->nextCode($category, $name),
                    'unit' => trim((string) ($data['unit'] ?? '')) ?: 'pcs',
                    'unit_price' => max(0, (int) ($data['unit_price'] ?? 0)),
                    'low_stock_threshold' => max(0, (int) ($data['low_stock_threshold'] ?? 5)),
                    'is_active' => (bool) ((int) ($data['is_active'] ?? 1)),
                ])->save();
                $count++;
            }
            fclose($handle);
        });
        return back()->with('success', "Import berhasil: {$count} barang diproses.");
    }

    public function masterStore(Request $request): RedirectResponse
    {
        $category = $this->category($request);
        $data = $this->validateMaster($request, $category);
        $data['category'] = $category;
        $data['code'] = $this->nextCode($category, (string) $data['name']);
        $data['image_path'] = $request->file('image')?->store('stock/' . $category, 'public');
        unset($data['image']);
        StockItem::query()->create($data);

        return redirect()->route('admin.stock.master.index', ['category' => $category])->with('success', 'Barang berhasil ditambahkan.');
    }

    public function masterUpdate(Request $request, string $category, string $stockItem): RedirectResponse
    {
        $category = $this->category($request);
        $item = $this->item($request, $stockItem);
        $data = $this->validateMaster($request, $category, $item->id);

        if ($request->hasFile('image')) {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
            $data['image_path'] = $request->file('image')->store('stock/' . $category, 'public');
        }
        unset($data['image']);

        $item->update($data);

        return redirect()->route('admin.stock.master.index', ['category' => $category])->with('success', 'Barang berhasil diperbarui.');
    }

    public function masterToggle(Request $request, string $category, string $stockItem): RedirectResponse
    {
        $item = $this->item($request, $stockItem);
        $item->update(['is_active' => !$item->is_active]);

        return back()->with('success', 'Status barang berhasil diperbarui.');
    }

    public function masterDestroy(Request $request, string $category, string $stockItem): RedirectResponse
    {
        $item = $this->item($request, $stockItem);
        if ($item->transactions()->exists()) {
            return back()->with('error', 'Barang yang sudah memiliki ledger tidak dapat dihapus. Nonaktifkan barang tersebut.');
        }

        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }
        $item->delete();

        return back()->with('success', 'Barang berhasil dihapus.');
    }

    public function restockIndex(Request $request): View
    {
        return view('pages.admin.stock.' . $this->category($request) . '.restock.index', $this->pageData($request));
    }

    public function restockStore(Request $request): RedirectResponse
    {
        $category = $this->category($request);
        $data = $request->validate([
            'stock_item_id' => ['required', 'integer', Rule::exists('stock_items', 'id')->where('category', $category)->where('is_active', true)],
            'site_code' => ['required', Rule::in(array_keys(self::SITES))],
            'qty' => ['required', 'integer', 'min:1'],
            'trx_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $data['site_code'] = $this->site($request);
            $transaction = $this->stock->postIn($data, $category);
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.stock.restock.index', ['category' => $category])->with('success', 'Stok masuk berhasil disimpan: ' . $transaction->trx_no);
    }

    public function requestIndex(Request $request): View
    {
        return view('pages.admin.stock.' . $this->category($request) . '.request.index', $this->pageData($request, true));
    }

    public function requestStore(Request $request): RedirectResponse
    {
        $category = $this->category($request);
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'site_code' => ['required', Rule::in(array_keys(self::SITES))],
            'items.*.stock_item_id' => ['required', 'integer', Rule::exists('stock_items', 'id')->where('category', $category)->where('is_active', true)],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'trx_date' => ['required', 'date'],
            'pic_id' => ['required', 'integer', 'exists:m_igi_employees,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            DB::transaction(function () use ($data, $category): void {
                foreach ($data['items'] as $line) {
                    $this->stock->postOut([
                        'stock_item_id' => $line['stock_item_id'],
                        'site_code' => $data['site_code'],
                        'qty' => $line['qty'],
                        'trx_date' => $data['trx_date'],
                        'pic_id' => $data['pic_id'],
                        'notes' => $data['notes'] ?? null,
                    ], $category);
                }
            });
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.stock.request.index', ['category' => $category])->with('success', 'Pengambilan stok berhasil diproses.');
    }

    public function ledgerIndex(Request $request): View
    {
        return view('pages.admin.stock.' . $this->category($request) . '.ledger.index', $this->pageData($request));
    }

    public function ledgerDatatable(Request $request): JsonResponse
    {
        $category = $this->category($request);
        $draw = (int) $request->input('draw', 0);
        $start = max(0, (int) $request->input('start', 0));
        $length = max(1, min((int) $request->input('length', 10), 200));
        $search = trim((string) data_get($request->all(), 'search.value', ''));
        $query = StockTransaction::query()
            ->join('stock_items', 'stock_transactions.stock_item_id', '=', 'stock_items.id')
            ->leftJoin('m_igi_employees', 'stock_transactions.pic_id', '=', 'm_igi_employees.id')
            ->leftJoin('users', 'stock_transactions.created_by', '=', 'users.id')
            ->where('stock_items.category', $category)
            ->select([
                'stock_transactions.id', 'stock_transactions.trx_date', 'stock_transactions.trx_no',
                'stock_transactions.trx_type', 'stock_transactions.qty', 'stock_transactions.stock_after',
                'stock_transactions.site_code',
                'stock_transactions.notes', 'stock_items.name as item_name', 'stock_items.unit as item_unit',
                'm_igi_employees.name as pic_name', 'users.name as creator_name',
            ]);

        $total = StockTransaction::query()->join('stock_items', 'stock_transactions.stock_item_id', '=', 'stock_items.id')->where('stock_items.category', $category)->count();
        $query->when($request->filled('trx_type'), fn ($q) => $q->where('stock_transactions.trx_type', $request->string('trx_type')))
            ->where('stock_transactions.site_code', $this->site($request))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('stock_transactions.trx_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('stock_transactions.trx_date', '<=', $request->date_to));

        if ($search !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('stock_transactions.trx_no', 'like', $like)->orWhere('stock_items.name', 'like', $like)
                    ->orWhere('m_igi_employees.name', 'like', $like)->orWhere('users.name', 'like', $like)
                    ->orWhere('stock_transactions.notes', 'like', $like);
            });
        }

        $filtered = (clone $query)->count('stock_transactions.id');
        $rows = $query->orderByDesc('stock_transactions.trx_date')->orderByDesc('stock_transactions.id')->skip($start)->take($length)->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $rows->map(fn ($row) => [
                'trx_date' => (string) $row->trx_date,
                'trx_no' => $row->trx_no,
                'item_name' => $row->item_name,
                'item_unit' => $row->item_unit,
                'site_code' => $row->site_code,
                'trx_type' => $row->trx_type,
                'qty' => (int) $row->qty,
                'stock_after' => (int) $row->stock_after,
                'pic_name' => $row->pic_name ?: '-',
                'notes' => $row->notes ?: '-',
                'creator_name' => $row->creator_name ?: '-',
            ])->values(),
        ]);
    }

    public function stockReport(Request $request, bool $download = false)
    {
        $category = $this->category($request);
        $site = $this->site($request);
        $items = StockItem::query()->with('siteBalances')->where('category', $category)->where('is_active', true)->orderBy('name')->get();
        $items->each(function (StockItem $item) use ($site): void {
            $item->setAttribute('report_stock', (int) ($item->siteBalances->firstWhere('site_code', $site)?->on_hand_qty ?? 0));
            $item->setAttribute('suggested_qty', max(0, (int) $item->low_stock_threshold - $item->report_stock));
        });
        $data = ['category' => $category, 'siteLabel' => self::SITES[$site], 'items' => $items, 'generatedAt' => now()->format('d/m/Y H:i')];
        if ($download) {
            $pdf = app('dompdf.wrapper')->loadView('pages.admin.stock.shared.report_pdf', $data)->setPaper('a4', 'landscape');
            return $pdf->download('laporan-stok-' . $category . '-' . $site . '-' . now()->format('Ymd_His') . '.pdf');
        }
        return view('pages.admin.stock.shared.report_print', $data);
    }

    public function ledgerExport(Request $request)
    {
        $category = $this->category($request);
        $site = $this->site($request);
        $rows = StockTransaction::query()->with(['item', 'pic', 'creator'])->where('site_code', $site)->whereHas('item', fn ($query) => $query->where('category', $category))->when($request->filled('trx_type'), fn ($query) => $query->where('trx_type', $request->input('trx_type')))->when($request->filled('date_from'), fn ($query) => $query->whereDate('trx_date', '>=', $request->input('date_from')))->when($request->filled('date_to'), fn ($query) => $query->whereDate('trx_date', '<=', $request->input('date_to')))->latest('trx_date')->get();
        return response()->streamDownload(function () use ($rows, $site): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Tanggal', 'No Transaksi', 'Barang', 'Satuan', 'Site', 'Tipe', 'Jumlah', 'Saldo Akhir', 'PIC', 'Catatan', 'Dibuat Oleh']);
            foreach ($rows as $row) fputcsv($handle, [(string) $row->trx_date, $row->trx_no, $row->item?->name, $row->item?->unit, ucfirst($site), $row->trx_type, $row->qty, $row->stock_after, $row->pic?->name ?? '-', $row->notes ?? '-', $row->creator?->name ?? '-']);
            fclose($handle);
        }, 'ledger-stock-' . $category . '-' . $site . '-' . now()->format('Ymd_His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function transferIndex(Request $request): View
    {
        $category = $this->category($request);
        $transfers = StockTransferRequest::query()
            ->with(['item', 'requestedPic', 'requester'])
            ->where('category', $category)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $items = StockItem::query()
            ->with('siteBalances')
            ->where('category', $category)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $items->each(function (StockItem $item): void {
            $item->setAttribute('source_stock', (int) ($item->siteBalances->firstWhere('site_code', 'jababeka')?->on_hand_qty ?? 0));
        });

        return view('pages.admin.stock.' . $category . '.transfer.index', [
            'category' => $category,
            'items' => $items,
            'employees' => Employee::query()->orderBy('name')->get(),
            'transfers' => $transfers,
        ]);
    }

    public function transferStore(Request $request): RedirectResponse
    {
        $category = $this->category($request);
        $data = $request->validate([
            'stock_item_id' => ['required', 'integer', Rule::exists('stock_items', 'id')->where('category', $category)->where('is_active', true)],
            'qty' => ['required', 'integer', 'min:1'],
            'pic_id' => ['nullable', 'integer', 'exists:m_igi_employees,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        try {
            $this->transfers->create($data, $category);
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }
        return back()->with('success', 'Permintaan transfer ke Karawang berhasil dibuat.');
    }

    public function transferPrepare(Request $request, string $category, StockTransferRequest $transfer): RedirectResponse
    {
        abort_unless($transfer->category === $this->category($request), 404);
        try { $this->transfers->prepare($transfer); } catch (RuntimeException $exception) { return back()->with('error', $exception->getMessage()); }
        return back()->with('success', 'Stok transfer sudah disiapkan dari Jababeka.');
    }

    public function transferShip(Request $request, string $category, StockTransferRequest $transfer): RedirectResponse
    {
        abort_unless($transfer->category === $this->category($request), 404);
        $data = $request->validate(['driver_name' => ['required', 'string', 'max:100'], 'driver_phone' => ['nullable', 'string', 'max:40'], 'shipping_notes' => ['nullable', 'string', 'max:1000']]);
        try { $this->transfers->ship($transfer, $data); } catch (RuntimeException $exception) { return back()->with('error', $exception->getMessage()); }
        return back()->with('success', 'Transfer ditandai sedang dikirim.');
    }

    public function transferReceive(Request $request, string $category, StockTransferRequest $transfer): RedirectResponse
    {
        abort_unless($transfer->category === $this->category($request), 404);
        try { $this->transfers->receive($transfer); } catch (RuntimeException $exception) { return back()->with('error', $exception->getMessage()); }
        return back()->with('success', 'Transfer diterima dan stok Karawang bertambah.');
    }

    public function notifications(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $notifications = StockNotification::query()->where('user_id', $userId)->latest()->limit(10)->get();
        $transferCategories = StockTransferRequest::query()
            ->whereIn('id', $notifications->pluck('data.transfer_id')->filter())
            ->pluck('category', 'id');
        $itemCategories = StockItem::query()
            ->whereIn('id', $notifications->pluck('data.stock_item_id')->filter())
            ->pluck('category', 'id');

        return response()->json([
            'unread' => StockNotification::query()->where('user_id', $userId)->whereNull('read_at')->count(),
            'data' => $notifications->map(function (StockNotification $notification) use ($transferCategories, $itemCategories): StockNotification {
                $data = $notification->data ?? [];
                $category = $data['category'] ?? $transferCategories->get($data['transfer_id'] ?? null) ?? $itemCategories->get($data['stock_item_id'] ?? null);

                if (in_array($category, ['atk', 'material', 'apd'], true)) {
                    $notification->setAttribute('context_url', str_starts_with($notification->type, 'transfer_')
                        ? route('admin.stock.transfer.index', ['category' => $category])
                        : route('admin.stock.master.index', ['category' => $category]));
                }

                return $notification;
            })->values(),
        ]);
    }

    public function notificationRead(Request $request, StockNotification $notification): JsonResponse
    {
        abort_unless((int) $notification->user_id === (int) Auth::id(), 403);
        $notification->update(['read_at' => now()]);
        return response()->json(['ok' => true]);
    }

    private function pageData(Request $request, bool $activeOnly = false): array
    {
        $site = $this->site($request);
        $query = StockItem::query()->with('siteBalances')->where('category', $this->category($request))->orderBy('name');
        if ($activeOnly) {
            $query->where('is_active', true);
        }

        $items = $query->get();
        $items->each(function (StockItem $item) use ($site): void {
            $item->setAttribute('selected_stock', (int) ($item->siteBalances->firstWhere('site_code', $site)?->on_hand_qty ?? 0));
            $item->setAttribute('image_url', $item->image_path ? url('storage/' . ltrim($item->image_path, '/')) : null);
        });

        return [
            'category' => $this->category($request),
            'currentSite' => $site,
            'siteLabel' => self::SITES[$site],
            'sites' => self::SITES,
            'items' => $items,
            'employees' => Employee::query()->orderBy('name')->get(),
        ];
    }

    private function validateMaster(Request $request, string $category, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('stock_items', 'name')->where('category', $category)->ignore($ignoreId)],
            'unit' => ['required', 'string', 'max:30'],
            'unit_price' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:1000000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    private function nextCode(string $category, string $name): string
    {
        $letter = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name) ?: 'X', 0, 1));
        $prefix = match ($category) {
            'atk' => 'ATK',
            'material' => 'MAT',
            'apd' => 'APD',
        } . $letter;
        $last = StockItem::query()->where('code', 'like', $prefix . '%')->orderByDesc('code')->value('code');
        $number = $last ? ((int) substr($last, strlen($prefix)) + 1) : 1;

        return $prefix . str_pad((string) $number, 3, '0', STR_PAD_LEFT);
    }

    private function imageUrl(Request $request, ?string $path): ?string
    {
        return $path ? $request->getSchemeAndHttpHost() . '/storage/' . ltrim($path, '/') : null;
    }

    private function category(Request $request): string
    {
        $category = (string) $request->route('category');
        abort_unless(in_array($category, ['atk', 'material', 'apd'], true), 404);

        return $category;
    }

    private function site(Request $request): string
    {
        $site = strtolower(trim((string) $request->input('site_code', $request->query('site', 'jababeka'))));
        abort_unless(array_key_exists($site, self::SITES), 404);

        return $site;
    }

    private function item(Request $request, string $id): StockItem
    {
        $normalizedId = (int) trim($id);
        abort_unless($normalizedId > 0 && (string) $normalizedId === ltrim(trim($id), '0'), 404);

        return StockItem::query()->where('category', $this->category($request))->findOrFail($normalizedId);
    }
}
