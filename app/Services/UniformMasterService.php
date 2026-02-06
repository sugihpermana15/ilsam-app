<?php

namespace App\Services;

use App\Models\Uniform;
use App\Models\UniformEntitlement;
use App\Models\UniformLot;
use App\Models\UniformVariant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Carbon\Carbon;

class UniformMasterService
{
    /**
     * @param array{code:string,name:string,is_active?:bool} $data
     */
    public function createUniform(array $data): Uniform
    {
        return DB::transaction(function () use ($data) {
            return Uniform::query()->create([
                'code' => trim((string) $data['code']),
                'name' => trim((string) $data['name']),
                'is_active' => (bool) ($data['is_active'] ?? true),
            ]);
        });
    }

    /**
     * @param array{code:string,name:string,is_active?:bool} $data
     */
    public function updateUniform(int $uniformId, array $data): Uniform
    {
        return DB::transaction(function () use ($uniformId, $data) {
            /** @var Uniform|null $uniform */
            $uniform = Uniform::query()->whereKey($uniformId)->lockForUpdate()->first();
            if ($uniform === null) {
                throw (new ModelNotFoundException())->setModel(Uniform::class, [$uniformId]);
            }

            $uniform->update([
                'code' => trim((string) $data['code']),
                'name' => trim((string) $data['name']),
                'is_active' => (bool) ($data['is_active'] ?? $uniform->is_active),
            ]);

            return $uniform;
        });
    }

    public function toggleUniform(int $uniformId): Uniform
    {
        return DB::transaction(function () use ($uniformId) {
            /** @var Uniform|null $uniform */
            $uniform = Uniform::query()->whereKey($uniformId)->lockForUpdate()->first();
            if ($uniform === null) {
                throw (new ModelNotFoundException())->setModel(Uniform::class, [$uniformId]);
            }

            $uniform->update(['is_active' => !(bool) $uniform->is_active]);

            return $uniform;
        });
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

        // MySQL: "Duplicate entry"; SQLite: "UNIQUE constraint failed"; PostgreSQL: "duplicate key".
        return str_contains($message, 'duplicate entry')
            || str_contains($message, 'unique constraint')
            || str_contains($message, 'duplicate key');
    }

    /**
     * @param array{uniform_id:int,size:string,is_active?:bool} $data
     */
    public function createVariant(array $data): UniformVariant
    {
        return DB::transaction(function () use ($data) {
            $uniformId = (int) Arr::get($data, 'uniform_id');
            if ($uniformId <= 0) {
                throw new RuntimeException('Uniform tidak valid.');
            }

            return UniformVariant::query()->create([
                'uniform_id' => $uniformId,
                'size' => trim((string) $data['size']),
                'is_active' => (bool) ($data['is_active'] ?? true),
            ]);
        });
    }

    /**
     * @param array{uniform_id:int,size:string,is_active?:bool} $data
     */
    public function updateVariant(int $variantId, array $data): UniformVariant
    {
        return DB::transaction(function () use ($variantId, $data) {
            /** @var UniformVariant|null $variant */
            $variant = UniformVariant::query()->whereKey($variantId)->lockForUpdate()->first();
            if ($variant === null) {
                throw (new ModelNotFoundException())->setModel(UniformVariant::class, [$variantId]);
            }

            $variant->update([
                'uniform_id' => (int) Arr::get($data, 'uniform_id', $variant->uniform_id),
                'size' => trim((string) $data['size']),
                'is_active' => (bool) ($data['is_active'] ?? $variant->is_active),
            ]);

            return $variant;
        });
    }

    public function toggleVariant(int $variantId): UniformVariant
    {
        return DB::transaction(function () use ($variantId) {
            /** @var UniformVariant|null $variant */
            $variant = UniformVariant::query()->whereKey($variantId)->lockForUpdate()->first();
            if ($variant === null) {
                throw (new ModelNotFoundException())->setModel(UniformVariant::class, [$variantId]);
            }

            $variant->update(['is_active' => !(bool) $variant->is_active]);

            return $variant;
        });
    }

