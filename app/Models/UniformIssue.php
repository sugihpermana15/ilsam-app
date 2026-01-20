<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class UniformIssue extends Model
{
  protected $table = 'm_igi_uniform_issues';
  public $timestamps = false;

  protected $guarded = [];

  protected $casts = [
    'issued_at' => 'datetime',
    'returned_at' => 'datetime',
  ];

  public function item()
  {
    return $this->belongsTo(UniformItem::class, 'uniform_item_id');
  }

  public function issuedTo()
  {
    return $this->belongsTo(User::class, 'issued_to_user_id');
  }

  public function issuedToEmployee()
  {
    return $this->belongsTo(Employee::class, 'issued_to_employee_id');
  }

  public function issuedBy()
  {
    return $this->belongsTo(User::class, 'issued_by');
  }

  public function referenceIssue()
  {
    return $this->belongsTo(self::class, 'reference_issue_id');
  }

  public function issueLots()
  {
    return $this->hasMany(UniformIssueLot::class, 'issue_id');
  }

  public function movements()
  {
    return $this->hasMany(UniformMovement::class, 'issue_id');
  }
}
