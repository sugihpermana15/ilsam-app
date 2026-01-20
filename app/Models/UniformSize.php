<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniformSize extends Model
{
  protected $table = 'm_igi_uniform_sizes';

  protected $fillable = [
    'code',
    'name',
    'is_active',
  ];

  protected $casts = [
    'is_active' => 'boolean',
  ];

  public function items()
  {
    return $this->hasMany(UniformItem::class, 'uniform_size_id');
  }
}
