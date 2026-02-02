<?php

namespace App\Http\Requests;

use App\Enums\DailyTaskStatus;
use App\Models\DailyTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class DailyTaskUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();
        $isAdmin = in_array((string) ($user?->role?->role_name ?? ''), ['Super Admin', 'Admin'], true);

        /** @var DailyTask|null $task */
        $task = $this->route('task');

        $currentStatus = $task?->status;
        $allowedStatusValues = DailyTaskStatus::allowedNextValues($currentStatus, $isAdmin);

        $statusRule = [
            $isAdmin ? 'sometimes' : 'required',
            'integer',
            Rule::in($allowedStatusValues),
        ];

        if (!$isAdmin) {
            // Users are intentionally restricted: status is the only editable field.
            return [
                'status' => $statusRule,
                'task_type' => ['prohibited'],
                'title' => ['prohibited'],
                'description' => ['prohibited'],
                'due_start' => ['prohibited'],
                'due_end' => ['prohibited'],
                'priority' => ['prohibited'],
                'assigned_employee_id' => ['prohibited'],
                'assigned_to' => ['prohibited'],
            ];
        }

        return [
            'task_type' => ['sometimes', 'integer', 'exists:m_igi_daily_task_types,id'],
            'title' => ['sometimes', 'string', 'max:200'],
            'description' => ['sometimes', 'nullable', 'string'],
            'due_start' => ['sometimes', 'nullable', 'date'],
            'due_end' => ['sometimes', 'nullable', 'date', 'after_or_equal:due_start'],
            'status' => $statusRule,
            'priority' => ['sometimes', 'integer', 'exists:m_igi_daily_task_priorities,id'],
            'assigned_employee_id' => ['sometimes', 'nullable', 'integer', 'exists:m_igi_employees,id'],
            'assigned_to' => ['prohibited'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $status = (int) $this->input('status', 0);
            if ($status !== DailyTaskStatus::Done->value) {
                return;
            }

            /** @var DailyTask|null $task */
            $task = $this->route('task');
            if (!$task) {
                return;
            }

            $hasOpenChecklist = $task->checklists()->where('is_done', false)->exists();
            if ($hasOpenChecklist) {
                $validator->errors()->add('status', 'Status tidak bisa Done selama masih ada checklist yang belum selesai.');
            }
        });
    }
}
