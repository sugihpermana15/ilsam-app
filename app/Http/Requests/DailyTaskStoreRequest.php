<?php

namespace App\Http\Requests;

use App\Enums\DailyTaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class DailyTaskStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'task_type' => ['required', 'integer', 'exists:m_igi_daily_task_types,id'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'due_start' => ['nullable', 'date'],
            'due_end' => ['nullable', 'date', 'after_or_equal:due_start'],
            'status' => ['required', 'integer', 'exists:m_igi_daily_task_statuses,id'],
            'priority' => ['required', 'integer', 'exists:m_igi_daily_task_priorities,id'],
            'assigned_employee_id' => ['nullable', 'integer', 'exists:m_igi_employees,id'],
            // Backward-compatible (optional)
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'checklist_lines' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $status = (int) $this->input('status', 0);
            if ($status !== DailyTaskStatus::Done->value) {
                return;
            }

            $lines = (string) $this->input('checklist_lines', '');
            $rows = preg_split('/\r\n|\r|\n/', $lines) ?: [];
            $rows = array_values(array_filter(array_map(fn ($l) => trim((string) $l), $rows)));

            if (count($rows) > 0) {
                $validator->errors()->add('status', 'Status tidak bisa Done selama masih ada checklist yang belum selesai.');
            }
        });
    }
}
