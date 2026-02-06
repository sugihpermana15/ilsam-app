<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UniformVariant extends Model
{
    protected $table = 'm_igi_uniform_variants';

    protected $fillable = [
        'uniform_id',
        'size',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function uniform(): BelongsTo
    {
        return $this->belongsTo(Uniform::class, 'uniform_id');
    }

    public function lotStocks(): HasMany
    {
        return $this->hasMany(UniformLotStock::class, 'uniform_variant_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(UniformStockMovement::class, 'uniform_variant_id');
    }

    public function allocationItems(): HasMany
    {
        return $this->hasMany(UniformAllocationItem::class, 'uniform_variant_id');
    }
}
