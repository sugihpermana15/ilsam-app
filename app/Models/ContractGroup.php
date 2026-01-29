<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ContractGroup extends Model
{
    protected $table = 'm_igi_contract_groups';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'title',
        'vendor_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(AssetVendor::class, 'vendor_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'contract_group_id', 'id');
    }
}
