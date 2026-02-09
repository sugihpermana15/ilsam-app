<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class RecruitmentFormSubmission extends Model
{
    protected $table = 'recruitment_form_submissions';

    protected $fillable = [
        'uuid',
        'public_token',
        'recruitment_form_id',
        'candidate_code',
        'full_name',
        'email',
        'phone',
        'position_applied',
        'height_cm',
        'weight_kg',
        'address_ktp',
        'address_domicile',
        'last_education',
        'work_experience',
        'status',
        'test_submitted_at',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'height_cm' => 'integer',
            'weight_kg' => 'integer',
            'test_submitted_at' => 'datetime',
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

    public function form(): BelongsTo
    {
        return $this->belongsTo(RecruitmentForm::class, 'recruitment_form_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(RecruitmentFormSubmissionAnswer::class, 'recruitment_form_submission_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(RecruitmentFormSubmissionFile::class, 'recruitment_form_submission_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ((string) $this->status) {
            'profile_submitted' => 'Profil terkirim',
            'test_submitted' => 'Tes terkirim',
            default => (string) $this->status,
        };
    }
}
