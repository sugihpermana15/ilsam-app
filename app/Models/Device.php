<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use SoftDeletes;

    protected $table = 'm_igi_devices';

    protected $fillable = [
        'asset_id',
        'asset_code',
        'asset_name',
        'asset_serial_number',
        'asset_status',
        'asset_location',
        'asset_department',
        'asset_department_id',
        'asset_person_in_charge',
        'asset_person_in_charge_employee_id',
        'asset_payload',

        'device_name',
        'device_id',
        'product_id',
        'os_name',
        'os_edition',
        'os_version',
        'domain_workgroup',
        'domain_join_status',
        'domain_name',
        'workgroup_name',
        'processor',
        'ram_gb',
        'storage_total_gb',
        'storage_type',
        'storage_items',
        'gpu',

        'location_site',
        'location_room',

        'owner_type',
        'device_role',

        'mac_lan',
        'mac_wifi',
        'ip_address',
        'subnet_mask',
        'gateway',
        'dns_primary',
        'dns_secondary',
        'connectivity',
        'ssid',
        'internet_download_mbps',
        'internet_upload_mbps',

        'remote_app_type',
        'remote_id',
        'remote_password',
        'remote_unattended',
        'remote_notes',

        'vault_mode',
        'local_admin_username',
        'local_admin_password',

        'last_maintenance_at',

        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'asset_payload' => 'array',
        'storage_items' => 'array',
        'remote_password' => 'encrypted',
        'local_admin_password' => 'encrypted',
        'remote_unattended' => 'boolean',
        'vault_mode' => 'boolean',
        'ram_gb' => 'integer',
        'storage_total_gb' => 'integer',
        'internet_download_mbps' => 'decimal:2',
        'internet_upload_mbps' => 'decimal:2',
        'last_maintenance_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(DeviceMaintenance::class, 'device_id')->orderByDesc('maintenance_at')->orderByDesc('id');
    }
}
