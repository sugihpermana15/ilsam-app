<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniformItemName extends Model
{
  protected $table = 'm_igi_uniform_item_names';

  protected $fillable = [
    'name',
    'is_active',
  ];

  protected $casts = [
    'is_active' => 'boolean',
  ];
}
