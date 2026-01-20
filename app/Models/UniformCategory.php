<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniformCategory extends Model
{
  protected $table = 'm_igi_uniform_categories';

  protected $fillable = [
    'name',
    'is_active',
  ];

  protected $casts = [
    'is_active' => 'boolean',
  ];
}
