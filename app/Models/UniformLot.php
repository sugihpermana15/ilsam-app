<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniformLot extends Model
{
  protected $table = 'm_igi_uniform_lots';

  protected $fillable = [
    'uniform_item_id',
    'lot_number',
    'qty_in',
    'remaining_qty',
    'expired_at',
    'received_at',
    'received_by',
    'notes',
  ];

  protected $casts = [
    'expired_at' => 'date',
    'received_at' => 'datetime',
  ];

  public function item()
  {
    return $this->belongsTo(UniformItem::class, 'uniform_item_id');
  }

  public function receivedBy()
  {
    return $this->belongsTo(User::class, 'received_by');
  }

  public function movements()
  {
    return $this->hasMany(UniformMovement::class, 'lot_id');
  }
}
