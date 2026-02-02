<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyTaskPriority extends Model
{
    protected $table = 'm_igi_daily_task_priorities';

    protected $fillable = [
        'name',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
