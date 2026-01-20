<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $employeeId = $this->route('employee')?->id ?? $this->route('employee');

    return [
      'name' => ['required', 'string', 'max:255'],
      'gender' => ['required', 'in:Laki-laki,Perempuan'],
      'birth_date' => ['nullable', 'date'],
      'address' => ['nullable', 'string'],
      'phone' => ['nullable', 'string', 'max:30'],
      'email' => ['nullable', 'email', 'max:255', 'unique:m_igi_employees,email,' . $employeeId],
      'department_id' => ['required', 'integer', 'exists:m_igi_departments,id'],
      'position_id' => ['required', 'integer', 'exists:m_igi_positions,id'],
      'employment_status' => ['nullable', 'in:PKWT,PKWTT'],
      'join_date' => ['required', 'date'],
      'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
      'remove_photo' => ['nullable', 'boolean'],
      'modal_context' => ['nullable', 'in:create,edit'],
      'employee_id' => ['nullable', 'integer'],
    ];
  }
}
