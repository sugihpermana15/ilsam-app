<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountSecret extends Model
{
    use SoftDeletes;

    protected $table = 'm_igi_account_secrets';

    protected $fillable = [
        'account_id',
        'label',
        'kind',
        'username',
        'secret_ciphertext',
        'secret_algo',
        'secret_key_version',
        'valid_from',
        'valid_to',
        'is_active',
        'created_by',
        'rotated_from_secret_id',
        'metadata',
    ];

    protected $hidden = [
        'secret_ciphertext',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'is_active' => 'bool',
            'valid_from' => 'datetime',
            'valid_to' => 'datetime',
        ];
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function rotatedFrom()
    {
        return $this->belongsTo(self::class, 'rotated_from_secret_id');
    }
}
