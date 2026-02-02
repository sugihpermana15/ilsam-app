<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyTaskType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DailyTaskTypeController extends Controller
{
    public function index()
    {
        $types = DailyTaskType::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('pages.admin.master_data.daily_task_types', compact('types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:m_igi_daily_task_types,name'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ]);

        DailyTaskType::query()->create([
            'name' => trim((string) $validated['name']),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => true,
        ]);

        return back()->with('success', 'Task type berhasil ditambahkan.');
    }

    public function update(Request $request, DailyTaskType $type): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:m_igi_daily_task_types,name,' . $type->id],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $type->update([
            'name' => trim((string) $validated['name']),
            'sort_order' => (int) ($validated['sort_order'] ?? $type->sort_order),
            'is_active' => (bool) ($validated['is_active'] ?? $type->is_active),
        ]);

        return back()->with('success', 'Task type berhasil diperbarui.');
    }

    public function toggle(DailyTaskType $type): RedirectResponse
    {
        $type->update(['is_active' => !$type->is_active]);

        return back()->with('success', 'Status task type berhasil diubah.');
    }
}
