<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StampTransaction extends Model
{
    protected $fillable = [
        'trx_no',
        'stamp_id',
        'trx_type',
        'trx_date',
        'qty',
        'pic_id',
        'notes',
        'created_by',
        'on_hand_after',
    ];

    protected $casts = [
        'trx_date' => 'date',
        'qty' => 'int',
        'on_hand_after' => 'int',
    ];

    public function stamp(): BelongsTo
    {
        return $this->belongsTo(Stamp::class, 'stamp_id');
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
