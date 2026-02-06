<?php

namespace App\Http\Controllers\Admin\Uniform;

use App\Http\Controllers\Controller;
use App\Http\Requests\Uniform\UniformVariantStoreRequest;
use App\Http\Requests\Uniform\UniformVariantUpdateRequest;
use App\Models\Uniform;
use App\Models\UniformVariant;
use App\Services\UniformMasterService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VariantMasterController extends Controller
{
    public function __construct(private readonly UniformMasterService $svc)
    {
    }

    public function index(): View
    {
        $uniforms = Uniform::query()->orderBy('name')->get(['id', 'code', 'name']);

        return view('pages.admin.uniforms.uniform_variant_master_index', [
            'uniforms' => $uniforms,
        ]);
    }

    public function json(UniformVariant $variant): JsonResponse
    {
        $variant->loadMissing('uniform');

        return response()->json([
            'id' => (int) $variant->id,
            'uniform_id' => (int) $variant->uniform_id,
            'uniform_label' => (string) (($variant->uniform?->code ?? '') . ' - ' . ($variant->uniform?->name ?? '')),
            'size' => (string) $variant->size,
            'is_active' => (bool) $variant->is_active,
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        $draw = (int) $request->input('draw', 0);
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $length = $length < 0 ? 200 : $length;
        $length = max(1, min($length, 200));

        $filterQ = trim((string) $request->input('q', ''));
        $filterIsActive = $request->filled('is_active') ? (int) $request->boolean('is_active') : null;
        $search = trim((string) data_get($request->all(), 'search.value', ''));

        $baseQuery = UniformVariant::query()
            ->leftJoin('m_igi_uniforms', 'm_igi_uniform_variants.uniform_id', '=', 'm_igi_uniforms.id')
            ->select([
                'm_igi_uniform_variants.id as id',
                'm_igi_uniforms.code as uniform_code',
                'm_igi_uniforms.name as uniform_name',
                'm_igi_uniform_variants.size as size',
                'm_igi_uniform_variants.is_active as is_active',
            ]);

        $recordsTotal = (clone $baseQuery)->count();

        $query = (clone $baseQuery)
            ->when($filterIsActive !== null, fn ($q) => $q->where('m_igi_uniform_variants.is_active', (bool) $filterIsActive));

        $applySearch = function ($q, string $term): void {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $term) . '%';
            $q->where(function ($sub) use ($like) {
                $sub->where('m_igi_uniforms.code', 'like', $like)
                    ->orWhere('m_igi_uniforms.name', 'like', $like)
                    ->orWhere('m_igi_uniform_variants.size', 'like', $like);
            });
        };

        if ($filterQ !== '') {
            $applySearch($query, $filterQ);
        }
        if ($search !== '') {
            $applySearch($query, $search);
        }

        $recordsFiltered = (clone $query)->count();

        $orderColumnIndex = (int) data_get($request->all(), 'order.0.column', -1);
        $orderDir = strtolower((string) data_get($request->all(), 'order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $orderDataKey = (string) data_get($request->all(), "columns.$orderColumnIndex.data", '');

        $sortable = [
            'uniform_code' => 'uniform_code',
            'uniform_name' => 'uniform_name',
            'size' => 'size',
            'is_active' => 'is_active',
        ];

        if (!empty($orderDataKey) && isset($sortable[$orderDataKey])) {
            $query->orderBy($sortable[$orderDataKey], $orderDir);
        } else {
            $query->orderByDesc('is_active')->orderBy('uniform_name')->orderBy('size');
        }

        $rows = $query
            ->skip($start)
            ->take($length)
            ->get()
            ->map(fn ($r) => [
                'id' => (int) ($r->id ?? 0),
                'uniform_code' => (string) ($r->uniform_code ?? ''),
                'uniform_name' => (string) ($r->uniform_name ?? ''),
                'size' => (string) ($r->size ?? ''),
                'is_active' => (bool) ($r->is_active ?? false),
            ])
            ->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    public function store(UniformVariantStoreRequest $request): RedirectResponse
    {
        try {
            $this->svc->createVariant($request->validated());
        } catch (QueryException $e) {
            $msg = strtolower($e->getMessage());
            if (str_contains($msg, 'duplicate') || str_contains($msg, 'unique') || str_contains($msg, 'uniq')) {
                return back()->withInput()->with('error', 'Ukuran untuk uniform tersebut sudah ada.');
            }
            throw $e;
        }

        return redirect()->route('admin.uniforms.variants.index')->with('success', 'Varian/ukuran berhasil ditambahkan.');
    }

    public function update(UniformVariantUpdateRequest $request, UniformVariant $variant): RedirectResponse
    {
        try {
            $this->svc->updateVariant((int) $variant->id, $request->validated());
        } catch (QueryException $e) {
            $msg = strtolower($e->getMessage());
            if (str_contains($msg, 'duplicate') || str_contains($msg, 'unique') || str_contains($msg, 'uniq')) {
                return back()->withInput()->with('error', 'Ukuran untuk uniform tersebut sudah ada.');
            }
            throw $e;
        }

        return redirect()->route('admin.uniforms.variants.index')->with('success', 'Varian/ukuran berhasil diperbarui.');
    }

    public function toggle(UniformVariant $variant): RedirectResponse
    {
        $this->svc->toggleVariant((int) $variant->id);

        return back()->with('success', 'Status aktif varian diperbarui.');
    }
}
