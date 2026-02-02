<?php

namespace App\Models;

use App\Enums\DailyTaskStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DailyTask extends Model
{
    use SoftDeletes;

    protected $table = 'm_igi_daily_tasks';

    protected $fillable = [
        'task_type',
        'title',
        'description_preview',
        'description',
        'due_start',
        'due_end',
        'status',
        'priority',
        'assigned_to',
        'assigned_employee_id',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => DailyTaskStatus::class,
            'task_type' => 'integer',
            'priority' => 'integer',
            'due_start' => 'datetime',
            'due_end' => 'datetime',
            'completed_at' => 'datetime',
            'canceled_at' => 'datetime',
        ];
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(DailyTaskAttachment::class, 'daily_task_id');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(DailyTaskChecklistItem::class, 'daily_task_id');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        $role = (string) ($user->role?->role_name ?? '');
        if ($role === 'Super Admin' || $role === 'Admin') {
            return $query;
        }

        $employeeId = $user->employee_id;

        return $query->where(function (Builder $q) use ($user, $employeeId) {
            if ($employeeId) {
                $q->where('assigned_employee_id', $employeeId);
            } else {
                $q->where('assigned_to', $user->id);
            }

            $q->orWhere('created_by', $user->id);
        });
    }

    public function syncDescriptionPreview(): void
    {
        $preview = $this->description;
        if ($preview === null || $preview === '') {
            $this->description_preview = null;
            return;
        }

        $preview = Str::squish(strip_tags((string) $preview));
        $this->description_preview = Str::limit($preview, 240, '…');
    }
}
