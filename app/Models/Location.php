<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Location extends Model
{
    protected $table = 'm_igi_locations';

    protected $fillable = [
        'plant_site',
        'building',
        'floor',
        'room_rack',
        'name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'bool',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        // Treat NULL as active (legacy data / incomplete seeds)
        return $query->where(function (Builder $q) {
            $q->where('is_active', true)->orWhereNull('is_active');
        });
    }
}
