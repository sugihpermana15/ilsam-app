<?php

namespace App\Http\Requests\Stamp;

use Illuminate\Foundation\Http\FormRequest;

class StoreStampInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stamp_id' => ['required', 'integer', 'exists:stamps,id'],
            'trx_date' => ['required', 'date'],
            'qty' => ['required', 'integer', 'min:1'],
            'pic_id' => ['nullable', 'integer', 'exists:m_igi_employees,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $user = $this->user();
        $picId = $user?->employee_id ? (int) $user->employee_id : null;

        // Force PIC to the logged-in user for Pembelian (IN).
        $this->merge([
            'pic_id' => $picId,
        ]);
    }
}
