<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniformColor extends Model
{
  protected $table = 'm_igi_uniform_colors';

  protected $fillable = [
    'name',
    'is_active',
  ];

  protected $casts = [
    'is_active' => 'boolean',
  ];
}
