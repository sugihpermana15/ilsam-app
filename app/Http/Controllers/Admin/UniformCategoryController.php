<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UniformCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UniformCategoryController extends Controller
{
  public function index()
  {
    $categories = UniformCategory::query()->orderBy('name')->get();

    return view('pages.admin.master_data.uniform_categories', compact('categories'));
  }

  public function store(Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:100', 'unique:m_igi_uniform_categories,name'],
    ]);

    $name = trim((string) $validated['name']);

    UniformCategory::query()->create([
      'name' => $name,
      'is_active' => true,
    ]);

    return back()->with('success', 'Kategori seragam berhasil ditambahkan.');
  }

  public function update(Request $request, UniformCategory $category): RedirectResponse
  {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:100', 'unique:m_igi_uniform_categories,name,' . $category->id],
      'is_active' => ['nullable', 'boolean'],
    ]);

    $name = trim((string) $validated['name']);

    $category->update([
      'name' => $name,
      'is_active' => (bool) ($validated['is_active'] ?? $category->is_active),
    ]);

    return back()->with('success', 'Kategori seragam berhasil diperbarui.');
  }

  public function toggle(UniformCategory $category): RedirectResponse
  {
    $category->update(['is_active' => !$category->is_active]);

    return back()->with('success', 'Status kategori berhasil diubah.');
  }
}
