<?php

namespace App\Http\Controllers;

use App\Support\WebsiteProducts;

class ProductsController extends Controller
{
    public function index()
    {
        // "All Products" landing page removed; keep /products as a friendly entry.
        return redirect()->route('products.colorants');
    }

    public function colorants()
    {
        $colorants = \App\Models\Colorant::latest()->get();

        return view('pages.products.colorants', [
            'product'   => $this->productOrFallback('colorants', fn() => $this->colorantsData()),
            'colorants' => $colorants,
        ]);
    }

    private function productOrFallback(string $slug, \Closure $fallback): array
    {
        $row = WebsiteProducts::findBySlug($slug);
        if (is_array($row) && ($row['is_active'] ?? true)) {
            return $row;
        }

        return (array) $fallback();
    }

    private function colorantsData(): array
    {
        return [
            'title' => 'Chemical Colorants',
            'tagline' => 'Colorants for PU/PVC synthetic leather, printing, and water-based systems.',
            'intro' => 'We supply colorants designed for stable processing and consistent output across batches. Select the suitable line and code based on your application.',
            'heroImage' => asset('assets/img/img10.jpg'),
            'lines' => [
                [
                    'title' => 'Colorants for Leather and Synthetic Leather PU',
                    'subtitle' => 'PU applications for leather and synthetic leather.',
                    'codes' => ['SW', 'SU', 'SF'],
                ],
                [
                    'title' => 'Colorants for Synthetic Leather PVC',
                    'subtitle' => 'PVC applications for synthetic leather systems.',
                    'codes' => ['SV', 'SFV'],
                ],
                [
                    'title' => 'Colorants for Printing',
                    'subtitle' => 'Printing applications requiring consistent shade and performance.',
                    'codes' => ['SP', 'SG'],
                ],
                [
                    'title' => 'Colorants for Water-based',
                    'subtitle' => 'Water-based systems and related applications.',
                    'codes' => ['SUW'],
                ],
            ],
            'applicationsIntro' => 'Common applications for our colorants include:',
            'applications' => [
                'Leather and synthetic leather (PU)',
                'Synthetic leather (PVC)',
                'Printing applications',
                'Water-based systems',
            ],
            'capabilities' => [
                [
                    'title' => 'Color Matching',
                    'desc' => 'Support for target shades and repeatable batches for production needs.',
                    'icon' => 'bi-palette',
                ],
                [
                    'title' => 'Process Stability',
                    'desc' => 'Designed to maintain dispersion and performance during processing.',
                    'icon' => 'bi-gear',
                ],
                [
                    'title' => 'Quality & Consistency',
                    'desc' => 'Focus on consistent output to reduce variation across lots.',
                    'icon' => 'bi-check2-circle',
                ],
                [
                    'title' => 'Supply Reliability',
                    'desc' => 'Structured for long-term partnership with dependable delivery.',
                    'icon' => 'bi-truck',
                ],
            ],
            'specs' => [
                'Format options' => 'Liquid / paste / concentrate (depending on application)',
                'Color range' => 'Standard shades + custom matching',
                'Packaging' => 'Industrial packaging options available on request',
                'Documentation' => 'COA, MSDS/SDS available upon request',
            ],
            'cta' => [
                'primaryText' => 'Request a Quote',
                'primaryUrl' => route('contact'),
            ],
            'ctaHeading' => 'Ready to match a target shade?',
            'ctaText' => 'Send your target color, application type, and process details. Our team will respond with recommended product line and code options.',
        ];
    }
}
