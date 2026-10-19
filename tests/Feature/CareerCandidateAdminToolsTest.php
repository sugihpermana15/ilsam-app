<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\CareerCandidateController;
use App\Models\CareerCandidate;
use App\Models\CareerEmailTemplate;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CareerCandidateAdminToolsTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();
    config(['career_security.antivirus.enabled' => false]);

    Schema::create('career_candidates', function (Blueprint $table) {
      $table->id();
      $table->string('job_id', 36)->nullable();
      $table->string('candidate_code')->nullable();
      $table->string('full_name');
      $table->string('email');
      $table->string('phone')->nullable();
      $table->string('job_title')->nullable();
      $table->string('current_address')->nullable();
      $table->string('linkedin_url')->nullable();
      $table->string('portfolio_url')->nullable();
      $table->text('message')->nullable();
      $table->string('cv_path')->nullable();
      $table->string('cv_original_name')->nullable();
      $table->string('cv_mime')->nullable();
      $table->unsignedBigInteger('cv_size')->nullable();
      $table->string('ip_address')->nullable();
      $table->text('user_agent')->nullable();
      $table->string('recruitment_stage')->default('screening_cv');
      $table->string('selection_status')->default('in_process');
      $table->boolean('is_talent_pool')->default(false);
      $table->text('talent_pool_notes')->nullable();
      $table->string('domicile')->nullable();
      $table->string('experience_range')->nullable();
      $table->text('stage_notes')->nullable();
      $table->dateTime('processed_at')->nullable();
      $table->timestamps();
    });

    Schema::create('career_email_templates', function (Blueprint $table) {
      $table->id();
      $table->string('name', 120);
      $table->string('subject', 255);
      $table->longText('body');
      $table->timestamps();
    });
  }

  public function test_stage_export_downloads_excel_compatible_csv_with_candidates_from_only_that_stage(): void
  {
    Storage::fake('local');
    $cvPath = 'career/cv/psychology-candidate.pdf';
    Storage::disk('local')->put($cvPath, '%PDF-1.4');
    $candidate = CareerCandidate::create([
      'candidate_code' => 'PSI-001',
      'full_name' => 'Kandidat Psikotes',
      'email' => 'candidate@example.test',
      'job_title' => 'Tax Officer',
      'recruitment_stage' => 'psychology_test',
      'cv_path' => $cvPath,
    ]);
    CareerCandidate::create([
      'candidate_code' => 'PSI-002',
      'full_name' => 'CV Tidak Ada',
      'email' => 'missing-cv@example.test',
      'job_title' => 'Tax Officer',
      'recruitment_stage' => 'psychology_test',
    ]);
    CareerCandidate::create([
      'candidate_code' => 'CV-002',
      'full_name' => 'Kandidat Screening',
      'email' => 'screening@example.test',
      'job_title' => 'Tax Officer',
      'recruitment_stage' => 'screening_cv',
    ]);

    $response = app(CareerCandidateController::class)->exportStage('psychology_test');
    $this->assertStringEndsWith('.csv', $response->headers->get('content-disposition'));
    $this->assertSame('text/csv; charset=UTF-8', $response->headers->get('content-type'));
    $rows = $this->csvRows($response);

    $this->assertSame(['Candidate Code', 'Nama Kandidat', 'Email', 'Posisi', 'Tahap', 'Tanggal Mendaftar', 'Link Download CV'], $rows[0]);
    $this->assertSame('PSI-001', $rows[1][0]);
    $this->assertSame('Kandidat Psikotes', $rows[1][1]);
    $this->assertSame('candidate@example.test', $rows[1][2]);
    $this->assertSame(route('admin.career_candidates.cv', $candidate), $rows[1][6]);
    $this->assertSame('CV tidak tersedia', $rows[2][6]);
    $this->assertCount(3, $rows);

    $screeningRows = $this->csvRows(app(CareerCandidateController::class)->exportStage('screening_cv'));
    $this->assertSame(
      ['Candidate Code', 'Nama Kandidat', 'Email', 'Posisi', 'Tahap', 'Tanggal Mendaftar'],
      $screeningRows[0],
    );
  }

  public function test_email_templates_can_be_created_updated_and_deleted(): void
  {
    $controller = app(CareerCandidateController::class);
    $controller->storeEmailTemplate(Request::create('/', 'POST', [
      'name' => 'Undangan Psikotes',
      'subject' => 'Undangan untuk {{name}}',
      'body' => 'Kode kandidat: {{candidate_code}}',
    ]));

    $template = CareerEmailTemplate::query()->firstOrFail();
    $this->assertSame('Undangan untuk {{name}}', $template->subject);
    $this->assertSame('Kode kandidat: {{candidate_code}}', $template->body);

    $controller->updateEmailTemplate(Request::create('/', 'PUT', [
      'name' => 'Undangan Psikotes Online',
      'subject' => 'Tes {{position}}',
      'body' => 'Halo {{name}} ({{email}})',
    ]), $template);

    $this->assertDatabaseHas('career_email_templates', [
      'id' => $template->id,
      'name' => 'Undangan Psikotes Online',
      'subject' => 'Tes {{position}}',
    ]);

    $controller->destroyEmailTemplate($template);
    $this->assertDatabaseMissing('career_email_templates', ['id' => $template->id]);
  }

  public function test_failed_candidate_export_downloads_excel_compatible_csv(): void
  {
    CareerCandidate::create([
      'candidate_code' => 'FAILED-001',
      'full_name' => 'Kandidat Gagal',
      'email' => 'failed@example.test',
      'phone' => '081234567890',
      'domicile' => 'Jakarta',
      'experience_range' => '1_to_2',
      'job_title' => 'Tax Officer',
      'recruitment_stage' => 'psychology_test',
      'selection_status' => 'rejected',
      'stage_notes' => 'Tidak memenuhi kriteria.',
      'processed_at' => now(),
    ]);

    $response = app(CareerCandidateController::class)->exportFailed();
    $this->assertStringEndsWith('.csv', $response->headers->get('content-disposition'));
    $rows = $this->csvRows($response);

    $this->assertSame('Candidate Code', $rows[0][0]);
    $this->assertSame('FAILED-001', $rows[1][0]);
    $this->assertSame('Kandidat Gagal', $rows[1][1]);
    $this->assertSame('failed@example.test', $rows[1][2]);
    $this->assertSame('Tidak memenuhi kriteria.', $rows[1][7]);
  }

  public function test_stage_csv_export_sanitizes_formula_like_values(): void
  {
    CareerCandidate::create([
      'candidate_code' => 'PSI-003',
      'full_name' => '=HYPERLINK("https://example.test")',
      'email' => 'candidate3@example.test',
      'job_title' => 'Tax Officer',
      'recruitment_stage' => 'psychology_test',
    ]);

    $rows = $this->csvRows(app(CareerCandidateController::class)->exportStage('psychology_test'));

    $this->assertSame('\'=HYPERLINK("https://example.test")', $rows[1][1]);
  }

  public function test_rejecting_candidate_preserves_cv_and_candidate_details(): void
  {
    Storage::fake('local');
    $cvPath = 'career/cv/active-candidate.pdf';
    Storage::disk('local')->put($cvPath, '%PDF-1.4');
    $candidate = CareerCandidate::create([
      'candidate_code' => 'TAX-001',
      'full_name' => 'Kandidat Dipulihkan',
      'email' => 'candidate@example.test',
      'phone' => '081234567890',
      'job_title' => 'Tax Officer',
      'cv_path' => $cvPath,
      'cv_original_name' => 'candidate.pdf',
      'cv_mime' => 'application/pdf',
      'cv_size' => 8,
      'recruitment_stage' => 'screening_cv',
    ]);

    app(CareerCandidateController::class)->reject(
      Request::create('/', 'PUT', ['rejection_reason' => 'Salah klik']),
      $candidate,
    );

    $candidate->refresh();
    $this->assertSame('rejected', $candidate->selection_status);
    $this->assertSame('Tax Officer', $candidate->job_title);
    $this->assertSame('TAX-001', $candidate->candidate_code);
    $this->assertSame($cvPath, $candidate->cv_path);
    Storage::disk('local')->assertExists($cvPath);
  }

  public function test_restoring_existing_failed_candidate_reuses_saved_cv(): void
  {
    Storage::fake('local');
    $cvPath = 'career/cv/failed-candidate.pdf';
    Storage::disk('local')->put($cvPath, '%PDF-1.4');
    $candidate = CareerCandidate::create([
      'candidate_code' => 'TAX-002',
      'full_name' => 'Kandidat Salah Status',
      'email' => 'candidate2@example.test',
      'phone' => '081234567891',
      'job_title' => 'Tax Officer',
      'cv_path' => $cvPath,
      'cv_original_name' => 'candidate2.pdf',
      'cv_mime' => 'application/pdf',
      'cv_size' => 8,
      'recruitment_stage' => 'psychology_test',
      'selection_status' => 'rejected',
      'stage_notes' => 'Salah klik',
    ]);

    app(CareerCandidateController::class)->restore(Request::create('/', 'PUT'), $candidate);

    $candidate->refresh();
    $this->assertSame('screening_cv', $candidate->recruitment_stage);
    $this->assertSame('in_process', $candidate->selection_status);
    $this->assertNull($candidate->stage_notes);
    $this->assertSame('TAX-002', $candidate->candidate_code);
    Storage::disk('local')->assertExists($cvPath);
  }

  public function test_restoring_old_failed_candidate_does_not_require_a_missing_pdf(): void
  {
    Storage::fake('local');
    $candidate = CareerCandidate::create([
      'candidate_code' => null,
      'full_name' => 'Kandidat Lama',
      'email' => 'legacy@example.test',
      'phone' => '081234567892',
      'domicile' => 'Jakarta',
      'job_title' => null,
      'cv_path' => null,
      'recruitment_stage' => 'psychology_test',
      'selection_status' => 'rejected',
    ]);
    $request = Request::create('/', 'PUT', ['job_title' => 'Tax Officer']);

    app(CareerCandidateController::class)->restore($request, $candidate);

    $candidate->refresh();
    $this->assertSame('screening_cv', $candidate->recruitment_stage);
    $this->assertSame('Tax Officer', $candidate->job_title);
    $this->assertNotEmpty($candidate->candidate_code);
    $this->assertNull($candidate->cv_path);
  }

  private function csvRows($response): array
  {
    ob_start();
    $response->sendContent();
    $csv = ob_get_clean();
    $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);

    $stream = fopen('php://temp', 'r+');
    fwrite($stream, substr($csv, 3));
    rewind($stream);
    $rows = [];
    while (($row = fgetcsv($stream, null, ',', '"', '')) !== false) {
      $rows[] = $row;
    }
    fclose($stream);

    return $rows;
  }
}
