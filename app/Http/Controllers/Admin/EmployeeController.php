<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeAuditLog;
use App\Models\EmployeeSequence;
use App\Models\Position;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
  private function writeAuditLog(
    ?int $employeeId,
    string $action,
    ?array $oldValues,
    ?array $newValues
  ): void {
    try {
      $user = Auth::user();

      EmployeeAuditLog::query()->create([
        'employee_id' => $employeeId,
        'action' => $action,
        'performed_by' => $user?->id,
        'performed_by_name' => $user?->name,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'old_values' => $oldValues,
        'new_values' => $newValues,
        'created_at' => now(),
      ]);
    } catch (\Throwable $ignored) {
    }
  }

  private function auditPayload(Employee $employee): array
  {
    return [
      'id' => $employee->id,
      'sequence_number' => $employee->sequence_number,
      'no_id' => $employee->no_id,
      'name' => $employee->name,
      'gender' => $employee->gender,
      'birth_date' => optional($employee->birth_date)->format('Y-m-d'),
      'address' => $employee->address,
      'phone' => $employee->phone,
      'email' => $employee->email,
      'department_id' => $employee->department_id,
      'position_id' => $employee->position_id,
      'employment_status' => $employee->employment_status,
      'join_date' => optional($employee->join_date)->format('Y-m-d'),
      'photo' => $employee->photo,
    ];
  }

  private function diffPayload(array $before, array $after): array
  {
    $changed = [];
    foreach ($after as $key => $value) {
      $beforeValue = $before[$key] ?? null;
      if ($beforeValue !== $value) {
        $changed[$key] = [
          'from' => $beforeValue,
          'to' => $value,
        ];
      }
    }
    return $changed;
  }

  private function storeEmployeePhoto($file): string
  {
    $dir = public_path('assets/img/karyawan');
    if (!is_dir($dir)) {
      @mkdir($dir, 0755, true);
    }

    $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
    $filename = 'karyawan_' . Str::uuid() . '.' . $ext;
    $file->move($dir, $filename);

    return 'assets/img/karyawan/' . $filename;
  }

  private function deleteEmployeePhoto(?string $photoPath): void
  {
    if (!$photoPath) {
      return;
    }

    try {
      if (Str::startsWith($photoPath, 'assets/')) {
        $fullPath = public_path($photoPath);
        if (is_file($fullPath)) {
          @unlink($fullPath);
        }
        return;
      }

      Storage::disk('public')->delete($photoPath);
    } catch (\Throwable $ignored) {
    }
  }

  public function index()
  {
    $employees = Employee::query()
      ->with(['department', 'position'])
      ->orderByDesc('id')
      ->get();

    $departments = Department::query()->orderBy('name')->get();
    $positions = Position::query()->orderBy('name')->get();

    return view('pages.admin.employee.employee_master', compact('employees', 'departments', 'positions'));
  }

  public function deleted()
  {
    $employees = Employee::onlyTrashed()
      ->with(['department', 'position'])
      ->orderByDesc('deleted_at')
      ->get();

    return view('pages.admin.employee.employee_deleted', compact('employees'));
  }

  public function audit()
  {
    $logs = EmployeeAuditLog::query()
      ->with(['employee', 'performedBy'])
      ->orderByDesc('created_at')
      ->limit(500)
      ->get();

    return view('pages.admin.employee.employee_audit', compact('logs'));
  }

  public function store(StoreEmployeeRequest $request): RedirectResponse
  {
    $validated = $request->validated();

    $photoPath = null;
    if ($request->hasFile('photo')) {
      $photoPath = $this->storeEmployeePhoto($request->file('photo'));
    }

    try {
      DB::transaction(function () use ($validated, $photoPath) {
        // Lock sequence row (prevents race condition / duplicates).
        $sequence = EmployeeSequence::query()
          ->where('name', 'employee')
          ->lockForUpdate()
          ->first();

        if (!$sequence) {
          EmployeeSequence::query()->create(['name' => 'employee', 'last_value' => 0]);
          $sequence = EmployeeSequence::query()
            ->where('name', 'employee')
            ->lockForUpdate()
            ->firstOrFail();
        }

        $nextSequence = (int) $sequence->last_value + 1;

        $position = Position::query()->findOrFail($validated['position_id']);
        $joinDate = Carbon::parse($validated['join_date']);

        $level = Str::upper($position->level_code);
        $yearCode = $joinDate->format('y');
        $suffix = str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);
        $noId = "IGI-{$level}-{$yearCode}-{$suffix}";

        $employee = Employee::query()->create([
          'sequence_number' => $nextSequence,
          'no_id' => $noId,
          'name' => $validated['name'],
          'gender' => $validated['gender'],
          'birth_date' => $validated['birth_date'],
          'address' => $validated['address'],
          'phone' => $validated['phone'],
          'email' => $validated['email'],
          'department_id' => $validated['department_id'],
          'position_id' => $validated['position_id'],
          'employment_status' => $validated['employment_status'] ?? null,
          'join_date' => $validated['join_date'],
          'photo' => $photoPath,
        ]);

        $sequence->update(['last_value' => $nextSequence]);

        $this->writeAuditLog($employee->id, 'create', null, $this->auditPayload($employee));
      }, 3);

      return back()->with('success', 'Employee created successfully.');
    } catch (\Throwable $e) {
      $this->deleteEmployeePhoto($photoPath);

      return back()->withInput()->with('error', 'Failed to create employee.');
    }
  }

  public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
  {
    $validated = $request->validated();

    $oldPhotoPath = $employee->photo;
    $before = $this->auditPayload($employee);

    $newPhotoPath = null;
    if ($request->hasFile('photo')) {
      $newPhotoPath = $this->storeEmployeePhoto($request->file('photo'));
    }

    // Re-generate No ID to follow latest Position (LEVEL) + Join Date year.
    // Suffix remains the immutable global sequence number.
    $position = Position::query()->findOrFail($validated['position_id']);
    $joinDate = Carbon::parse($validated['join_date']);
    $level = Str::upper($position->level_code ?: 'NA');
    $yearCode = $joinDate->format('y');
    $suffix = str_pad((string) $employee->sequence_number, 4, '0', STR_PAD_LEFT);
    $newNoId = "IGI-{$level}-{$yearCode}-{$suffix}";

    try {
      DB::transaction(function () use ($employee, $validated, $newPhotoPath, $before, $newNoId) {
        $updates = [
          'name' => $validated['name'],
          'no_id' => $newNoId,
          'gender' => $validated['gender'],
          'birth_date' => $validated['birth_date'],
          'address' => $validated['address'],
          'phone' => $validated['phone'],
          'email' => $validated['email'],
          'department_id' => $validated['department_id'],
          'position_id' => $validated['position_id'],
          'join_date' => $validated['join_date'],
        ];

        if (array_key_exists('employment_status', $validated)) {
          $updates['employment_status'] = $validated['employment_status'];
        }

        $removePhoto = !empty($validated['remove_photo']);
        if ($newPhotoPath) {
          $updates['photo'] = $newPhotoPath;
        } elseif ($removePhoto) {
          $updates['photo'] = null;
        }

        $employee->update($updates);

        $after = $this->auditPayload($employee->fresh());
        $diff = $this->diffPayload($before, $after);
        if (!empty($diff)) {
          $this->writeAuditLog($employee->id, 'update', $before, $after);
        }
      }, 3);

      if ($newPhotoPath) {
        $this->deleteEmployeePhoto($oldPhotoPath);
      } elseif (!empty($validated['remove_photo'])) {
        $this->deleteEmployeePhoto($oldPhotoPath);
      }

      return back()->with('success', 'Employee updated successfully.');
    } catch (\Throwable $e) {
      if ($newPhotoPath) {
        $this->deleteEmployeePhoto($newPhotoPath);
      }

      return back()->withInput()->with('error', 'Failed to update employee.');
    }
  }

  public function destroy(Employee $employee): RedirectResponse
  {
    try {
      DB::transaction(function () use ($employee) {
        $before = $this->auditPayload($employee);
        $employee->delete();
        $this->writeAuditLog($employee->id, 'delete', $before, null);
      }, 3);

      return back()->with('success', 'Employee deleted successfully.');
    } catch (\Throwable $e) {
      return back()->with('error', 'Failed to delete employee.');
    }
  }

  public function restore(int $id): RedirectResponse
  {
    try {
      DB::transaction(function () use ($id) {
        $employee = Employee::withTrashed()->findOrFail($id);
        $before = $this->auditPayload($employee);
        $employee->restore();
        $after = $this->auditPayload($employee->fresh());
        $this->writeAuditLog($employee->id, 'restore', $before, $after);
      }, 3);

      return back()->with('success', 'Employee restored successfully.');
    } catch (\Throwable $e) {
      return back()->with('error', 'Failed to restore employee.');
    }
  }
}
