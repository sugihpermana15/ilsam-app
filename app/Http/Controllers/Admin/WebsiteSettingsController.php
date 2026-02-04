<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\WebsiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class WebsiteSettingsController extends Controller
{
    public function edit()
    {
        return view('pages.admin.website.settings', [
            'settings' => WebsiteSettings::all(),
            'locales' => config('app.locales', ['en' => 'English']),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'brand.favicon' => ['nullable', 'string', 'max:500'],
            'brand.favicon_file' => ['nullable', 'file', 'max:5120', 'mimes:png,ico,svg,jpg,jpeg,webp,gif'],
            'brand.logo' => ['nullable', 'string', 'max:500'],
            'brand.logo_file' => ['nullable', 'file', 'max:5120', 'mimes:png,svg,jpg,jpeg,webp,gif'],
            'brand.logo_white' => ['nullable', 'string', 'max:500'],
            'brand.logo_white_file' => ['nullable', 'file', 'max:5120', 'mimes:png,svg,jpg,jpeg,webp,gif'],
            'brand.logo_svg' => ['nullable', 'string', 'max:500'],
            'brand.logo_svg_file' => ['nullable', 'file', 'max:5120', 'mimes:svg,png,jpg,jpeg,webp,gif'],
            'brand.logo_min' => ['nullable', 'string', 'max:500'],
            'brand.logo_min_file' => ['nullable', 'file', 'max:5120', 'mimes:svg,png,jpg,jpeg,webp,gif'],

            'footer.bg_image' => ['nullable', 'string', 'max:500'],
            'footer.bg_image_file' => ['nullable', 'file', 'max:5120', 'mimes:png,svg,jpg,jpeg,webp,gif'],
            'footer.logo' => ['nullable', 'string', 'max:500'],
            'footer.logo_file' => ['nullable', 'file', 'max:5120', 'mimes:png,svg,jpg,jpeg,webp,gif'],

            'products.page.breadcrumb_bg' => ['nullable', 'string', 'max:500'],
            'products.page.breadcrumb_bg_file' => ['nullable', 'file', 'max:5120', 'mimes:png,svg,jpg,jpeg,webp,gif'],

            'technology.page.breadcrumb_bg' => ['nullable', 'string', 'max:500'],
            'technology.page.breadcrumb_bg_file' => ['nullable', 'file', 'max:5120', 'mimes:png,svg,jpg,jpeg,webp,gif'],

            'privacy_policy.page.breadcrumb_bg' => ['nullable', 'string', 'max:500'],
            'privacy_policy.page.breadcrumb_bg_file' => ['nullable', 'file', 'max:5120', 'mimes:png,svg,jpg,jpeg,webp,gif'],

            'contact.phone_display' => ['nullable', 'string', 'max:80'],
            'contact.phone_tel' => ['nullable', 'string', 'max:40'],
            'contact.phone_display_alt' => ['nullable', 'string', 'max:80'],
            'contact.phone_tel_alt' => ['nullable', 'string', 'max:40'],
            'contact.email' => ['nullable', 'email', 'max:200'],
            'contact.map_url' => ['nullable', 'url', 'max:500'],
            'contact.address_text' => ['nullable', 'string', 'max:2000'],

            'top.website_url' => ['nullable', 'url', 'max:500'],
            'top.website_label' => ['nullable', 'string', 'max:120'],

            'offcanvas.website_url' => ['nullable', 'url', 'max:500'],
            'offcanvas.email' => ['nullable', 'email', 'max:200'],
            'offcanvas.location_url' => ['nullable', 'url', 'max:500'],

            'downloads.company_profile_url' => ['nullable', 'url', 'max:500'],

            'seo.home.meta_image' => ['nullable', 'string', 'max:500'],
            'seo.home.meta_image_file' => ['nullable', 'image', 'max:5120'],
            'seo.home.meta_description.en' => ['nullable', 'string', 'max:1000'],
            'seo.home.meta_description.id' => ['nullable', 'string', 'max:1000'],
            'seo.home.meta_description.ko' => ['nullable', 'string', 'max:1000'],

            'seo.about.company.meta_image' => ['nullable', 'string', 'max:500'],
            'seo.about.company.meta_image_file' => ['nullable', 'image', 'max:5120'],
            'seo.about.company.meta_description.en' => ['nullable', 'string', 'max:1000'],
            'seo.about.company.meta_description.id' => ['nullable', 'string', 'max:1000'],
            'seo.about.company.meta_description.ko' => ['nullable', 'string', 'max:1000'],

            'seo.about.ceo.meta_image' => ['nullable', 'string', 'max:500'],
            'seo.about.ceo.meta_image_file' => ['nullable', 'image', 'max:5120'],
            'seo.about.ceo.meta_description.en' => ['nullable', 'string', 'max:1000'],
            'seo.about.ceo.meta_description.id' => ['nullable', 'string', 'max:1000'],
            'seo.about.ceo.meta_description.ko' => ['nullable', 'string', 'max:1000'],

            'seo.about.philosophy.meta_image' => ['nullable', 'string', 'max:500'],
            'seo.about.philosophy.meta_image_file' => ['nullable', 'image', 'max:5120'],
            'seo.about.philosophy.meta_description.en' => ['nullable', 'string', 'max:1000'],
            'seo.about.philosophy.meta_description.id' => ['nullable', 'string', 'max:1000'],
            'seo.about.philosophy.meta_description.ko' => ['nullable', 'string', 'max:1000'],

            'seo.career.meta_image' => ['nullable', 'string', 'max:500'],
            'seo.career.meta_image_file' => ['nullable', 'image', 'max:5120'],
            'seo.career.meta_description.en' => ['nullable', 'string', 'max:1000'],
            'seo.career.meta_description.id' => ['nullable', 'string', 'max:1000'],
            'seo.career.meta_description.ko' => ['nullable', 'string', 'max:1000'],

            'seo.technology.meta_image' => ['nullable', 'string', 'max:500'],
            'seo.technology.meta_image_file' => ['nullable', 'image', 'max:5120'],
            'seo.technology.meta_description.en' => ['nullable', 'string', 'max:1000'],
            'seo.technology.meta_description.id' => ['nullable', 'string', 'max:1000'],
            'seo.technology.meta_description.ko' => ['nullable', 'string', 'max:1000'],

            'seo.technology_certification_status.meta_image' => ['nullable', 'string', 'max:500'],
            'seo.technology_certification_status.meta_image_file' => ['nullable', 'image', 'max:5120'],
            'seo.technology_certification_status.meta_description.en' => ['nullable', 'string', 'max:1000'],
            'seo.technology_certification_status.meta_description.id' => ['nullable', 'string', 'max:1000'],
            'seo.technology_certification_status.meta_description.ko' => ['nullable', 'string', 'max:1000'],

            'about.company.image' => ['nullable', 'string', 'max:500'],
            'about.company.image_file' => ['nullable', 'image', 'max:5120'],

            'about.ceo.portrait_image' => ['nullable', 'string', 'max:500'],
            'about.ceo.portrait_image_file' => ['nullable', 'image', 'max:5120'],

            'about.philosophy.hero_bg' => ['nullable', 'string', 'max:500'],
            'about.philosophy.hero_bg_file' => ['nullable', 'image', 'max:5120'],

            'technology.page.hero_bg' => ['nullable', 'string', 'max:500'],
            'technology.page.hero_bg_file' => ['nullable', 'image', 'max:5120'],
            'technology.page.workflow_bg' => ['nullable', 'string', 'max:500'],
            'technology.page.workflow_bg_file' => ['nullable', 'image', 'max:5120'],

            'home.hero_slides_text' => ['nullable', 'string', 'max:4000'],
            'home.hero_slides_files' => ['nullable', 'array', 'max:10'],
            'home.hero_slides_files.*' => ['image', 'max:5120'],

            'home.decorations.banner_shape_2' => ['nullable', 'string', 'max:500'],
            'home.decorations.banner_shape_2_file' => ['nullable', 'file', 'max:5120', 'mimes:png,svg,jpg,jpeg,webp,gif'],
            'home.decorations.banner_shape_3' => ['nullable', 'string', 'max:500'],
            'home.decorations.banner_shape_3_file' => ['nullable', 'file', 'max:5120', 'mimes:png,svg,jpg,jpeg,webp,gif'],
            'home.decorations.products_shape_2' => ['nullable', 'string', 'max:500'],
            'home.decorations.products_shape_2_file' => ['nullable', 'file', 'max:5120', 'mimes:png,svg,jpg,jpeg,webp,gif'],
            'home.decorations.products_shape_3' => ['nullable', 'string', 'max:500'],
            'home.decorations.products_shape_3_file' => ['nullable', 'file', 'max:5120', 'mimes:png,svg,jpg,jpeg,webp,gif'],
            'home.decorations.products_shape_4' => ['nullable', 'string', 'max:500'],
            'home.decorations.products_shape_4_file' => ['nullable', 'file', 'max:5120', 'mimes:png,svg,jpg,jpeg,webp,gif'],

            'home.sections.about_image' => ['nullable', 'string', 'max:500'],
            'home.sections.about_image_file' => ['nullable', 'file', 'max:5120', 'mimes:png,svg,jpg,jpeg,webp,gif'],
            'home.sections.experience_bg' => ['nullable', 'string', 'max:500'],
            'home.sections.experience_bg_file' => ['nullable', 'file', 'max:5120', 'mimes:png,svg,jpg,jpeg,webp,gif'],

            'home.sections.products_cards.colorants_bg' => ['nullable', 'string', 'max:500'],
            'home.sections.products_cards.colorants_bg_file' => ['nullable', 'file', 'max:5120', 'mimes:png,svg,jpg,jpeg,webp,gif'],
            'home.sections.products_cards.surface_coating_agents_bg' => ['nullable', 'string', 'max:500'],
            'home.sections.products_cards.surface_coating_agents_bg_file' => ['nullable', 'file', 'max:5120', 'mimes:png,svg,jpg,jpeg,webp,gif'],
            'home.sections.products_cards.additive_coating_bg' => ['nullable', 'string', 'max:500'],
            'home.sections.products_cards.additive_coating_bg_file' => ['nullable', 'file', 'max:5120', 'mimes:png,svg,jpg,jpeg,webp,gif'],
            'home.sections.products_cards.pu_resin_bg' => ['nullable', 'string', 'max:500'],
            'home.sections.products_cards.pu_resin_bg_file' => ['nullable', 'file', 'max:5120', 'mimes:png,svg,jpg,jpeg,webp,gif'],
        ]);

        $settings = WebsiteSettings::all();

        // Merge simple nested fields
        foreach (['brand', 'footer', 'products', 'privacy_policy', 'contact', 'top', 'offcanvas', 'downloads', 'seo', 'home', 'technology'] as $root) {
            if (array_key_exists($root, $validated)) {
                $settings[$root] = array_replace_recursive($settings[$root] ?? [], $validated[$root] ?? []);
            }
        }

        if (array_key_exists('about', $validated)) {
            $settings['about'] = array_replace_recursive($settings['about'] ?? [], $validated['about'] ?? []);
        }

        // Hero slides from textarea (one per line)
        $slidesText = (string) ($validated['home']['hero_slides_text'] ?? '');
        if ($slidesText !== '') {
            $lines = preg_split('/\r\n|\r|\n/', $slidesText) ?: [];
            $slides = [];
            foreach ($lines as $line) {
                $line = trim((string) $line);
                if ($line === '') {
                    continue;
                }
                $slides[] = $line;
            }
            $settings['home']['hero_slides'] = $slides;
        }

        // Hero slide uploads (append)
        $uploadedSlides = $request->file('home.hero_slides_files');
        if (is_array($uploadedSlides) && count($uploadedSlides) > 0) {
            $current = data_get($settings, 'home.hero_slides', []);
            if (!is_array($current)) {
                $current = [];
            }

            foreach ($uploadedSlides as $file) {
                if ($file instanceof UploadedFile) {
                    $current[] = $this->storeWebsiteImage($file);
                }
            }

            data_set($settings, 'home.hero_slides', $current);
        }

        // Single image uploads -> overwrite string paths
        $mapUploads = [
            // Brand
            ['file' => 'brand.favicon_file', 'target' => 'brand.favicon'],
            ['file' => 'brand.logo_file', 'target' => 'brand.logo'],
            ['file' => 'brand.logo_white_file', 'target' => 'brand.logo_white'],
            ['file' => 'brand.logo_svg_file', 'target' => 'brand.logo_svg'],
            ['file' => 'brand.logo_min_file', 'target' => 'brand.logo_min'],

            // Footer
            ['file' => 'footer.bg_image_file', 'target' => 'footer.bg_image'],
            ['file' => 'footer.logo_file', 'target' => 'footer.logo'],

            // Breadcrumb backgrounds
            ['file' => 'products.page.breadcrumb_bg_file', 'target' => 'products.page.breadcrumb_bg'],
            ['file' => 'technology.page.breadcrumb_bg_file', 'target' => 'technology.page.breadcrumb_bg'],
            ['file' => 'privacy_policy.page.breadcrumb_bg_file', 'target' => 'privacy_policy.page.breadcrumb_bg'],

            // Home SEO
            ['file' => 'seo.home.meta_image_file', 'target' => 'seo.home.meta_image'],

            // About SEO
            ['file' => 'seo.about.company.meta_image_file', 'target' => 'seo.about.company.meta_image'],
            ['file' => 'seo.about.ceo.meta_image_file', 'target' => 'seo.about.ceo.meta_image'],
            ['file' => 'seo.about.philosophy.meta_image_file', 'target' => 'seo.about.philosophy.meta_image'],

            // Other pages SEO
            ['file' => 'seo.career.meta_image_file', 'target' => 'seo.career.meta_image'],
            ['file' => 'seo.technology.meta_image_file', 'target' => 'seo.technology.meta_image'],
            ['file' => 'seo.technology_certification_status.meta_image_file', 'target' => 'seo.technology_certification_status.meta_image'],

            // About content images
            ['file' => 'about.company.image_file', 'target' => 'about.company.image'],
            ['file' => 'about.ceo.portrait_image_file', 'target' => 'about.ceo.portrait_image'],
            ['file' => 'about.philosophy.hero_bg_file', 'target' => 'about.philosophy.hero_bg'],

            // Technology page images
            ['file' => 'technology.page.hero_bg_file', 'target' => 'technology.page.hero_bg'],
            ['file' => 'technology.page.workflow_bg_file', 'target' => 'technology.page.workflow_bg'],

            // Home section images
            ['file' => 'home.decorations.banner_shape_2_file', 'target' => 'home.decorations.banner_shape_2'],
            ['file' => 'home.decorations.banner_shape_3_file', 'target' => 'home.decorations.banner_shape_3'],
            ['file' => 'home.decorations.products_shape_2_file', 'target' => 'home.decorations.products_shape_2'],
            ['file' => 'home.decorations.products_shape_3_file', 'target' => 'home.decorations.products_shape_3'],
            ['file' => 'home.decorations.products_shape_4_file', 'target' => 'home.decorations.products_shape_4'],
            ['file' => 'home.sections.about_image_file', 'target' => 'home.sections.about_image'],
            ['file' => 'home.sections.experience_bg_file', 'target' => 'home.sections.experience_bg'],
            ['file' => 'home.sections.products_cards.colorants_bg_file', 'target' => 'home.sections.products_cards.colorants_bg'],
            ['file' => 'home.sections.products_cards.surface_coating_agents_bg_file', 'target' => 'home.sections.products_cards.surface_coating_agents_bg'],
            ['file' => 'home.sections.products_cards.additive_coating_bg_file', 'target' => 'home.sections.products_cards.additive_coating_bg'],
            ['file' => 'home.sections.products_cards.pu_resin_bg_file', 'target' => 'home.sections.products_cards.pu_resin_bg'],
        ];
        foreach ($mapUploads as $m) {
            $file = $request->file($m['file']);
            if ($file instanceof UploadedFile) {
                data_set($settings, $m['target'], $this->storeWebsiteImage($file));
            }
        }

        // Ensure hero_slides_text is not persisted
        unset($settings['home']['hero_slides_text']);

        // Ensure helper upload keys are not persisted
        unset($settings['brand']['favicon_file']);
        unset($settings['brand']['logo_file']);
        unset($settings['brand']['logo_white_file']);
        unset($settings['brand']['logo_svg_file']);
        unset($settings['brand']['logo_min_file']);

        unset($settings['footer']['bg_image_file']);
        unset($settings['footer']['logo_file']);

        unset($settings['products']['page']['breadcrumb_bg_file']);
        unset($settings['technology']['page']['breadcrumb_bg_file']);
        unset($settings['privacy_policy']['page']['breadcrumb_bg_file']);

        unset($settings['seo']['home']['meta_image_file']);
        unset($settings['seo']['about']['company']['meta_image_file']);
        unset($settings['seo']['about']['ceo']['meta_image_file']);
        unset($settings['seo']['about']['philosophy']['meta_image_file']);
        unset($settings['seo']['career']['meta_image_file']);
        unset($settings['seo']['technology']['meta_image_file']);
        unset($settings['seo']['technology_certification_status']['meta_image_file']);
        unset($settings['about']['company']['image_file']);
        unset($settings['about']['ceo']['portrait_image_file']);
        unset($settings['about']['philosophy']['hero_bg_file']);
        unset($settings['technology']['page']['hero_bg_file']);
        unset($settings['technology']['page']['workflow_bg_file']);
        unset($settings['home']['sections']['about_image_file']);
        unset($settings['home']['sections']['experience_bg_file']);
        unset($settings['home']['sections']['products_cards']['colorants_bg_file']);
        unset($settings['home']['sections']['products_cards']['surface_coating_agents_bg_file']);
        unset($settings['home']['sections']['products_cards']['additive_coating_bg_file']);
        unset($settings['home']['sections']['products_cards']['pu_resin_bg_file']);
        unset($settings['home']['decorations']['banner_shape_2_file']);
        unset($settings['home']['decorations']['banner_shape_3_file']);
        unset($settings['home']['decorations']['products_shape_2_file']);
        unset($settings['home']['decorations']['products_shape_3_file']);
        unset($settings['home']['decorations']['products_shape_4_file']);
        unset($settings['home']['hero_slides_files']);

        WebsiteSettings::replace($settings);

        return back()->with('success', 'Website settings updated.');
    }

    private function storeWebsiteImage(UploadedFile $file): string
    {
        $path = $file->store('website', 'public');
        return 'storage/' . ltrim($path, '/');
    }
}
