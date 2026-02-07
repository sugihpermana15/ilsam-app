<?php

namespace App\Http\Controllers\Admin\Uniform;

use App\Http\Controllers\Controller;
use App\Http\Requests\Uniform\LotStockAdjustRequest;
use App\Http\Requests\Uniform\StockInRequest;
use App\Models\UniformLot;
use App\Models\UniformLotStock;
use App\Models\UniformVariant;
use App\Services\UniformStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class StockController extends Controller
{
    public function __construct(private readonly UniformStockService $stock)
    {
    }

    public function index(): View
    {
        $variants = UniformVariant::query()
            ->with('uniform')
            ->orderBy('uniform_id')
            ->orderBy('size')
            ->get();

        $lots = UniformLot::query()
            ->orderByDesc('received_at')
            ->get();

        return view('pages.admin.uniforms.uniform_stock_index', [
            'variants' => $variants,
            'lots' => $lots,
        ]);
    }

    public function lotIndex(Request $request): View
    {
        return view('pages.admin.uniforms.uniform_stock_lot_manage_index', [
            'variantId' => (int) $request->query('uniform_variant_id', 0),
        ]);
    }

    public function lotDatatable(Request $request): JsonResponse
    {
        $draw = (int) $request->input('draw', 0);
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $length = $length < 0 ? 200 : $length;
        $length = max(1, min($length, 200));

        $search = trim((string) data_get($request->all(), 'search.value', ''));
        $variantId = (int) $request->query('uniform_variant_id', 0);

        $baseCount = UniformLotStock::query();
        if ($variantId > 0) {
            $baseCount->where('uniform_variant_id', $variantId);
        }

        $recordsTotal = (clone $baseCount)->count();

        $query = UniformLotStock::query()
            ->leftJoin('m_igi_uniform_variants', 'm_igi_uniform_lot_stocks.uniform_variant_id', '=', 'm_igi_uniform_variants.id')
            ->leftJoin('m_igi_uniforms', 'm_igi_uniform_variants.uniform_id', '=', 'm_igi_uniforms.id')
            ->leftJoin('m_igi_uniform_lots', 'm_igi_uniform_lot_stocks.uniform_lot_id', '=', 'm_igi_uniform_lots.id')
            ->select([
                'm_igi_uniform_lot_stocks.id as lot_stock_id',
                'm_igi_uniform_lot_stocks.uniform_variant_id as uniform_variant_id',
                'm_igi_uniform_lot_stocks.uniform_lot_id as uniform_lot_id',
                'm_igi_uniforms.name as uniform_name',
                'm_igi_uniform_variants.size as size',
                'm_igi_uniform_lots.lot_code as lot_code',
                'm_igi_uniform_lots.received_at as received_at',
                'm_igi_uniform_lot_stocks.stock_on_hand as stock_on_hand',
            ]);

        if ($variantId > 0) {
            $query->where('m_igi_uniform_lot_stocks.uniform_variant_id', $variantId);
        }

        if ($search !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('m_igi_uniforms.name', 'like', $like)
                    ->orWhere('m_igi_uniform_variants.size', 'like', $like)
                    ->orWhere('m_igi_uniform_lots.lot_code', 'like', $like);
            });
        }

        $recordsFiltered = (clone $query)->count('m_igi_uniform_lot_stocks.id');

        $query->orderBy('m_igi_uniforms.name')
            ->orderBy('m_igi_uniform_variants.size')
            ->orderBy('m_igi_uniform_lots.received_at');

        $rows = $query
            ->skip($start)
            ->take($length)
            ->get()
            ->map(function ($r) {
                return [
                    'lot_stock_id' => (int) ($r->lot_stock_id ?? 0),
                    'uniform_variant_id' => (int) ($r->uniform_variant_id ?? 0),
                    'uniform_lot_id' => (int) ($r->uniform_lot_id ?? 0),
                    'uniform_name' => (string) ($r->uniform_name ?? ''),
                    'size' => (string) ($r->size ?? ''),
                    'lot_code' => (string) ($r->lot_code ?? ''),
                    'received_at' => $r->received_at ? (string) $r->received_at : null,
                    'stock_on_hand' => (int) ($r->stock_on_hand ?? 0),
                ];
            })
            ->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        $draw = (int) $request->input('draw', 0);
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $length = $length < 0 ? 200 : $length;
        $length = max(1, min($length, 200));

        $search = trim((string) data_get($request->all(), 'search.value', ''));

        // Recap should show all variants, including zero stock variants.
        // Therefore we build the grouped SUM query from the variants table and LEFT JOIN lot stocks.
        // For DataTables counts, we count variants (not lot stock rows), because grouped queries
        // make Builder::count() unreliable.
        $countBase = UniformVariant::query()
            ->leftJoin('m_igi_uniforms', 'm_igi_uniform_variants.uniform_id', '=', 'm_igi_uniforms.id');

        $recordsTotal = (clone $countBase)->count('m_igi_uniform_variants.id');

        $base = UniformVariant::query()
            ->leftJoin('m_igi_uniforms', 'm_igi_uniform_variants.uniform_id', '=', 'm_igi_uniforms.id')
            ->leftJoin('m_igi_uniform_lot_stocks', 'm_igi_uniform_lot_stocks.uniform_variant_id', '=', 'm_igi_uniform_variants.id')
            ->select([
                'm_igi_uniform_variants.id as variant_id',
                'm_igi_uniforms.code as uniform_code',
                'm_igi_uniforms.name as uniform_name',
                'm_igi_uniform_variants.size as size',
            ])
            ->selectRaw('COALESCE(SUM(m_igi_uniform_lot_stocks.stock_on_hand), 0) as stock_total')
            ->groupBy([
                'm_igi_uniform_variants.id',
                'm_igi_uniforms.code',
                'm_igi_uniforms.name',
                'm_igi_uniform_variants.size',
            ]);

        $countFiltered = clone $countBase;

        if ($search !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';
            $base->where(function ($q) use ($like) {
                $q->where('m_igi_uniforms.name', 'like', $like)
                    ->orWhere('m_igi_uniforms.code', 'like', $like)
                    ->orWhere('m_igi_uniform_variants.size', 'like', $like);
            });

            $countFiltered->where(function ($q) use ($like) {
                $q->where('m_igi_uniforms.name', 'like', $like)
                    ->orWhere('m_igi_uniforms.code', 'like', $like)
                    ->orWhere('m_igi_uniform_variants.size', 'like', $like);
            });
        }

        $recordsFiltered = (clone $countFiltered)->count('m_igi_uniform_variants.id');

        $orderColumnIndex = (int) data_get($request->all(), 'order.0.column', -1);
        $orderDir = strtolower((string) data_get($request->all(), 'order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $orderDataKey = (string) data_get($request->all(), "columns.$orderColumnIndex.data", '');

        $sortable = [
            'uniform_code' => 'uniform_code',
            'uniform_name' => 'uniform_name',
            'size' => 'size',
            'stock_total' => 'stock_total',
        ];

        if (!empty($orderDataKey) && isset($sortable[$orderDataKey])) {
            $base->orderBy($sortable[$orderDataKey], $orderDir);
        } else {
            $base->orderBy('uniform_name')->orderBy('size');
        }

        $rows = $base
            ->skip($start)
            ->take($length)
            ->get()
            ->map(function ($r) {
                return [
                    'variant_id' => (int) ($r->variant_id ?? 0),
                    'uniform_code' => (string) ($r->uniform_code ?? ''),
                    'uniform_name' => (string) ($r->uniform_name ?? ''),
                    'size' => (string) ($r->size ?? ''),
                    'stock_total' => (int) ($r->stock_total ?? 0),
                ];
            })
            ->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    public function stockIn(StockInRequest $request): RedirectResponse
    {
        try {
            $movement = $this->stock->stockInToLot($request->validated());
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.uniforms.stock.index')
            ->with('success', 'Stok masuk berhasil disimpan: ' . $movement->movement_no);
    }

    public function adjustLotStock(LotStockAdjustRequest $request, UniformLotStock $lotStock): RedirectResponse
    {
        $data = $request->validated();

        try {
            $movement = $this->stock->adjustLotStockOnHand(
                (int) $lotStock->getKey(),
                (int) ($data['stock_on_hand'] ?? 0),
                $data['occurred_at'] ?? null,
                (string) ($data['notes'] ?? '')
            );
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.uniforms.stock.lots.index')
            ->with('success', 'Penyesuaian stok berhasil disimpan: ' . $movement->movement_no);
    }
}
