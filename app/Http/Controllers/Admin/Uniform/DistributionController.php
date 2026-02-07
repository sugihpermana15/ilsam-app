<?php

namespace App\Http\Controllers\Admin\Uniform;

use App\Http\Controllers\Controller;
use App\Http\Requests\Uniform\DistributionStoreRequest;
use App\Models\Employee;
use App\Models\UniformAllocation;
use App\Models\UniformAllocationItem;
use App\Models\UniformLot;
use App\Models\UniformVariant;
use App\Services\UniformDistributionOptionsService;
use App\Services\UniformStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DistributionController extends Controller
{
    public function __construct(
        private readonly UniformStockService $stock,
        private readonly UniformDistributionOptionsService $options,
    )
    {
    }

    public function index(): View
    {
        return view('pages.admin.uniforms.uniform_distribution_index', [
            'employees' => Employee::query()->orderBy('name')->get(['id', 'name']),
            'variants' => UniformVariant::query()->with('uniform')->orderBy('uniform_id')->orderBy('size')->get(),
            'lots' => UniformLot::query()->orderBy('received_at')->get(),
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

        $recordsTotal = UniformAllocation::query()->count();

        $query = UniformAllocation::query()
            ->leftJoin('m_igi_employees', 'm_igi_uniform_allocations.employee_id', '=', 'm_igi_employees.id')
            ->leftJoin('users', 'm_igi_uniform_allocations.created_by', '=', 'users.id')
            ->select([
                'm_igi_uniform_allocations.id',
                'm_igi_uniform_allocations.allocation_no',
                'm_igi_uniform_allocations.allocation_method',
                'm_igi_uniform_allocations.allocated_at',
                'm_igi_uniform_allocations.notes',
                'm_igi_employees.name as employee_name',
                'users.name as creator_name',
            ]);

        if ($search !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('m_igi_uniform_allocations.allocation_no', 'like', $like)
                    ->orWhere('m_igi_uniform_allocations.allocation_method', 'like', $like)
                    ->orWhere('m_igi_employees.name', 'like', $like)
                    ->orWhere('users.name', 'like', $like)
                    ->orWhere('m_igi_uniform_allocations.notes', 'like', $like);
            });
        }

        $recordsFiltered = (clone $query)->count('m_igi_uniform_allocations.id');

        $query->orderByDesc('m_igi_uniform_allocations.allocated_at')->orderByDesc('m_igi_uniform_allocations.id');

        $allocations = $query
            ->skip($start)
            ->take($length)
            ->get();

        $allocationIds = $allocations->pluck('id')->map(fn ($v) => (int) $v)->filter()->values()->all();

        $byAllocation = [];
        if (count($allocationIds) > 0) {
            $items = UniformAllocationItem::query()
                ->leftJoin('m_igi_uniform_variants', 'm_igi_uniform_allocation_items.uniform_variant_id', '=', 'm_igi_uniform_variants.id')
                ->leftJoin('m_igi_uniforms as u_from_variant', 'm_igi_uniform_variants.uniform_id', '=', 'u_from_variant.id')
                ->leftJoin('m_igi_uniforms as u_direct', 'm_igi_uniform_allocation_items.uniform_id', '=', 'u_direct.id')
                ->whereIn('m_igi_uniform_allocation_items.uniform_allocation_id', $allocationIds)
                ->select([
                    'm_igi_uniform_allocation_items.uniform_allocation_id as allocation_id',
                    DB::raw('COALESCE(u_direct.name, u_from_variant.name) as uniform_name'),
                    'm_igi_uniform_variants.size as size',
                    'm_igi_uniform_allocation_items.qty as qty',
                ])
                ->orderByRaw('COALESCE(u_direct.name, u_from_variant.name)')
                ->orderBy('m_igi_uniform_variants.size')
                ->get();

            foreach ($items as $it) {
                $aid = (int) ($it->allocation_id ?? 0);
                if ($aid <= 0) {
                    continue;
                }

                $byAllocation[$aid] = $byAllocation[$aid] ?? [
                    'total_qty' => 0,
                    'lines' => [],
                ];

                $uniformName = (string) ($it->uniform_name ?? '-');
                $size = (string) ($it->size ?? '');
                $qty = (int) ($it->qty ?? 0);

                $byAllocation[$aid]['total_qty'] += max(0, $qty);
                if (trim($size) === '') {
                    $byAllocation[$aid]['lines'][] = trim($uniformName) . ' (x' . $qty . ')';
                } else {
                    $byAllocation[$aid]['lines'][] = trim($uniformName) . ' - ' . trim($size) . ' (x' . $qty . ')';
                }
            }
        }

        $rows = $allocations
            ->map(function ($r) use ($byAllocation) {
                $id = (int) ($r->id ?? 0);
                $meta = $byAllocation[$id] ?? ['total_qty' => 0, 'lines' => []];

                return [
                    'id' => $id,
                    'allocation_no' => (string) ($r->allocation_no ?? ''),
                    'allocation_method' => (string) ($r->allocation_method ?? ''),
                    'allocated_at' => $r->allocated_at ? (string) $r->allocated_at : null,
                    'employee_name' => (string) ($r->employee_name ?? '-'),
                    'items_summary' => implode('; ', $meta['lines'] ?? []),
                    'total_qty' => (int) ($meta['total_qty'] ?? 0),
                    'notes' => (string) ($r->notes ?? '-'),
                    'creator_name' => (string) ($r->creator_name ?? '-'),
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

    public function assignedEmployees(Request $request): JsonResponse
    {
        $atRaw = trim((string) $request->query('allocated_at', ''));
        $at = $atRaw !== '' ? Carbon::parse($atRaw) : null;

        return response()->json([
            'data' => $this->options->eligibleEmployees($at),
        ]);
    }

    public function assignedUniforms(Request $request): JsonResponse
    {
        $employeeId = (int) $request->query('employee_id', 0);
        $atRaw = trim((string) $request->query('allocated_at', ''));
        $at = $atRaw !== '' ? Carbon::parse($atRaw) : null;

        return response()->json([
            'data' => $this->options->uniformsForEmployee($employeeId, $at),
        ]);
    }

    public function uniformVariants(Request $request): JsonResponse
    {
        $uniformId = (int) $request->query('uniform_id', 0);

        return response()->json([
            'data' => $this->options->variantsForUniform($uniformId),
        ]);
    }

    public function dashboard(): View
    {
        return view('pages.admin.uniforms.uniform_distribution_dashboard');
    }

    public function dashboardDatatable(Request $request): JsonResponse
    {
        $draw = (int) $request->input('draw', 0);
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $length = $length < 0 ? 200 : $length;
        $length = max(1, min($length, 200));

        $search = trim((string) data_get($request->all(), 'search.value', ''));

        $base = UniformAllocationItem::query()
            ->join('m_igi_uniform_allocations', 'm_igi_uniform_allocation_items.uniform_allocation_id', '=', 'm_igi_uniform_allocations.id')
            ->join('m_igi_employees', 'm_igi_uniform_allocations.employee_id', '=', 'm_igi_employees.id')
            ->join('m_igi_uniform_variants', 'm_igi_uniform_allocation_items.uniform_variant_id', '=', 'm_igi_uniform_variants.id')
            ->join('m_igi_uniforms', 'm_igi_uniform_variants.uniform_id', '=', 'm_igi_uniforms.id');

        $countExpr = "COUNT(DISTINCT CONCAT(m_igi_uniform_allocations.employee_id, '-', m_igi_uniform_allocation_items.uniform_variant_id))";
        $recordsTotal = (int) (clone $base)
            ->selectRaw("{$countExpr} as agg_count")
            ->value('agg_count');

        if ($search !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';
            $base->where(function ($q) use ($like) {
                $q->where('m_igi_employees.no_id', 'like', $like)
                    ->orWhere('m_igi_employees.name', 'like', $like)
                    ->orWhere('m_igi_uniforms.code', 'like', $like)
                    ->orWhere('m_igi_uniforms.name', 'like', $like)
                    ->orWhere('m_igi_uniform_variants.size', 'like', $like);
            });
        }

        $recordsFiltered = (int) (clone $base)
            ->selectRaw("{$countExpr} as agg_count")
            ->value('agg_count');

        $orderColumnIndex = (int) data_get($request->all(), 'order.0.column', -1);
        $orderDir = strtolower((string) data_get($request->all(), 'order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $orderDataKey = (string) data_get($request->all(), "columns.$orderColumnIndex.data", '');

        $sortable = [
            'employee_no_id' => 'employee_no_id',
            'employee_name' => 'employee_name',
            'uniform_code' => 'uniform_code',
            'uniform_name' => 'uniform_name',
            'size' => 'size',
            'qty_total' => 'qty_total',
            'last_allocated_at' => 'last_allocated_at',
        ];

        $query = clone $base;
        $query
            ->select([
                'm_igi_employees.no_id as employee_no_id',
                'm_igi_employees.name as employee_name',
                'm_igi_uniforms.code as uniform_code',
                'm_igi_uniforms.name as uniform_name',
                'm_igi_uniform_variants.size as size',
            ])
            ->selectRaw('SUM(m_igi_uniform_allocation_items.qty) as qty_total')
            ->selectRaw('MAX(m_igi_uniform_allocations.allocated_at) as last_allocated_at')
            ->groupBy([
                'm_igi_uniform_allocations.employee_id',
                'm_igi_uniform_allocation_items.uniform_variant_id',
                'm_igi_employees.no_id',
                'm_igi_employees.name',
                'm_igi_uniforms.code',
                'm_igi_uniforms.name',
                'm_igi_uniform_variants.size',
            ]);

        if (!empty($orderDataKey) && isset($sortable[$orderDataKey])) {
            $query->orderBy($sortable[$orderDataKey], $orderDir);
        } else {
            $query->orderBy('employee_name')->orderBy('uniform_name')->orderBy('size');
        }

        $rows = $query
            ->skip($start)
            ->take($length)
            ->get()
            ->map(function ($r) {
                return [
                    'employee_no_id' => (string) ($r->employee_no_id ?? ''),
                    'employee_name' => (string) ($r->employee_name ?? ''),
                    'uniform_code' => (string) ($r->uniform_code ?? ''),
                    'uniform_name' => (string) ($r->uniform_name ?? ''),
                    'size' => (string) ($r->size ?? ''),
                    'qty_total' => (int) ($r->qty_total ?? 0),
                    'last_allocated_at' => $r->last_allocated_at ? (string) $r->last_allocated_at : null,
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

    public function store(DistributionStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $method = (string) ($data['allocation_method'] ?? '');

        try {
            $allocation = $method === 'ASSIGNED'
                ? $this->stock->distributeAssigned($data)
                : $this->stock->distributeUniversal($data);
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.uniforms.distributions.index')
            ->with('success', 'Distribusi berhasil disimpan: ' . $allocation->allocation_no);
    }
}
