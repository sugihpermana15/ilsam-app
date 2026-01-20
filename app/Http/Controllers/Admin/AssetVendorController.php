<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetVendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AssetVendorController extends Controller
{
  public function index()
  {
    $vendors = AssetVendor::query()->orderBy('name')->get();

    return view('pages.admin.master_data.asset_vendors', compact('vendors'));
  }

  public function store(Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:100', 'unique:m_igi_asset_vendors,name'],
    ]);

    AssetVendor::query()->create([
      'name' => trim((string) $validated['name']),
      'is_active' => true,
    ]);

    return back()->with('success', 'Vendor/Supplier berhasil ditambahkan.');
  }

  public function update(Request $request, AssetVendor $vendor): RedirectResponse
  {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:100', 'unique:m_igi_asset_vendors,name,' . $vendor->id],
      'is_active' => ['nullable', 'boolean'],
    ]);

    $vendor->update([
      'name' => trim((string) $validated['name']),
      'is_active' => (bool) ($validated['is_active'] ?? $vendor->is_active),
    ]);

    return back()->with('success', 'Vendor/Supplier berhasil diperbarui.');
  }

  public function toggle(AssetVendor $vendor): RedirectResponse
  {
    $vendor->update(['is_active' => !$vendor->is_active]);

    return back()->with('success', 'Status vendor berhasil diubah.');
  }
}
