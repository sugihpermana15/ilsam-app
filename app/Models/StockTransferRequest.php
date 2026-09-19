<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransferRequest extends Model
{
    public const REQUESTED = 'REQUESTED';
    public const PREPARED = 'PREPARED';
    public const SHIPPED = 'SHIPPED';
    public const RECEIVED = 'RECEIVED';
    public const REJECTED = 'REJECTED';

    protected $fillable = [
        'request_no', 'stock_item_id', 'category', 'qty', 'from_site', 'to_site',
        'requested_by', 'requested_pic_id', 'driver_name', 'driver_phone', 'status',
        'notes', 'shipping_notes', 'prepared_by', 'prepared_at', 'shipped_by',
        'shipped_at', 'received_by', 'received_at',
    ];

    protected $casts = [
        'qty' => 'integer',
        'prepared_at' => 'datetime',
        'shipped_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function item(): BelongsTo { return $this->belongsTo(StockItem::class, 'stock_item_id'); }
    public function requester(): BelongsTo { return $this->belongsTo(User::class, 'requested_by'); }
    public function requestedPic(): BelongsTo { return $this->belongsTo(Employee::class, 'requested_pic_id'); }
}
