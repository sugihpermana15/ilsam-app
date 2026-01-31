<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceMaintenance extends Model
{
    protected $table = 'm_igi_device_maintenances';

    protected $fillable = [
        'device_id',
        'maintenance_at',
        'type',
        'description',
        'performed_by',
        'attachment_path',
        'next_schedule_at',
        'created_by',
    ];

    protected $casts = [
        'maintenance_at' => 'datetime',
        'next_schedule_at' => 'date',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id');
    }
}
