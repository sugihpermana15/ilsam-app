<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UniformColor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UniformColorController extends Controller
{
  public function index()
  {
    $colors = UniformColor::query()->orderBy('name')->get();

    return view('pages.admin.master_data.uniform_colors', compact('colors'));
  }

  public function store(Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:50', 'unique:m_igi_uniform_colors,name'],
    ]);

    $name = trim((string) $validated['name']);

    UniformColor::query()->create([
      'name' => $name,
      'is_active' => true,
    ]);

    return back()->with('success', 'Warna seragam berhasil ditambahkan.');
  }

  public function update(Request $request, UniformColor $color): RedirectResponse
  {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:50', 'unique:m_igi_uniform_colors,name,' . $color->id],
      'is_active' => ['nullable', 'boolean'],
    ]);

    $name = trim((string) $validated['name']);

    $color->update([
      'name' => $name,
      'is_active' => (bool) ($validated['is_active'] ?? $color->is_active),
    ]);

    return back()->with('success', 'Warna seragam berhasil diperbarui.');
  }

  public function toggle(UniformColor $color): RedirectResponse
  {
    $color->update(['is_active' => !$color->is_active]);

    return back()->with('success', 'Status warna berhasil diubah.');
  }
}
