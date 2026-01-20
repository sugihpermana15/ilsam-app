<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAuditLog extends Model
{
  protected $table = 'm_igi_employee_audit_logs';

  public $timestamps = false;

  protected $fillable = [
    'employee_id',
    'action',
    'performed_by',
    'performed_by_name',
    'ip_address',
    'user_agent',
    'old_values',
    'new_values',
    'created_at',
  ];

  protected $casts = [
    'old_values' => 'array',
    'new_values' => 'array',
    'created_at' => 'datetime',
  ];

  public function employee(): BelongsTo
  {
    return $this->belongsTo(Employee::class, 'employee_id')->withTrashed();
  }

  public function performedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'performed_by');
  }
}
