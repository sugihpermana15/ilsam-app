<?php

namespace App\Http\Requests\Uniform;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UniformUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $uniformId = (int) ($this->route('uniform')?->id ?? $this->route('uniform') ?? 0);

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('m_igi_uniforms', 'code')->ignore($uniformId)],
            'name' => ['required', 'string', 'max:150'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
