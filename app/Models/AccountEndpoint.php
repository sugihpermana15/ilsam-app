<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountEndpoint extends Model
{
    use SoftDeletes;

    protected $table = 'm_igi_account_endpoints';

    protected $fillable = [
        'account_id',
        'service',
        'protocol',
        'ip_local',
        'ip_public',
        'hostname',
        'port',
        'path',
        'is_management',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_management' => 'bool',
            'metadata' => 'array',
        ];
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
