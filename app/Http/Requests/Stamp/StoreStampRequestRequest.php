<?php

namespace App\Http\Requests\Stamp;

use Illuminate\Foundation\Http\FormRequest;

class StoreStampRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();
        $isSuperAdmin = (int) ($user?->role_id ?? 0) === 1;
        $needsManualPic = $isSuperAdmin && (int) ($user?->employee_id ?? 0) <= 0;

        return [
            'stamp_id' => ['required', 'integer', 'exists:stamps,id'],
            'qty' => ['required', 'integer', 'min:1'],
            'trx_date' => ['nullable', 'date'],
            'pic_id' => $needsManualPic
                ? ['required', 'integer', 'exists:m_igi_employees,id']
                : ['nullable', 'integer', 'exists:m_igi_employees,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
