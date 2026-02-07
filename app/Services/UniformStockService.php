<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Uniform;
use App\Models\UniformAllocation;
use App\Models\UniformEntitlement;
use App\Models\UniformLot;
use App\Models\UniformLotStock;
use App\Models\UniformStockMovement;
use App\Models\UniformVariant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class UniformStockService
{
    public function __construct(private readonly TrxNoGeneratorService $trxNoGenerator)
    {
    }

    /**
     * Adjust stock_on_hand for a specific lot stock.
     *
     * This does NOT rewrite history. Instead, it creates a compensating movement (IN/OUT)
     * so the audit trail remains consistent.
     */
    public function adjustLotStockOnHand(int $lotStockId, int $newStockOnHand, mixed $occurredAt = null, ?string $notes = null): UniformStockMovement
    {
        if ($lotStockId <= 0) {
            throw new RuntimeException('Lot stock tidak valid.');
        }
        if ($newStockOnHand < 0) {
            throw new RuntimeException('Stock on hand tidak boleh kurang dari 0.');
        }
        if ($notes === null || trim($notes) === '') {
            throw new RuntimeException('Catatan penyesuaian wajib diisi.');
        }

        $userId = Auth::id();
        if ($userId === null) {
            throw new RuntimeException('User belum login.');
        }

        $occurredAt = $occurredAt ?? now();

        return DB::transaction(function () use ($lotStockId, $newStockOnHand, $occurredAt, $notes, $userId) {
            /** @var UniformLotStock|null $lotStock */
            $lotStock = UniformLotStock::query()->whereKey($lotStockId)->lockForUpdate()->first();
            if ($lotStock === null) {
                throw (new ModelNotFoundException())->setModel(UniformLotStock::class, [$lotStockId]);
            }

            $before = (int) $lotStock->stock_on_hand;
            $after = (int) $newStockOnHand;

            if ($before === $after) {
                throw new RuntimeException('Tidak ada perubahan stok.');
            }

            $delta = $after - $before;
            $movementType = $delta > 0 ? 'IN' : 'OUT';
            $qty = (int) abs($delta);

            $movementNo = $this->trxNoGenerator->next(
                'uniform_movement',
                'UF',
                $occurredAt instanceof Carbon ? $occurredAt : Carbon::parse((string) $occurredAt)
            );

            /** @var UniformStockMovement $movement */
            $movement = UniformStockMovement::query()->create([
                'movement_no' => $movementNo,
                'movement_type' => $movementType,
                'occurred_at' => $occurredAt,
                'uniform_variant_id' => (int) $lotStock->uniform_variant_id,
                'uniform_lot_id' => (int) $lotStock->uniform_lot_id,
                'qty' => $qty,
                'stock_on_hand_after' => $after,
                'reference_type' => 'stock_adjustment',
                'reference_id' => (int) $lotStock->getKey(),
                'notes' => $notes,
                'created_by' => (int) $userId,
            ]);

            $lotStock->update(['stock_on_hand' => $after]);

            return $movement;
        });
    }

    /**
     * Stock IN to a lot (mandatory).
     *
     * @param array{
     *   uniform_variant_id:int,
     *   qty:int,
     *   occurred_at?:string|Carbon,
     *   uniform_lot_id?:int|null,
     *   lot_code?:string|null,
     *   received_at?:string|Carbon|null,
     *   lot_notes?:string|null,
     *   notes?:string|null
     * } $data
     */
    public function stockInToLot(array $data): UniformStockMovement
    {
        $variantId = (int) Arr::get($data, 'uniform_variant_id');
        $qty = (int) Arr::get($data, 'qty');
        $occurredAt = Arr::get($data, 'occurred_at') ?? now();

        if ($variantId <= 0) {
            throw new RuntimeException('Variant tidak valid.');
        }
        if ($qty <= 0) {
            throw new RuntimeException('Qty harus lebih dari 0.');
        }

        $userId = Auth::id();
        if ($userId === null) {
            throw new RuntimeException('User belum login.');
        }

        return DB::transaction(function () use ($data, $variantId, $qty, $occurredAt, $userId) {
            /** @var UniformVariant|null $variant */
            $variant = UniformVariant::query()->whereKey($variantId)->lockForUpdate()->first();
            if ($variant === null) {
                throw (new ModelNotFoundException())->setModel(UniformVariant::class, [$variantId]);
            }

            $lot = $this->resolveLotForStockIn($data);

            /** @var UniformLotStock|null $lotStock */
            $lotStock = UniformLotStock::query()
                ->where('uniform_variant_id', $variantId)
                ->where('uniform_lot_id', (int) $lot->getKey())
                ->lockForUpdate()
                ->first();

            if ($lotStock === null) {
                try {
                    $lotStock = UniformLotStock::query()->create([
                        'uniform_variant_id' => $variantId,
                        'uniform_lot_id' => (int) $lot->getKey(),
                        'stock_on_hand' => 0,
                    ]);
                } catch (QueryException $e) {
                    $lotStock = UniformLotStock::query()
                        ->where('uniform_variant_id', $variantId)
                        ->where('uniform_lot_id', (int) $lot->getKey())
                        ->lockForUpdate()
                        ->first();

                    if ($lotStock === null) {
                        throw $e;
                    }
                }

                $lotStock = UniformLotStock::query()->whereKey($lotStock->getKey())->lockForUpdate()->firstOrFail();
            }

            $before = (int) $lotStock->stock_on_hand;
            $after = $before + $qty;

            $movementNo = $this->trxNoGenerator->next('uniform_movement', 'UF', $occurredAt instanceof Carbon ? $occurredAt : Carbon::parse((string) $occurredAt));

            /** @var UniformStockMovement $movement */
            $movement = UniformStockMovement::query()->create([
                'movement_no' => $movementNo,
                'movement_type' => 'IN',
                'occurred_at' => $occurredAt,
                'uniform_variant_id' => $variantId,
                'uniform_lot_id' => (int) $lot->getKey(),
                'qty' => $qty,
                'stock_on_hand_after' => $after,
                'reference_type' => 'stock_in',
                'reference_id' => (int) $lot->getKey(),
                'notes' => Arr::get($data, 'notes'),
                'created_by' => (int) $userId,
            ]);

            $lotStock->update(['stock_on_hand' => $after]);

            return $movement;
        });
    }

    /**
     * Allocate lots for a variant with optional selected lot. Uses FIFO by UniformLot.received_at.
     * Must be called inside an open transaction.
     *
     * @return array<int, array{uniform_lot_id:int, qty:int}>
     */
    public function allocateFifoLots(int $uniformVariantId, int $qty, ?int $selectedLotId = null): array
    {
        if ($uniformVariantId <= 0) {
            throw new RuntimeException('Variant tidak valid.');
        }
        if ($qty <= 0) {
            throw new RuntimeException('Qty harus lebih dari 0.');
        }

        $remaining = $qty;
        $allocations = [];

        $base = UniformLotStock::query()
            ->leftJoin('m_igi_uniform_lots', 'm_igi_uniform_lot_stocks.uniform_lot_id', '=', 'm_igi_uniform_lots.id')
            ->where('m_igi_uniform_lot_stocks.uniform_variant_id', $uniformVariantId)
            ->where('m_igi_uniform_lot_stocks.stock_on_hand', '>', 0)
            ->when($selectedLotId !== null, fn ($q) => $q->where('m_igi_uniform_lot_stocks.uniform_lot_id', $selectedLotId))
            ->orderBy('m_igi_uniform_lots.received_at')
            ->orderBy('m_igi_uniform_lot_stocks.uniform_lot_id')
            ->select([
                'm_igi_uniform_lot_stocks.id',
                'm_igi_uniform_lot_stocks.uniform_lot_id',
                'm_igi_uniform_lot_stocks.stock_on_hand',
            ]);

        /** @var \Illuminate\Support\Collection<int, object{ id:int, uniform_lot_id:int, stock_on_hand:int }> $rows */
        $rows = $base->lockForUpdate()->get();

        foreach ($rows as $r) {
            if ($remaining <= 0) {
                break;
            }

            $available = (int) ($r->stock_on_hand ?? 0);
            if ($available <= 0) {
                continue;
            }

            $take = min($remaining, $available);
            $allocations[] = [
                'uniform_lot_id' => (int) $r->uniform_lot_id,
                'qty' => (int) $take,
            ];
            $remaining -= $take;
        }

        if ($remaining > 0) {
            $suffix = $selectedLotId !== null ? ' pada LOT terpilih.' : ' (total semua LOT).';
            throw new RuntimeException('Stok tidak mencukupi' . $suffix);
        }

        return $allocations;
    }

    /**
     * Distribute using universal method: only reduces physical stock.
     *
     * @param array{
     *   employee_id:int,
     *   allocated_at?:string|Carbon,
     *   notes?:string|null,
     *   items: array<int, array{uniform_variant_id:int, qty:int, uniform_lot_id?:int|null}>
     * } $data
     */
    public function distributeUniversal(array $data): UniformAllocation
    {
        return $this->distribute($data, 'UNIVERSAL');
    }

    /**
     * Distribute using assigned method: checks and reduces entitlement + reduces physical stock.
     *
     * @param array{
     *   employee_id:int,
     *   allocated_at?:string|Carbon,
     *   notes?:string|null,
     *   items: array<int, array{uniform_variant_id:int, qty:int, uniform_lot_id?:int|null}>
     * } $data
     */
    public function distributeAssigned(array $data): UniformAllocation
    {
        return $this->distribute($data, 'ASSIGNED');
    }

    /**
     * @param 'UNIVERSAL'|'ASSIGNED' $method
     */
    private function distribute(array $data, string $method): UniformAllocation
    {
        $employeeId = (int) Arr::get($data, 'employee_id');
        $allocatedAt = Arr::get($data, 'allocated_at') ?? now();
        $notes = Arr::get($data, 'notes');
        $items = Arr::get($data, 'items', []);

        if ($employeeId <= 0) {
            throw new RuntimeException('Karyawan tidak valid.');
        }
        if (!is_array($items) || count($items) === 0) {
            throw new RuntimeException('Item distribusi wajib diisi.');
        }

        $userId = Auth::id();
        if ($userId === null) {
            throw new RuntimeException('User belum login.');
        }

        return DB::transaction(function () use ($employeeId, $allocatedAt, $notes, $items, $method, $userId) {
            /** @var Employee|null $employee */
            $employee = Employee::query()->whereKey($employeeId)->lockForUpdate()->first();
            if ($employee === null) {
                throw (new ModelNotFoundException())->setModel(Employee::class, [$employeeId]);
            }

            if ($method === 'ASSIGNED') {
                // ASSIGNED: kuota per uniform, tanpa ukuran/LOT, tidak mengurangi stok fisik.
                $normalizedUniformItems = $this->normalizeUniformItems($items);
                $this->assertAndConsumeEntitlementsForUniformItems($employeeId, $normalizedUniformItems, $allocatedAt);

                $allocationNo = $this->trxNoGenerator->next('uniform_allocation', 'UFA', $allocatedAt instanceof Carbon ? $allocatedAt : Carbon::parse((string) $allocatedAt));

                /** @var UniformAllocation $allocation */
                $allocation = UniformAllocation::query()->create([
                    'allocation_no' => $allocationNo,
                    'allocation_method' => $method,
                    'allocated_at' => $allocatedAt,
                    'employee_id' => $employeeId,
                    'notes' => $notes,
                    'created_by' => (int) $userId,
                ]);

                foreach ($normalizedUniformItems as $item) {
                    $allocation->items()->create([
                        'uniform_id' => (int) $item['uniform_id'],
                        'uniform_variant_id' => null,
                        'uniform_lot_id' => null,
                        'qty' => (int) $item['qty'],
                    ]);
                }

                return $allocation->load(['employee', 'items.uniform']);
            }

            $normalizedItems = $this->normalizeItems($items);
            $variantIds = array_values(array_unique(array_map(fn ($i) => (int) $i['uniform_variant_id'], $normalizedItems)));

            /** @var \Illuminate\Support\Collection<int, UniformVariant> $variants */
            $variants = UniformVariant::query()
                ->with('uniform')
                ->whereIn('id', $variantIds)
                ->lockForUpdate()
                ->get();

            if ($variants->count() !== count($variantIds)) {
                throw new RuntimeException('Ada variant yang tidak ditemukan.');
            }

            $allocationNo = $this->trxNoGenerator->next('uniform_allocation', 'UFA', $allocatedAt instanceof Carbon ? $allocatedAt : Carbon::parse((string) $allocatedAt));

            /** @var UniformAllocation $allocation */
            $allocation = UniformAllocation::query()->create([
                'allocation_no' => $allocationNo,
                'allocation_method' => $method,
                'allocated_at' => $allocatedAt,
                'employee_id' => $employeeId,
                'notes' => $notes,
                'created_by' => (int) $userId,
            ]);

            foreach ($normalizedItems as $item) {
                $variantId = (int) $item['uniform_variant_id'];
                $qty = (int) $item['qty'];
                $selectedLotId = array_key_exists('uniform_lot_id', $item) ? ($item['uniform_lot_id'] !== null ? (int) $item['uniform_lot_id'] : null) : null;

                $allocLots = $this->allocateFifoLots($variantId, $qty, $selectedLotId);

                foreach ($allocLots as $alloc) {
                    $lotId = (int) $alloc['uniform_lot_id'];
                    $takeQty = (int) $alloc['qty'];

                    /** @var UniformLotStock|null $lotStock */
                    $lotStock = UniformLotStock::query()
                        ->where('uniform_variant_id', $variantId)
                        ->where('uniform_lot_id', $lotId)
                        ->lockForUpdate()
                        ->first();

                    if ($lotStock === null) {
                        throw new RuntimeException('Stok LOT tidak ditemukan.');
                    }

                    $before = (int) $lotStock->stock_on_hand;
                    if ($before < $takeQty) {
                        throw new RuntimeException('Stok LOT tidak mencukupi saat proses transaksi.');
                    }
                    $after = $before - $takeQty;

                    $movementNo = $this->trxNoGenerator->next('uniform_movement', 'UF', $allocatedAt instanceof Carbon ? $allocatedAt : Carbon::parse((string) $allocatedAt));

                    UniformStockMovement::query()->create([
                        'movement_no' => $movementNo,
                        'movement_type' => 'OUT',
                        'occurred_at' => $allocatedAt,
                        'uniform_variant_id' => $variantId,
                        'uniform_lot_id' => $lotId,
                        'qty' => $takeQty,
                        'stock_on_hand_after' => $after,
                        'reference_type' => 'uniform_allocation',
                        'reference_id' => (int) $allocation->getKey(),
                        'notes' => $notes,
                        'created_by' => (int) $userId,
                    ]);

                    $allocation->items()->create([
                        'uniform_variant_id' => $variantId,
                        'uniform_lot_id' => $lotId,
                        'qty' => $takeQty,
                    ]);

                    $lotStock->update(['stock_on_hand' => $after]);
                }
            }

            return $allocation->load(['employee', 'items.variant.uniform', 'items.lot']);
        });
    }

    /**
     * @param array<int, array{uniform_id:mixed, qty:mixed}> $items
     * @return array<int, array{uniform_id:int, qty:int}>
     */
    private function normalizeUniformItems(array $items): array
    {
        $out = [];

        foreach ($items as $row) {
            $uniformId = (int) Arr::get($row, 'uniform_id');
            $qty = (int) Arr::get($row, 'qty');

            if ($uniformId <= 0 || $qty <= 0) {
                continue;
            }

            $out[] = [
                'uniform_id' => $uniformId,
                'qty' => $qty,
            ];
        }

        if (count($out) === 0) {
            throw new RuntimeException('Item distribusi tidak valid.');
        }

        return $out;
    }

    /**
     * @param array<int, array{uniform_id:int, qty:int}> $items
     * @param string|Carbon $allocatedAt
     */
    private function assertAndConsumeEntitlementsForUniformItems(int $employeeId, array $items, $allocatedAt): void
    {
        $byUniform = [];
        foreach ($items as $i) {
            $uniformId = (int) $i['uniform_id'];
            $qty = (int) $i['qty'];
            if ($uniformId <= 0) {
                continue;
            }
            $byUniform[$uniformId] = (int) ($byUniform[$uniformId] ?? 0) + $qty;
        }

        $when = $allocatedAt instanceof Carbon ? $allocatedAt : Carbon::parse((string) $allocatedAt);
        $whenDate = $when->toDateString();

        foreach ($byUniform as $uniformId => $needQty) {
            /** @var \Illuminate\Support\Collection<int, UniformEntitlement> $ents */
            $ents = UniformEntitlement::query()
                ->where('employee_id', $employeeId)
                ->where('uniform_id', (int) $uniformId)
                ->where(function ($q) use ($whenDate) {
                    $q->whereNull('effective_from')->orWhereDate('effective_from', '<=', $whenDate);
                })
                ->where(function ($q) use ($whenDate) {
                    $q->whereNull('effective_to')->orWhereDate('effective_to', '>=', $whenDate);
                })
                ->orderBy('effective_from')
                ->lockForUpdate()
                ->get();

            if ($ents->count() === 0) {
                /** @var Uniform|null $uniform */
                $uniform = Uniform::query()->whereKey((int) $uniformId)->first();
                $name = $uniform?->name ?? 'Uniform';
                throw new RuntimeException('Kuota seragam tidak ditemukan / tidak aktif untuk: ' . $name);
            }

            $totalRemaining = 0;
            foreach ($ents as $ent) {
                $totalRemaining += max(0, (int) $ent->total_qty - (int) $ent->used_qty);
            }

            if ((int) $totalRemaining < (int) $needQty) {
                /** @var Uniform|null $uniform */
                $uniform = Uniform::query()->whereKey((int) $uniformId)->first();
                $name = $uniform?->name ?? 'Uniform';
                throw new RuntimeException('Kuota tidak mencukupi untuk: ' . $name . ' (sisa ' . (int) $totalRemaining . ', butuh ' . (int) $needQty . ')');
            }

            $toConsume = (int) $needQty;
            foreach ($ents as $ent) {
                if ($toConsume <= 0) {
                    break;
                }

                $used = (int) $ent->used_qty;
                $remaining = max(0, (int) $ent->total_qty - $used);
                if ($remaining <= 0) {
                    continue;
                }

                $take = min($remaining, $toConsume);
                $ent->update(['used_qty' => $used + (int) $take]);
                $toConsume -= (int) $take;
            }
        }
    }

    /**
     * @param array<int, array{uniform_variant_id:mixed, qty:mixed, uniform_lot_id?:mixed}> $items
     * @return array<int, array{uniform_variant_id:int, qty:int, uniform_lot_id?:int|null}>
     */
    private function normalizeItems(array $items): array
    {
        $out = [];

        foreach ($items as $row) {
            $variantId = (int) Arr::get($row, 'uniform_variant_id');
            $qty = (int) Arr::get($row, 'qty');
            $lotId = Arr::exists($row, 'uniform_lot_id') ? Arr::get($row, 'uniform_lot_id') : null;
            $lotId = ($lotId === '' || $lotId === null) ? null : (int) $lotId;

            if ($variantId <= 0 || $qty <= 0) {
                continue;
            }

            $normalized = [
                'uniform_variant_id' => $variantId,
                'qty' => $qty,
            ];
            if (Arr::exists($row, 'uniform_lot_id')) {
                $normalized['uniform_lot_id'] = $lotId;
            }

            $out[] = $normalized;
        }

        if (count($out) === 0) {
            throw new RuntimeException('Item distribusi tidak valid.');
        }

        return $out;
    }

    /**
     * @param \Illuminate\Support\Collection<int, UniformVariant> $variants
     * @param array<int, array{uniform_variant_id:int, qty:int, uniform_lot_id?:int|null}> $items
     * @param string|Carbon $allocatedAt
     */
    private function assertAndConsumeEntitlements(int $employeeId, $variants, array $items, $allocatedAt): void
    {
        $when = $allocatedAt instanceof Carbon ? $allocatedAt : Carbon::parse((string) $allocatedAt);
        $whenDate = $when->toDateString();

        $byUniform = [];

        foreach ($items as $i) {
            $variantId = (int) $i['uniform_variant_id'];
            $qty = (int) $i['qty'];

            /** @var UniformVariant|null $variant */
            $variant = $variants->firstWhere('id', $variantId);
            $uniformId = (int) ($variant?->uniform_id ?? 0);

            if ($uniformId <= 0) {
                throw new RuntimeException('Uniform untuk variant tidak ditemukan.');
            }

            $byUniform[$uniformId] = (int) ($byUniform[$uniformId] ?? 0) + $qty;
        }

        foreach ($byUniform as $uniformId => $needQty) {
            /** @var \Illuminate\Support\Collection<int, UniformEntitlement> $ents */
            $ents = UniformEntitlement::query()
                ->where('employee_id', $employeeId)
                ->where('uniform_id', (int) $uniformId)
                ->where(function ($q) use ($whenDate) {
                    $q->whereNull('effective_from')->orWhereDate('effective_from', '<=', $whenDate);
                })
                ->where(function ($q) use ($whenDate) {
                    $q->whereNull('effective_to')->orWhereDate('effective_to', '>=', $whenDate);
                })
                ->orderBy('effective_from')
                ->lockForUpdate()
                ->get();

            if ($ents->count() === 0) {
                /** @var Uniform|null $uniform */
                $uniform = Uniform::query()->whereKey((int) $uniformId)->first();
                $name = $uniform?->name ?? 'Uniform';
                throw new RuntimeException('Kuota seragam tidak ditemukan / tidak aktif untuk: ' . $name);
            }

            $totalRemaining = 0;
            foreach ($ents as $ent) {
                $totalRemaining += max(0, (int) $ent->total_qty - (int) $ent->used_qty);
            }

            if ((int) $totalRemaining < (int) $needQty) {
                /** @var Uniform|null $uniform */
                $uniform = Uniform::query()->whereKey((int) $uniformId)->first();
                $name = $uniform?->name ?? 'Uniform';
                throw new RuntimeException('Kuota tidak mencukupi untuk: ' . $name . ' (sisa ' . (int) $totalRemaining . ', butuh ' . (int) $needQty . ')');
            }

            $toConsume = (int) $needQty;
            foreach ($ents as $ent) {
                if ($toConsume <= 0) {
                    break;
                }

                $used = (int) $ent->used_qty;
                $remaining = max(0, (int) $ent->total_qty - $used);
                if ($remaining <= 0) {
                    continue;
                }

                $take = min($remaining, $toConsume);
                $ent->update(['used_qty' => $used + (int) $take]);
                $toConsume -= (int) $take;
            }
        }
    }

    /**
     * Must be called inside a transaction.
     *
     * @param array{uniform_lot_id?:int|null, lot_code?:string|null, received_at?:string|Carbon|null, lot_notes?:string|null} $data
     */
    private function resolveLotForStockIn(array $data): UniformLot
    {
        $lotId = Arr::get($data, 'uniform_lot_id');
        $lotId = ($lotId === '' || $lotId === null) ? null : (int) $lotId;

        if ($lotId !== null && $lotId > 0) {
            /** @var UniformLot|null $lot */
            $lot = UniformLot::query()->whereKey($lotId)->lockForUpdate()->first();
            if ($lot === null) {
                throw (new ModelNotFoundException())->setModel(UniformLot::class, [$lotId]);
            }

            return $lot;
        }

        $lotCode = trim((string) Arr::get($data, 'lot_code', ''));
        $receivedAt = Arr::get($data, 'received_at');
        $lotNotes = Arr::get($data, 'lot_notes');

        if ($receivedAt === null || (string) $receivedAt === '') {
            throw new RuntimeException('Tanggal terima (received_at) wajib diisi.');
        }

        $receivedAtCarbon = $receivedAt instanceof Carbon ? $receivedAt : Carbon::parse((string) $receivedAt);

        if ($lotCode !== '') {
            /** @var UniformLot|null $lot */
            $lot = UniformLot::query()->where('lot_code', $lotCode)->lockForUpdate()->first();
            if ($lot !== null) {
                return $lot;
            }
        }

        $attempts = 0;
        while (true) {
            if ($lotCode === '') {
                $lotCode = $this->nextLotCodeForReceivedAt($receivedAtCarbon);
            }

            try {
                $lot = UniformLot::query()->create([
                    'lot_code' => $lotCode,
                    'received_at' => $receivedAtCarbon,
                    'notes' => $lotNotes,
                ]);
                break;
            } catch (QueryException $e) {
                $existing = UniformLot::query()->where('lot_code', $lotCode)->lockForUpdate()->first();
                if ($existing !== null) {
                    return $existing;
                }

                if ($this->isDuplicateKeyException($e) && $attempts < 3) {
                    $attempts++;
                    $lotCode = '';
                    continue;
                }

                throw $e;
            }
        }

        return UniformLot::query()->whereKey($lot->getKey())->lockForUpdate()->firstOrFail();
    }

    private function nextLotCodeForReceivedAt(Carbon $receivedAt): string
    {
        $base = 'LOT-UF-' . $receivedAt->format('Ymd');

        $baseExists = UniformLot::query()->where('lot_code', $base)->exists();

        $baseLenPlusDash = strlen($base) + 2; // SUBSTRING is 1-indexed, start after "{base}-"
        $maxSuffix = (int) (UniformLot::query()
            ->where('lot_code', 'like', $base . '-%')
            ->selectRaw('MAX(CAST(SUBSTRING(lot_code, ?) AS UNSIGNED)) as max_num', [$baseLenPlusDash])
            ->value('max_num') ?? 0);

        if (! $baseExists && $maxSuffix === 0) {
            return $base;
        }

        $next = max(1, $maxSuffix) + 1;
        return $base . '-' . str_pad((string) $next, 2, '0', STR_PAD_LEFT);
    }

    private function isDuplicateKeyException(QueryException $e): bool
    {
        $message = strtolower((string) $e->getMessage());

        return str_contains($message, 'duplicate entry')
            || str_contains($message, 'unique constraint')
            || str_contains($message, 'duplicate key');
    }
}
