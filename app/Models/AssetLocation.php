<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetLocation extends Model
{
  protected $table = 'm_igi_asset_locations';

  protected $fillable = [
    'name',
    'asset_code_prefix',
    'is_active',
  ];
}
