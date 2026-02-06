<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UniformLotStock extends Model
{
    protected $table = 'm_igi_uniform_lot_stocks';

    protected $fillable = [
        'uniform_variant_id',
        'uniform_lot_id',
        'stock_on_hand',
    ];

    protected $casts = [
        'stock_on_hand' => 'int',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(UniformVariant::class, 'uniform_variant_id');
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(UniformLot::class, 'uniform_lot_id');
    }
}
