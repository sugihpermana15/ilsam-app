<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecruitmentFormQuestion extends Model
{
    protected $table = 'recruitment_form_questions';

    protected $fillable = [
        'recruitment_form_id',
        'type',
        'question_text',
        'is_required',
        'sort_order',
        'points',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'sort_order' => 'integer',
            'points' => 'integer',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(RecruitmentForm::class, 'recruitment_form_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(RecruitmentFormQuestionOption::class, 'recruitment_form_question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(RecruitmentFormSubmissionAnswer::class, 'recruitment_form_question_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ((string) $this->type) {
            'multiple_choice' => 'Pilihan Ganda',
            'short_text' => 'Isian Singkat',
            'essay' => 'Essay',
            default => (string) $this->type,
        };
    }
}
