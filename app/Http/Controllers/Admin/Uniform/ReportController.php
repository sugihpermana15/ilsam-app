<?php

namespace App\Http\Controllers\Admin\Uniform;

use App\Http\Controllers\Controller;
use App\Models\Uniform;
use App\Models\UniformLotStock;
use App\Models\UniformVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function pivotIndex(): View
    {
        $sizes = UniformVariant::query()
            ->select('size')
            ->distinct()
            ->orderBy('size')
            ->pluck('size')
            ->values();

        return view('pages.admin.uniforms.uniform_stock_pivot_index', [
            'sizes' => $sizes,
        ]);
    }

    public function pivotDatatable(Request $request): JsonResponse
    {
        $draw = (int) $request->input('draw', 0);
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $length = $length < 0 ? 200 : $length;
        $length = max(1, min($length, 200));

        $sizes = UniformVariant::query()->select('size')->distinct()->orderBy('size')->pluck('size')->values();
        $search = trim((string) data_get($request->all(), 'search.value', ''));

        $uniformsQuery = Uniform::query()->select(['id', 'code', 'name']);
        $recordsTotal = (clone $uniformsQuery)->count();

        if ($search !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';
            $uniformsQuery->where(function ($q) use ($like) {
                $q->where('code', 'like', $like)->orWhere('name', 'like', $like);
            });
        }

        $recordsFiltered = (clone $uniformsQuery)->count();

        $uniforms = $uniformsQuery
            ->orderBy('name')
            ->skip($start)
            ->take($length)
            ->get();

        $uniformIds = $uniforms->pluck('id')->all();

        $agg = [];
        if (count($uniformIds) > 0) {
            $rows = UniformLotStock::query()
                ->leftJoin('m_igi_uniform_variants', 'm_igi_uniform_lot_stocks.uniform_variant_id', '=', 'm_igi_uniform_variants.id')
                ->whereIn('m_igi_uniform_variants.uniform_id', $uniformIds)
                ->select([
                    'm_igi_uniform_variants.uniform_id as uniform_id',
                    'm_igi_uniform_variants.size as size',
                ])
                ->selectRaw('SUM(m_igi_uniform_lot_stocks.stock_on_hand) as qty')
                ->groupBy(['m_igi_uniform_variants.uniform_id', 'm_igi_uniform_variants.size'])
                ->get();

            foreach ($rows as $r) {
                $uid = (int) ($r->uniform_id ?? 0);
                $size = (string) ($r->size ?? '');
                $qty = (int) ($r->qty ?? 0);
                $agg[$uid] = $agg[$uid] ?? [];
                $agg[$uid][$size] = $qty;
            }
        }

        $data = $uniforms->map(function ($u) use ($sizes, $agg) {
            $row = [
                'uniform_code' => (string) ($u->code ?? ''),
                'uniform_name' => (string) ($u->name ?? ''),
            ];

            $total = 0;
            foreach ($sizes as $s) {
                $qty = (int) (($agg[(int) $u->id][$s] ?? 0));
                $row['size_' . $this->sanitizeSizeKey($s)] = $qty;
                $total += $qty;
            }

            $row['total'] = $total;

            return $row;
        })->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
            'meta' => [
                'sizes' => $sizes,
            ],
        ]);
    }

    public function lotIndex(): View
    {
        return view('pages.admin.uniforms.uniform_stock_lot_index');
    }

    public function lotDatatable(Request $request): JsonResponse
    {
        $draw = (int) $request->input('draw', 0);
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $length = $length < 0 ? 200 : $length;
        $length = max(1, min($length, 200));

        $search = trim((string) data_get($request->all(), 'search.value', ''));

        $recordsTotal = UniformLotStock::query()->count();

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

    private function sanitizeSizeKey(string $size): string
    {
        $k = strtolower(trim($size));
        $k = preg_replace('/[^a-z0-9]+/', '_', $k) ?? $k;
        $k = trim($k, '_');

        return $k === '' ? 'unknown' : $k;
    }
}
