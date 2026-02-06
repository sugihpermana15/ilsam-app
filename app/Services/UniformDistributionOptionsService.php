<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Uniform;
use App\Models\UniformEntitlement;
use App\Models\UniformVariant;
use Illuminate\Support\Carbon;

class UniformDistributionOptionsService
{
    /**
     * Eligible employees for ASSIGNED method: must have at least one active entitlement.
     * Active means within effective date window (if set) and remaining quota can be > 0.
     *
     * @return array<int, array{id:int,label:string}>
     */
    public function eligibleEmployees(?Carbon $at = null): array
    {
        $at = $at ?? now();
        $date = $at->toDateString();

        $employeeIds = UniformEntitlement::query()
            ->whereRaw('COALESCE(total_qty, 0) > COALESCE(used_qty, 0)')
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_from')->orWhere('effective_from', '<=', $date);
            })
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_to')->orWhere('effective_to', '>=', $date);
            })
            ->pluck('employee_id')
            ->map(fn ($v) => (int) $v)
            ->filter(fn ($v) => $v > 0)
            ->unique()
            ->values()
            ->all();

        if (count($employeeIds) === 0) {
            return [];
        }

        return Employee::query()
            ->whereIn('id', $employeeIds)
            ->orderBy('name')
            ->get(['id', 'name', 'no_id'])
            ->map(fn ($e) => [
                'id' => (int) $e->id,
                'label' => trim((string) ($e->no_id ?? '')) !== ''
                    ? ((string) $e->no_id . ' - ' . (string) $e->name)
                    : (string) $e->name,
            ])
            ->values()
            ->all();
    }

    /**
     * Uniform options for a specific employee (ASSIGNED): based on active entitlements.
     *
     * @return array<int, array{id:int,label:string,remaining_qty:int}>
     */
    public function uniformsForEmployee(int $employeeId, ?Carbon $at = null): array
    {
        if ($employeeId <= 0) {
            return [];
        }

        $at = $at ?? now();
        $date = $at->toDateString();

        $ents = UniformEntitlement::query()
            ->where('employee_id', $employeeId)
            ->whereRaw('COALESCE(total_qty, 0) > COALESCE(used_qty, 0)')
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_from')->orWhere('effective_from', '<=', $date);
            })
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_to')->orWhere('effective_to', '>=', $date);
            })
            ->get(['uniform_id', 'total_qty', 'used_qty']);

        $remainingByUniform = [];
        foreach ($ents as $ent) {
            $uniformId = (int) ($ent->uniform_id ?? 0);
            if ($uniformId <= 0) {
                continue;
            }

            $remaining = max(0, (int) ($ent->total_qty ?? 0) - (int) ($ent->used_qty ?? 0));
            $remainingByUniform[$uniformId] = (int) ($remainingByUniform[$uniformId] ?? 0) + (int) $remaining;
        }

        $uniformIds = array_values(array_keys($remainingByUniform));
        if (count($uniformIds) === 0) {
            return [];
        }

        $uniforms = Uniform::query()->whereIn('id', $uniformIds)->get(['id', 'code', 'name'])->keyBy('id');

        return collect($remainingByUniform)
            ->map(function ($remainingQty, $uniformId) use ($uniforms) {
                $uniformId = (int) $uniformId;
                $u = $uniforms->get($uniformId);

                return [
                    'id' => $uniformId,
                    'label' => $u ? ((string) $u->code . ' - ' . (string) $u->name) : (string) $uniformId,
                    'remaining_qty' => (int) $remainingQty,
                ];
            })
            ->sortBy('label')
            ->values()
            ->all();
    }

    /**
     * Variant options for a uniform (size list).
     *
     * @return array<int, array{id:int,label:string}>
     */
    public function variantsForUniform(int $uniformId): array
    {
        if ($uniformId <= 0) {
            return [];
        }

        return UniformVariant::query()
            ->where('uniform_id', $uniformId)
            ->where('is_active', true)
            ->orderBy('size')
            ->get(['id', 'size'])
            ->map(fn ($v) => [
                'id' => (int) $v->id,
                'label' => (string) ($v->size ?? '-'),
            ])
            ->values()
            ->all();
    }
}
