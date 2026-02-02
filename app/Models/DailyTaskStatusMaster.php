<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyTaskStatusMaster extends Model
{
    protected $table = 'm_igi_daily_task_statuses';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'name',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
