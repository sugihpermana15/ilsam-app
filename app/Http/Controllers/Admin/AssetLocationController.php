<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AssetLocationController extends Controller
{
  public function index()
  {
    $locations = AssetLocation::query()->orderBy('name')->get();

    return view('pages.admin.master_data.asset_locations', compact('locations'));
  }

  public function store(Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:100', 'unique:m_igi_asset_locations,name'],
      'asset_code_prefix' => ['required', 'string', 'max:5'],
    ]);

    AssetLocation::query()->create([
      'name' => trim((string) $validated['name']),
      'asset_code_prefix' => trim((string) $validated['asset_code_prefix']),
      'is_active' => true,
    ]);

    return back()->with('success', 'Lokasi asset berhasil ditambahkan.');
  }

  public function update(Request $request, AssetLocation $location): RedirectResponse
  {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:100', 'unique:m_igi_asset_locations,name,' . $location->id],
      'asset_code_prefix' => ['required', 'string', 'max:5'],
      'is_active' => ['nullable', 'boolean'],
    ]);

    $location->update([
      'name' => trim((string) $validated['name']),
      'asset_code_prefix' => trim((string) $validated['asset_code_prefix']),
      'is_active' => (bool) ($validated['is_active'] ?? $location->is_active),
    ]);

    return back()->with('success', 'Lokasi asset berhasil diperbarui.');
  }

  public function toggle(AssetLocation $location): RedirectResponse
  {
    $location->update(['is_active' => !$location->is_active]);

    return back()->with('success', 'Status lokasi asset berhasil diubah.');
  }
}
