<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UniformItemName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UniformItemNameController extends Controller
{
  public function index()
  {
    $itemNames = UniformItemName::query()->orderBy('name')->get();

    return view('pages.admin.master_data.uniform_item_names', compact('itemNames'));
  }

  public function store(Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:255', 'unique:m_igi_uniform_item_names,name'],
    ]);

    $name = trim((string) $validated['name']);

    UniformItemName::query()->create([
      'name' => $name,
      'is_active' => true,
    ]);

    return back()->with('success', 'Nama item seragam berhasil ditambahkan.');
  }

  public function update(Request $request, UniformItemName $itemName): RedirectResponse
  {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:255', 'unique:m_igi_uniform_item_names,name,' . $itemName->id],
      'is_active' => ['nullable', 'boolean'],
    ]);

    $name = trim((string) $validated['name']);

    $itemName->update([
      'name' => $name,
      'is_active' => (bool) ($validated['is_active'] ?? $itemName->is_active),
    ]);

    return back()->with('success', 'Nama item seragam berhasil diperbarui.');
  }

  public function toggle(UniformItemName $itemName): RedirectResponse
  {
    $itemName->update(['is_active' => !$itemName->is_active]);

    return back()->with('success', 'Status nama item berhasil diubah.');
  }
}
