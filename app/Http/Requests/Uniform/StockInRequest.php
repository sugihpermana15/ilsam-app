<?php

namespace App\Http\Requests\Uniform;

use Illuminate\Foundation\Http\FormRequest;

class StockInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'uniform_variant_id' => ['required', 'integer', 'exists:m_igi_uniform_variants,id'],
            'qty' => ['required', 'integer', 'min:1', 'max:1000000'],
            'occurred_at' => ['nullable', 'date'],

            // Either choose existing lot OR provide lot_code + received_at.
            'uniform_lot_id' => ['nullable', 'integer', 'exists:m_igi_uniform_lots,id'],
            'lot_code' => ['nullable', 'string', 'max:60'],
            'received_at' => ['nullable', 'date', 'required_without:uniform_lot_id'],
            'lot_notes' => ['nullable', 'string', 'max:2000'],

            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
