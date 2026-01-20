<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSequence extends Model
{
  protected $table = 'm_igi_employee_sequences';

  protected $fillable = [
    'name',
    'last_value',
  ];
}
