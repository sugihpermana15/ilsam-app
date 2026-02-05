<?php

namespace App\Services;

use App\Models\Stamp;
use App\Models\StampBalance;
use App\Models\StampTransaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StampStockService
{
    public function __construct(private readonly TrxNoGeneratorService $trxNoGenerator)
    {
    }

    /**
     * @param array{stamp_id:int,trx_date?:string|Carbon,qty:int,pic_id?:int|null,notes?:string|null} $data
     */
    public function postIn(array $data): StampTransaction
    {
        return $this->post($data, 'IN');
    }

    /**
     * @param array{stamp_id:int,trx_date?:string|Carbon,qty:int,pic_id?:int|null,notes?:string|null} $data
     */
    public function postOut(array $data): StampTransaction
    {
        return $this->post($data, 'OUT');
    }

    public function getOnHand(int $stampId): int
    {
        /** @var StampBalance|null $balance */
        $balance = StampBalance::query()->where('stamp_id', $stampId)->first();

        return (int) ($balance?->on_hand_qty ?? 0);
    }

    /**
     * @param array{stamp_id:int,trx_date?:string|Carbon,qty:int,pic_id?:int|null,notes?:string|null} $data
     */
    private function post(array $data, string $trxType): StampTransaction
    {
        $stampId = (int) Arr::get($data, 'stamp_id');
        $qty = (int) Arr::get($data, 'qty');
        $picId = Arr::get($data, 'pic_id');
        $notes = Arr::get($data, 'notes');
        $trxDate = Arr::get($data, 'trx_date') ?? now();

        if ($qty <= 0) {
            throw new RuntimeException('Qty must be greater than 0.');
        }

        return DB::transaction(function () use ($stampId, $qty, $picId, $notes, $trxDate, $trxType) {
            /** @var Stamp|null $stamp */
            $stamp = Stamp::query()->whereKey($stampId)->lockForUpdate()->first();
            if ($stamp === null) {
                throw (new ModelNotFoundException())->setModel(Stamp::class, [$stampId]);
            }

            /** @var StampBalance|null $balance */
            $balance = StampBalance::query()
                ->where('stamp_id', $stampId)
                ->lockForUpdate()
                ->first();

            if ($balance === null) {
                try {
                    $balance = StampBalance::query()->create([
                        'stamp_id' => $stampId,
                        'on_hand_qty' => 0,
                    ]);
                } catch (QueryException $e) {
                    // Unique(stamp_id) can race on first-create; re-read with lock.
                    $balance = StampBalance::query()
                        ->where('stamp_id', $stampId)
                        ->lockForUpdate()
                        ->first();

                    if ($balance === null) {
                        throw $e;
                    }
                }

                $balance = StampBalance::query()
                    ->whereKey($balance->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $before = (int) $balance->on_hand_qty;

            if ($trxType === 'OUT' && $before < $qty) {
                throw new RuntimeException('Stok materai tidak mencukupi.');
            }

            $after = $trxType === 'IN' ? ($before + $qty) : ($before - $qty);

            $trxNo = $this->trxNoGenerator->next('stamp', 'SM', $trxDate instanceof Carbon ? $trxDate : Carbon::parse((string) $trxDate));

            $userId = Auth::id();
            if ($userId === null) {
                throw new RuntimeException('User belum login.');
            }

            /** @var StampTransaction $trx */
            $trx = StampTransaction::query()->create([
                'trx_no' => $trxNo,
                'stamp_id' => $stampId,
                'trx_type' => $trxType,
                'trx_date' => $trxDate,
                'qty' => $qty,
                'pic_id' => $picId,
                'notes' => $notes,
                'created_by' => (int) $userId,
                'on_hand_after' => $after,
            ]);

            $balance->update(['on_hand_qty' => $after]);

            return $trx;
        });
    }
}
