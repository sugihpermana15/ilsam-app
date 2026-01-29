<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountChange extends Model
{
    protected $table = 'm_igi_account_changes';

    protected $fillable = [
        'account_id',
        'change_type',
        'changed_by',
        'diff',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'diff' => 'array',
        ];
    }
}
