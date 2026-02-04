@extends('layouts.master')

@section('title', 'Contact Page | ILSAM')
@section('title-sub', 'Website')
@section('pagetitle', 'Contact Page')

@section('content')
  @php
    $settings = $settings ?? [];
    $locales = $locales ?? ['en' => 'English'];

    $md = data_get($settings, 'seo.contact.meta_description', []);
    if (!is_array($md)) {
      $md = [];
    }
  @endphp

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0">Contact Page</h5>
            <div class="text-muted small">SEO, map embed, backgrounds, and contact form recipient.</div>
          </div>
          <a href="{{ route('contact') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-external-link-alt"></i> Preview Contact
          </a>
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

          <form method="POST" action="{{ route('admin.website_contact_page.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">
              <div class="col-12">
                <h6 class="mb-1">Contact Form</h6>
                <div class="text-muted small">Email tujuan penerima pesan dari form contact.</div>
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Recipient Email</label>
                <input class="form-control" name="contact[form_recipient_email]" value="{{ old('contact.form_recipient_email', data_get($settings, 'contact.form_recipient_email')) }}">
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Opening Hours (override)</label>
                <input class="form-control" name="contact[opening_hours]" value="{{ old('contact.opening_hours', data_get($settings, 'contact.opening_hours')) }}" placeholder="Monday - Friday : 08:00 - 17:00">
              </div>

              <hr class="my-2" />

              <div class="col-12">
                <h6 class="mb-1">Map</h6>
                <div class="text-muted small">Google Maps embed URL untuk iframe.</div>
              </div>

              <div class="col-12">
                <label class="form-label">Map Embed Src</label>
                <input class="form-control" name="contact[map_embed_src]" value="{{ old('contact.map_embed_src', data_get($settings, 'contact.map_embed_src')) }}">
              </div>

              <hr class="my-2" />

              <div class="col-12">
                <h6 class="mb-1">Images</h6>
                <div class="text-muted small">Bisa path (contoh: <code>assets/img/img14.jpg</code>) atau URL penuh.</div>
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Breadcrumb Background</label>
                <input class="form-control" name="contact[page][breadcrumb_bg]" value="{{ old('contact.page.breadcrumb_bg', data_get($settings, 'contact.page.breadcrumb_bg')) }}">
                @include('partials.admin.image-preview', [
                  'raw' => old('contact.page.breadcrumb_bg', data_get($settings, 'contact.page.breadcrumb_bg')),
                  'alt' => 'Breadcrumb background',
                  'maxHeight' => 60,
                ])
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Breadcrumb Background Upload</label>
                <input class="form-control" type="file" name="contact[page][breadcrumb_bg_file]" accept="image/*">
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Lets Talk Section Background</label>
                <input class="form-control" name="contact[page][lets_talk_bg]" value="{{ old('contact.page.lets_talk_bg', data_get($settings, 'contact.page.lets_talk_bg')) }}">
                @include('partials.admin.image-preview', [
                  'raw' => old('contact.page.lets_talk_bg', data_get($settings, 'contact.page.lets_talk_bg')),
                  'alt' => 'Lets talk background',
                  'maxHeight' => 60,
                ])
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Lets Talk Background Upload</label>
                <input class="form-control" type="file" name="contact[page][lets_talk_bg_file]" accept="image/*">
              </div>

              <hr class="my-2" />

              <div class="col-12">
                <h6 class="mb-1">SEO</h6>
              </div>

              @foreach($locales as $code => $label)
                <div class="col-12">
                  <label class="form-label">Meta Description ({{ $label }})</label>
                  <textarea class="form-control" name="seo[contact][meta_description][{{ $code }}]" rows="2">{{ old('seo.contact.meta_description.' . $code, $md[$code] ?? '') }}</textarea>
                </div>
              @endforeach

              <div class="col-12">
                <label class="form-label">Meta Image (URL/path)</label>
                <input class="form-control" name="seo[contact][meta_image]" value="{{ old('seo.contact.meta_image', data_get($settings, 'seo.contact.meta_image')) }}">
                @include('partials.admin.image-preview', [
                  'raw' => old('seo.contact.meta_image', data_get($settings, 'seo.contact.meta_image')),
                  'alt' => 'Contact meta image',
                  'maxHeight' => 60,
                ])
              </div>

              <div class="col-12">
                <label class="form-label">Meta Image Upload</label>
                <input class="form-control" type="file" name="seo[contact][meta_image_file]" accept="image/*">
              </div>

              <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Save Contact Page</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
