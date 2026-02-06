<?php

namespace App\Http\Requests\Uniform;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UniformLotUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $lotId = (int) ($this->route('lot')?->id ?? $this->route('lot') ?? 0);

        return [
            'lot_code' => ['nullable', 'string', 'max:60', Rule::unique('m_igi_uniform_lots', 'lot_code')->ignore($lotId)],
            'received_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
