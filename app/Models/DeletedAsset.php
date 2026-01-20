<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeletedAsset extends Model
{
  protected $table = 'deleted_asset';
  protected $primaryKey = 'id';
  public $timestamps = true;

  protected $fillable = [
    'asset_id',
    'asset_code',
    'asset_name',
    'asset_category',
    'brand_type_model',
    'serial_number',
    'description',
    'purchase_date',
    'price',
    'qty',
    'satuan',
    'vendor_supplier',
    'invoice_number',
    'asset_location',
    'department',
    'person_in_charge',
    'ownership_status',
    'asset_condition',
    'asset_status',
    'start_use_date',
    'warranty_status',
    'warranty_end_date',
    'input_date',
    'input_by',
    'last_updated',
    'notes',
    'image_1',
    'image_2',
    'image_3',
    'deleted_at',
    'deleted_by',
  ];
}
