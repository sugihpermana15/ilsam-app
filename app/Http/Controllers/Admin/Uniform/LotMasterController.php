<?php

namespace App\Http\Controllers\Admin\Uniform;

use App\Http\Controllers\Controller;
use App\Http\Requests\Uniform\UniformLotStoreRequest;
use App\Http\Requests\Uniform\UniformLotUpdateRequest;
use App\Models\UniformLot;
use App\Services\UniformMasterService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LotMasterController extends Controller
{
    public function __construct(private readonly UniformMasterService $svc)
    {
    }

    public function index(): View
    {
        return view('pages.admin.uniforms.uniform_lot_master_index');
    }

    public function json(UniformLot $lot): JsonResponse
    {
        return response()->json([
            'id' => (int) $lot->id,
            'lot_code' => (string) $lot->lot_code,
            'received_at' => optional($lot->received_at)->format('Y-m-d\\TH:i'),
            'notes' => (string) ($lot->notes ?? ''),
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
        $search = trim((string) data_get($request->all(), 'search.value', ''));

        $baseQuery = UniformLot::query();
        $recordsTotal = (clone $baseQuery)->count();

        $query = (clone $baseQuery);

        $applySearch = function ($q, string $term): void {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $term) . '%';
            $q->where(function ($sub) use ($like) {
                $sub->where('lot_code', 'like', $like)
                    ->orWhere('notes', 'like', $like);
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
            'lot_code' => 'lot_code',
            'received_at' => 'received_at',
        ];

        if (!empty($orderDataKey) && isset($sortable[$orderDataKey])) {
            $query->orderBy($sortable[$orderDataKey], $orderDir);
        } else {
            $query->orderByDesc('received_at');
        }

        $rows = $query
            ->skip($start)
            ->take($length)
            ->get(['id', 'lot_code', 'received_at', 'notes'])
            ->map(fn (UniformLot $l) => [
                'id' => (int) $l->id,
                'lot_code' => (string) $l->lot_code,
                'received_at' => optional($l->received_at)->format('Y-m-d H:i'),
                'notes' => (string) ($l->notes ?? ''),
            ])
            ->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    public function store(UniformLotStoreRequest $request): RedirectResponse
    {
        try {
            $this->svc->createLot($request->validated());
        } catch (QueryException $e) {
            $msg = strtolower($e->getMessage());
            if (str_contains($msg, 'duplicate') || str_contains($msg, 'unique') || str_contains($msg, 'uniq')) {
                return back()->withInput()->with('error', 'Kode LOT sudah digunakan.');
            }
            throw $e;
        }

        return redirect()->route('admin.uniforms.lots.index')->with('success', 'LOT berhasil ditambahkan.');
    }

    public function update(UniformLotUpdateRequest $request, UniformLot $lot): RedirectResponse
    {
        try {
            $this->svc->updateLot((int) $lot->id, $request->validated());
        } catch (QueryException $e) {
            $msg = strtolower($e->getMessage());
            if (str_contains($msg, 'duplicate') || str_contains($msg, 'unique') || str_contains($msg, 'uniq')) {
                return back()->withInput()->with('error', 'Kode LOT sudah digunakan.');
            }
            throw $e;
        }

        return redirect()->route('admin.uniforms.lots.index')->with('success', 'LOT berhasil diperbarui.');
    }
}
