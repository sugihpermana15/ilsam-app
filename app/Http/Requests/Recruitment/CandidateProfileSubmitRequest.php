<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class CandidateProfileSubmitRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_security_position' => filter_var($this->input('is_security_position'), FILTER_VALIDATE_BOOL),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isSecurity = (bool) $this->input('is_security_position', false);

        $commonFiles = ['file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'];

        return [
            'full_name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:200'],
            'phone' => ['required', 'string', 'max:60'],
            'position_applied' => ['required', 'string', 'max:160'],
            'height_cm' => ['required', 'integer', 'min:50', 'max:300'],
            'weight_kg' => ['required', 'integer', 'min:20', 'max:400'],
            'address_ktp' => ['required', 'string'],
            'address_domicile' => ['required', 'string'],
            'last_education' => ['nullable', 'string', 'max:160'],
            'work_experience' => ['nullable', 'string'],

            'is_security_position' => ['nullable', 'boolean'],

            'cv' => $isSecurity ? ['nullable'] : array_merge(['required'], $commonFiles),
            'security_garda_pratama' => $isSecurity ? array_merge(['required'], $commonFiles) : ['nullable'],
            'security_kta' => $isSecurity ? array_merge(['required'], $commonFiles) : ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'cv.required' => 'CV wajib diunggah untuk posisi non-security.',
            'security_garda_pratama.required' => 'Sertifikat Garda Pratama wajib diunggah untuk posisi security.',
            'security_kta.required' => 'KTA Security wajib diunggah untuk posisi security.',
        ];
    }
}
