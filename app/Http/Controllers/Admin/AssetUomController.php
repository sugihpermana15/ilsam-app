<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetUom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AssetUomController extends Controller
{
  public function index()
  {
    $uoms = AssetUom::query()->orderBy('name')->get();

    return view('pages.admin.master_data.asset_uoms', compact('uoms'));
  }

  public function store(Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:50', 'unique:m_igi_asset_uoms,name'],
    ]);

    AssetUom::query()->create([
      'name' => trim((string) $validated['name']),
      'is_active' => true,
    ]);

    return back()->with('success', 'Satuan asset berhasil ditambahkan.');
  }

  public function update(Request $request, AssetUom $uom): RedirectResponse
  {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:50', 'unique:m_igi_asset_uoms,name,' . $uom->id],
      'is_active' => ['nullable', 'boolean'],
    ]);

    $uom->update([
      'name' => trim((string) $validated['name']),
      'is_active' => (bool) ($validated['is_active'] ?? $uom->is_active),
    ]);

    return back()->with('success', 'Satuan asset berhasil diperbarui.');
  }

  public function toggle(AssetUom $uom): RedirectResponse
  {
    $uom->update(['is_active' => !$uom->is_active]);

    return back()->with('success', 'Status satuan asset berhasil diubah.');
  }
}
