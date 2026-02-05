<?php

namespace App\Http\Requests\Stamp;

use Illuminate\Foundation\Http\FormRequest;

class StoreStampMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['nullable', 'string', 'max:50', 'unique:stamps,code'],
            'name' => ['required', 'string', 'max:255'],
            'face_value' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => (bool) $this->input('is_active', true),
        ]);
    }
}
