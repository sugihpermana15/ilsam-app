<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecruitmentFormSubmissionFile extends Model
{
    protected $table = 'recruitment_form_submission_files';

    protected $fillable = [
        'recruitment_form_submission_id',
        'field_key',
        'disk',
        'storage_path',
        'original_name',
        'mime',
        'size',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(RecruitmentFormSubmission::class, 'recruitment_form_submission_id');
    }

    public function getFieldLabelAttribute(): string
    {
        return match ((string) $this->field_key) {
            'cv' => 'CV',
            'security_garda_pratama' => 'Sertifikat Garda Pratama',
            'security_kta' => 'KTA Security',
            default => (string) $this->field_key,
        };
    }
}
