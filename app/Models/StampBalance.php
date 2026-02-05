<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StampBalance extends Model
{
    protected $fillable = [
        'stamp_id',
        'on_hand_qty',
    ];

    protected $casts = [
        'on_hand_qty' => 'int',
    ];

    public function stamp(): BelongsTo
    {
        return $this->belongsTo(Stamp::class, 'stamp_id');
    }
}
