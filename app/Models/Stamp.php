<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Stamp extends Model
{
    protected $fillable = [
        'code',
        'name',
        'face_value',
        'is_active',
    ];

    protected $casts = [
        'face_value' => 'int',
        'is_active' => 'bool',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(StampTransaction::class, 'stamp_id');
    }

    public function balance(): HasOne
    {
        return $this->hasOne(StampBalance::class, 'stamp_id');
    }
}
