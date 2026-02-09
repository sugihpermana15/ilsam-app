<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class RecruitmentForm extends Model
{
    protected $table = 'recruitment_forms';

    protected $fillable = [
        'uuid',
        'public_token',
        'title',
        'position_name',
        'position_code_initial',
        'is_security_position',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_security_position' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->public_token)) {
                $model->public_token = (string) Str::uuid();
            }
        });
    }

    public function questions(): HasMany
    {
        return $this->hasMany(RecruitmentFormQuestion::class, 'recruitment_form_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(RecruitmentFormSubmission::class, 'recruitment_form_id');
    }
}
