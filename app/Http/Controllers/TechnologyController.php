<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class TechnologyController extends Controller
{
    public function index()
    {
        return view('pages.technology.index', [
            'portfolio' => $this->portfolioData(),
        ]);
    }

    public function certificationStatus()
    {
        $today = Carbon::today();
        $expiringSoonThreshold = $today->copy()->addDays(30);

        $certificates = Certificate::query()
            ->orderBy('chemical_name')
            ->orderByDesc('id')
            ->get()
            ->map(function (Certificate $c) use ($today, $expiringSoonThreshold) {
                $expiry = $c->expiry_date ? Carbon::parse($c->expiry_date) : null;

                if ($expiry && $expiry->lt($today)) {
                    $status = 'expired';
                } elseif ($expiry && $expiry->lte($expiringSoonThreshold)) {
                    $status = 'expiring_soon';
                } else {
                    $status = 'active';
                }

                $proofUrl = $c->proof_path ? route('certificates.proof', $c) : null;

                return [
                    'id' => $c->id,
                    'chemical_name' => $c->chemical_name,
                    'supplier' => $c->supplier,
                    'certification_type' => $c->certification_type,
                    'certificate_no' => $c->certificate_no,
                    'issued_date' => optional($c->issued_date)->toDateString(),
                    'expiry_date' => optional($c->expiry_date)->toDateString(),
                    'scope' => $c->scope,
                    'zdhc_link' => $c->zdhc_link,
                    'proof_type' => $proofUrl ? 'pdf' : 'missing',
                    'proof_url' => $proofUrl,
                    'status' => $status,
                ];
            })
            ->values()
            ->all();

        return view('pages.technology.sertifikat', [
            'certificates' => $certificates,
            'generated_at' => Carbon::now()->toDateTimeString(),
        ]);
    }

    public function certificateProof(Certificate $certificate)
    {
        if (!$certificate->proof_path) {
            abort(404);
        }

        $relativePath = ltrim($certificate->proof_path, '/');
        $lower = strtolower($relativePath);
        if (!str_ends_with($lower, '.pdf')) {
            abort(404);
        }

        if (!Storage::disk('public')->exists($relativePath)) {
            abort(404);
        }

        $absolutePath = Storage::disk('public')->path($relativePath);

        return response()->file($absolutePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($relativePath) . '"',
        ]);
    }

    private function portfolioData(): array
    {
        return [
            [
                'title' => 'Chemical Colorants',
                'subtitle' => 'Colorants for PU/PVC synthetic leather, printing, and water-based systems.',
                'route' => route('products.colorants'),
                'lines' => [
                    [
                        'title' => 'Colorants for Leather and Synthetic Leather PU',
                        'codes' => ['SW', 'SU', 'SF'],
                    ],
                    [
                        'title' => 'Colorants for Synthetic Leather PVC',
                        'codes' => ['SV', 'SFV'],
                    ],
                    [
                        'title' => 'Colorants for Printing',
                        'codes' => ['SP', 'SG'],
                    ],
                    [
                        'title' => 'Colorants for Water-based',
                        'codes' => ['SUW'],
                    ],
                ],
            ],
            [
                'title' => 'Surface Coating Agents',
                'subtitle' => 'Solution-type surface coating agent for leather and synthetic leather PU/PVC.',
                'route' => route('products.surface-coating-agents'),
                'lines' => [
                    [
                        'title' => 'Solution-type Surface Coating Agent (PU & PVC)',
                        'codes' => ['SUS'],
                    ],
                ],
            ],
            [
                'title' => 'Additive Coating',
                'subtitle' => 'Supplementary agent for promoting quality and curing PU and PVC.',
                'route' => route('products.additive-coating'),
                'lines' => [
                    [
                        'title' => 'Supplementary agent for PU & PVC curing',
                        'codes' => ['SC', 'SS', 'SI'],
                    ],
                ],
            ],
            [
                'title' => 'PU Resin',
                'subtitle' => 'Resin and adhesive systems for PU applications and resin production lines.',
                'route' => route('products.pu-resin'),
                'lines' => [
                    [
                        'title' => 'Skin and Adhesive for Leather and Synthetic Leather PU',
                        'codes' => ['ISU', 'ISA', 'ISN', 'IWD', 'IWA', 'IWS', 'IEU', 'IEA', 'IEW'],
                    ],
                    [
                        'title' => 'Polyester for production Resin PU',
                        'codes' => ['EB', 'B', 'DEB'],
                    ],
                ],
            ],
        ];
    }
}
