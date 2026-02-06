<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UniformLot extends Model
{
    protected $table = 'm_igi_uniform_lots';

    protected $fillable = [
        'lot_code',
        'received_at',
        'notes',
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    public function lotStocks(): HasMany
    {
        return $this->hasMany(UniformLotStock::class, 'uniform_lot_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(UniformStockMovement::class, 'uniform_lot_id');
    }

    public function allocationItems(): HasMany
    {
        return $this->hasMany(UniformAllocationItem::class, 'uniform_lot_id');
    }
}
