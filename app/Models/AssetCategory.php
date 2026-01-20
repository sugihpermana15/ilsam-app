<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
  protected $table = 'm_igi_asset_categories';

  protected $fillable = [
    'code',
    'name',
    'asset_code_prefix',
    'is_active',
  ];
}
