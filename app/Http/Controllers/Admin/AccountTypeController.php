<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountTypeController extends Controller
{
    public function index()
    {
        $types = AccountType::query()->orderBy('name')->get();

        return view('pages.admin.master_data.account_types', compact('types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:m_igi_account_types,name'],
        ]);

        AccountType::query()->create([
            'name' => trim((string) $validated['name']),
            'is_active' => true,
        ]);

        return back()->with('success', 'Kategori akun berhasil ditambahkan.');
    }

    public function update(Request $request, AccountType $type): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:m_igi_account_types,name,' . $type->id],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $type->update([
            'name' => trim((string) $validated['name']),
            'is_active' => (bool) ($validated['is_active'] ?? $type->is_active),
        ]);

        return back()->with('success', 'Kategori akun berhasil diperbarui.');
    }

    public function toggle(AccountType $type): RedirectResponse
    {
        $type->update(['is_active' => !$type->is_active]);

        return back()->with('success', 'Status kategori akun berhasil diubah.');
    }
}
