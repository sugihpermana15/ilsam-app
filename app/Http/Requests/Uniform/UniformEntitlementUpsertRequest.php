<?php

namespace App\Http\Requests\Uniform;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UniformEntitlementUpsertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:m_igi_employees,id'],
            'uniform_id' => ['required', 'integer', 'exists:m_igi_uniforms,id'],
            'total_qty' => ['required', 'integer', 'min:0'],
            'effective_from' => ['nullable', 'date'],
            'effective_to' => ['nullable', 'date'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $from = $this->input('effective_from');
            $to = $this->input('effective_to');
            if ($from && $to && strtotime((string) $from) > strtotime((string) $to)) {
                $v->errors()->add('effective_to', 'Tanggal akhir harus >= tanggal mulai.');
            }
        });
    }
}
