<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UniformStockMovement extends Model
{
    protected $table = 'm_igi_uniform_stock_movements';

    protected $fillable = [
        'movement_no',
        'movement_type',
        'occurred_at',
        'uniform_variant_id',
        'uniform_lot_id',
        'qty',
        'stock_on_hand_after',
        'reference_type',
        'reference_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'qty' => 'int',
        'stock_on_hand_after' => 'int',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(UniformVariant::class, 'uniform_variant_id');
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(UniformLot::class, 'uniform_lot_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
