<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetUom extends Model
{
  protected $table = 'm_igi_asset_uoms';

  protected $fillable = [
    'name',
    'is_active',
  ];
}
