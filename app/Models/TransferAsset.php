<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferAsset extends Model
{
  protected $table = 'm_igi_transfer_asset';
  protected $guarded = [];

  protected $casts = [
    'asset_payload' => 'array',
    'purchase_date' => 'date',
    'last_updated' => 'datetime',
    'transferred_at' => 'datetime',
    'requested_at' => 'datetime',
    'received_at' => 'datetime',
    'cancelled_at' => 'datetime',
  ];
}
