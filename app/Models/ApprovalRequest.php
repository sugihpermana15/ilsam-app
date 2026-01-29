<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalRequest extends Model
{
    protected $table = 'm_igi_approval_requests';

    protected $fillable = [
        'requester_id',
        'request_type',
        'account_id',
        'secret_id',
        'reason',
        'status',
        'approver_id',
        'approved_at',
        'expires_at',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'expires_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }
}
