<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrxNoSequence extends Model
{
    protected $table = 'trx_no_sequences';

    protected $fillable = [
        'name',
        'current_value',
    ];

    protected $casts = [
        'current_value' => 'int',
    ];
}
