<?php

namespace App\Http\Requests\Uniform;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UniformLotStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lot_code' => ['nullable', 'string', 'max:60', Rule::unique('m_igi_uniform_lots', 'lot_code')],
            'received_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
