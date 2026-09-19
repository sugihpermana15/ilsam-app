<?php

namespace App\Services;

use App\Models\StockItem;
use App\Models\StockItemSiteBalance;
use App\Models\StockTransaction;
use App\Models\StockNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    public function __construct(private readonly TrxNoGeneratorService $trxNoGenerator)
    {
    }

    public function postIn(array $data, string $category): StockTransaction
    {
        return $this->post($data, $category, 'IN');
    }

    public function postOut(array $data, string $category): StockTransaction
    {
        return $this->post($data, $category, 'OUT');
    }

    private function post(array $data, string $category, string $trxType): StockTransaction
    {
        $itemId = (int) Arr::get($data, 'stock_item_id');
        $siteCode = strtolower(trim((string) Arr::get($data, 'site_code')));
        $qty = (int) Arr::get($data, 'qty');
        $trxDate = Arr::get($data, 'trx_date') ?? now();
        $picId = Arr::get($data, 'pic_id');
        $notes = Arr::get($data, 'notes');
        $userId = Auth::id();

        if ($qty <= 0) {
            throw new RuntimeException('Jumlah stok harus lebih besar dari 0.');
        }

        if (!in_array($siteCode, ['jababeka', 'karawang'], true)) {
            throw new RuntimeException('Site stok tidak valid.');
        }

        if ($userId === null) {
            throw new RuntimeException('User belum login.');
        }

        return DB::transaction(function () use ($category, $itemId, $siteCode, $qty, $trxDate, $picId, $notes, $userId, $trxType) {
            $item = StockItem::query()
                ->whereKey($itemId)
                ->where('category', $category)
                ->lockForUpdate()
                ->first();

            if ($item === null) {
                throw (new ModelNotFoundException())->setModel(StockItem::class, [$itemId]);
            }

            if (!$item->is_active) {
                throw new RuntimeException('Barang sedang nonaktif.');
            }

            DB::table('stock_item_site_balances')->insertOrIgnore([
                'stock_item_id' => $item->id,
                'site_code' => $siteCode,
                'on_hand_qty' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $balance = StockItemSiteBalance::query()
                ->where('stock_item_id', $item->id)
                ->where('site_code', $siteCode)
                ->lockForUpdate()
                ->firstOrFail();

            $before = (int) $balance->on_hand_qty;
            if ($trxType === 'OUT' && $before < $qty) {
                throw new RuntimeException("Stok {$item->name} tidak mencukupi. Sisa stok: {$before}.");
            }

            $after = $trxType === 'IN' ? $before + $qty : $before - $qty;
            $date = $trxDate instanceof Carbon ? $trxDate : Carbon::parse((string) $trxDate);
            $prefix = match ($category) {
                'atk' => 'ATK',
                'material' => 'MAT',
                'apd' => 'APD',
            };
            $trxNo = $this->trxNoGenerator->next('stock_' . $category, $prefix, $date);

            $transaction = StockTransaction::query()->create([
                'trx_no' => $trxNo,
                'stock_item_id' => $item->id,
                'site_code' => $siteCode,
                'trx_type' => $trxType,
                'trx_date' => $date,
                'qty' => $qty,
                'stock_after' => $after,
                'pic_id' => $picId,
                'notes' => $notes,
                'created_by' => (int) $userId,
            ]);

            $balance->update(['on_hand_qty' => $after]);
            $item->update(['current_stock' => (int) $item->siteBalances()->sum('on_hand_qty')]);

            if ($after <= (int) $item->low_stock_threshold) {
                User::query()->whereIn('role_id', [1, 2])->pluck('id')->each(function ($adminId) use ($item, $category, $siteCode, $after): void {
                    StockNotification::query()->create([
                        'user_id' => (int) $adminId,
                        'type' => 'low_stock',
                        'title' => 'Stok menipis',
                        'message' => "{$item->name} di " . ucfirst($siteCode) . " tersisa {$after}. Batas minimum: {$item->low_stock_threshold}.",
                        'data' => ['stock_item_id' => $item->id, 'site_code' => $siteCode, 'category' => $category],
                    ]);
                });
            }

            return $transaction;
        });
    }
}
