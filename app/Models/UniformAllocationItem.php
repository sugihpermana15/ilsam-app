<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UniformAllocationItem extends Model
{
    protected $table = 'm_igi_uniform_allocation_items';

    protected $fillable = [
        'uniform_allocation_id',
        'uniform_id',
        'uniform_variant_id',
        'uniform_lot_id',
        'qty',
    ];

    protected $casts = [
        'qty' => 'int',
    ];

    public function allocation(): BelongsTo
    {
        return $this->belongsTo(UniformAllocation::class, 'uniform_allocation_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(UniformVariant::class, 'uniform_variant_id');
    }

    public function uniform(): BelongsTo
    {
        return $this->belongsTo(Uniform::class, 'uniform_id');
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(UniformLot::class, 'uniform_lot_id');
    }
}
