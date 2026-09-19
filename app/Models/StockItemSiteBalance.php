<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockItemSiteBalance extends Model
{
    protected $fillable = [
        'stock_item_id',
        'site_code',
        'on_hand_qty',
    ];

    protected $casts = [
        'on_hand_qty' => 'integer',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }
}
