@extends('layouts.master')

@php
  $mode = $mode ?? 'create';
  $product = $product ?? [];
  $allowedSlugs = $allowedSlugs ?? [];

  $isEdit = $mode === 'edit';
  $action = $isEdit
    ? route('admin.website_products.update', $product['id'] ?? '')
    : route('admin.website_products.store');

  $method = $isEdit ? 'PUT' : 'POST';

  $pageTitle = $isEdit ? 'Edit Website Product' : 'Add Website Product';
@endphp

@section('title', $pageTitle . ' | ILSAM')
@section('title-sub', 'Website')
@section('pagetitle', $pageTitle)

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0">{{ $pageTitle }}</h5>
            <div class="text-muted small">Format: pakai pemisah <code>|</code> dan baris baru untuk list.</div>
          </div>
          <div class="d-flex gap-2">
            <a href="{{ route('admin.website_products.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
          </div>
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

          <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
            @csrf
            @if($isEdit)
              @method('PUT')
            @endif

            <div class="row g-3">
              <div class="col-12 col-lg-4">
                <label class="form-label">Slug</label>
                <select class="form-select" name="slug" required>
                  <option value="">Select slug</option>
                  @foreach($allowedSlugs as $s)
                    <option value="{{ $s }}" {{ old('slug', $product['slug'] ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                  @endforeach
                </select>
                <div class="form-text">Harus cocok dengan halaman publik yang sudah ada.</div>
              </div>

              <div class="col-12 col-lg-6">
                <label class="form-label">Title</label>
                <input class="form-control" name="title" value="{{ old('title', $product['title'] ?? '') }}" required>
              </div>

              <div class="col-12 col-lg-2">
                <label class="form-label">Sort</label>
                <input type="number" class="form-control" name="sort_order" min="0" max="9999" value="{{ old('sort_order', $product['sort_order'] ?? 0) }}">
              </div>

              <div class="col-12">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" {{ old('is_active', ($product['is_active'] ?? true) ? 1 : 0) ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_active">Active</label>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label">Tagline</label>
                <input class="form-control" name="tagline" value="{{ old('tagline', $product['tagline'] ?? '') }}">
              </div>

              <div class="col-12">
                <label class="form-label">Intro</label>
                <textarea class="form-control" name="intro" rows="3">{{ old('intro', $product['intro'] ?? '') }}</textarea>
              </div>

              <div class="col-12">
                <label class="form-label">Hero Image (URL/path)</label>
                <input class="form-control" name="heroImage" value="{{ old('heroImage', $product['heroImage'] ?? '') }}" placeholder="{{ asset('assets/img/img1.jpg') }}">
              </div>

              <div class="col-12">
                <label class="form-label">Hero Image Upload</label>
                <input class="form-control" type="file" name="heroImage_file" accept="image/*">
                <div class="form-text">Jika diupload, akan menimpa field Hero Image.</div>
              </div>

              <div class="col-12">
                <h6 class="mb-1">SEO</h6>
                <div class="text-muted small">Optional: override meta description + image untuk halaman produk.</div>
              </div>

              <div class="col-12">
                <label class="form-label">SEO Description</label>
                <textarea class="form-control" name="seoDescription" rows="2">{{ old('seoDescription', $product['seoDescription'] ?? '') }}</textarea>
              </div>

              <div class="col-12">
                <label class="form-label">SEO Image (URL/path)</label>
                <input class="form-control" name="seoImage" value="{{ old('seoImage', $product['seoImage'] ?? '') }}" placeholder="storage/website/... atau assets/img/...">
              </div>

              <div class="col-12">
                <label class="form-label">SEO Image Upload</label>
                <input class="form-control" type="file" name="seoImage_file" accept="image/*">
                <div class="form-text">Jika diupload, akan menimpa field SEO Image.</div>
              </div>

              <div class="col-12">
                <label class="form-label">Product Lines</label>
                <textarea class="form-control" name="lines_text" rows="5" placeholder="Title|Subtitle|CODE1,CODE2">{{ old('lines_text', $product['lines_text'] ?? '') }}</textarea>
                <div class="form-text">Satu baris per item. Contoh: <code>Solution-type Surface Coating Agent (PU &amp; PVC)|For leather systems|SUS</code></div>
              </div>

              <div class="col-12 col-lg-6">
                <label class="form-label">Applications Intro</label>
                <input class="form-control" name="applications_intro" value="{{ old('applications_intro', $product['applications_intro'] ?? '') }}" placeholder="Common applications include:">
              </div>

              <div class="col-12">
                <label class="form-label">Applications</label>
                <textarea class="form-control" name="applications_text" rows="4" placeholder="One per line">{{ old('applications_text', $product['applications_text'] ?? '') }}</textarea>
              </div>

              <div class="col-12">
                <label class="form-label">Capabilities</label>
                <textarea class="form-control" name="capabilities_text" rows="5" placeholder="Title|Desc|Icon">{{ old('capabilities_text', $product['capabilities_text'] ?? '') }}</textarea>
                <div class="form-text">Icon pakai class bootstrap icon (mis: <code>bi-gear</code>, <code>bi-truck</code>).</div>
              </div>

              <div class="col-12">
                <label class="form-label">Specs</label>
                <textarea class="form-control" name="specs_text" rows="5" placeholder="Key|Value">{{ old('specs_text', $product['specs_text'] ?? '') }}</textarea>
              </div>

              <div class="col-12 col-lg-6">
                <label class="form-label">CTA Button Text</label>
                <input class="form-control" name="cta_primary_text" value="{{ old('cta_primary_text', $product['cta_primary_text'] ?? '') }}" placeholder="Request a Quote">
              </div>

              <div class="col-12 col-lg-6">
                <label class="form-label">CTA Button URL</label>
                <input class="form-control" name="cta_primary_url" value="{{ old('cta_primary_url', $product['cta_primary_url'] ?? '') }}" placeholder="{{ route('contact') }}">
              </div>

              <div class="col-12 col-lg-6">
                <label class="form-label">CTA Heading</label>
                <input class="form-control" name="cta_heading" value="{{ old('cta_heading', $product['cta_heading'] ?? '') }}">
              </div>

              <div class="col-12">
                <label class="form-label">CTA Text</label>
                <textarea class="form-control" name="cta_text" rows="3">{{ old('cta_text', $product['cta_text'] ?? '') }}</textarea>
              </div>

              <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.website_products.index') }}" class="btn btn-light">Cancel</a>
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Save' }}</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
