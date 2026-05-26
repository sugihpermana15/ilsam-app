<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Colorant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ColorantController extends Controller
{
    public function index()
    {
        $colorants = Colorant::latest()->get();
        return view('pages.admin.products.index', compact('colorants'));
    }

    public function create()
    {
        return view('pages.admin.products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'bg_color' => 'nullable|string|max:20',
            'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        if ($request->hasFile('image1')) {
            $data['image1'] = $request->file('image1')->store('colorants', 'public');
        }
        if ($request->hasFile('image2')) {
            $data['image2'] = $request->file('image2')->store('colorants', 'public');
        }

        Colorant::create($data);
        return redirect()->route('admin.colorants.index')->with('success', 'Colorant created successfully.');
    }

    public function edit(Colorant $colorant)
    {
        return view('pages.admin.products.edit', compact('colorant'));
    }

    public function update(Request $request, Colorant $colorant)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'bg_color' => 'nullable|string|max:20',
            'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        if ($request->hasFile('image1')) {
            if ($colorant->image1) Storage::disk('public')->delete($colorant->image1);
            $data['image1'] = $request->file('image1')->store('colorants', 'public');
        }
        if ($request->hasFile('image2')) {
            if ($colorant->image2) Storage::disk('public')->delete($colorant->image2);
            $data['image2'] = $request->file('image2')->store('colorants', 'public');
        }

        $colorant->update($data);
        return redirect()->route('admin.colorants.index')->with('success', 'Colorant updated successfully.');
    }

    public function destroy(Colorant $colorant)
    {
        if ($colorant->image1) Storage::disk('public')->delete($colorant->image1);
        if ($colorant->image2) Storage::disk('public')->delete($colorant->image2);
        $colorant->delete();
        return redirect()->route('admin.colorants.index')->with('success', 'Colorant deleted successfully.');
    }
}
