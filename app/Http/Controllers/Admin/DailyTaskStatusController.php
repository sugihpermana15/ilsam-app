<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyTaskStatusMaster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DailyTaskStatusController extends Controller
{
    public function index()
    {
        $statuses = DailyTaskStatusMaster::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('pages.admin.master_data.daily_task_statuses', compact('statuses'));
    }

    public function update(Request $request, DailyTaskStatusMaster $status): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:40', 'unique:m_igi_daily_task_statuses,name,' . $status->id . ',id'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $status->update([
            'name' => trim((string) $validated['name']),
            'sort_order' => (int) ($validated['sort_order'] ?? $status->sort_order),
            'is_active' => (bool) ($validated['is_active'] ?? $status->is_active),
        ]);

        return back()->with('success', 'Status berhasil diperbarui.');
    }

    public function toggle(DailyTaskStatusMaster $status): RedirectResponse
    {
        $status->update(['is_active' => !$status->is_active]);

        return back()->with('success', 'Aktif/nonaktif status berhasil diubah.');
    }
}
