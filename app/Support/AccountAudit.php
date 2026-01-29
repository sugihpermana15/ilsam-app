<?php

namespace App\Support;

use App\Models\AccountAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class AccountAudit
{
    public static function log(Request $request, string $action, string $result = 'success', array $data = []): void
    {
        $user = Auth::user();

        $log = AccountAccessLog::create([
            'actor_user_id' => $user?->id,
            'account_id' => $data['account_id'] ?? null,
            'action' => $action,
            'result' => $result,
            'reason' => $data['reason'] ?? null,
            'target_type' => $data['target_type'] ?? null,
            'target_id' => $data['target_id'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'request_id' => (string) ($request->headers->get('X-Request-Id') ?? $request->headers->get('X-Correlation-Id') ?? ''),
            'metadata' => $data['metadata'] ?? null,
        ]);

        // Auto-prune low-value logs to reduce DB growth:
        // Keep only the latest N low-value entries per account.
        self::pruneLowValueLogs((int) ($log->account_id ?? 0), (string) $action);
    }

    private static function pruneLowValueLogs(int $accountId, string $action): void
    {
        if ($accountId <= 0) {
            return;
        }

        $lowValue = (array) config('accounts_audit.low_value_actions', ['ACCOUNT_DETAIL_VIEW']);
        if (!in_array($action, $lowValue, true)) {
            return;
        }

        $sensitive = (array) config('accounts_audit.sensitive_actions', ['SECRET_REVEAL', 'SECRET_ROTATE', 'SECRET_DEACTIVATE', 'SECRET_ADD']);
        if (in_array($action, $sensitive, true)) {
            return;
        }

        $keep = (int) config('accounts_audit.low_value_keep_per_account', 50);
        if ($keep < 1) {
            return;
        }

        $skip = $keep - 1;
        $cutoffId = AccountAccessLog::query()
            ->where('account_id', $accountId)
            ->whereIn('action', $lowValue)
            ->orderByDesc('id')
            ->skip($skip)
            ->value('id');

        if (!$cutoffId) {
            return;
        }

        AccountAccessLog::query()
            ->where('account_id', $accountId)
            ->whereIn('action', $lowValue)
            ->where('id', '<', $cutoffId)
            ->delete();
    }
}
