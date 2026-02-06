<?php

namespace App\Http\Requests\Uniform;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UniformVariantStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $uniformId = (int) $this->input('uniform_id');

        return [
            'uniform_id' => ['required', 'integer', 'exists:m_igi_uniforms,id'],
            'size' => [
                'required',
                'string',
                'max:20',
                Rule::unique('m_igi_uniform_variants', 'size')->where(fn ($q) => $q->where('uniform_id', $uniformId)),
            ],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
