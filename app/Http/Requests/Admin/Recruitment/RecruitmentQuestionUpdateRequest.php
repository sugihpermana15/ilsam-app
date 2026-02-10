<?php

namespace App\Http\Requests\Admin\Recruitment;

use App\Enums\RecruitmentQuestionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'correct_option' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $type = (string) $this->input('type');
            if ($type !== RecruitmentQuestionType::MULTIPLE_CHOICE) {
                return;
            }

            $options = $this->input('options', []);
            $normalized = collect(is_array($options) ? $options : [])
                ->map(fn ($val) => $val === null ? '' : trim((string) $val))
                ->filter(fn ($val) => $val !== '')
                ->values();

            if ($normalized->count() < 2) {
                $v->errors()->add('options_text', 'Untuk Pilihan Ganda, minimal harus ada 2 opsi.');
                return;
            }

            $correct = $this->input('correct_option');
            if ($correct === null || $correct === '') {
                $v->errors()->add('correct_option', 'Jawaban benar wajib diisi untuk Pilihan Ganda.');
                return;
            }

            $correctInt = (int) $correct;
            if ($correctInt < 1 || $correctInt > $normalized->count()) {
                $v->errors()->add('correct_option', 'Nomor jawaban benar harus sesuai jumlah opsi.');
            }
        });
    }
}
