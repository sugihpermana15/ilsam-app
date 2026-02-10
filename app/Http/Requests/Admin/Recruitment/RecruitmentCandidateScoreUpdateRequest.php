<?php

namespace App\Http\Requests\Admin\Recruitment;

use App\Enums\RecruitmentQuestionType;
use App\Models\RecruitmentFormSubmission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RecruitmentCandidateScoreUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'scores' => ['required', 'array'],
            'scores.*' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            /** @var RecruitmentFormSubmission|null $submission */
            $submission = $this->route('submission');
            if (!$submission instanceof RecruitmentFormSubmission) {
                return;
            }

            $submission->loadMissing('form.questions');
            $questions = $submission->form?->questions ?? collect();

            $scores = $this->input('scores', []);
            if (!is_array($scores)) {
                return;
            }

            foreach ($scores as $questionId => $scoreRaw) {
                $qid = (int) $questionId;
                $q = $questions->firstWhere('id', $qid);
                if (!$q) {
                    $v->errors()->add('scores', 'Ada pertanyaan yang tidak valid.');
                    continue;
                }

                $type = (string) $q->type;
                if ($type === RecruitmentQuestionType::MULTIPLE_CHOICE) {
                    $v->errors()->add('scores', 'Nilai manual hanya untuk Essay dan Isian Singkat.');
                    continue;
                }

                if ($scoreRaw === null || $scoreRaw === '') {
                    continue;
                }

                $score = (int) $scoreRaw;
                $max = (int) ($q->points ?? 0);
                if ($score > $max) {
                    $v->errors()->add('scores.' . $qid, 'Nilai tidak boleh melebihi poin maksimal (' . $max . ').');
                }
            }
        });
    }
}
