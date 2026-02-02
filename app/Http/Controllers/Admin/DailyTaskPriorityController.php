<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyTaskPriority;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DailyTaskPriorityController extends Controller
{
    public function index()
    {
        $priorities = DailyTaskPriority::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('pages.admin.master_data.daily_task_priorities', compact('priorities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:40', 'unique:m_igi_daily_task_priorities,name'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ]);

        DailyTaskPriority::query()->create([
            'name' => trim((string) $validated['name']),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => true,
        ]);

        return back()->with('success', 'Priority berhasil ditambahkan.');
    }

    public function update(Request $request, DailyTaskPriority $priority): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:40', 'unique:m_igi_daily_task_priorities,name,' . $priority->id],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $priority->update([
            'name' => trim((string) $validated['name']),
            'sort_order' => (int) ($validated['sort_order'] ?? $priority->sort_order),
            'is_active' => (bool) ($validated['is_active'] ?? $priority->is_active),
        ]);

        return back()->with('success', 'Priority berhasil diperbarui.');
    }

    public function toggle(DailyTaskPriority $priority): RedirectResponse
    {
        $priority->update(['is_active' => !$priority->is_active]);

        return back()->with('success', 'Status priority berhasil diubah.');
    }
}
