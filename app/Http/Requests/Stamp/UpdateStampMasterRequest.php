<?php

namespace App\Http\Requests\Stamp;

use App\Models\Stamp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStampMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $routeStamp = $this->route('stamp');
        $stampId = $routeStamp instanceof Stamp ? (int) $routeStamp->id : (int) $routeStamp;

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('stamps', 'code')->ignore($stampId)],
            'name' => ['required', 'string', 'max:255'],
            'face_value' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => (bool) $this->input('is_active', false),
        ]);
    }
}
