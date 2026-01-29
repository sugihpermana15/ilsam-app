<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Document extends Model
{
    use SoftDeletes;

    protected $table = 'm_igi_documents';

    protected $primaryKey = 'document_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'document_id',
        'document_number',
        'document_title',
        'document_type',
        'vendor_id',
        'contract_group_id',
        'department_owner_id',
        'pic_user_id',
        'status',
        'confidentiality_level',
        'tags',
        'description',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->document_id)) {
                $model->document_id = (string) Str::uuid();
            }
        });
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(AssetVendor::class, 'vendor_id');
    }

    public function contractGroup(): BelongsTo
    {
        return $this->belongsTo(ContractGroup::class, 'contract_group_id', 'id');
    }

    public function departmentOwner(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_owner_id');
    }

    public function picUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function files(): HasMany
    {
        return $this->hasMany(DocumentFile::class, 'document_id', 'document_id');
    }

    public function latestFile(): HasOne
    {
        return $this->hasOne(DocumentFile::class, 'document_id', 'document_id')->where('is_latest', true);
    }

    public function contractTerms(): HasOne
    {
        return $this->hasOne(ContractTerms::class, 'document_id', 'document_id');
    }

    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'm_igi_document_sites', 'document_id', 'location_id')
            ->withTimestamps();
    }

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class, 'm_igi_document_assets', 'document_id', 'asset_id')
            ->withTimestamps();
    }

    public function scopeVisibleTo(Builder $query, ?User $user, bool $canSeeRestricted): Builder
    {
        if ($canSeeRestricted) {
            return $query;
        }

        return $query->where('confidentiality_level', '!=', 'Restricted');
    }
}
