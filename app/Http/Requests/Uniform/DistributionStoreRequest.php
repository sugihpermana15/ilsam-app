<?php

namespace App\Http\Requests\Uniform;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DistributionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $method = strtoupper((string) $this->input('allocation_method', ''));
        $isUniversal = $method === 'UNIVERSAL';
        $isAssigned = $method === 'ASSIGNED';

        return [
            'allocation_method' => ['required', 'string', 'in:UNIVERSAL,ASSIGNED'],
            'employee_id' => ['required', 'integer', 'exists:m_igi_employees,id'],
            'allocated_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],

            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.uniform_id' => [
                Rule::requiredIf($isAssigned),
                Rule::prohibitedIf($isUniversal),
                'integer',
                'exists:m_igi_uniforms,id',
            ],
            'items.*.uniform_variant_id' => [
                Rule::requiredIf($isUniversal),
                Rule::prohibitedIf($isAssigned),
                'integer',
                'exists:m_igi_uniform_variants,id',
            ],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:1000000'],
            'items.*.uniform_lot_id' => [
                Rule::prohibitedIf($isAssigned),
                'nullable',
                'integer',
                'exists:m_igi_uniform_lots,id',
            ],
        ];
    }
}
