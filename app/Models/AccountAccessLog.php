<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountAccessLog extends Model
{
    protected $table = 'm_igi_account_access_logs';

    protected $fillable = [
        'actor_user_id',
        'account_id',
        'action',
        'result',
        'reason',
        'target_type',
        'target_id',
        'ip_address',
        'user_agent',
        'request_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
