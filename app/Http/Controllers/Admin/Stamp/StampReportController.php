<?php

namespace App\Http\Controllers\Admin\Stamp;

use App\Http\Controllers\Controller;
use App\Models\Stamp;
use App\Models\StampTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StampReportController extends Controller
{
    public function pdf(Request $request): Response
    {
        $stampId = $request->input('stamp_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $makeSafe = static function (?string $value): string {
            $value = trim((string) $value);
            if ($value === '') {
                return 'all';
            }

            $value = strtolower($value);
            // Keep it Windows-friendly.
            $value = preg_replace('/[^a-z0-9_-]+/', '-', $value);
            $value = trim((string) $value, '-');

            return $value !== '' ? $value : 'all';
        };

        $openingBalance = null;
        $closingBalance = null;

        $trxQuery = StampTransaction::query()->with(['stamp', 'pic', 'creator']);

        if ($stampId) {
            $trxQuery->where('stamp_id', (int) $stampId);
        }
        if ($dateFrom) {
            $trxQuery->whereDate('trx_date', '>=', (string) $dateFrom);
        }
        if ($dateTo) {
            $trxQuery->whereDate('trx_date', '<=', (string) $dateTo);
        }

        $transactions = $trxQuery->orderByDesc('trx_date')->orderByDesc('id')->get();

        $totIn = (int) $transactions->where('trx_type', 'IN')->sum('qty');
        $totOut = (int) $transactions->where('trx_type', 'OUT')->sum('qty');

        if ($stampId) {
            $stampIdInt = (int) $stampId;

            // Opening balance = saldo terakhir sebelum periode.
            $prev = StampTransaction::query()
                ->where('stamp_id', $stampIdInt)
                ->when($dateFrom, fn ($q) => $q->whereDate('trx_date', '<', (string) $dateFrom))
                ->orderByDesc('trx_date')
                ->orderByDesc('id')
                ->first();

            $openingBalance = (int) ($prev?->on_hand_after ?? 0);
            // Closing balance = saldo di transaksi terakhir dalam periode (newest-first => first()).
            $closingBalance = (int) (($transactions->first()?->on_hand_after) ?? $openingBalance);
        }

        $pdf = Pdf::loadView('pages.admin.stamps.report.pdf', [
            'transactions' => $transactions,
            'stamps' => Stamp::query()->orderBy('name')->get(),
            'filter' => [
                'stamp_id' => $stampId ? (int) $stampId : null,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
            'totals' => [
                'in' => $totIn,
                'out' => $totOut,
            ],
            'balances' => [
                'opening' => $openingBalance,
                'closing' => $closingBalance,
            ],
        ])->setPaper('a4', 'landscape');

        $stampLabel = 'all';
        if ($stampId) {
            $stamp = Stamp::query()->select(['id', 'code', 'name'])->find((int) $stampId);
            $stampLabel = $makeSafe($stamp?->code ?: ($stamp?->name ?: (string) $stampId));
        }

        $fromLabel = $makeSafe($dateFrom);
        $toLabel = $makeSafe($dateTo);
        $periodLabel = ($fromLabel === 'all' && $toLabel === 'all') ? 'all' : ($fromLabel . '_to_' . $toLabel);

        $filename = 'materai-ledger_' . $stampLabel . '_' . $periodLabel . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }
}
