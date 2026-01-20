<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
  use SoftDeletes;
  protected $table = 'm_igi_asset';
  protected $primaryKey = 'id';
  public $timestamps = false;

  protected $fillable = [
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
    'department_id',
    'person_in_charge',
    'person_in_charge_employee_id',
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
  ];
}
