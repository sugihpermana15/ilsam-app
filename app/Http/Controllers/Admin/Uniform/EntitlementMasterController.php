<?php

namespace App\Http\Controllers\Admin\Uniform;

use App\Http\Controllers\Controller;
use App\Http\Requests\Uniform\UniformEntitlementUpsertRequest;
use App\Models\Employee;
use App\Models\Uniform;
use App\Models\UniformEntitlement;
use App\Services\UniformMasterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EntitlementMasterController extends Controller
{
    public function __construct(private readonly UniformMasterService $svc)
    {
    }

    public function index(): View
    {
        $employees = Employee::query()->orderBy('name')->get(['id', 'no_id', 'name']);
        $uniforms = Uniform::query()->orderBy('name')->get(['id', 'code', 'name']);

        return view('pages.admin.uniforms.uniform_entitlement_master_index', [
            'employees' => $employees,
            'uniforms' => $uniforms,
        ]);
    }

    public function json(UniformEntitlement $entitlement): JsonResponse
    {
        $entitlement->loadMissing(['employee', 'uniform']);

        return response()->json([
            'id' => (int) $entitlement->id,
            'employee_id' => (int) $entitlement->employee_id,
            'employee_label' => (string) (($entitlement->employee?->no_id ?? '') . ' - ' . ($entitlement->employee?->name ?? '')),
            'uniform_id' => (int) $entitlement->uniform_id,
            'uniform_label' => (string) (($entitlement->uniform?->code ?? '') . ' - ' . ($entitlement->uniform?->name ?? '')),
            'total_qty' => (int) $entitlement->total_qty,
            'used_qty' => (int) $entitlement->used_qty,
            'remaining_qty' => (int) max(0, (int) $entitlement->total_qty - (int) $entitlement->used_qty),
            'effective_from' => optional($entitlement->effective_from)->format('Y-m-d'),
            'effective_to' => optional($entitlement->effective_to)->format('Y-m-d'),
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

        $base = UniformEntitlement::query()
            ->leftJoin('m_igi_employees', 'm_igi_uniform_entitlements.employee_id', '=', 'm_igi_employees.id')
            ->leftJoin('m_igi_uniforms', 'm_igi_uniform_entitlements.uniform_id', '=', 'm_igi_uniforms.id')
            ->select([
                'm_igi_uniform_entitlements.id as id',
                'm_igi_employees.no_id as employee_no_id',
                'm_igi_employees.name as employee_name',
                'm_igi_uniforms.code as uniform_code',
                'm_igi_uniforms.name as uniform_name',
                'm_igi_uniform_entitlements.total_qty as total_qty',
                'm_igi_uniform_entitlements.used_qty as used_qty',
                'm_igi_uniform_entitlements.effective_from as effective_from',
                'm_igi_uniform_entitlements.effective_to as effective_to',
            ]);

        $recordsTotal = (clone $base)->count();

        if ($search !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';
            $base->where(function ($q) use ($like) {
                $q->where('m_igi_employees.no_id', 'like', $like)
                    ->orWhere('m_igi_employees.name', 'like', $like)
                    ->orWhere('m_igi_uniforms.code', 'like', $like)
                    ->orWhere('m_igi_uniforms.name', 'like', $like);
            });
        }

        $recordsFiltered = (clone $base)->count();

        $orderColumnIndex = (int) data_get($request->all(), 'order.0.column', -1);
        $orderDir = strtolower((string) data_get($request->all(), 'order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $orderDataKey = (string) data_get($request->all(), "columns.$orderColumnIndex.data", '');

        $sortable = [
            'employee_no_id' => 'employee_no_id',
            'employee_name' => 'employee_name',
            'uniform_code' => 'uniform_code',
            'uniform_name' => 'uniform_name',
            'total_qty' => 'total_qty',
            'used_qty' => 'used_qty',
            'effective_from' => 'effective_from',
            'effective_to' => 'effective_to',
        ];

        if (!empty($orderDataKey) && isset($sortable[$orderDataKey])) {
            $base->orderBy($sortable[$orderDataKey], $orderDir);
        } else {
            $base->orderBy('employee_name')->orderBy('uniform_name');
        }

        $rows = $base
            ->skip($start)
            ->take($length)
            ->get()
            ->map(fn ($r) => [
                'id' => (int) ($r->id ?? 0),
                'employee_no_id' => (string) ($r->employee_no_id ?? ''),
                'employee_name' => (string) ($r->employee_name ?? ''),
                'uniform_code' => (string) ($r->uniform_code ?? ''),
                'uniform_name' => (string) ($r->uniform_name ?? ''),
                'total_qty' => (int) ($r->total_qty ?? 0),
                'used_qty' => (int) ($r->used_qty ?? 0),
                'remaining_qty' => max(0, (int) ($r->total_qty ?? 0) - (int) ($r->used_qty ?? 0)),
                'effective_from' => $r->effective_from ? date('Y-m-d', strtotime((string) $r->effective_from)) : '',
                'effective_to' => $r->effective_to ? date('Y-m-d', strtotime((string) $r->effective_to)) : '',
            ])
            ->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    public function store(UniformEntitlementUpsertRequest $request): RedirectResponse
    {
        $this->svc->upsertEntitlement($request->validated());

        return redirect()->route('admin.uniforms.entitlements.index')->with('success', 'Kuota seragam berhasil disimpan.');
    }

    public function update(UniformEntitlementUpsertRequest $request, UniformEntitlement $entitlement): RedirectResponse
    {
        $payload = $request->validated();
        $payload['employee_id'] = (int) $entitlement->employee_id;
        $payload['uniform_id'] = (int) $entitlement->uniform_id;

        $this->svc->upsertEntitlement($payload);

        return redirect()->route('admin.uniforms.entitlements.index')->with('success', 'Kuota seragam berhasil diperbarui.');
    }
}
