<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Support\WebsiteProducts;
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
        $slugToRoute = [
            'colorants' => 'products.colorants',
            'surface-coating-agents' => 'products.surface-coating-agents',
            'additive-coating' => 'products.additive-coating',
            'pu-resin' => 'products.pu-resin',
        ];

        $products = collect(WebsiteProducts::all())
            ->filter(fn($p) => (bool) ($p['is_active'] ?? true))
            ->values();

        return $products
            ->map(function ($p) use ($slugToRoute) {
                $slug = (string) ($p['slug'] ?? '');
                $routeName = $slugToRoute[$slug] ?? null;

                $lines = collect($p['lines'] ?? [])
                    ->filter(fn($row) => is_array($row))
                    ->map(function ($row) {
                        return [
                            'title' => $row['title'] ?? '',
                            'codes' => is_array($row['codes'] ?? null) ? array_values($row['codes']) : [],
                        ];
                    })
                    ->values()
                    ->all();

                return [
                    'title' => $p['title'] ?? '',
                    'subtitle' => $p['tagline'] ?? '',
                    'route' => $routeName ? route($routeName) : route('products'),
                    'lines' => $lines,
                ];
            })
            ->all();
    }
}
