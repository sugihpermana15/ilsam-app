<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecruitmentFormSubmissionAnswer extends Model
{
    protected $table = 'recruitment_form_submission_answers';

    protected $fillable = [
        'recruitment_form_submission_id',
        'recruitment_form_question_id',
        'recruitment_form_question_option_id',
        'answer_text',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(RecruitmentFormSubmission::class, 'recruitment_form_submission_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(RecruitmentFormQuestion::class, 'recruitment_form_question_id');
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(RecruitmentFormQuestionOption::class, 'recruitment_form_question_option_id');
    }
}
