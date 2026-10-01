@extends('layouts.master')

@section('title', 'Dashboard Career | ILSAM')
@section('title-sub', 'Career')
@section('pagetitle', 'Dashboard Career')

@section('content')
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
      <h5 class="mb-1">Dashboard Career</h5>
      <div class="text-muted small">Ringkasan lowongan dan aktivitas rekrutmen terkini.</div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.career_candidates.index') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-users"></i> Screening CV</a>
      <a href="{{ route('admin.careers.index') }}" class="btn btn-primary btn-sm"><i class="fas fa-briefcase"></i> Job Openings</a>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-6 col-xl-3"><div class="card h-100"><div class="card-body"><div class="text-muted small">Lowongan Aktif</div><div class="fs-3 fw-semibold text-primary">{{ number_format($stats['active_openings']) }}</div></div></div></div>
    <div class="col-6 col-xl-3"><div class="card h-100"><div class="card-body"><div class="text-muted small">Deadline 7 Hari</div><div class="fs-3 fw-semibold text-warning">{{ number_format($stats['expiring_openings']) }}</div></div></div></div>
    <div class="col-6 col-xl-3"><div class="card h-100"><div class="card-body"><div class="text-muted small">Kandidat Hari Ini</div><div class="fs-3 fw-semibold text-success">{{ number_format($stats['new_today']) }}</div><div class="small text-muted">{{ number_format($stats['new_this_week']) }} minggu ini</div></div></div></div>
    <div class="col-6 col-xl-3"><div class="card h-100"><div class="card-body"><div class="text-muted small">Talent Pool</div><div class="fs-3 fw-semibold text-info">{{ number_format($stats['talent_pool']) }}</div><a class="small" href="{{ route('admin.career_candidates.talent_pool') }}">Lihat kandidat</a></div></div></div>
  </div>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center"><h6 class="mb-0">Pipeline Seleksi</h6><a href="{{ route('admin.career_candidates.failed') }}" class="small text-danger">{{ number_format($stats['failed_candidates']) }} kandidat gagal</a></div>
        <div class="card-body">
          <div class="row g-3">
            @foreach($stages as $key => $label)
              <div class="col-6 col-md-4">
                <a href="{{ route('admin.career_candidates.datatable', ['stage' => $key]) }}" class="border rounded p-3 d-block text-decoration-none h-100">
                  <div class="text-muted small">{{ $label }}</div>
                  <div class="fs-4 fw-semibold text-dark">{{ number_format($pipelineCounts[$key] ?? 0) }}</div>
                </a>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center"><h6 class="mb-0">Kandidat Terbaru</h6><a href="{{ route('admin.career_candidates.index') }}" class="small">Lihat Screening CV</a></div>
        <div class="list-group list-group-flush">
          @forelse($recentCandidates as $candidate)
            <div class="list-group-item"><div class="fw-semibold">{{ $candidate->full_name }}</div><div class="small text-muted">{{ $candidate->job_title ?: '-' }} · {{ $stages[$candidate->recruitment_stage] ?? '-' }}</div><div class="small text-muted">{{ optional($candidate->created_at)->format('d M Y H:i') }}</div></div>
          @empty
            <div class="list-group-item text-muted">Belum ada kandidat aktif.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
@endsection