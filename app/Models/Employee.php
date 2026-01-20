<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
  use SoftDeletes;

  protected $table = 'm_igi_employees';

  protected $fillable = [
    'sequence_number',
    'no_id',
    'name',
    'gender',
    'birth_date',
    'address',
    'phone',
    'email',
    'department_id',
    'position_id',
    'employment_status',
    'join_date',
    'photo',
  ];

  protected $casts = [
    'birth_date' => 'date',
    'join_date' => 'date',
    'deleted_at' => 'datetime',
  ];

  public function department(): BelongsTo
  {
    return $this->belongsTo(Department::class, 'department_id');
  }

  public function position(): BelongsTo
  {
    return $this->belongsTo(Position::class, 'position_id');
  }

  public function auditLogs(): HasMany
  {
    return $this->hasMany(EmployeeAuditLog::class, 'employee_id');
  }
}
