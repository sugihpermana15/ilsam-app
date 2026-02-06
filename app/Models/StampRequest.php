<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StampRequest extends Model
{
    public const STATUS_SUBMITTED = 'SUBMITTED';
    public const STATUS_APPROVED = 'APPROVED';
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_HANDED_OVER = 'HANDED_OVER';

    protected $fillable = [
        'request_no',
        'stamp_id',
        'qty',
        'trx_date',
        'pic_id',
        'notes',
        'status',
        'requested_by',
        'requested_at',
        'validator_user_id',
        'validated_by',
        'validated_at',
        'validation_notes',
        'handed_over_by',
        'handed_over_at',
        'handover_trx_id',
    ];

    protected $casts = [
        'qty' => 'int',
        'trx_date' => 'date',
        'requested_at' => 'datetime',
        'validated_at' => 'datetime',
        'handed_over_at' => 'datetime',
    ];

    public function stamp(): BelongsTo
    {
        return $this->belongsTo(Stamp::class, 'stamp_id');
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'pic_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validator_user_id');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function handedOverBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handed_over_by');
    }

    public function handoverTransaction(): BelongsTo
    {
        return $this->belongsTo(StampTransaction::class, 'handover_trx_id');
    }
}
