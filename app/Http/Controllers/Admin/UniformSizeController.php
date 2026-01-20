<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UniformSize;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UniformSizeController extends Controller
{
  public function index()
  {
    $sizes = UniformSize::query()->orderBy('code')->get();

    return view('pages.admin.master_data.uniform_sizes', compact('sizes'));
  }

  public function store(Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'code' => ['required', 'string', 'max:50', 'unique:m_igi_uniform_sizes,code'],
      'name' => ['nullable', 'string', 'max:100'],
    ]);

    $code = strtoupper(trim((string) $validated['code']));
    $code = preg_replace('/\s+/', ' ', $code);

    UniformSize::query()->create([
      'code' => $code,
      'name' => (string) ($validated['name'] ?? $validated['code']),
      'is_active' => true,
    ]);

    return back()->with('success', 'Ukuran seragam berhasil ditambahkan.');
  }

  public function update(Request $request, UniformSize $size): RedirectResponse
  {
    $validated = $request->validate([
      'code' => ['required', 'string', 'max:50', 'unique:m_igi_uniform_sizes,code,' . $size->id],
      'name' => ['nullable', 'string', 'max:100'],
      'is_active' => ['nullable', 'boolean'],
    ]);

    $code = strtoupper(trim((string) $validated['code']));
    $code = preg_replace('/\s+/', ' ', $code);

    $size->update([
      'code' => $code,
      'name' => (string) ($validated['name'] ?? $validated['code']),
      'is_active' => (bool) ($validated['is_active'] ?? $size->is_active),
    ]);

    return back()->with('success', 'Ukuran seragam berhasil diperbarui.');
  }

  public function toggle(UniformSize $size): RedirectResponse
  {
    $size->update(['is_active' => !$size->is_active]);

    return back()->with('success', 'Status ukuran berhasil diubah.');
  }
}
