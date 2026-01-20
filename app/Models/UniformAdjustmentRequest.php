<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniformAdjustmentRequest extends Model
{
  protected $table = 'm_igi_uniform_adjustment_requests';

  protected $fillable = [
    'uniform_item_id',
    'lot_id',
    'qty_change',
    'reason',
    'reference_movement_id',
    'approval_status',
    'requested_by',
    'requested_at',
    'approved_by',
    'approved_at',
    'rejection_reason',
    'approved_movement_id',
  ];

  protected $casts = [
    'requested_at' => 'datetime',
    'approved_at' => 'datetime',
  ];

  public function item()
  {
    return $this->belongsTo(UniformItem::class, 'uniform_item_id');
  }

  public function lot()
  {
    return $this->belongsTo(UniformLot::class, 'lot_id');
  }

  public function requestedBy()
  {
    return $this->belongsTo(User::class, 'requested_by');
  }

  public function approvedBy()
  {
    return $this->belongsTo(User::class, 'approved_by');
  }

  public function referenceMovement()
  {
    return $this->belongsTo(UniformMovement::class, 'reference_movement_id');
  }

  public function approvedMovement()
  {
    return $this->belongsTo(UniformMovement::class, 'approved_movement_id');
  }
}
