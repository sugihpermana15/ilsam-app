<?php

namespace App\Http\Requests\Admin\Recruitment;

use App\Enums\RecruitmentQuestionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecruitmentQuestionUpdateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $text = (string) $this->input('options_text', '');
        $lines = preg_split("/\r\n|\r|\n/", $text) ?: [];

        $this->merge([
            'options' => $lines,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(RecruitmentQuestionType::all())],
            'question_text' => ['required', 'string'],
            'is_required' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'points' => ['nullable', 'integer', 'min:0'],
            'options_text' => ['nullable', 'string'],
            'options' => ['array'],
            'options.*' => ['nullable', 'string', 'max:255'],
        ];
    }
}
