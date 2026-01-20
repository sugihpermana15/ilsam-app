<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetVendor extends Model
{
  protected $table = 'm_igi_asset_vendors';

  protected $fillable = [
    'name',
    'is_active',
  ];
}
