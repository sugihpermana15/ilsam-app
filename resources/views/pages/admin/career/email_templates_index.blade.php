@extends('layouts.master')

@section('title', 'Template Email Kandidat | ILSAM')
@section('title-sub', 'Career')
@section('pagetitle', 'Template Email Kandidat')

@section('content')
  @php
    $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'career', 'update');
    $canDelete = \App\Support\MenuAccess::can(auth()->user(), 'career', 'delete');
  @endphp

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold">Template belum tersimpan. Periksa kembali:</div>
      <ul class="mb-0 mt-1">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
      <div>
        <h5 class="card-title mb-0">Template Email Kandidat</h5>
        <div class="text-muted small">Buat template yang nanti dapat dipersonalisasi dan dikirim melalui Outlook.</div>
      </div>
      <a href="{{ route('admin.career_candidates.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali ke Kandidat
      </a>
    </div>
    <div class="card-body">
      <div class="alert alert-info">
        <div class="fw-semibold mb-1">Placeholder personalisasi</div>
        <code>@{{name}}</code> Nama kandidat,
        <code>@{{email}}</code> Email kandidat,
        <code>@{{candidate_code}}</code> Candidate code,
        <code>@{{position}}</code> Posisi yang dilamar.
        Pengiriman email Outlook belum diaktifkan.
      </div>

      <div class="card border mb-4">
        <div class="card-header"><h6 class="mb-0">Buat Template Baru</h6></div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.career_candidates.email_templates.store') }}">
            @csrf
            <div class="row g-3">
              <div class="col-12 col-lg-4">
                <label for="new-template-name" class="form-label">Nama Template</label>
                <input id="new-template-name" name="name" class="form-control" maxlength="120" required value="{{ old('name') }}" {{ $canUpdate ? '' : 'disabled' }}>
              </div>
              <div class="col-12 col-lg-8">
                <label for="new-template-subject" class="form-label">Subjek Email</label>
                <input id="new-template-subject" name="subject" class="form-control" maxlength="255" required value="{{ old('subject') }}" {{ $canUpdate ? '' : 'disabled' }}>
              </div>
              <div class="col-12">
                <label for="new-template-body" class="form-label">Isi / Deskripsi Email</label>
                <textarea id="new-template-body" name="body" class="form-control" rows="7" maxlength="20000" required {{ $canUpdate ? '' : 'disabled' }}>{{ old('body') }}</textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-primary" {{ $canUpdate ? '' : 'disabled' }}>
                  <i class="fas fa-save"></i> Simpan Template
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <h6 class="mb-3">Template Tersimpan</h6>
      @forelse($templates as $template)
        <div class="card border mb-3">
          <div class="card-body">
            <form method="POST" action="{{ route('admin.career_candidates.email_templates.update', $template) }}">
              @csrf
              @method('PUT')
              <div class="row g-3">
                <div class="col-12 col-lg-4">
                  <label for="template-name-{{ $template->id }}" class="form-label">Nama Template</label>
                  <input id="template-name-{{ $template->id }}" name="name" class="form-control" maxlength="120" required value="{{ $template->name }}" {{ $canUpdate ? '' : 'disabled' }}>
                </div>
                <div class="col-12 col-lg-8">
                  <label for="template-subject-{{ $template->id }}" class="form-label">Subjek Email</label>
                  <input id="template-subject-{{ $template->id }}" name="subject" class="form-control" maxlength="255" required value="{{ $template->subject }}" {{ $canUpdate ? '' : 'disabled' }}>
                </div>
                <div class="col-12">
                  <label for="template-body-{{ $template->id }}" class="form-label">Isi / Deskripsi Email</label>
                  <textarea id="template-body-{{ $template->id }}" name="body" class="form-control" rows="7" maxlength="20000" required {{ $canUpdate ? '' : 'disabled' }}>{{ $template->body }}</textarea>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-primary btn-sm" {{ $canUpdate ? '' : 'disabled' }}>
                    <i class="fas fa-save"></i> Simpan Perubahan
                  </button>
                </div>
              </div>
            </form>
            <form method="POST" action="{{ route('admin.career_candidates.email_templates.destroy', $template) }}" class="mt-2" onsubmit="return confirm('Hapus template email ini?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-outline-danger btn-sm" {{ $canDelete ? '' : 'disabled' }}>
                <i class="fas fa-trash"></i> Hapus
              </button>
            </form>
          </div>
        </div>
      @empty
        <div class="alert alert-secondary mb-0">Belum ada template email.</div>
      @endforelse
    </div>
  </div>
@endsection
