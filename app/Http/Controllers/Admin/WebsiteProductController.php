<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\WebsiteProducts;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WebsiteProductController extends Controller
{
    /**
     * Lock slugs to existing public routes (so preview links always work).
     */
    private const ALLOWED_SLUGS = [
        'colorants',
        'surface-coating-agents',
        'additive-coating',
        'pu-resin',
    ];

    public function index()
    {
        $products = collect(WebsiteProducts::all());

        return view('pages.admin.website.products_index', [
            'products' => $products,
            'allowedSlugs' => self::ALLOWED_SLUGS,
        ]);
    }

    public function create()
    {
        return view('pages.admin.website.products_form', [
            'mode' => 'create',
            'product' => $this->blankProduct(),
            'allowedSlugs' => self::ALLOWED_SLUGS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);

        $product = $this->fromForm($validated);
        $product['id'] = (string) Str::uuid();

        WebsiteProducts::upsert($product);

        return redirect()->route('admin.website_products.index')->with('success', 'Website product created.');
    }

    public function edit(string $id)
    {
        $product = WebsiteProducts::findById($id);
        if (!$product) {
            return redirect()->route('admin.website_products.index')->with('error', 'Product not found.');
        }

        return view('pages.admin.website.products_form', [
            'mode' => 'edit',
            'product' => $this->toForm($product),
            'allowedSlugs' => self::ALLOWED_SLUGS,
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $existing = WebsiteProducts::findById($id);
        if (!$existing) {
            return redirect()->route('admin.website_products.index')->with('error', 'Product not found.');
        }

        $validated = $this->validateProduct($request, $id);
        $product = $this->fromForm($validated);
        $product['id'] = (string) $id;

        WebsiteProducts::upsert($product);

        return redirect()->route('admin.website_products.index')->with('success', 'Website product updated.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $ok = WebsiteProducts::delete($id);
        return redirect()->route('admin.website_products.index')->with($ok ? 'success' : 'error', $ok ? 'Website product deleted.' : 'Product not found.');
    }

    private function validateProduct(Request $request, ?string $editingId = null): array
    {
        $rules = [
            'slug' => ['required', 'string', 'in:' . implode(',', self::ALLOWED_SLUGS)],
            'title' => ['required', 'string', 'max:160'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'intro' => ['nullable', 'string'],
            'heroImage' => ['nullable', 'string', 'max:500'],
            'heroImage_file' => ['nullable', 'image', 'max:5120'],

            'seoDescription' => ['nullable', 'string', 'max:1000'],
            'seoImage' => ['nullable', 'string', 'max:500'],
            'seoImage_file' => ['nullable', 'image', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable'],

            // Multiline admin-form fields
            'lines_text' => ['nullable', 'string'],
            'applications_intro' => ['nullable', 'string', 'max:255'],
            'applications_text' => ['nullable', 'string'],
            'capabilities_text' => ['nullable', 'string'],
            'specs_text' => ['nullable', 'string'],

            // CTA
            'cta_primary_text' => ['nullable', 'string', 'max:80'],
            'cta_primary_url' => ['nullable', 'string', 'max:500'],
            'cta_heading' => ['nullable', 'string', 'max:160'],
            'cta_text' => ['nullable', 'string'],
        ];

        $validated = $request->validate($rules);

        // Upload handling (store to public disk, persist as "storage/..." for public usage)
        if ($request->hasFile('heroImage_file')) {
            $path = $request->file('heroImage_file')->store('website/products', 'public');
            $validated['heroImage'] = 'storage/' . ltrim((string) $path, '/');
        }
        if ($request->hasFile('seoImage_file')) {
            $path = $request->file('seoImage_file')->store('website/products', 'public');
            $validated['seoImage'] = 'storage/' . ltrim((string) $path, '/');
        }

        unset($validated['heroImage_file'], $validated['seoImage_file']);

        // Slug uniqueness (across JSON store)
        $slug = (string) ($validated['slug'] ?? '');
        foreach (WebsiteProducts::all() as $row) {
            if ((string) ($row['slug'] ?? '') !== $slug) {
                continue;
            }
            if ($editingId && (string) ($row['id'] ?? '') === (string) $editingId) {
                continue;
            }
            throw ValidationException::withMessages([
                'slug' => 'Slug already used by another product.',
            ]);
        }

        $validated['is_active'] = (bool) ((int) ($validated['is_active'] ?? 0));

        return $validated;
    }

    /**
     * Convert a product array into edit-form friendly fields.
     *
     * @param array<string, mixed> $product
     * @return array<string, mixed>
     */
    private function toForm(array $product): array
    {
        $product['lines_text'] = $this->linesToText(Arr::get($product, 'lines', []));
        $product['applications_text'] = $this->listToText(Arr::get($product, 'applications', []));
        $product['capabilities_text'] = $this->capabilitiesToText(Arr::get($product, 'capabilities', []));
        $product['specs_text'] = $this->specsToText(Arr::get($product, 'specs', []));

        $cta = Arr::get($product, 'cta', []);
        $product['cta_primary_text'] = is_array($cta) ? (string) ($cta['primaryText'] ?? '') : '';
        $product['cta_primary_url'] = is_array($cta) ? (string) ($cta['primaryUrl'] ?? '') : '';

        $product['applications_intro'] = (string) ($product['applicationsIntro'] ?? '');
        $product['cta_heading'] = (string) ($product['ctaHeading'] ?? '');
        $product['cta_text'] = (string) ($product['ctaText'] ?? '');

        return $product;
    }

    /**
     * Convert validated form to stored product array.
     *
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function fromForm(array $validated): array
    {
        return [
            'slug' => (string) ($validated['slug'] ?? ''),
            'title' => (string) ($validated['title'] ?? ''),
            'tagline' => $validated['tagline'] ?? null,
            'intro' => $validated['intro'] ?? null,
            'heroImage' => $validated['heroImage'] ?? null,
            'seoDescription' => $validated['seoDescription'] ?? null,
            'seoImage' => $validated['seoImage'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => (bool) ($validated['is_active'] ?? false),

            'lines' => $this->parseLines((string) ($validated['lines_text'] ?? '')),
            'applicationsIntro' => $validated['applications_intro'] ?? null,
            'applications' => $this->parseList((string) ($validated['applications_text'] ?? '')),
            'capabilities' => $this->parseCapabilities((string) ($validated['capabilities_text'] ?? '')),
            'specs' => $this->parseSpecs((string) ($validated['specs_text'] ?? '')),

            'cta' => [
                'primaryText' => $validated['cta_primary_text'] ?? null,
                'primaryUrl' => $validated['cta_primary_url'] ?? null,
            ],
            'ctaHeading' => $validated['cta_heading'] ?? null,
            'ctaText' => $validated['cta_text'] ?? null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function blankProduct(): array
    {
        return [
            'id' => '',
            'slug' => '',
            'title' => '',
            'tagline' => '',
            'intro' => '',
            'heroImage' => '',
            'seoDescription' => '',
            'seoImage' => '',
            'sort_order' => 0,
            'is_active' => true,
            'applicationsIntro' => '',
            'ctaHeading' => '',
            'ctaText' => '',
            'cta_primary_text' => 'Request a Quote',
            'cta_primary_url' => url('/contact'),
            'lines_text' => "",
            'applications_text' => "",
            'capabilities_text' => "",
            'specs_text' => "",
        ];
    }

    /**
     * Lines format: Title|Subtitle|CODE1,CODE2
     *
     * @return array<int, array<string, mixed>>
     */
    private function parseLines(string $text): array
    {
        $rows = $this->splitLines($text);
        $out = [];

        foreach ($rows as $line) {
            [$title, $subtitle, $codes] = array_pad(array_map('trim', explode('|', $line)), 3, '');
            if ($title === '' && $subtitle === '' && $codes === '') {
                continue;
            }

            $codesArr = collect(explode(',', (string) $codes))
                ->map(fn($v) => trim((string) $v))
                ->filter()
                ->values()
                ->all();

            $out[] = [
                'title' => $title,
                'subtitle' => $subtitle !== '' ? $subtitle : null,
                'codes' => $codesArr,
            ];
        }

        return $out;
    }

    private function linesToText(array $lines): string
    {
        $lines = is_array($lines) ? $lines : [];
        $out = [];
        foreach ($lines as $row) {
            if (!is_array($row)) {
                continue;
            }
            $title = trim((string) ($row['title'] ?? ''));
            $subtitle = trim((string) ($row['subtitle'] ?? ''));
            $codes = $row['codes'] ?? [];
            if (!is_array($codes)) {
                $codes = [];
            }
            $codesStr = implode(',', array_values(array_filter(array_map(fn($v) => trim((string) $v), $codes))));
            $out[] = $title . '|' . $subtitle . '|' . $codesStr;
        }
        return implode("\n", $out);
    }

    /**
     * Capabilities format: Title|Desc|Icon
     *
     * @return array<int, array<string, mixed>>
     */
    private function parseCapabilities(string $text): array
    {
        $rows = $this->splitLines($text);
        $out = [];

        foreach ($rows as $line) {
            [$title, $desc, $icon] = array_pad(array_map('trim', explode('|', $line)), 3, '');
            if ($title === '' && $desc === '' && $icon === '') {
                continue;
            }
            $out[] = [
                'title' => $title,
                'desc' => $desc,
                'icon' => $icon !== '' ? $icon : null,
            ];
        }

        return $out;
    }

    private function capabilitiesToText(array $caps): string
    {
        $caps = is_array($caps) ? $caps : [];
        $out = [];
        foreach ($caps as $row) {
            if (!is_array($row)) {
                continue;
            }
            $title = trim((string) ($row['title'] ?? ''));
            $desc = trim((string) ($row['desc'] ?? ''));
            $icon = trim((string) ($row['icon'] ?? ''));
            $out[] = $title . '|' . $desc . '|' . $icon;
        }
        return implode("\n", $out);
    }

    /**
     * Specs format: Key|Value
     *
     * @return array<string, string>
     */
    private function parseSpecs(string $text): array
    {
        $rows = $this->splitLines($text);
        $out = [];

        foreach ($rows as $line) {
            [$k, $v] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
            if ($k === '') {
                continue;
            }
            $out[$k] = $v;
        }

        return $out;
    }

    private function specsToText(array $specs): string
    {
        if (!is_array($specs)) {
            return '';
        }
        $out = [];
        foreach ($specs as $k => $v) {
            $k = trim((string) $k);
            if ($k === '') {
                continue;
            }
            $out[] = $k . '|' . trim((string) $v);
        }
        return implode("\n", $out);
    }

    /**
     * One per line
     *
     * @return array<int, string>
     */
    private function parseList(string $text): array
    {
        return collect($this->splitLines($text))
            ->map(fn($v) => trim((string) $v))
            ->filter()
            ->values()
            ->all();
    }

    private function listToText(array $items): string
    {
        if (!is_array($items)) {
            return '';
        }
        return implode("\n", array_values(array_filter(array_map(fn($v) => trim((string) $v), $items))));
    }

    /**
     * @return array<int, string>
     */
    private function splitLines(string $text): array
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        return array_values(array_filter(array_map('trim', explode("\n", $text)), fn($v) => $v !== ''));
    }
}
