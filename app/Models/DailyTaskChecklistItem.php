<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyTaskChecklistItem extends Model
{
    protected $table = 'm_igi_daily_task_checklist_items';

    protected $fillable = [
        'daily_task_id',
        'item_text',
        'is_done',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_done' => 'boolean',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(DailyTask::class, 'daily_task_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
