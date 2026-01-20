<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
  public function index()
  {
    $certificates = Certificate::query()
      ->orderByDesc('id')
      ->get();

    return view('pages.admin.technology.certificates', compact('certificates'));
  }

  public function store(Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'chemical_name' => ['required', 'string', 'max:200'],
      'supplier' => ['nullable', 'string', 'max:200'],
      'certification_type' => ['nullable', 'string', 'max:200'],
      'certificate_no' => ['nullable', 'string', 'max:120'],
      'issued_date' => ['nullable', 'date'],
      'expiry_date' => ['nullable', 'date'],
      'scope' => ['nullable', 'string', 'max:255'],
      'zdhc_link' => ['nullable', 'string', 'max:500'],
      'proof_pdf' => ['required', 'file', 'mimetypes:application/pdf,application/x-pdf', 'max:10240'],
    ]);

    $proofPath = null;
    if ($request->hasFile('proof_pdf')) {
      $proofPath = $request->file('proof_pdf')->store('certificates', 'public');
    }

    Certificate::create([
      'chemical_name' => $validated['chemical_name'],
      'supplier' => $validated['supplier'] ?? null,
      'certification_type' => $validated['certification_type'] ?? null,
      'certificate_no' => $validated['certificate_no'] ?? null,
      'issued_date' => $validated['issued_date'] ?? null,
      'expiry_date' => $validated['expiry_date'] ?? null,
      'scope' => $validated['scope'] ?? null,
      'zdhc_link' => $validated['zdhc_link'] ?? null,
      'proof_path' => $proofPath,
    ]);

    return back()->with('success', 'Certificate created.');
  }

  public function update(Request $request, Certificate $certificate): RedirectResponse
  {
    $validated = $request->validate([
      'chemical_name' => ['required', 'string', 'max:200'],
      'supplier' => ['nullable', 'string', 'max:200'],
      'certification_type' => ['nullable', 'string', 'max:200'],
      'certificate_no' => ['nullable', 'string', 'max:120'],
      'issued_date' => ['nullable', 'date'],
      'expiry_date' => ['nullable', 'date'],
      'scope' => ['nullable', 'string', 'max:255'],
      'zdhc_link' => ['nullable', 'string', 'max:500'],
      'proof_pdf' => ['nullable', 'file', 'mimetypes:application/pdf,application/x-pdf', 'max:10240'],
    ]);

    $proofPath = $certificate->proof_path;
    if ($request->hasFile('proof_pdf')) {
      $newPath = $request->file('proof_pdf')->store('certificates', 'public');
      if ($proofPath) {
        Storage::disk('public')->delete($proofPath);
      }
      $proofPath = $newPath;
    }

    $certificate->update([
      'chemical_name' => $validated['chemical_name'],
      'supplier' => $validated['supplier'] ?? null,
      'certification_type' => $validated['certification_type'] ?? null,
      'certificate_no' => $validated['certificate_no'] ?? null,
      'issued_date' => $validated['issued_date'] ?? null,
      'expiry_date' => $validated['expiry_date'] ?? null,
      'scope' => $validated['scope'] ?? null,
      'zdhc_link' => $validated['zdhc_link'] ?? null,
      'proof_path' => $proofPath,
    ]);

    return back()->with('success', 'Certificate updated.');
  }

  public function destroy(Certificate $certificate): RedirectResponse
  {
    if ($certificate->proof_path) {
      Storage::disk('public')->delete($certificate->proof_path);
    }

    $certificate->delete();

    return back()->with('success', 'Certificate deleted.');
  }
}
