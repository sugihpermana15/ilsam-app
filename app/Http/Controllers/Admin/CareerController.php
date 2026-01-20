<?php

namespace App\Http\Controllers\Admin;

use App\Models\AssetLocation;
use App\Models\Department;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CareerController extends Controller
{
  private string $storagePath = 'career.json';

  public function index()
  {
    $data = $this->loadDataWithDefaults();
    $data = $this->ensureOpeningIds($data);

    $companyProfile = $this->companyToAdminForm($data['company'] ?? []);
    $careers = collect($data['openings'] ?? [])->map(fn($row) => new Fluent($row));

    $departmentOptions = Department::query()
      ->orderBy('name')
      ->pluck('name')
      ->filter()
      ->values()
      ->all();

    $locationOptions = AssetLocation::query()
      ->where('is_active', true)
      ->orderBy('name')
      ->pluck('name')
      ->filter()
      ->values()
      ->all();

    $stats = [
      'total' => $careers->count(),
      'active' => $careers->where('is_active', true)->count(),
      'draft' => $careers->where('is_active', false)->count(),
    ];

    return view('pages.admin.career.career_index', compact('companyProfile', 'careers', 'stats', 'departmentOptions', 'locationOptions'));
  }

  public function updateCompany(Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'company.name' => ['required', 'string', 'max:120'],
      'company.headline' => ['required', 'string', 'max:200'],
      'company.subheadline' => ['nullable', 'string', 'max:400'],
      'company.location' => ['nullable', 'string', 'max:200'],
      'company.email' => ['nullable', 'email', 'max:200'],
      'company.phone' => ['nullable', 'string', 'max:50'],
      'company.hero_image' => ['nullable', 'string', 'max:500'],
      'company.overview' => ['nullable', 'string'],
      'company.culture' => ['nullable', 'string'],
      'company.benefits' => ['nullable', 'string'],
      'company.process' => ['nullable', 'string'],
      'company.faq' => ['nullable', 'string'],
    ]);

    $data = $this->loadDataWithDefaults();
    $data = $this->ensureOpeningIds($data);

    $company = $data['company'] ?? [];
    $incoming = Arr::get($validated, 'company', []);

    // Keep it in "admin-form" string format in JSON so HR can edit easily.
    $company = array_merge($company, [
      'name' => $incoming['name'] ?? ($company['name'] ?? null),
      'headline' => $incoming['headline'] ?? ($company['headline'] ?? null),
      'subheadline' => $incoming['subheadline'] ?? ($company['subheadline'] ?? null),
      'location' => $incoming['location'] ?? ($company['location'] ?? null),
      'email' => $incoming['email'] ?? ($company['email'] ?? null),
      'phone' => $incoming['phone'] ?? ($company['phone'] ?? null),
      'hero_image' => $incoming['hero_image'] ?? ($company['hero_image'] ?? null),
      'overview' => $incoming['overview'] ?? ($company['overview'] ?? null),
      'culture' => $incoming['culture'] ?? ($company['culture'] ?? null),
      'benefits' => $incoming['benefits'] ?? ($company['benefits'] ?? null),
      'process' => $incoming['process'] ?? ($company['process'] ?? null),
      'faq' => $incoming['faq'] ?? ($company['faq'] ?? null),
    ]);

    $data['company'] = $company;
    $this->saveData($data);

    return back()->with('success', 'Company profile updated.');
  }

  public function store(Request $request): RedirectResponse
  {
    $validated = $this->validateOpening($request);

    $data = $this->loadDataWithDefaults();
    $data = $this->ensureOpeningIds($data);

    $openings = collect($data['openings'] ?? []);

    $opening = array_merge($validated, [
      'id' => (string) Str::uuid(),
      'is_active' => (bool) ($validated['is_active'] ?? true),
    ]);

    $openings->push($opening);

    $data['openings'] = $openings->values()->all();
    $this->saveData($data);

    return back()->with('success', 'Job opening created.');
  }

  public function update(Request $request, string $id): RedirectResponse
  {
    $validated = $this->validateOpening($request);

    $data = $this->loadDataWithDefaults();
    $data = $this->ensureOpeningIds($data);

    $openings = collect($data['openings'] ?? []);
    $index = $openings->search(fn($row) => (string) ($row['id'] ?? '') === (string) $id);

    if ($index === false) {
      return back()->with('error', 'Job opening not found.');
    }

    $existing = $openings->get($index);
    $updated = array_merge($existing, $validated, [
      'id' => (string) $id,
      'is_active' => (bool) ($validated['is_active'] ?? ($existing['is_active'] ?? true)),
    ]);

    $openings->put($index, $updated);

    $data['openings'] = $openings->values()->all();
    $this->saveData($data);

    return back()->with('success', 'Job opening updated.');
  }

  public function destroy(string $id): RedirectResponse
  {
    $data = $this->loadDataWithDefaults();
    $data = $this->ensureOpeningIds($data);

    $openings = collect($data['openings'] ?? []);
    $filtered = $openings->reject(fn($row) => (string) ($row['id'] ?? '') === (string) $id)->values();

    if ($filtered->count() === $openings->count()) {
      return back()->with('error', 'Job opening not found.');
    }

    $data['openings'] = $filtered->all();
    $this->saveData($data);

    return back()->with('success', 'Job opening deleted.');
  }

  private function validateOpening(Request $request): array
  {
    $validated = $request->validate([
      'title' => ['required', 'string', 'max:160'],
      'department' => ['nullable', 'string', 'max:160'],
      'location' => ['nullable', 'string', 'max:160'],
      'type' => ['nullable', 'string', 'max:60'],
      'work_mode' => ['nullable', 'string', 'max:60'],
      'experience' => ['nullable', 'string', 'max:120'],
      'summary' => ['nullable', 'string', 'max:1000'],
      'responsibilities' => ['nullable', 'string'],
      'requirements' => ['nullable', 'string'],
      'apply_url' => ['nullable', 'string', 'max:500'],
      'deadline' => ['nullable', 'date'],
      'is_active' => ['required'],
    ]);

    $validated['is_active'] = (bool) ((int) ($validated['is_active'] ?? 1));

    return $validated;
  }

  private function loadDataWithDefaults(): array
  {
    $data = [];
    if (Storage::disk('local')->exists($this->storagePath)) {
      $raw = Storage::disk('local')->get($this->storagePath);
      $decoded = json_decode($raw, true);
      if (is_array($decoded)) {
        $data = $decoded;
      }
    }

    $data['company'] = array_merge($this->defaultCompanyProfileAdminForm(), $data['company'] ?? []);
    $data['openings'] = $data['openings'] ?? [];

    return $data;
  }

  private function saveData(array $data): void
  {
    $payload = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    Storage::disk('local')->put($this->storagePath, $payload === false ? '{}' : $payload);
  }

  private function ensureOpeningIds(array $data): array
  {
    $openings = $data['openings'] ?? [];
    $changed = false;

    foreach ($openings as $i => $row) {
      if (!is_array($row)) {
        continue;
      }
      if (!isset($row['id']) || trim((string) $row['id']) === '') {
        $row['id'] = (string) Str::uuid();
        $openings[$i] = $row;
        $changed = true;
      }
      if (!array_key_exists('is_active', $row)) {
        $row['is_active'] = true;
        $openings[$i] = $row;
        $changed = true;
      }
    }

    $data['openings'] = array_values($openings);

    if ($changed) {
      $this->saveData($data);
    }

    return $data;
  }

  private function companyToAdminForm(array $company): array
  {
    $benefits = $company['benefits'] ?? '';
    if (is_array($benefits)) {
      $benefits = implode("\n", array_values(array_filter(array_map('trim', $benefits))));
    }

    $process = $company['process'] ?? '';
    if (is_array($process)) {
      $process = collect($process)
        ->map(fn($row) => trim((string) ($row['title'] ?? '')) . '|' . trim((string) ($row['text'] ?? '')))
        ->filter(fn($line) => $line !== '|')
        ->implode("\n");
    }

    $faq = $company['faq'] ?? '';
    if (is_array($faq)) {
      $faq = collect($faq)
        ->map(fn($row) => trim((string) ($row['q'] ?? '')) . '|' . trim((string) ($row['a'] ?? '')))
        ->filter(fn($line) => $line !== '|')
        ->implode("\n");
    }

    $company['benefits'] = $benefits;
    $company['process'] = $process;
    $company['faq'] = $faq;

    return $company;
  }

  private function defaultCompanyProfileAdminForm(): array
  {
    return [
      'name' => 'Ilsam',
      'headline' => 'Build your next career move with us',
      'subheadline' => 'We believe in people, safety, and innovation to deliver world-class manufacturing.',
      'location' => 'Jababeka & Karawang, Indonesia',
      'email' => 'hrd@ilsam.co.id',
      'phone' => '+62 21 0000 0000',
      'hero_image' => asset('assets/img/aboutus/img11.jpg'),
      'overview' => 'Ilsam is a global manufacturer focused on quality, sustainability, and continuous improvement.',
      'culture' => 'Integrity, teamwork, and accountability define how we work.',
      'benefits' => "Competitive salary\nBPJS Kesehatan & Ketenagakerjaan\nTraining & certification programs",
      'process' => "Apply|Submit CV\nScreening|HR review\nInterview|Meet the team\nOffer|Receive the offer",
      'faq' => "Can I apply for multiple roles?|Yes, you may apply to multiple positions.\nDo you accept fresh graduates?|Yes, we have openings suitable for fresh graduates.",
    ];
  }
}
