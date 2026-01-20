<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniformUom extends Model
{
  protected $table = 'm_igi_uniform_uoms';

  protected $fillable = [
    'code',
    'name',
    'is_active',
  ];

  protected $casts = [
    'is_active' => 'boolean',
  ];
}
