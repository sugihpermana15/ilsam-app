<?php

namespace App\Services;

use App\Models\TrxNoSequence;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TrxNoGeneratorService
{
    /**
     * Generate sequential transaction number.
     *
     * Format example: SM-20260204-000001
     */
    public function next(string $sequenceName = 'stamp', string $prefix = 'SM', ?Carbon $date = null): string
    {
        $date = $date ?? now();
        $datePart = $date->format('Ymd');

        return DB::transaction(function () use ($sequenceName, $prefix, $datePart) {
            /** @var TrxNoSequence|null $sequence */
            $sequence = TrxNoSequence::query()
                ->where('name', $sequenceName)
                ->lockForUpdate()
                ->first();

            if ($sequence === null) {
                try {
                    $sequence = TrxNoSequence::query()->create([
                        'name' => $sequenceName,
                        'current_value' => 0,
                    ]);
                } catch (QueryException $e) {
                    // Concurrent first-create can hit unique(name). Re-read with lock.
                    $sequence = TrxNoSequence::query()
                        ->where('name', $sequenceName)
                        ->lockForUpdate()
                        ->first();

                    if ($sequence === null) {
                        throw $e;
                    }
                }

                // Lock the row we just created (or re-read).
                $sequence = TrxNoSequence::query()
                    ->whereKey($sequence->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $nextValue = (int) $sequence->current_value + 1;
            $sequence->update(['current_value' => $nextValue]);

            return sprintf('%s-%s-%06d', $prefix, $datePart, $nextValue);
        });
    }
}
