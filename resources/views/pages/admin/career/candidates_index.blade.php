@extends('layouts.master')

@section('title', 'Career Candidates | ILSAM')
@section('title-sub', 'Career')
@section('pagetitle', 'Career Candidates')

@section('content')
  @php
    $candidates = $candidates ?? collect();
    $q = $q ?? '';
  @endphp

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="row g-3">
    <div class="col-12">
      <div class="card">
        <div
          class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
          <div>
            <h5 class="card-title mb-0">Candidate Submissions</h5>
            <div class="text-muted small">Applications submitted from the public Career page.</div>
          </div>
          <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.careers.index') }}" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-arrow-left"></i> Back to Career Management
            </a>
          </div>
        </div>
        <div class="card-body">
          <form method="GET" class="row g-2 align-items-end mb-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Search</label>
              <input type="text" name="q" value="{{ $q }}" class="form-control"
                placeholder="Name, email, phone, position...">
            </div>
            <div class="col-12 col-md-auto">
              <button class="btn btn-primary" type="submit">Search</button>
            </div>
            <div class="col-12 col-md-auto">
              <a class="btn btn-outline-secondary" href="{{ route('admin.career_candidates.index') }}">Reset</a>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
              <thead>
                <tr>
                  <th style="width: 170px;">Submitted</th>
                  <th>Candidate</th>
                  <th>Position</th>
                  <th style="width: 160px;">Contact</th>
                  <th style="width: 140px;">CV</th>
                </tr>
              </thead>
              <tbody>
                @forelse($candidates as $c)
                  <tr>
                    <td>
                      <div class="fw-semibold">{{ optional($c->created_at)->format('d M Y') }}</div>
                      <div class="text-muted small">{{ optional($c->created_at)->format('H:i') }}</div>
                    </td>
                    <td>
                      <div class="fw-semibold">{{ $c->full_name }}</div>
                      <div class="text-muted small">{{ $c->email }}</div>
                      @if(!empty($c->message))
                        <div class="small mt-1" style="max-width: 560px;">
                          {{ \Illuminate\Support\Str::limit($c->message, 140) }}
                        </div>
                      @endif
                    </td>
                    <td>
                      <div class="fw-semibold">{{ $c->job_title ?? '-' }}</div>
                      <div class="text-muted small">{{ $c->domicile ?? '-' }}</div>
                    </td>
                    <td>
                      <div class="small"><b>Phone:</b> {{ $c->phone }}</div>
                      @if($c->linkedin_url)
                        <div class="small"><a href="{{ $c->linkedin_url }}" target="_blank" rel="noopener">LinkedIn</a></div>
                      @endif
                      @if($c->portfolio_url)
                        <div class="small"><a href="{{ $c->portfolio_url }}" target="_blank" rel="noopener">Portfolio</a>
                        </div>
                      @endif
                    </td>
                    <td>
                      <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.career_candidates.cv', $c) }}">
                        <i class="bi bi-download"></i> Download
                      </a>
                      <div class="text-muted small mt-1">
                        {{ $c->cv_original_name }}
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5">
                      <div class="alert alert-info mb-0">No candidate submissions yet.</div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          @if(method_exists($candidates, 'links'))
            <div class="d-flex justify-content-end">
              {{ $candidates->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection