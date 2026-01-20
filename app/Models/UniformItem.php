<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniformItem extends Model
{
  protected $table = 'm_igi_uniform_items';
  public $timestamps = false;

  protected $fillable = [
    'item_code',
    'item_name',
    'category',
    'size',
    'uniform_size_id',
    'color',
    'uom',
    'location',
    'min_stock',
    'current_stock',
    'is_active',
    'input_date',
    'last_updated',
    'notes',
  ];

  protected $casts = [
    'is_active' => 'boolean',
    'input_date' => 'datetime',
    'last_updated' => 'datetime',
  ];

  public function movements()
  {
    return $this->hasMany(UniformMovement::class, 'uniform_item_id');
  }

  public function sizeMaster()
  {
    return $this->belongsTo(UniformSize::class, 'uniform_size_id');
  }

  public function lots()
  {
    return $this->hasMany(UniformLot::class, 'uniform_item_id');
  }

  public function issues()
  {
    return $this->hasMany(UniformIssue::class, 'uniform_item_id');
  }
}
