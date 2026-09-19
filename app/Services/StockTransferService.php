<?php

namespace App\Services;

use App\Models\StockNotification;
use App\Models\StockTransferRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockTransferService
{
    public function __construct(
        private readonly StockService $stock,
        private readonly TrxNoGeneratorService $trxNo,
    ) {
    }

    public function create(array $data, string $category): StockTransferRequest
    {
        $userId = Auth::id();
        if (!$userId) throw new RuntimeException('User belum login.');

        return DB::transaction(function () use ($data, $category, $userId) {
            $requestNo = $this->trxNo->next('stock_transfer_' . $category, 'STR', now());
            $transfer = StockTransferRequest::query()->create([
                'request_no' => $requestNo,
                'stock_item_id' => $data['stock_item_id'],
                'category' => $category,
                'qty' => $data['qty'],
                'from_site' => 'jababeka',
                'to_site' => 'karawang',
                'requested_by' => $userId,
                'requested_pic_id' => $data['pic_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => StockTransferRequest::REQUESTED,
            ]);
            $this->notifyAdmins('transfer_requested', 'Permintaan transfer stok baru', "{$requestNo}: {$data['qty']} {$transfer->item()->value('name')} dari Jababeka ke Karawang.", $transfer);
            return $transfer;
        });
    }

    public function prepare(StockTransferRequest $transfer): StockTransferRequest
    {
        return DB::transaction(function () use ($transfer) {
            $transfer->refresh();
            if ($transfer->status !== StockTransferRequest::REQUESTED) throw new RuntimeException('Transfer tidak berada pada status REQUESTED.');
            $this->stock->postOut([
                'stock_item_id' => $transfer->stock_item_id,
                'site_code' => 'jababeka',
                'qty' => $transfer->qty,
                'trx_date' => now()->toDateString(),
                'notes' => 'Transfer ' . $transfer->request_no . ' ke Karawang',
            ], $transfer->category);
            $transfer->update(['status' => StockTransferRequest::PREPARED, 'prepared_by' => Auth::id(), 'prepared_at' => now()]);
            $this->notifyUser($transfer->requested_by, 'transfer_prepared', 'Stok transfer sudah disiapkan', "{$transfer->request_no} sudah disiapkan di Jababeka.", $transfer);
            return $transfer;
        });
    }

    public function ship(StockTransferRequest $transfer, array $data): StockTransferRequest
    {
        if ($transfer->status !== StockTransferRequest::PREPARED) throw new RuntimeException('Transfer harus disiapkan sebelum dikirim.');
        $transfer->update(['status' => StockTransferRequest::SHIPPED, 'driver_name' => $data['driver_name'], 'driver_phone' => $data['driver_phone'] ?? null, 'shipping_notes' => $data['shipping_notes'] ?? null, 'shipped_by' => Auth::id(), 'shipped_at' => now()]);
        $this->notifyUser($transfer->requested_by, 'transfer_shipped', 'Stok sedang dikirim', "{$transfer->request_no} sedang dikirim melalui driver {$transfer->driver_name}.", $transfer);
        return $transfer;
    }

    public function receive(StockTransferRequest $transfer): StockTransferRequest
    {
        return DB::transaction(function () use ($transfer) {
            $transfer->refresh();
            if ($transfer->status !== StockTransferRequest::SHIPPED) throw new RuntimeException('Transfer belum berstatus SHIPPED.');
            $this->stock->postIn([
                'stock_item_id' => $transfer->stock_item_id,
                'site_code' => 'karawang',
                'qty' => $transfer->qty,
                'trx_date' => now()->toDateString(),
                'notes' => 'Penerimaan transfer ' . $transfer->request_no . ' dari Jababeka',
            ], $transfer->category);
            $transfer->update(['status' => StockTransferRequest::RECEIVED, 'received_by' => Auth::id(), 'received_at' => now()]);
            $this->notifyAdmins('transfer_received', 'Transfer stok diterima', "{$transfer->request_no} sudah diterima di Karawang.", $transfer);
            return $transfer;
        });
    }

    private function notifyAdmins(string $type, string $title, string $message, StockTransferRequest $transfer): void
    {
        User::query()->whereIn('role_id', [1, 2])->pluck('id')->each(fn ($id) => $this->notifyUser((int) $id, $type, $title, $message, $transfer));
    }

    private function notifyUser(int $userId, string $type, string $title, string $message, StockTransferRequest $transfer): void
    {
        StockNotification::query()->create(['user_id' => $userId, 'type' => $type, 'title' => $title, 'message' => $message, 'data' => ['transfer_id' => $transfer->id, 'category' => $transfer->category]]);
    }
}
