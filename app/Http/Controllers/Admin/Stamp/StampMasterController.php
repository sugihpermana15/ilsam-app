<?php

namespace App\Http\Controllers\Admin\Stamp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stamp\StoreStampMasterRequest;
use App\Http\Requests\Stamp\UpdateStampMasterRequest;
use App\Models\Stamp;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StampMasterController extends Controller
{
    public function index(Request $request): View
    {
        return view('pages.admin.stamps.master.index', [
            'nextCode' => $this->nextStampCode(),
        ]);
    }

    public function json(Stamp $stamp): JsonResponse
    {
        return response()->json([
            'id' => (int) $stamp->id,
            'code' => (string) $stamp->code,
            'name' => (string) $stamp->name,
            'face_value' => (int) $stamp->face_value,
            'is_active' => (bool) $stamp->is_active,
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

        $baseQuery = Stamp::query();
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
            'face_value' => 'face_value',
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
            ->get(['id', 'code', 'name', 'face_value', 'is_active'])
            ->map(fn (Stamp $s) => [
                'id' => (int) $s->id,
                'code' => (string) $s->code,
                'name' => (string) $s->name,
                'face_value' => (int) $s->face_value,
                'is_active' => (bool) $s->is_active,
            ])
            ->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.stamps.master.create');
    }

    public function store(StoreStampMasterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        unset($data['code']);

        // Generate sequential code: IGI-STAMP-001, +1, ...
        for ($attempt = 0; $attempt < 3; $attempt++) {
            $data['code'] = $this->nextStampCode();

            try {
                Stamp::query()->create($data);
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

        return redirect()->route('admin.stamps.master.index')->with('success', 'Materai berhasil ditambahkan.');
    }

    private function nextStampCode(): string
    {
        $prefix = 'IGI-STAMP-';
        $start = strlen($prefix) + 1;

        $max = (int) (Stamp::query()
            ->where('code', 'like', $prefix . '%')
            ->selectRaw("MAX(CAST(SUBSTRING(code, $start) AS UNSIGNED)) as max_num")
            ->value('max_num') ?? 0);

        $next = $max + 1;

        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    public function edit(Stamp $stamp): View
    {
        return view('pages.admin.stamps.master.edit', [
            'stamp' => $stamp,
        ]);
    }

    public function update(UpdateStampMasterRequest $request, Stamp $stamp): RedirectResponse
    {
        $stamp->update($request->validated());

        return redirect()->route('admin.stamps.master.index')->with('success', 'Materai berhasil diperbarui.');
    }

    public function toggle(Stamp $stamp): RedirectResponse
    {
        $stamp->update(['is_active' => !$stamp->is_active]);

        return back()->with('success', 'Status aktif materai diperbarui.');
    }
}