    /**
     * @param array{lot_code?:string|null,received_at:string,notes?:string|null} $data
     */
    public function createLot(array $data): UniformLot
    {
        return DB::transaction(function () use ($data): UniformLot {
            $receivedAt = Carbon::parse($data['received_at']);

            // Auto-generate lot_code if not provided.
            $lotCode = trim((string) ($data['lot_code'] ?? ''));

            $attempts = 0;
            while (true) {
                if ($lotCode === '') {
                    $lotCode = $this->nextLotCodeForReceivedAt($receivedAt);
                }

                try {
                    return UniformLot::query()->create([
                        'lot_code' => $lotCode,
                        'received_at' => $receivedAt,
                        'notes' => $data['notes'] ?? null,
                    ]);
                } catch (QueryException $e) {
                    if ($this->isDuplicateKeyException($e) && $attempts < 3) {
                        $attempts++;
                        $lotCode = '';
                        continue;
                    }

                    throw $e;
                }
            }
        });
    }

    /**
     * @param array{lot_code?:string|null,received_at:string,notes?:string|null} $data
     */
    public function updateLot(int $lotId, array $data): UniformLot
    {
        return DB::transaction(function () use ($lotId, $data) {
            /** @var UniformLot|null $lot */
            $lot = UniformLot::query()->whereKey($lotId)->lockForUpdate()->first();
            if ($lot === null) {
                throw (new ModelNotFoundException())->setModel(UniformLot::class, [$lotId]);
            }

            $receivedAt = Carbon::parse($data['received_at']);

            $lotCode = trim((string) ($data['lot_code'] ?? ''));

            $attempts = 0;
            while (true) {
                if ($lotCode === '') {
                    $lotCode = $this->nextLotCodeForReceivedAt($receivedAt);
                }

                try {
                    $lot->update([
                        'lot_code' => $lotCode,
                        'received_at' => $receivedAt,
                        'notes' => $data['notes'] ?? null,
                    ]);
                    break;
                } catch (QueryException $e) {
                    if ($this->isDuplicateKeyException($e) && $attempts < 3) {
                        $attempts++;
                        $lotCode = '';
                        continue;
                    }

                    throw $e;
                }
            }

            return $lot;
        });
    }

    /**
     * Create or replace entitlement total (used_qty stays untouched).
     *
     * @param array{employee_id:int,uniform_id:int,total_qty:int,effective_from?:string|null,effective_to?:string|null} $data
     */
    public function upsertEntitlement(array $data): UniformEntitlement
    {
        return DB::transaction(function () use ($data) {
            $employeeId = (int) Arr::get($data, 'employee_id');
            $uniformId = (int) Arr::get($data, 'uniform_id');
            $totalQty = (int) Arr::get($data, 'total_qty');

            if ($employeeId <= 0 || $uniformId <= 0) {
                throw new RuntimeException('Karyawan atau uniform tidak valid.');
            }
            if ($totalQty < 0) {
                throw new RuntimeException('Total kuota tidak valid.');
            }

            /** @var UniformEntitlement|null $ent */
            $ent = UniformEntitlement::query()
                ->where('employee_id', $employeeId)
                ->where('uniform_id', $uniformId)
                ->lockForUpdate()
                ->first();

            if ($ent === null) {
                return UniformEntitlement::query()->create([
                    'employee_id' => $employeeId,
                    'uniform_id' => $uniformId,
                    'total_qty' => $totalQty,
                    'used_qty' => 0,
                    'effective_from' => Arr::get($data, 'effective_from'),
                    'effective_to' => Arr::get($data, 'effective_to'),
                ]);
            }

            $ent->update([
                'total_qty' => $totalQty,
                'effective_from' => Arr::get($data, 'effective_from'),
                'effective_to' => Arr::get($data, 'effective_to'),
            ]);

            return $ent;
        });
    }
}
