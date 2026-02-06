<?php

namespace App\Http\Controllers\Admin\Stamp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stamp\StoreStampRequestRequest;
use App\Models\Employee;
use App\Models\Stamp;
use App\Models\StampRequest;
use App\Models\User;
use App\Support\MenuAccess;
use App\Services\StampStockService;
use App\Services\TrxNoGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RuntimeException;

class StampRequestController extends Controller
{
    public function __construct(
        private readonly TrxNoGeneratorService $trxNo,
        private readonly StampStockService $stock,
    ) {
    }

    public function myIndex(Request $request): View
    {
        $userId = Auth::id();
        if ($userId === null) {
            abort(403);
        }

        $rows = StampRequest::query()
            ->with([
                'stamp',
                'pic',
                'requester.employee',
                'validator.employee',
                'validatedBy.employee',
                'handedOverBy.employee',
                'handoverTransaction',
            ])
            ->where('requested_by', $userId)
            ->orderByDesc('id')
            ->get();

        return view('pages.admin.stamps.requests.index', [
            'requests' => $rows,
            'stampsActive' => Stamp::query()->where('is_active', true)->orderBy('name')->get(),
            'employees' => Employee::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreStampRequestRequest $request): RedirectResponse
    {
        $userId = Auth::id();
        if ($userId === null) {
            return back()->with('error', 'User belum login.');
        }

        $data = $request->validated();

        $user = Auth::user();
        $isSuperAdmin = (int) ($user?->role_id ?? 0) === 1;

        $picId = (int) ($user?->employee_id ?? 0);
        if ($picId <= 0 && $isSuperAdmin) {
            $picId = (int) ($data['pic_id'] ?? 0);
        }
        if ($picId <= 0) {
            return back()->withInput()->with('error', 'Akun Anda belum terhubung ke data karyawan (employee_id).');
        }

        $validatorUserId = (int) ($user?->stamp_validator_user_id ?? 0);
        if ($validatorUserId <= 0) {
            $validatorUserId = null;
        } else {
            $candidate = User::query()->whereKey($validatorUserId)->first(['id', 'role_id', 'menu_permissions']);
            if ($candidate === null || !MenuAccess::can($candidate, 'stamps_validation', MenuAccess::ACTION_READ)) {
                $validatorUserId = null;
            }
        }

        $reqNo = $this->trxNo->next('stamp_request', 'SMR', now());

        StampRequest::query()->create([
            'request_no' => $reqNo,
            'stamp_id' => (int) $data['stamp_id'],
            'qty' => (int) $data['qty'],
            'trx_date' => $data['trx_date'] ?? null,
            'pic_id' => $picId,
            'validator_user_id' => $validatorUserId,
            'notes' => $data['notes'] ?? null,
            'status' => StampRequest::STATUS_SUBMITTED,
            'requested_by' => (int) $userId,
            'requested_at' => now(),
        ]);

        return redirect()
            ->route('admin.stamps.requests.index')
            ->with('success', 'Permintaan materai berhasil dikirim: ' . $reqNo);
    }

    public function validationIndex(Request $request): View
    {
        $userId = Auth::id();
        if ($userId === null) {
            abort(403);
        }

        $status = trim((string) $request->input('status', 'PENDING'));

        $query = StampRequest::query()
            ->with([
                'stamp',
                'pic',
                'requester.employee',
                'validator.employee',
                'validatedBy.employee',
                'handedOverBy.employee',
                'handoverTransaction',
            ])
            ->when($status === 'PENDING', function ($q) use ($userId) {
                $q->whereIn('status', [StampRequest::STATUS_SUBMITTED, StampRequest::STATUS_APPROVED])
                    ->where(function ($sub) use ($userId) {
                        $sub->whereNull('validator_user_id')
                            ->orWhere('validator_user_id', $userId);
                    });
            })
            ->when($status !== 'PENDING' && $status !== '', fn ($q) => $q->where('status', $status))
            ->orderByDesc('id');

        $rows = $query->get();

        return view('pages.admin.stamps.requests.validation', [
            'requests' => $rows,
            'status' => $status,
        ]);
    }

    public function approve(Request $request, StampRequest $stampRequest): RedirectResponse
    {
        $userId = Auth::id();
        if ($userId === null) {
            return back()->with('error', 'User belum login.');
        }

        if (!in_array($stampRequest->status, [StampRequest::STATUS_SUBMITTED, StampRequest::STATUS_APPROVED], true)) {
            return back()->with('error', 'Hanya permintaan SUBMITTED/APPROVED yang bisa divalidasi.');
        }

        if ($stampRequest->validator_user_id !== null && (int) $stampRequest->validator_user_id !== (int) $userId) {
            return back()->with('error', 'Permintaan ini ditugaskan ke validator lain.');
        }

        $request->validate([
            'validation_notes' => ['nullable', 'string', 'max:1000'],
            'handover_date' => ['nullable', 'date'],
        ]);

        $notes = $request->input('validation_notes');
        $trxDate = $request->input('handover_date') ?: now()->toDateString();

        // Single-step: approval immediately posts OUT to ledger and reduces stock.
        try {
            $trx = $this->stock->postOut([
                'stamp_id' => (int) $stampRequest->stamp_id,
                'trx_date' => $trxDate,
                'qty' => (int) $stampRequest->qty,
                'pic_id' => (int) $stampRequest->pic_id,
                'notes' => trim('Request ' . $stampRequest->request_no . ($stampRequest->notes ? (' | ' . $stampRequest->notes) : '')),
            ]);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $validatedAt = $stampRequest->validated_at ?? now();
        $validatedBy = $stampRequest->validated_by ?? (int) $userId;

        $stampRequest->update([
            'status' => StampRequest::STATUS_HANDED_OVER,
            'validated_by' => (int) $validatedBy,
            'validated_at' => $validatedAt,
            'validation_notes' => $notes ?: ($stampRequest->validation_notes ?: null),
            'handed_over_by' => (int) $userId,
            'handed_over_at' => now(),
            'handover_trx_id' => (int) $trx->id,
        ]);

        return back()->with('success', 'Permintaan ' . $stampRequest->request_no . ' disetujui. Ledger OUT: ' . $trx->trx_no);
    }

    public function reject(Request $request, StampRequest $stampRequest): RedirectResponse
    {
        $userId = Auth::id();
        if ($userId === null) {
            return back()->with('error', 'User belum login.');
        }

        if ($stampRequest->status !== StampRequest::STATUS_SUBMITTED) {
            return back()->with('error', 'Hanya permintaan SUBMITTED yang bisa ditolak.');
        }

        if ($stampRequest->validator_user_id !== null && (int) $stampRequest->validator_user_id !== (int) $userId) {
            return back()->with('error', 'Permintaan ini ditugaskan ke validator lain.');
        }

        $request->validate([
            'validation_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $stampRequest->update([
            'status' => StampRequest::STATUS_REJECTED,
            'validated_by' => (int) $userId,
            'validated_at' => now(),
            'validation_notes' => $request->input('validation_notes') ?: null,
        ]);

        return back()->with('success', 'Permintaan ' . $stampRequest->request_no . ' ditolak.');
    }

    public function handover(Request $request, StampRequest $stampRequest): RedirectResponse
    {
        $userId = Auth::id();
        if ($userId === null) {
            return back()->with('error', 'User belum login.');
        }

        if ($stampRequest->status !== StampRequest::STATUS_APPROVED) {
            return back()->with('error', 'Hanya permintaan APPROVED yang bisa diserahkan.');
        }

        if ($stampRequest->validator_user_id !== null && (int) $stampRequest->validator_user_id !== (int) $userId) {
            return back()->with('error', 'Permintaan ini ditugaskan ke validator lain.');
        }

        $request->validate([
            'handover_date' => ['nullable', 'date'],
        ]);

        try {
            $trx = $this->stock->postOut([
                'stamp_id' => (int) $stampRequest->stamp_id,
                'trx_date' => $request->input('handover_date') ?: now()->toDateString(),
                'qty' => (int) $stampRequest->qty,
                'pic_id' => (int) $stampRequest->pic_id,
                'notes' => trim('Handover request ' . $stampRequest->request_no . ($stampRequest->notes ? (' | ' . $stampRequest->notes) : '')),
            ]);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $stampRequest->update([
            'status' => StampRequest::STATUS_HANDED_OVER,
            'handed_over_by' => (int) $userId,
            'handed_over_at' => now(),
            'handover_trx_id' => (int) $trx->id,
        ]);

        return back()->with('success', 'Materai diserahkan. Ledger OUT: ' . $trx->trx_no);
    }
}
