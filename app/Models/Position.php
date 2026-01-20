<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
  protected $table = 'm_igi_positions';

  protected $fillable = [
    'name',
    'level_code',
  ];

  public function employees(): HasMany
  {
    return $this->hasMany(Employee::class, 'position_id');
  }
}
