@extends('layouts.master')

@section('title', 'Home Sections | ILSAM')
@section('title-sub', 'Website')
@section('pagetitle', 'Home Sections')

@section('content')
  @php
    $settings = $settings ?? [];
    $companiesText = $companiesText ?? '';
  @endphp

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">Text Slider (Companies)</h5>
          <div class="text-muted small">Konten ini dipakai untuk text slider di Home (dan Contact jika dipakai).</div>
        </div>

        <div class="card-body">
          @if($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('admin.website_home_sections.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <label class="form-label">Companies (1 per line)</label>
              <textarea class="form-control" name="home[text_slider_companies_text]" rows="14" placeholder="Company A\nCompany B\nCompany C">{{ old('home.text_slider_companies_text', $companiesText) }}</textarea>
              <div class="form-text">Empty lines akan diabaikan.</div>
            </div>

            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-primary">Save Home Sections</button>
            </div>
          </form>

          <hr />

          <div class="text-muted small">
            Saat ini tersimpan: {{ is_array(data_get($settings, 'home.text_slider_companies')) ? count(data_get($settings, 'home.text_slider_companies')) : 0 }} item.
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
