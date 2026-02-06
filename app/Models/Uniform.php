<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Uniform extends Model
{
    protected $table = 'm_igi_uniforms';

    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function variants(): HasMany
    {
        return $this->hasMany(UniformVariant::class, 'uniform_id');
    }

    public function entitlements(): HasMany
    {
        return $this->hasMany(UniformEntitlement::class, 'uniform_id');
    }
}
