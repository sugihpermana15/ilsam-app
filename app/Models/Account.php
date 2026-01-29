<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;

    protected $table = 'm_igi_accounts';

    protected $fillable = [
        'account_type_id',
        'environment',
        'asset_id',
        'location_id',
        'asset_code_snapshot',
        'asset_name_snapshot',
        'plant_site_snapshot',
        'location_name_snapshot',
        'department_owner',
        'criticality',
        'status',
        'vendor_installer',
        'last_verified_at',
        'last_verified_by',
        'note',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'last_verified_at' => 'datetime',
        ];
    }

    public function type()
    {
        return $this->belongsTo(AccountType::class, 'account_type_id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function endpoints()
    {
        return $this->hasMany(AccountEndpoint::class, 'account_id');
    }

    public function secrets()
    {
        return $this->hasMany(AccountSecret::class, 'account_id');
    }
}
