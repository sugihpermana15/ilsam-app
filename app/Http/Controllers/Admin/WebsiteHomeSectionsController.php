<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\WebsiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WebsiteHomeSectionsController extends Controller
{
    public function edit()
    {
        $settings = WebsiteSettings::all();
        $companies = data_get($settings, 'home.text_slider_companies', []);
        if (!is_array($companies)) {
            $companies = [];
        }

        return view('pages.admin.website.home_sections', [
            'settings' => $settings,
            'companiesText' => implode("\n", array_map(fn($v) => (string) $v, $companies)),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'home.text_slider_companies_text' => ['nullable', 'string', 'max:8000'],
        ]);

        $settings = WebsiteSettings::all();

        $text = (string) data_get($validated, 'home.text_slider_companies_text', '');
        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];
        $companies = [];
        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }
            $companies[] = $line;
        }

        data_set($settings, 'home.text_slider_companies', $companies);

        // do not persist the textarea helper
        unset($settings['home']['text_slider_companies_text']);

        WebsiteSettings::replace($settings);

        return back()->with('success', 'Home sections updated.');
    }
}
