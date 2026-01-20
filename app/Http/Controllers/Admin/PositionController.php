<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PositionController extends Controller
{
  public function index()
  {
    $positions = Position::query()->orderBy('name')->get();

    return view('pages.admin.employee.positions', compact('positions'));
  }

  public function store(Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:255', 'unique:m_igi_positions,name'],
      'level_code' => ['required', 'string', 'max:10'],
    ]);

    Position::query()->create($validated);

    return back()->with('success', 'Position created successfully.');
  }

  public function update(Request $request, Position $position): RedirectResponse
  {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:255', 'unique:m_igi_positions,name,' . $position->id],
      'level_code' => ['required', 'string', 'max:10'],
    ]);

    $position->update($validated);

    return back()->with('success', 'Position updated successfully.');
  }

  public function destroy(Position $position): RedirectResponse
  {
    try {
      $position->delete();

      return back()->with('success', 'Position deleted successfully.');
    } catch (\Throwable $e) {
      return back()->with('error', 'Failed to delete position. It may be used by employees.');
    }
  }
}
