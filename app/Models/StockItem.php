<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StockItem extends Model
{
    protected $fillable = [
        'category',
        'code',
        'name',
        'unit',
        'unit_price',
        'image_path',
        'current_stock',
        'low_stock_threshold',
        'is_active',
    ];

    protected $casts = [
        'unit_price' => 'integer',
        'current_stock' => 'integer',
        'low_stock_threshold' => 'integer',
        'is_active' => 'boolean',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function siteBalances(): HasMany
    {
        return $this->hasMany(StockItemSiteBalance::class);
    }

    public function latestTransaction(): HasOne
    {
        return $this->hasOne(StockTransaction::class)->latestOfMany();
    }
}
