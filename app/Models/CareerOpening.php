<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CareerOpening extends Model
{
    use HasUuids;

    protected $fillable = [
        'title',
        'department',
        'location',
        'type',
        'work_mode',
        'experience',
        'summary',
        'responsibilities',
        'requirements',
        'apply_url',
        'deadline',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function scopePubliclyAvailable(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $query) {
                $query->whereNull('deadline')->orWhereDate('deadline', '>=', today());
            });
    }

    public function isPastDeadline(): bool
    {
        return $this->deadline !== null && $this->deadline->lt(today());
    }

    public function getIsPubliclyAvailableAttribute(): bool
    {
        return (bool) $this->getRawOriginal('is_active') && !$this->isPastDeadline();
    }
}