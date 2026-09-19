<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransaction extends Model
{
    protected $fillable = [
        'trx_no',
        'stock_item_id',
        'site_code',
        'trx_type',
        'trx_date',
        'qty',
        'stock_after',
        'pic_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'trx_date' => 'date',
        'qty' => 'integer',
        'stock_after' => 'integer',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'pic_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
