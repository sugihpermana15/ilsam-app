<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\WebsiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class WebsiteContactPageController extends Controller
{
    public function edit()
    {
        return view('pages.admin.website.contact_page', [
            'settings' => WebsiteSettings::all(),
            'locales' => config('app.locales', ['en' => 'English']),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contact.form_recipient_email' => ['nullable', 'email', 'max:200'],
            'contact.map_embed_src' => ['nullable', 'url', 'max:2000'],
            'contact.opening_hours' => ['nullable', 'string', 'max:200'],

            'contact.page.breadcrumb_bg' => ['nullable', 'string', 'max:500'],
            'contact.page.breadcrumb_bg_file' => ['nullable', 'image', 'max:5120'],
            'contact.page.lets_talk_bg' => ['nullable', 'string', 'max:500'],
            'contact.page.lets_talk_bg_file' => ['nullable', 'image', 'max:5120'],

            'seo.contact.meta_image' => ['nullable', 'string', 'max:500'],
            'seo.contact.meta_image_file' => ['nullable', 'image', 'max:5120'],
            'seo.contact.meta_description.en' => ['nullable', 'string', 'max:1000'],
            'seo.contact.meta_description.id' => ['nullable', 'string', 'max:1000'],
            'seo.contact.meta_description.ko' => ['nullable', 'string', 'max:1000'],
        ]);

        $settings = WebsiteSettings::all();

        // Merge simple nested fields
        foreach (['contact', 'seo'] as $root) {
            if (array_key_exists($root, $validated)) {
                $settings[$root] = array_replace_recursive($settings[$root] ?? [], $validated[$root] ?? []);
            }
        }

        $uploads = [
            ['file' => 'contact.page.breadcrumb_bg_file', 'target' => 'contact.page.breadcrumb_bg'],
            ['file' => 'contact.page.lets_talk_bg_file', 'target' => 'contact.page.lets_talk_bg'],
            ['file' => 'seo.contact.meta_image_file', 'target' => 'seo.contact.meta_image'],
        ];
        foreach ($uploads as $m) {
            $file = $request->file($m['file']);
            if ($file instanceof UploadedFile) {
                data_set($settings, $m['target'], $this->storeWebsiteImage($file));
            }
        }

        unset($settings['contact']['page']['breadcrumb_bg_file']);
        unset($settings['contact']['page']['lets_talk_bg_file']);
        unset($settings['seo']['contact']['meta_image_file']);

        WebsiteSettings::replace($settings);

        return back()->with('success', 'Contact page settings updated.');
    }

    private function storeWebsiteImage(UploadedFile $file): string
    {
        $path = $file->store('website', 'public');
        return 'storage/' . ltrim($path, '/');
    }
}
