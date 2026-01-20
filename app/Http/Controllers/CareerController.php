<?php

namespace App\Http\Controllers;

use App\Models\CareerCandidate;
use Illuminate\Http\Request;
use Illuminate\Support\Fluent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CareerController extends Controller
{
  private string $storagePath = 'career.json';

  public function index(Request $request)
  {
    $data = $this->loadData();
    $company = $this->buildCompanyPublic($data['company'] ?? []);

    $openingsAll = collect($data['openings'] ?? [])
      ->map(fn($row) => new Fluent($row))
      ->filter(fn($row) => (bool) ($row->is_active ?? false))
      ->values();

    $filterOptions = [
      'departments' => $openingsAll->pluck('department')->filter()->unique()->sort()->values()->all(),
      'locations' => $openingsAll->pluck('location')->filter()->unique()->sort()->values()->all(),
      'types' => $openingsAll->pluck('type')->filter()->unique()->sort()->values()->all(),
      'work_modes' => $openingsAll->pluck('work_mode')->filter()->unique()->sort()->values()->all(),
      'sorts' => [
        ['value' => 'title_asc', 'label' => 'Title (A–Z)'],
        ['value' => 'title_desc', 'label' => 'Title (Z–A)'],
        ['value' => 'dept_asc', 'label' => 'Department (A–Z)'],
        ['value' => 'loc_asc', 'label' => 'Location (A–Z)'],
      ],
    ];

    $filters = [
      'q' => trim((string) $request->query('q', '')),
      'department' => trim((string) $request->query('department', '')),
      'location' => trim((string) $request->query('location', '')),
      'type' => trim((string) $request->query('type', '')),
      'work_mode' => trim((string) $request->query('work_mode', '')),
      'sort' => trim((string) $request->query('sort', 'title_asc')),
    ];

    $normalize = static fn($value) => mb_strtolower(trim((string) $value));

    $openings = $openingsAll
      ->when($filters['q'] !== '', function ($rows) use ($filters, $normalize) {
        $q = $normalize($filters['q']);

        return $rows->filter(function (Fluent $row) use ($q, $normalize) {
          $haystack = implode(' ', [
            $row->title ?? '',
            $row->department ?? '',
            $row->location ?? '',
            $row->type ?? '',
            $row->work_mode ?? '',
            $row->summary ?? '',
          ]);

          return str_contains($normalize($haystack), $q);
        });
      })
      ->when($filters['department'] !== '', fn($rows) => $rows->where('department', $filters['department']))
      ->when($filters['location'] !== '', fn($rows) => $rows->where('location', $filters['location']))
      ->when($filters['type'] !== '', fn($rows) => $rows->where('type', $filters['type']))
      ->when($filters['work_mode'] !== '', fn($rows) => $rows->where('work_mode', $filters['work_mode']))
      ->values();

    $openings = match ($filters['sort']) {
      'title_desc' => $openings->sortByDesc(fn(Fluent $row) => (string) ($row->title ?? '')),
      'dept_asc' => $openings->sortBy(fn(Fluent $row) => (string) ($row->department ?? '')),
      'loc_asc' => $openings->sortBy(fn(Fluent $row) => (string) ($row->location ?? '')),
      default => $openings->sortBy(fn(Fluent $row) => (string) ($row->title ?? '')),
    };

    $openings = $openings->values();

    return view('career', [
      'company' => $company,
      'openings' => $openings,
      'openingsAllCount' => $openingsAll->count(),
      'filters' => $filters,
      'filterOptions' => $filterOptions,
    ]);
  }

  public function applyForm(Request $request, ?string $job = null)
  {
    $data = $this->loadData();
    $company = $this->buildCompanyPublic($data['company'] ?? []);

    $openingsAll = collect($data['openings'] ?? [])
      ->map(fn($row) => new Fluent($row))
      ->filter(fn($row) => (bool) ($row->is_active ?? false))
      ->values();

    $selectedJob = null;
    if ($job !== null && trim($job) !== '') {
      $selectedJob = $openingsAll->first(fn(Fluent $row) => (string) ($row->id ?? '') === (string) $job);
      if (!$selectedJob) {
        return redirect()->route('career')->with('error', 'Job opening not found.');
      }
    }

    return view('pages.career.apply', [
      'company' => $company,
      'openings' => $openingsAll,
      'selectedJob' => $selectedJob,
    ]);
  }

  public function submitApplication(Request $request)
  {
    $recaptchaEnabled = (bool) config('career_security.recaptcha.enabled');
    $recaptchaSecret = (string) config('services.recaptcha.secret');
    $needsRecaptcha = $recaptchaEnabled && $recaptchaSecret !== '';

    $validated = $request->validate([
      'job_id' => ['nullable', 'string', 'max:36'],
      'job_title' => ['nullable', 'string', 'max:160'],
      'full_name' => ['required', 'string', 'max:160'],
      'email' => ['required', 'email', 'max:200'],
      'phone' => ['required', 'string', 'max:60'],
      'domicile' => ['nullable', 'string', 'max:160'],
      'linkedin_url' => ['nullable', 'url', 'max:500'],
      'portfolio_url' => ['nullable', 'url', 'max:500'],
      'message' => ['nullable', 'string', 'max:5000'],
      'cv' => [
        'required',
        'file',
        'max:2048',
        // PDF only (safer)
        'mimes:pdf',
        // MIME allowlist (best-effort; depends on PHP fileinfo)
        'mimetypes:application/pdf',
      ],
      'consent' => ['accepted'],
      'g-recaptcha-response' => $needsRecaptcha ? ['required', 'string'] : ['nullable'],
    ], [
      'cv.max' => 'CV maximum size is 2MB.',
      'cv.mimes' => 'CV must be a PDF file.',
      'cv.mimetypes' => 'CV must be a PDF file.',
      'consent.accepted' => 'Please confirm that you consent to your data being processed by HRD.',
      'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
    ]);

    if ($needsRecaptcha) {
      $token = (string) ($validated['g-recaptcha-response'] ?? '');
      if (!$this->verifyRecaptcha($token, (string) $request->ip())) {
        return back()->withInput()->withErrors([
          'g-recaptcha-response' => 'reCAPTCHA verification failed. Please try again.',
        ]);
      }
    }

    // Normalize job from source-of-truth JSON (avoid tampering).
    $jobId = trim((string) ($validated['job_id'] ?? ''));
    $jobTitle = trim((string) ($validated['job_title'] ?? ''));

    if ($jobId !== '') {
      $data = $this->loadData();
      $openingsAll = collect($data['openings'] ?? [])
        ->map(fn($row) => new Fluent($row))
        ->filter(fn($row) => (bool) ($row->is_active ?? false))
        ->values();

      $job = $openingsAll->first(fn(Fluent $row) => (string) ($row->id ?? '') === (string) $jobId);
      if (!$job) {
        return back()->withInput()->withErrors(['job_id' => 'Selected job is not available.']);
      }
      $jobTitle = (string) ($job->title ?? $jobTitle);
    }

    $cvFile = $request->file('cv');

    // Optional antivirus scan (ClamAV) BEFORE storing file.
    if ((bool) config('career_security.antivirus.enabled')) {
      $scan = $this->scanWithClamav((string) $cvFile->getRealPath());
      if ($scan === 'infected') {
        return back()->withInput()->withErrors([
          'cv' => 'CV file was rejected (malware detected).',
        ]);
      }
      if ($scan === 'error' && (bool) config('career_security.antivirus.fail_closed')) {
        return back()->withInput()->withErrors([
          'cv' => 'CV scan failed. Please try again later.',
        ]);
      }
    }

    // Sanitize user-supplied filename (never trust it).
    $originalName = (string) $cvFile->getClientOriginalName();
    $originalName = str_replace("\0", '', $originalName);
    $originalName = trim(basename($originalName));
    if ($originalName === '') {
      $originalName = 'cv';
    }

    $mime = $cvFile->getClientMimeType();
    $size = $cvFile->getSize();

    $safeName = Str::uuid()->toString() . '.' . $cvFile->getClientOriginalExtension();
    $cvPath = $cvFile->storeAs('career/cv', $safeName, 'local');

    CareerCandidate::query()->create([
      'job_id' => $jobId !== '' ? $jobId : null,
      'job_title' => $jobTitle !== '' ? $jobTitle : null,
      'full_name' => $validated['full_name'],
      'email' => $validated['email'],
      'phone' => $validated['phone'],
      'domicile' => $validated['domicile'] ?? null,
      'linkedin_url' => $validated['linkedin_url'] ?? null,
      'portfolio_url' => $validated['portfolio_url'] ?? null,
      'message' => $validated['message'] ?? null,
      'cv_path' => $cvPath,
      'cv_original_name' => $originalName,
      'cv_mime' => $mime,
      'cv_size' => $size,
      'ip_address' => $request->ip(),
      'user_agent' => substr((string) $request->userAgent(), 0, 5000),
    ]);

    return redirect()->route('career')->with('success', 'Application submitted successfully.');
  }

  private function verifyRecaptcha(string $token, string $ip): bool
  {
    $secret = (string) config('services.recaptcha.secret');
    if ($token === '' || $secret === '') {
      return false;
    }

    try {
      $res = Http::asForm()->timeout(8)->post('https://www.google.com/recaptcha/api/siteverify', [
        'secret' => $secret,
        'response' => $token,
        'remoteip' => $ip,
      ]);

      if (!$res->ok()) {
        return false;
      }

      $json = $res->json() ?: [];
      return (bool) ($json['success'] ?? false);
    } catch (\Throwable $e) {
      return false;
    }
  }

  /**
   * Returns: 'clean' | 'infected' | 'error'
   * Exit codes for clamscan: 0=clean, 1=infected, 2=error
   */
  private function scanWithClamav(string $absoluteFilePath): string
  {
    $cmd = trim((string) config('career_security.antivirus.command'));
    if ($cmd === '' || $absoluteFilePath === '') {
      return 'error';
    }

    $command = $cmd . ' --no-summary ' . escapeshellarg($absoluteFilePath);

    $output = [];
    $exit = 2;
    @exec($command, $output, $exit);

    return match ((int) $exit) {
      0 => 'clean',
      1 => 'infected',
      default => 'error',
    };
  }

  private function loadData(): array
  {
    if (!Storage::disk('local')->exists($this->storagePath)) {
      return [];
    }

    $raw = Storage::disk('local')->get($this->storagePath);
    return json_decode($raw, true) ?: [];
  }

  private function buildCompanyPublic(array $company): array
  {
    $defaults = $this->defaultCompanyProfile();
    $merged = array_merge($defaults, $company);

    $benefits = $merged['benefits'] ?? [];
    if (is_string($benefits)) {
      $benefits = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $benefits))));
    }

    $process = $merged['process'] ?? [];
    if (is_string($process)) {
      $process = $this->parseKeyValueLines($process, 'title', 'text');
    }

    $faq = $merged['faq'] ?? [];
    if (is_string($faq)) {
      $faq = $this->parseKeyValueLines($faq, 'q', 'a');
    }

    $merged['benefits'] = $benefits;
    $merged['process'] = $process;
    $merged['faq'] = $faq;

    return $merged;
  }

  private function parseKeyValueLines(string $raw, string $key, string $value): array
  {
    $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $raw)));
    $rows = [];

    foreach ($lines as $line) {
      [$left, $right] = array_pad(explode('|', $line, 2), 2, '');
      if ($left === '' && $right === '') {
        continue;
      }
      $rows[] = [
        $key => trim($left),
        $value => trim($right),
      ];
    }

    return $rows;
  }

  private function defaultCompanyProfile(): array
  {
    return [
      'name' => 'Ilsam',
      'headline' => 'Build your next career move with us',
      'subheadline' => 'We believe in people, safety, and innovation to deliver world-class manufacturing.',
      'location' => 'Jababeka & Karawang, Indonesia',
      'email' => 'hrd@ilsam.co.id',
      'phone' => '+62 21 0000 0000',
      'hero_image' => asset('assets/img/aboutus/img11.jpg'),
      'overview' => 'Ilsam is a global manufacturer focused on quality, sustainability, and continuous improvement. We invest in technology and people to create a safe, inclusive, and high-performance culture.',
      'culture' => 'Integrity, teamwork, and accountability define how we work. We are committed to growth through continuous learning and cross-functional collaboration.',
      'stats' => [
        ['label' => 'Years of Excellence', 'value' => '25+'],
        ['label' => 'Employees', 'value' => '1,200+'],
        ['label' => 'Facilities', 'value' => '2'],
        ['label' => 'Departments', 'value' => '14'],
      ],
      'benefits' => [
        'Competitive salary & performance incentives',
        'BPJS Kesehatan & Ketenagakerjaan',
        'Training & certification programs',
        'Career growth & internal mobility',
        'Safe and modern manufacturing environment',
        'Employee engagement & wellness activities',
      ],
      'process' => [
        ['title' => 'Apply', 'text' => 'Submit your CV and complete the online form.'],
        ['title' => 'Screening', 'text' => 'HRD will review your profile and contact shortlisted candidates.'],
        ['title' => 'Interview', 'text' => 'Meet the team and discuss your experience and goals.'],
        ['title' => 'Offer', 'text' => 'Receive your offer letter and onboarding plan.'],
      ],
      'faq' => [
        ['q' => 'Can I apply for multiple roles?', 'a' => 'Yes, you may apply to multiple positions that fit your background.'],
        ['q' => 'Do you accept fresh graduates?', 'a' => 'Yes, we have openings suitable for fresh graduates.'],
        ['q' => 'Where will the interviews be held?', 'a' => 'Interviews may be conducted online or at our facilities depending on the role.'],
      ],
    ];
  }
}
