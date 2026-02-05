<?php

namespace App\Http\Requests\Stamp;

use Illuminate\Foundation\Http\FormRequest;

class StoreStampOutRequest extends FormRequest
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
            'pic_id' => ['required', 'integer', 'exists:m_igi_employees,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
