<?php

namespace App\Http\Controllers;

class ProductsController extends Controller
{
    public function index()
    {
        // "All Products" landing page removed; keep /products as a friendly entry.
        return redirect()->route('products.colorants');
    }

    public function colorants()
    {
        return view('pages.products.colorants', [
            'product' => $this->colorantsData(),
        ]);
    }

    public function surfaceCoatingAgents()
    {
        return view('pages.products.surface-coating-agents', [
            'product' => $this->surfaceCoatingAgentsData(),
        ]);
    }

    public function additiveCoating()
    {
        return view('pages.products.additive-coating', [
            'product' => $this->additiveCoatingData(),
        ]);
    }

    public function puResin()
    {
        return view('pages.products.pu-resin', [
            'product' => $this->puResinData(),
        ]);
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

    private function surfaceCoatingAgentsData(): array
    {
        return [
            'title' => 'Surface Coating Agents',
            'tagline' => 'Solution-type surface coating agent for leather and synthetic leather PU and PVC.',
            'intro' => 'Surface coating agents support finishing requirements and surface performance for PU and PVC synthetic leather applications.',
            'heroImage' => asset('assets/img/img6.jpg'),
            'lines' => [
                [
                    'title' => 'Solution-type Surface Coating Agent (PU & PVC)',
                    'subtitle' => 'For leather and synthetic leather systems.',
                    'codes' => ['SUS'],
                ],
            ],
            'applications' => [
                'Leather finishing (PU systems)',
                'Synthetic leather finishing (PU)',
                'Synthetic leather finishing (PVC)',
                'General surface performance enhancement',
            ],
            'capabilities' => [
                [
                    'title' => 'Surface Performance',
                    'desc' => 'Supports durable surface quality for demanding end-uses.',
                    'icon' => 'bi-shield-check',
                ],
                [
                    'title' => 'Finishing Support',
                    'desc' => 'Helps achieve targeted surface feel and appearance.',
                    'icon' => 'bi-stars',
                ],
                [
                    'title' => 'Process-Friendly',
                    'desc' => 'Designed to integrate smoothly into production processes.',
                    'icon' => 'bi-gear',
                ],
                [
                    'title' => 'Consistent Quality',
                    'desc' => 'Focus on stable results across production batches.',
                    'icon' => 'bi-check2-circle',
                ],
            ],
            'specs' => [
                'Product type' => 'Solution-type surface coating agent',
                'Target systems' => 'PU and PVC synthetic leather',
                'Packaging' => 'Industrial packaging options available on request',
                'Documentation' => 'COA, MSDS/SDS available upon request',
            ],
            'cta' => [
                'primaryText' => 'Request a Quote',
                'primaryUrl' => route('contact'),
            ],
        ];
    }

    private function additiveCoatingData(): array
    {
        return [
            'title' => 'Additive Coating',
            'tagline' => 'Supplementary agent for promoting quality and curing PU and PVC.',
            'intro' => 'Additive coating agents help promote quality and curing performance for PU and PVC applications.',
            'heroImage' => asset('assets/img/img3.jpg'),
            'lines' => [
                [
                    'title' => 'Supplementary agent for PU & PVC curing',
                    'subtitle' => 'Promoting quality and curing performance.',
                    'codes' => ['SC', 'SS', 'SI'],
                ],
            ],
            'applications' => [
                'PU coating processes',
                'PVC coating processes',
                'Curing and quality improvement support',
                'Production efficiency optimization',
            ],
            'capabilities' => [
                [
                    'title' => 'Quality Support',
                    'desc' => 'Helps maintain consistent output quality in production.',
                    'icon' => 'bi-award',
                ],
                [
                    'title' => 'Curing Assistance',
                    'desc' => 'Designed to support curing behavior for PU/PVC systems.',
                    'icon' => 'bi-lightning-charge',
                ],
                [
                    'title' => 'Process Stability',
                    'desc' => 'Supports stable processing for predictable results.',
                    'icon' => 'bi-gear',
                ],
                [
                    'title' => 'Partner Reliability',
                    'desc' => 'Built for long-term industrial supply needs.',
                    'icon' => 'bi-truck',
                ],
            ],
            'specs' => [
                'Product type' => 'Supplementary additive coating agent',
                'Target systems' => 'PU and PVC',
                'Packaging' => 'Industrial packaging options available on request',
                'Documentation' => 'COA, MSDS/SDS available upon request',
            ],
            'cta' => [
                'primaryText' => 'Request a Quote',
                'primaryUrl' => route('contact'),
            ],
        ];
    }

    private function puResinData(): array
    {
        return [
            'title' => 'PU Resin',
            'tagline' => 'Resin and adhesive systems for PU applications and resin production lines.',
            'intro' => 'PU resin category includes skin/adhesive systems for synthetic leather PU and polyester products for PU resin production.',
            'heroImage' => asset('assets/img/img1.jpg'),
            'lines' => [
                [
                    'title' => 'Skin and Adhesive for Leather and Synthetic Leather PU',
                    'subtitle' => 'For PU applications in leather and synthetic leather.',
                    'codes' => ['ISU', 'ISA', 'ISN', 'IWD', 'IWA', 'IWS', 'IEU', 'IEA', 'IEW'],
                ],
                [
                    'title' => 'Polyester for production Resin PU',
                    'subtitle' => 'Polyester products for PU resin production needs.',
                    'codes' => ['EB', 'B', 'DEB'],
                ],
            ],
            'applications' => [
                'PU synthetic leather production',
                'Adhesive and skin layers for PU systems',
                'PU resin production and related manufacturing',
                'Industrial formulation and process targets',
            ],
            'capabilities' => [
                [
                    'title' => 'Formulation Support',
                    'desc' => 'Supports industrial formulation and production targets.',
                    'icon' => 'bi-bezier2',
                ],
                [
                    'title' => 'Process Stability',
                    'desc' => 'Designed for predictable behavior in production processes.',
                    'icon' => 'bi-gear',
                ],
                [
                    'title' => 'Quality Consistency',
                    'desc' => 'Stable output to reduce variation across lots.',
                    'icon' => 'bi-check2-circle',
                ],
                [
                    'title' => 'Reliable Supply',
                    'desc' => 'Structured for long-term partnership and continuity.',
                    'icon' => 'bi-truck',
                ],
            ],
            'specs' => [
                'Product scope' => 'PU resin / skin / adhesive and polyester lines',
                'Target systems' => 'PU synthetic leather and resin production',
                'Packaging' => 'Industrial packaging options available on request',
                'Documentation' => 'COA, MSDS/SDS available upon request',
            ],
            'cta' => [
                'primaryText' => 'Request a Quote',
                'primaryUrl' => route('contact'),
            ],
        ];
    }
}
