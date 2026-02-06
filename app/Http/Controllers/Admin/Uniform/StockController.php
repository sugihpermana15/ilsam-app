<?php

namespace App\Http\Controllers\Admin\Uniform;

use App\Http\Controllers\Controller;
use App\Http\Requests\Uniform\StockInRequest;
use App\Models\UniformLot;
use App\Models\UniformLotStock;
use App\Models\UniformVariant;
use App\Services\UniformStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
        $movement = $this->stock->stockInToLot($request->validated());

        return redirect()
            ->route('admin.uniforms.stock.index')
            ->with('success', 'Stok masuk berhasil disimpan: ' . $movement->movement_no);
    }
}
