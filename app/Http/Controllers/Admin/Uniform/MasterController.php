<?php

namespace App\Http\Controllers\Admin\Uniform;

use App\Http\Controllers\Controller;
use App\Http\Requests\Uniform\UniformStoreRequest;
use App\Http\Requests\Uniform\UniformUpdateRequest;
use App\Models\Uniform;
use App\Services\UniformMasterService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterController extends Controller
{
    public function __construct(private readonly UniformMasterService $svc)
    {
    }

    public function index(): View
    {
        return view('pages.admin.uniforms.uniform_master_index', [
            'nextCode' => $this->nextUniformCode(),
        ]);
    }

    public function json(Uniform $uniform): JsonResponse
    {
        return response()->json([
            'id' => (int) $uniform->id,
            'code' => (string) $uniform->code,
            'name' => (string) $uniform->name,
            'is_active' => (bool) $uniform->is_active,
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

        $baseQuery = Uniform::query();
        $recordsTotal = (clone $baseQuery)->count();

        $query = (clone $baseQuery)
            ->when($filterIsActive !== null, fn ($q) => $q->where('is_active', (bool) $filterIsActive));

        $applySearch = function ($q, string $term): void {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $term) . '%';
            $q->where(function ($sub) use ($like) {
                $sub->where('code', 'like', $like)
                    ->orWhere('name', 'like', $like);
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
            'code' => 'code',
            'name' => 'name',
            'is_active' => 'is_active',
        ];

        if (!empty($orderDataKey) && isset($sortable[$orderDataKey])) {
            $query->orderBy($sortable[$orderDataKey], $orderDir);
        } else {
            $query->orderByDesc('is_active')->orderBy('name');
        }

        $rows = $query
            ->skip($start)
            ->take($length)
            ->get(['id', 'code', 'name', 'is_active'])
            ->map(fn (Uniform $u) => [
                'id' => (int) $u->id,
                'code' => (string) $u->code,
                'name' => (string) $u->name,
                'is_active' => (bool) $u->is_active,
            ])
            ->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    public function store(UniformStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Code is generated automatically: IGI-UF-001, +1, ...
        unset($data['code']);

        for ($attempt = 0; $attempt < 3; $attempt++) {
            $data['code'] = $this->nextUniformCode();

            try {
                $this->svc->createUniform($data);
                break;
            } catch (QueryException $e) {
                // Retry on duplicate code race.
                $sqlState = (string) ($e->errorInfo[0] ?? '');
                $driverCode = (string) ($e->errorInfo[1] ?? '');
                $msg = strtolower($e->getMessage());
                $isDuplicate = str_contains($msg, 'duplicate') || $sqlState === '23000' || $driverCode === '1062';
                if (!$isDuplicate || $attempt === 2) {
                    throw $e;
                }
            }
        }

        return redirect()->route('admin.uniforms.master.index')->with('success', 'Uniform berhasil ditambahkan.');
    }

    private function nextUniformCode(): string
    {
        $prefix = 'IGI-UF-';
        $start = strlen($prefix) + 1;

        $max = (int) (Uniform::query()
            ->where('code', 'like', $prefix . '%')
            ->selectRaw("MAX(CAST(SUBSTRING(code, $start) AS UNSIGNED)) as max_num")
            ->value('max_num') ?? 0);

        $next = $max + 1;

        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    public function update(UniformUpdateRequest $request, Uniform $uniform): RedirectResponse
    {
        try {
            $payload = $request->validated();
            $this->svc->updateUniform((int) $uniform->id, $payload);
        } catch (QueryException $e) {
            $msg = strtolower($e->getMessage());
            if (str_contains($msg, 'duplicate') || str_contains($msg, 'unique') || str_contains($msg, 'uniq')) {
                return back()->withInput()->with('error', 'Kode uniform sudah digunakan.');
            }
            throw $e;
        }

        return redirect()->route('admin.uniforms.master.index')->with('success', 'Uniform berhasil diperbarui.');
    }

    public function toggle(Uniform $uniform): RedirectResponse
    {
        $this->svc->toggleUniform((int) $uniform->id);

        return back()->with('success', 'Status aktif uniform diperbarui.');
    }
}
