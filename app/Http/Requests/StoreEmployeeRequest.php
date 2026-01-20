<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name' => ['required', 'string', 'max:255'],
      'gender' => ['required', 'in:Laki-laki,Perempuan'],
      'birth_date' => ['nullable', 'date'],
      'address' => ['nullable', 'string'],
      'phone' => ['nullable', 'string', 'max:30'],
      'email' => ['nullable', 'email', 'max:255', 'unique:m_igi_employees,email'],
      'department_id' => ['required', 'integer', 'exists:m_igi_departments,id'],
      'position_id' => ['required', 'integer', 'exists:m_igi_positions,id'],
      'employment_status' => ['nullable', 'in:PKWT,PKWTT'],
      'join_date' => ['required', 'date'],
      'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
    ];
  }
}
