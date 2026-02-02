<?php

namespace App\Policies;

use App\Models\DailyTask;
use App\Models\User;

class DailyTaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, DailyTask $task): bool
    {
        return $this->isAdmin($user)
            || $this->isAssignedToUser($user, $task)
            || $task->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, DailyTask $task): bool
    {
        return $this->isAdmin($user)
            || $this->isAssignedToUser($user, $task)
            || $task->created_by === $user->id;
    }

    public function delete(User $user, DailyTask $task): bool
    {
        return $this->isAdmin($user) || $task->created_by === $user->id;
    }

    public function manageChecklist(User $user, DailyTask $task): bool
    {
        return $this->update($user, $task);
    }

    public function manageAttachments(User $user, DailyTask $task): bool
    {
        return $this->update($user, $task);
    }

    private function isAdmin(User $user): bool
    {
        $role = (string) ($user->role?->role_name ?? '');
        return $role === 'Super Admin' || $role === 'Admin';
    }

    private function isAssignedToUser(User $user, DailyTask $task): bool
    {
        if ($user->employee_id && $task->assigned_employee_id) {
            return (int) $task->assigned_employee_id === (int) $user->employee_id;
        }

        return (int) $task->assigned_to === (int) $user->id;
    }
}
