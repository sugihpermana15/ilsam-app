<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
  public function index()
  {
    $departments = Department::query()->orderBy('name')->get();

    return view('pages.admin.employee.departments', compact('departments'));
  }

  public function store(Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:255', 'unique:m_igi_departments,name'],
    ]);

    Department::query()->create($validated);

    return back()->with('success', 'Department created successfully.');
  }

  public function update(Request $request, Department $department): RedirectResponse
  {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:255', 'unique:m_igi_departments,name,' . $department->id],
    ]);

    $department->update($validated);

    return back()->with('success', 'Department updated successfully.');
  }

  public function destroy(Department $department): RedirectResponse
  {
    try {
      $department->delete();

      return back()->with('success', 'Department deleted successfully.');
    } catch (\Throwable $e) {
      return back()->with('error', 'Failed to delete department. It may be used by employees.');
    }
  }
}
