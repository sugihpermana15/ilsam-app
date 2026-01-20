<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UniformUom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UniformUomController extends Controller
{
  public function index()
  {
    $uoms = UniformUom::query()->orderBy('code')->get();

    return view('pages.admin.master_data.uniform_uoms', compact('uoms'));
  }

  public function store(Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'code' => ['required', 'string', 'max:20', 'unique:m_igi_uniform_uoms,code'],
      'name' => ['nullable', 'string', 'max:50'],
    ]);

    $code = trim((string) $validated['code']);

    UniformUom::query()->create([
      'code' => $code,
      'name' => (string) ($validated['name'] ?? $validated['code']),
      'is_active' => true,
    ]);

    return back()->with('success', 'UOM seragam berhasil ditambahkan.');
  }

  public function update(Request $request, UniformUom $uom): RedirectResponse
  {
    $validated = $request->validate([
      'code' => ['required', 'string', 'max:20', 'unique:m_igi_uniform_uoms,code,' . $uom->id],
      'name' => ['nullable', 'string', 'max:50'],
      'is_active' => ['nullable', 'boolean'],
    ]);

    $code = trim((string) $validated['code']);

    $uom->update([
      'code' => $code,
      'name' => (string) ($validated['name'] ?? $validated['code']),
      'is_active' => (bool) ($validated['is_active'] ?? $uom->is_active),
    ]);

    return back()->with('success', 'UOM seragam berhasil diperbarui.');
  }

  public function toggle(UniformUom $uom): RedirectResponse
  {
    $uom->update(['is_active' => !$uom->is_active]);

    return back()->with('success', 'Status UOM berhasil diubah.');
  }
}
