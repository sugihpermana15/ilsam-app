<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecruitmentFormQuestionOption extends Model
{
    protected $table = 'recruitment_form_question_options';

    protected $fillable = [
        'recruitment_form_question_id',
        'option_text',
        'sort_order',
        'is_correct',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_correct' => 'boolean',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(RecruitmentFormQuestion::class, 'recruitment_form_question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(RecruitmentFormSubmissionAnswer::class, 'recruitment_form_question_option_id');
    }
}
