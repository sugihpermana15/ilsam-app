<?php

namespace App\Http\Requests\Uniform;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LotStockAdjustRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'stock_on_hand' => ['required', 'integer', 'min:0', 'max:100000000'],
            'occurred_at' => ['nullable', 'date'],
            'notes' => ['required', 'string', 'max:2000'],
        ];
    }
}
