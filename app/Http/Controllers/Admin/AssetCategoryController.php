<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
  public function index()
  {
    $categories = AssetCategory::query()->orderBy('code')->get();

    return view('pages.admin.master_data.asset_categories', compact('categories'));
  }

  public function store(Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'code' => ['required', 'string', 'max:50', 'unique:m_igi_asset_categories,code'],
      'name' => ['required', 'string', 'max:100'],
      'asset_code_prefix' => ['required', 'string', 'max:10'],
    ]);

    AssetCategory::query()->create([
      'code' => trim((string) $validated['code']),
      'name' => trim((string) $validated['name']),
      'asset_code_prefix' => strtoupper(trim((string) $validated['asset_code_prefix'])),
      'is_active' => true,
    ]);

    return back()->with('success', 'Kategori asset berhasil ditambahkan.');
  }

  public function update(Request $request, AssetCategory $category): RedirectResponse
  {
    $validated = $request->validate([
      'code' => ['required', 'string', 'max:50', 'unique:m_igi_asset_categories,code,' . $category->id],
      'name' => ['required', 'string', 'max:100'],
      'asset_code_prefix' => ['required', 'string', 'max:10'],
      'is_active' => ['nullable', 'boolean'],
    ]);

    $category->update([
      'code' => trim((string) $validated['code']),
      'name' => trim((string) $validated['name']),
      'asset_code_prefix' => strtoupper(trim((string) $validated['asset_code_prefix'])),
      'is_active' => (bool) ($validated['is_active'] ?? $category->is_active),
    ]);

    return back()->with('success', 'Kategori asset berhasil diperbarui.');
  }

  public function toggle(AssetCategory $category): RedirectResponse
  {
    $category->update(['is_active' => !$category->is_active]);

    return back()->with('success', 'Status kategori asset berhasil diubah.');
  }
}
