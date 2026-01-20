<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniformMovement extends Model
{
  protected $table = 'm_igi_uniform_movements';
  public $timestamps = false;

  protected $guarded = [];

  protected $casts = [
    'performed_at' => 'datetime',
    'expired_at' => 'date',
  ];

  public function item()
  {
    return $this->belongsTo(UniformItem::class, 'uniform_item_id');
  }

  public function performedBy()
  {
    return $this->belongsTo(User::class, 'performed_by');
  }

  public function issue()
  {
    return $this->belongsTo(UniformIssue::class, 'issue_id');
  }

  public function lot()
  {
    return $this->belongsTo(UniformLot::class, 'lot_id');
  }

  public function referenceMovement()
  {
    return $this->belongsTo(self::class, 'reference_movement_id');
  }
}
