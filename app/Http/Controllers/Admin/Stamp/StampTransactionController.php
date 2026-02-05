<?php

namespace App\Http\Controllers\Admin\Stamp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stamp\StoreStampInRequest;
use App\Http\Requests\Stamp\StoreStampOutRequest;
use App\Models\Employee;
use App\Models\Stamp;
use App\Models\StampTransaction;
use App\Services\StampStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class StampTransactionController extends Controller
{
    public function __construct(private readonly StampStockService $stock)
    {
    }

    public function index(Request $request): View
    {
        return view('pages.admin.stamps.transactions.index', [
            'stamps' => Stamp::query()->orderBy('name')->get(),
            'stampsActive' => Stamp::query()->where('is_active', true)->orderBy('name')->get(),
            'employees' => Employee::query()->orderBy('name')->get(),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        $draw = (int) $request->input('draw', 0);
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $length = $length < 0 ? 200 : $length;
        $length = max(1, min($length, 200));

        $filterStampId = (int) $request->input('stamp_id', 0);
        $filterTrxType = trim((string) $request->input('trx_type', ''));
        $filterDateFrom = trim((string) $request->input('date_from', ''));
        $filterDateTo = trim((string) $request->input('date_to', ''));
        $search = trim((string) data_get($request->all(), 'search.value', ''));

        $recordsTotal = StampTransaction::query()->count();

        $query = StampTransaction::query()
            ->leftJoin('stamps', 'stamp_transactions.stamp_id', '=', 'stamps.id')
            ->leftJoin('m_igi_employees', 'stamp_transactions.pic_id', '=', 'm_igi_employees.id')
            ->leftJoin('users', 'stamp_transactions.created_by', '=', 'users.id')
            ->select([
                'stamp_transactions.id',
                'stamp_transactions.trx_date',
                'stamp_transactions.trx_no',
                'stamp_transactions.trx_type',
                'stamp_transactions.qty',
                'stamp_transactions.on_hand_after',
                'stamp_transactions.notes',
                'stamps.name as stamp_name',
                'stamps.code as stamp_code',
                'stamps.face_value as stamp_face_value',
                'm_igi_employees.name as pic_name',
                'users.name as creator_name',
            ])
            ->when($filterStampId > 0, fn ($q) => $q->where('stamp_transactions.stamp_id', $filterStampId))
            ->when($filterTrxType !== '', fn ($q) => $q->where('stamp_transactions.trx_type', $filterTrxType))
            ->when($filterDateFrom !== '', fn ($q) => $q->whereDate('stamp_transactions.trx_date', '>=', $filterDateFrom))
            ->when($filterDateTo !== '', fn ($q) => $q->whereDate('stamp_transactions.trx_date', '<=', $filterDateTo));

        if ($search !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('stamp_transactions.trx_no', 'like', $like)
                    ->orWhere('stamp_transactions.notes', 'like', $like)
                    ->orWhere('stamps.name', 'like', $like)
                    ->orWhere('stamps.code', 'like', $like)
                    ->orWhere('m_igi_employees.name', 'like', $like)
                    ->orWhere('users.name', 'like', $like);
            });
        }

        $recordsFiltered = (clone $query)->count('stamp_transactions.id');

        $orderColumnIndex = (int) data_get($request->all(), 'order.0.column', -1);
        $orderDir = strtolower((string) data_get($request->all(), 'order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderDataKey = (string) data_get($request->all(), "columns.$orderColumnIndex.data", '');

        $sortable = [
            'trx_date' => 'stamp_transactions.trx_date',
            'trx_no' => 'stamp_transactions.trx_no',
            'stamp_name' => 'stamps.name',
            'trx_type' => 'stamp_transactions.trx_type',
            'qty' => 'stamp_transactions.qty',
            'on_hand_after' => 'stamp_transactions.on_hand_after',
            'pic_name' => 'm_igi_employees.name',
            'notes' => 'stamp_transactions.notes',
            'creator_name' => 'users.name',
        ];

        if (!empty($orderDataKey) && isset($sortable[$orderDataKey])) {
            $query->orderBy($sortable[$orderDataKey], $orderDir);

            // Keep ordering consistent for same-date rows.
            if ($orderDataKey === 'trx_date') {
                $query->orderBy('stamp_transactions.id', $orderDir);
            }
        } else {
            $query->orderByDesc('stamp_transactions.trx_date')->orderByDesc('stamp_transactions.id');
        }

        $rows = $query
            ->skip($start)
            ->take($length)
            ->get()
            ->map(function ($r) {
                return [
                    'id' => (int) $r->id,
                    'trx_date' => $r->trx_date ? (string) $r->trx_date : null,
                    'trx_no' => (string) ($r->trx_no ?? ''),
                    'stamp_name' => (string) ($r->stamp_name ?? '-'),
                    'stamp_code' => (string) ($r->stamp_code ?? ''),
                    'stamp_face_value' => (int) ($r->stamp_face_value ?? 0),
                    'trx_type' => (string) ($r->trx_type ?? ''),
                    'qty' => (int) ($r->qty ?? 0),
                    'on_hand_after' => (int) ($r->on_hand_after ?? 0),
                    'pic_name' => (string) ($r->pic_name ?? '-'),
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

    public function storeIn(StoreStampInRequest $request): RedirectResponse
    {
        $trx = $this->stock->postIn($request->validated());

        return redirect()
            ->route('admin.stamps.transactions.index')
            ->with('success', 'Transaksi IN berhasil disimpan: ' . $trx->trx_no);
    }

    public function storeOut(StoreStampOutRequest $request): RedirectResponse
    {
        try {
            $trx = $this->stock->postOut($request->validated());
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.stamps.transactions.index')
            ->with('success', 'Transaksi OUT berhasil disimpan: ' . $trx->trx_no);
    }
}
