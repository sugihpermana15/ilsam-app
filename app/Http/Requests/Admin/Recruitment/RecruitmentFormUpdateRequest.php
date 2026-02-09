<?php

namespace App\Http\Requests\Admin\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class RecruitmentFormUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'position_name' => ['required', 'string', 'max:160'],
            'position_code_initial' => ['required', 'string', 'max:20'],
            'is_security_position' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
