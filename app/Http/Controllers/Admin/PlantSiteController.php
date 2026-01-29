<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlantSiteController extends Controller
{
    public function index()
    {
        $locations = Location::query()
            ->orderBy('plant_site')
            ->orderBy('name')
            ->orderBy('building')
            ->orderBy('floor')
            ->orderBy('room_rack')
            ->get();

        return view('pages.admin.master_data.plant_sites', compact('locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plant_site' => ['required', 'string', 'max:100'],
            'name' => ['nullable', 'string', 'max:100'],
            'building' => ['nullable', 'string', 'max:100'],
            'floor' => ['nullable', 'string', 'max:100'],
            'room_rack' => ['nullable', 'string', 'max:100'],
        ]);

        Location::query()->create([
            'plant_site' => trim((string) $validated['plant_site']),
            'name' => isset($validated['name']) ? trim((string) $validated['name']) : null,
            'building' => isset($validated['building']) ? trim((string) $validated['building']) : null,
            'floor' => isset($validated['floor']) ? trim((string) $validated['floor']) : null,
            'room_rack' => isset($validated['room_rack']) ? trim((string) $validated['room_rack']) : null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Plant/Site berhasil ditambahkan.');
    }

    public function update(Request $request, Location $site): RedirectResponse
    {
        $validated = $request->validate([
            'plant_site' => ['required', 'string', 'max:100'],
            'name' => ['nullable', 'string', 'max:100'],
            'building' => ['nullable', 'string', 'max:100'],
            'floor' => ['nullable', 'string', 'max:100'],
            'room_rack' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $site->update([
            'plant_site' => trim((string) $validated['plant_site']),
            'name' => isset($validated['name']) ? trim((string) $validated['name']) : null,
            'building' => isset($validated['building']) ? trim((string) $validated['building']) : null,
            'floor' => isset($validated['floor']) ? trim((string) $validated['floor']) : null,
            'room_rack' => isset($validated['room_rack']) ? trim((string) $validated['room_rack']) : null,
            'is_active' => (bool) ($validated['is_active'] ?? $site->is_active),
        ]);

        return back()->with('success', 'Plant/Site berhasil diperbarui.');
    }

    public function toggle(Location $site): RedirectResponse
    {
        $site->update(['is_active' => !$site->is_active]);

        return back()->with('success', 'Status Plant/Site berhasil diubah.');
    }
}
