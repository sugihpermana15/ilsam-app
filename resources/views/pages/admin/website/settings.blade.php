@extends('layouts.master')

@section('title', 'Website Settings | ILSAM')
@section('title-sub', 'Website')
@section('pagetitle', 'Website Settings')

@section('content')
  @php
    $settings = $settings ?? [];
    $locales = $locales ?? ['en' => 'English'];

    $slides = data_get($settings, 'home.hero_slides', []);
    if (!is_array($slides)) {
      $slides = [];
    }
    $slidesText = implode("\n", array_map(fn($v) => (string) $v, $slides));

    $md = data_get($settings, 'seo.home.meta_description', []);
    if (!is_array($md)) {
      $md = [];
    }
  @endphp

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0">Website Settings</h5>
            <div class="text-muted small">Konten header/footer + SEO homepage + slider images.</div>
          </div>
          <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-external-link-alt"></i> Preview Website
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

          <form method="POST" action="{{ route('admin.website_settings.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">
              <div class="col-12">
                <h6 class="mb-1">Contact</h6>
                <div class="text-muted small">Dipakai di navbar/footer/offcanvas.</div>
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Phone (display)</label>
                <input class="form-control" name="contact[phone_display]" value="{{ old('contact.phone_display', data_get($settings, 'contact.phone_display')) }}">
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Phone (tel)</label>
                <input class="form-control" name="contact[phone_tel]" value="{{ old('contact.phone_tel', data_get($settings, 'contact.phone_tel')) }}" placeholder="02189830313">
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Phone Alt (display)</label>
                <input class="form-control" name="contact[phone_display_alt]" value="{{ old('contact.phone_display_alt', data_get($settings, 'contact.phone_display_alt')) }}">
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Phone Alt (tel)</label>
                <input class="form-control" name="contact[phone_tel_alt]" value="{{ old('contact.phone_tel_alt', data_get($settings, 'contact.phone_tel_alt')) }}" placeholder="02189830314">
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Email</label>
                <input class="form-control" name="contact[email]" value="{{ old('contact.email', data_get($settings, 'contact.email')) }}">
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Map URL</label>
                <input class="form-control" name="contact[map_url]" value="{{ old('contact.map_url', data_get($settings, 'contact.map_url')) }}">
              </div>

              <div class="col-12">
                <label class="form-label">Address (text)</label>
                <textarea class="form-control" name="contact[address_text]" rows="3">{{ old('contact.address_text', data_get($settings, 'contact.address_text')) }}</textarea>
              </div>

              <hr class="my-2" />

              <div class="col-12">
                <h6 class="mb-1">Top Bar</h6>
                <div class="text-muted small">Link website di header (desktop).</div>
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Website URL</label>
                <input class="form-control" name="top[website_url]" value="{{ old('top.website_url', data_get($settings, 'top.website_url')) }}">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Website Label</label>
                <input class="form-control" name="top[website_label]" value="{{ old('top.website_label', data_get($settings, 'top.website_label')) }}" placeholder="www.ilsam.com">
              </div>

              <hr class="my-2" />

              <div class="col-12">
                <h6 class="mb-1">Offcanvas</h6>
                <div class="text-muted small">Quick links (mobile sidebar).</div>
              </div>

              <div class="col-12 col-md-4">
                <label class="form-label">Website URL</label>
                <input class="form-control" name="offcanvas[website_url]" value="{{ old('offcanvas.website_url', data_get($settings, 'offcanvas.website_url')) }}">
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label">Email</label>
                <input class="form-control" name="offcanvas[email]" value="{{ old('offcanvas.email', data_get($settings, 'offcanvas.email')) }}">
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label">Location URL</label>
                <input class="form-control" name="offcanvas[location_url]" value="{{ old('offcanvas.location_url', data_get($settings, 'offcanvas.location_url')) }}">
              </div>

              <hr class="my-2" />

              <div class="col-12">
                <h6 class="mb-1">Downloads</h6>
                <div class="text-muted small">Link download company profile di navbar.</div>
              </div>
              <div class="col-12">
                <label class="form-label">Company Profile URL</label>
                <input class="form-control" name="downloads[company_profile_url]" value="{{ old('downloads.company_profile_url', data_get($settings, 'downloads.company_profile_url')) }}">
              </div>

              <hr class="my-2" />

              <div class="col-12">
                <h6 class="mb-1">SEO - Home</h6>
                <div class="text-muted small">Meta description per bahasa.</div>
              </div>

              @foreach($locales as $code => $label)
                <div class="col-12">
                  <label class="form-label">Meta Description ({{ $label }})</label>
                  <textarea class="form-control" name="seo[home][meta_description][{{ $code }}]" rows="2">{{ old('seo.home.meta_description.' . $code, $md[$code] ?? '') }}</textarea>
                </div>
              @endforeach

              <div class="col-12">
                <label class="form-label">Meta Image (URL/path)</label>
                <input class="form-control" name="seo[home][meta_image]" value="{{ old('seo.home.meta_image', data_get($settings, 'seo.home.meta_image')) }}">
                @include('partials.admin.image-preview', [
                  'raw' => data_get($settings, 'seo.home.meta_image'),
                  'alt' => 'Home Meta Image',
                  'maxHeight' => 60,
                ])
              </div>

              <div class="col-12">
                <label class="form-label">Meta Image Upload (Home)</label>
                <input class="form-control" type="file" name="seo[home][meta_image_file]" accept="image/*">
                <div class="form-text">Upload akan mengganti field Meta Image di atas.</div>
              </div>

              <hr class="my-2" />

              <div class="col-12">
                <h6 class="mb-1">Home - Hero Slides</h6>
                <div class="text-muted small">Satu path/URL per baris (contoh: <code>assets/img/img1.jpg</code> atau URL penuh). Untuk path lokal akan diproses via route <code>/img</code>.</div>
              </div>

              <div class="col-12">
                <textarea class="form-control" name="home[hero_slides_text]" rows="6">{{ old('home.hero_slides_text', $slidesText) }}</textarea>
                @if(is_array($slides) && count($slides) > 0)
                  <div class="mt-2 d-flex flex-wrap" style="gap: 8px;">
                    @foreach($slides as $slide)
                      @php
                        $slide = is_string($slide) ? trim($slide) : '';
                        if ($slide === '') {
                          continue;
                        }

                        $slideUrl = preg_match('~^https?://~i', $slide)
                          ? $slide
                          : route('img', ['path' => ltrim($slide, '/'), 'w' => 240, 'q' => 65]);
                      @endphp
                      <img src="{{ $slideUrl }}" alt="Hero Slide" class="border rounded" style="max-height: 60px; width:auto;" loading="lazy" decoding="async">
                    @endforeach
                  </div>
                @endif
              </div>

              <div class="col-12">
                <label class="form-label">Upload Hero Slides (append)</label>
                <input class="form-control" type="file" name="home[hero_slides_files][]" accept="image/*" multiple>
                <div class="form-text">Gambar yang diupload akan ditambahkan ke daftar slides (append).</div>
              </div>

              <hr class="my-2" />

              <div class="col-12">
                <h6 class="mb-1">Home - Decorations</h6>
                <div class="text-muted small">Gambar dekorasi untuk banner & section products.</div>
              </div>

              <div class="col-12 col-lg-6">
                <label class="form-label">Banner Shape 2 (URL/path)</label>
                <input class="form-control" name="home[decorations][banner_shape_2]" value="{{ old('home.decorations.banner_shape_2', data_get($settings, 'home.decorations.banner_shape_2')) }}">
                @include('partials.admin.image-preview', [
                  'raw' => old('home.decorations.banner_shape_2', data_get($settings, 'home.decorations.banner_shape_2')),
                  'alt' => 'Banner shape 2',
                  'maxHeight' => 60,
                ])
              </div>
              <div class="col-12 col-lg-6">
                <label class="form-label">Banner Shape 2 Upload</label>
                <input class="form-control" type="file" name="home[decorations][banner_shape_2_file]" accept="image/*">
              </div>

              <div class="col-12 col-lg-6">
                <label class="form-label">Banner Shape 3 (URL/path)</label>
                <input class="form-control" name="home[decorations][banner_shape_3]" value="{{ old('home.decorations.banner_shape_3', data_get($settings, 'home.decorations.banner_shape_3')) }}">
                @include('partials.admin.image-preview', [
                  'raw' => old('home.decorations.banner_shape_3', data_get($settings, 'home.decorations.banner_shape_3')),
                  'alt' => 'Banner shape 3',
                  'maxHeight' => 60,
                ])
              </div>
              <div class="col-12 col-lg-6">
                <label class="form-label">Banner Shape 3 Upload</label>
                <input class="form-control" type="file" name="home[decorations][banner_shape_3_file]" accept="image/*">
              </div>

              <div class="col-12 col-lg-6">
                <label class="form-label">Products Shape 2 (URL/path)</label>
                <input class="form-control" name="home[decorations][products_shape_2]" value="{{ old('home.decorations.products_shape_2', data_get($settings, 'home.decorations.products_shape_2')) }}">
                @include('partials.admin.image-preview', [
                  'raw' => old('home.decorations.products_shape_2', data_get($settings, 'home.decorations.products_shape_2')),
                  'alt' => 'Products shape 2',
                  'maxHeight' => 60,
                ])
              </div>
              <div class="col-12 col-lg-6">
                <label class="form-label">Products Shape 2 Upload</label>
                <input class="form-control" type="file" name="home[decorations][products_shape_2_file]" accept="image/*">
              </div>

              <div class="col-12 col-lg-6">
                <label class="form-label">Products Shape 3 (URL/path)</label>
                <input class="form-control" name="home[decorations][products_shape_3]" value="{{ old('home.decorations.products_shape_3', data_get($settings, 'home.decorations.products_shape_3')) }}">
                @include('partials.admin.image-preview', [
                  'raw' => old('home.decorations.products_shape_3', data_get($settings, 'home.decorations.products_shape_3')),
                  'alt' => 'Products shape 3',
                  'maxHeight' => 60,
                ])
              </div>
              <div class="col-12 col-lg-6">
                <label class="form-label">Products Shape 3 Upload</label>
                <input class="form-control" type="file" name="home[decorations][products_shape_3_file]" accept="image/*">
              </div>

              <div class="col-12 col-lg-6">
                <label class="form-label">Products Shape 4 (URL/path)</label>
                <input class="form-control" name="home[decorations][products_shape_4]" value="{{ old('home.decorations.products_shape_4', data_get($settings, 'home.decorations.products_shape_4')) }}">
                @include('partials.admin.image-preview', [
                  'raw' => old('home.decorations.products_shape_4', data_get($settings, 'home.decorations.products_shape_4')),
                  'alt' => 'Products shape 4',
                  'maxHeight' => 60,
                ])
              </div>
              <div class="col-12 col-lg-6">
                <label class="form-label">Products Shape 4 Upload</label>
                <input class="form-control" type="file" name="home[decorations][products_shape_4_file]" accept="image/*">
              </div>

              <hr class="my-2" />

              <div class="col-12">
                <h6 class="mb-1">Home - Section Images</h6>
                <div class="text-muted small">Gambar untuk section About & Experience di Home.</div>
              </div>

              <div class="col-12 col-lg-6">
                <label class="form-label">About Image (URL/path)</label>
                <input class="form-control" name="home[sections][about_image]" value="{{ old('home.sections.about_image', data_get($settings, 'home.sections.about_image')) }}">
                @include('partials.admin.image-preview', [
                  'raw' => old('home.sections.about_image', data_get($settings, 'home.sections.about_image')),
                  'alt' => 'Home about image',
                  'maxHeight' => 70,
                ])
              </div>
              <div class="col-12 col-lg-6">
                <label class="form-label">About Image Upload</label>
                <input class="form-control" type="file" name="home[sections][about_image_file]" accept="image/*">
              </div>

              <div class="col-12 col-lg-6">
                <label class="form-label">Experience Background (URL/path)</label>
                <input class="form-control" name="home[sections][experience_bg]" value="{{ old('home.sections.experience_bg', data_get($settings, 'home.sections.experience_bg')) }}">
                @include('partials.admin.image-preview', [
                  'raw' => old('home.sections.experience_bg', data_get($settings, 'home.sections.experience_bg')),
                  'alt' => 'Experience background',
                  'maxHeight' => 70,
                ])
              </div>
              <div class="col-12 col-lg-6">
                <label class="form-label">Experience Background Upload</label>
                <input class="form-control" type="file" name="home[sections][experience_bg_file]" accept="image/*">
              </div>

              <hr class="my-2" />

              <div class="col-12">
                <h6 class="mb-1">Home - Product Cards Backgrounds</h6>
                <div class="text-muted small">Background image untuk kartu produk di Home.</div>
              </div>

              <div class="col-12 col-lg-6">
                <label class="form-label">Card: Colorants BG (URL/path)</label>
                <input class="form-control" name="home[sections][products_cards][colorants_bg]" value="{{ old('home.sections.products_cards.colorants_bg', data_get($settings, 'home.sections.products_cards.colorants_bg')) }}">
                @include('partials.admin.image-preview', [
                  'raw' => old('home.sections.products_cards.colorants_bg', data_get($settings, 'home.sections.products_cards.colorants_bg')),
                  'alt' => 'Products card colorants bg',
                  'maxHeight' => 70,
                ])
              </div>
              <div class="col-12 col-lg-6">
                <label class="form-label">Upload: Colorants BG</label>
                <input class="form-control" type="file" name="home[sections][products_cards][colorants_bg_file]" accept="image/*">
              </div>

              <div class="col-12 col-lg-6">
                <label class="form-label">Card: Surface Coating Agents BG (URL/path)</label>
                <input class="form-control" name="home[sections][products_cards][surface_coating_agents_bg]" value="{{ old('home.sections.products_cards.surface_coating_agents_bg', data_get($settings, 'home.sections.products_cards.surface_coating_agents_bg')) }}">
                @include('partials.admin.image-preview', [
                  'raw' => old('home.sections.products_cards.surface_coating_agents_bg', data_get($settings, 'home.sections.products_cards.surface_coating_agents_bg')),
                  'alt' => 'Products card surface coating agents bg',
                  'maxHeight' => 70,
                ])
              </div>
              <div class="col-12 col-lg-6">
                <label class="form-label">Upload: Surface Coating Agents BG</label>
                <input class="form-control" type="file" name="home[sections][products_cards][surface_coating_agents_bg_file]" accept="image/*">
              </div>

              <div class="col-12 col-lg-6">
                <label class="form-label">Card: Additive Coating BG (URL/path)</label>
                <input class="form-control" name="home[sections][products_cards][additive_coating_bg]" value="{{ old('home.sections.products_cards.additive_coating_bg', data_get($settings, 'home.sections.products_cards.additive_coating_bg')) }}">
                @include('partials.admin.image-preview', [
                  'raw' => old('home.sections.products_cards.additive_coating_bg', data_get($settings, 'home.sections.products_cards.additive_coating_bg')),
                  'alt' => 'Products card additive coating bg',
                  'maxHeight' => 70,
                ])
              </div>
              <div class="col-12 col-lg-6">
                <label class="form-label">Upload: Additive Coating BG</label>
                <input class="form-control" type="file" name="home[sections][products_cards][additive_coating_bg_file]" accept="image/*">
              </div>

              <div class="col-12 col-lg-6">
                <label class="form-label">Card: PU Resin BG (URL/path)</label>
                <input class="form-control" name="home[sections][products_cards][pu_resin_bg]" value="{{ old('home.sections.products_cards.pu_resin_bg', data_get($settings, 'home.sections.products_cards.pu_resin_bg')) }}">
                @include('partials.admin.image-preview', [
                  'raw' => old('home.sections.products_cards.pu_resin_bg', data_get($settings, 'home.sections.products_cards.pu_resin_bg')),
                  'alt' => 'Products card pu resin bg',
                  'maxHeight' => 70,
                ])
              </div>
              <div class="col-12 col-lg-6">
                <label class="form-label">Upload: PU Resin BG</label>
                <input class="form-control" type="file" name="home[sections][products_cards][pu_resin_bg_file]" accept="image/*">
              </div>

              <hr class="my-2" />

              <div class="col-12">
                <h6 class="mb-1">About Pages</h6>
                <div class="text-muted small">SEO + gambar untuk halaman Company / CEO / Philosophy.</div>
              </div>

              @php
                $aboutCompanyMd = data_get($settings, 'seo.about.company.meta_description', []);
                $aboutCeoMd = data_get($settings, 'seo.about.ceo.meta_description', []);
                $aboutPhilMd = data_get($settings, 'seo.about.philosophy.meta_description', []);
                if (!is_array($aboutCompanyMd)) { $aboutCompanyMd = []; }
                if (!is_array($aboutCeoMd)) { $aboutCeoMd = []; }
                if (!is_array($aboutPhilMd)) { $aboutPhilMd = []; }
              @endphp

              <div class="col-12">
                <h6 class="mb-0">Company Overview</h6>
              </div>

              @foreach($locales as $code => $label)
                <div class="col-12">
                  <label class="form-label">Meta Description ({{ $label }})</label>
                  <textarea class="form-control" name="seo[about][company][meta_description][{{ $code }}]" rows="2">{{ old('seo.about.company.meta_description.' . $code, $aboutCompanyMd[$code] ?? '') }}</textarea>
                </div>
              @endforeach

              <div class="col-12 col-md-6">
                <label class="form-label">Meta Image (URL/path)</label>
                <input class="form-control" name="seo[about][company][meta_image]" value="{{ old('seo.about.company.meta_image', data_get($settings, 'seo.about.company.meta_image')) }}">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Meta Image Upload</label>
                <input class="form-control" type="file" name="seo[about][company][meta_image_file]" accept="image/*">
              </div>
              <div class="col-12">
                @include('partials.admin.image-preview', [
                  'raw' => data_get($settings, 'seo.about.company.meta_image'),
                  'alt' => 'Company Overview Meta Image',
                  'maxHeight' => 60,
                ])
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Main Image (URL/path)</label>
                <input class="form-control" name="about[company][image]" value="{{ old('about.company.image', data_get($settings, 'about.company.image')) }}">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Main Image Upload</label>
                <input class="form-control" type="file" name="about[company][image_file]" accept="image/*">
              </div>
              <div class="col-12">
                @include('partials.admin.image-preview', [
                  'raw' => data_get($settings, 'about.company.image'),
                  'alt' => 'Company Overview Main Image',
                  'maxHeight' => 60,
                ])
              </div>

              <hr class="my-2" />

              <div class="col-12">
                <h6 class="mb-0">CEO Message</h6>
              </div>

              @foreach($locales as $code => $label)
                <div class="col-12">
                  <label class="form-label">Meta Description ({{ $label }})</label>
                  <textarea class="form-control" name="seo[about][ceo][meta_description][{{ $code }}]" rows="2">{{ old('seo.about.ceo.meta_description.' . $code, $aboutCeoMd[$code] ?? '') }}</textarea>
                </div>
              @endforeach

              <div class="col-12 col-md-6">
                <label class="form-label">Meta Image (URL/path)</label>
                <input class="form-control" name="seo[about][ceo][meta_image]" value="{{ old('seo.about.ceo.meta_image', data_get($settings, 'seo.about.ceo.meta_image')) }}">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Meta Image Upload</label>
                <input class="form-control" type="file" name="seo[about][ceo][meta_image_file]" accept="image/*">
              </div>
              <div class="col-12">
                @include('partials.admin.image-preview', [
                  'raw' => data_get($settings, 'seo.about.ceo.meta_image'),
                  'alt' => 'CEO Meta Image',
                  'maxHeight' => 60,
                ])
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Portrait Image (URL/path)</label>
                <input class="form-control" name="about[ceo][portrait_image]" value="{{ old('about.ceo.portrait_image', data_get($settings, 'about.ceo.portrait_image')) }}">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Portrait Image Upload</label>
                <input class="form-control" type="file" name="about[ceo][portrait_image_file]" accept="image/*">
              </div>
              <div class="col-12">
                @include('partials.admin.image-preview', [
                  'raw' => data_get($settings, 'about.ceo.portrait_image'),
                  'alt' => 'CEO Portrait Image',
                  'maxHeight' => 80,
                ])
              </div>

              <hr class="my-2" />

              <div class="col-12">
                <h6 class="mb-0">Business Philosophy</h6>
              </div>

              @foreach($locales as $code => $label)
                <div class="col-12">
                  <label class="form-label">Meta Description ({{ $label }})</label>
                  <textarea class="form-control" name="seo[about][philosophy][meta_description][{{ $code }}]" rows="2">{{ old('seo.about.philosophy.meta_description.' . $code, $aboutPhilMd[$code] ?? '') }}</textarea>
                </div>
              @endforeach

              <div class="col-12 col-md-6">
                <label class="form-label">Meta Image (URL/path)</label>
                <input class="form-control" name="seo[about][philosophy][meta_image]" value="{{ old('seo.about.philosophy.meta_image', data_get($settings, 'seo.about.philosophy.meta_image')) }}">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Meta Image Upload</label>
                <input class="form-control" type="file" name="seo[about][philosophy][meta_image_file]" accept="image/*">
              </div>
              <div class="col-12">
                @include('partials.admin.image-preview', [
                  'raw' => data_get($settings, 'seo.about.philosophy.meta_image'),
                  'alt' => 'Philosophy Meta Image',
                  'maxHeight' => 60,
                ])
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Hero Background (URL/path)</label>
                <input class="form-control" name="about[philosophy][hero_bg]" value="{{ old('about.philosophy.hero_bg', data_get($settings, 'about.philosophy.hero_bg')) }}">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Hero Background Upload</label>
                <input class="form-control" type="file" name="about[philosophy][hero_bg_file]" accept="image/*">
              </div>
              <div class="col-12">
                @include('partials.admin.image-preview', [
                  'raw' => data_get($settings, 'about.philosophy.hero_bg'),
                  'alt' => 'Philosophy Hero Background',
                  'maxHeight' => 60,
                ])
              </div>

              <hr class="my-2" />

              <div class="col-12">
                <h6 class="mb-1">SEO - Other Pages</h6>
                <div class="text-muted small">Career + Technology meta settings.</div>
              </div>

              @php
                $careerMd = data_get($settings, 'seo.career.meta_description', []);
                $techMd = data_get($settings, 'seo.technology.meta_description', []);
                $certMd = data_get($settings, 'seo.technology_certification_status.meta_description', []);
                if (!is_array($careerMd)) { $careerMd = []; }
                if (!is_array($techMd)) { $techMd = []; }
                if (!is_array($certMd)) { $certMd = []; }
              @endphp

              <div class="col-12">
                <h6 class="mb-0">Career</h6>
              </div>
              @foreach($locales as $code => $label)
                <div class="col-12">
                  <label class="form-label">Meta Description ({{ $label }})</label>
                  <textarea class="form-control" name="seo[career][meta_description][{{ $code }}]" rows="2">{{ old('seo.career.meta_description.' . $code, $careerMd[$code] ?? '') }}</textarea>
                </div>
              @endforeach

              <div class="col-12 col-md-6">
                <label class="form-label">Meta Image (URL/path)</label>
                <input class="form-control" name="seo[career][meta_image]" value="{{ old('seo.career.meta_image', data_get($settings, 'seo.career.meta_image')) }}">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Meta Image Upload</label>
                <input class="form-control" type="file" name="seo[career][meta_image_file]" accept="image/*">
              </div>
              <div class="col-12">
                @include('partials.admin.image-preview', [
                  'raw' => data_get($settings, 'seo.career.meta_image'),
                  'alt' => 'Career Meta Image',
                  'maxHeight' => 60,
                ])
              </div>

              <hr class="my-2" />

              <div class="col-12">
                <h6 class="mb-0">Technology</h6>
              </div>
              @foreach($locales as $code => $label)
                <div class="col-12">
                  <label class="form-label">Meta Description ({{ $label }})</label>
                  <textarea class="form-control" name="seo[technology][meta_description][{{ $code }}]" rows="2">{{ old('seo.technology.meta_description.' . $code, $techMd[$code] ?? '') }}</textarea>
                </div>
              @endforeach

              <div class="col-12 col-md-6">
                <label class="form-label">Meta Image (URL/path)</label>
                <input class="form-control" name="seo[technology][meta_image]" value="{{ old('seo.technology.meta_image', data_get($settings, 'seo.technology.meta_image')) }}">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Meta Image Upload</label>
                <input class="form-control" type="file" name="seo[technology][meta_image_file]" accept="image/*">
              </div>
              <div class="col-12">
                @include('partials.admin.image-preview', [
                  'raw' => data_get($settings, 'seo.technology.meta_image'),
                  'alt' => 'Technology Meta Image',
                  'maxHeight' => 60,
                ])
              </div>

              <hr class="my-2" />

              <div class="col-12">
                <h6 class="mb-0">Technology - Certification Status</h6>
              </div>
              @foreach($locales as $code => $label)
                <div class="col-12">
                  <label class="form-label">Meta Description ({{ $label }})</label>
                  <textarea class="form-control" name="seo[technology_certification_status][meta_description][{{ $code }}]" rows="2">{{ old('seo.technology_certification_status.meta_description.' . $code, $certMd[$code] ?? '') }}</textarea>
                </div>
              @endforeach

              <div class="col-12 col-md-6">
                <label class="form-label">Meta Image (URL/path)</label>
                <input class="form-control" name="seo[technology_certification_status][meta_image]" value="{{ old('seo.technology_certification_status.meta_image', data_get($settings, 'seo.technology_certification_status.meta_image')) }}">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Meta Image Upload</label>
                <input class="form-control" type="file" name="seo[technology_certification_status][meta_image_file]" accept="image/*">
              </div>
              <div class="col-12">
                @include('partials.admin.image-preview', [
                  'raw' => data_get($settings, 'seo.technology_certification_status.meta_image'),
                  'alt' => 'Certification Status Meta Image',
                  'maxHeight' => 60,
                ])
              </div>

              <hr class="my-2" />

              <div class="col-12">
                <h6 class="mb-1">Technology Page Images</h6>
                <div class="text-muted small">Background images for Technology page sections.</div>
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Technology Hero Background (URL/path)</label>
                <input class="form-control" name="technology[page][hero_bg]" value="{{ old('technology.page.hero_bg', data_get($settings, 'technology.page.hero_bg')) }}">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Technology Hero Background Upload</label>
                <input class="form-control" type="file" name="technology[page][hero_bg_file]" accept="image/*">
              </div>
              <div class="col-12">
                @include('partials.admin.image-preview', [
                  'raw' => data_get($settings, 'technology.page.hero_bg'),
                  'alt' => 'Technology Hero Background',
                  'maxHeight' => 60,
                ])
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Technology Workflow Background (URL/path)</label>
                <input class="form-control" name="technology[page][workflow_bg]" value="{{ old('technology.page.workflow_bg', data_get($settings, 'technology.page.workflow_bg')) }}">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Technology Workflow Background Upload</label>
                <input class="form-control" type="file" name="technology[page][workflow_bg_file]" accept="image/*">
              </div>
              <div class="col-12">
                @include('partials.admin.image-preview', [
                  'raw' => data_get($settings, 'technology.page.workflow_bg'),
                  'alt' => 'Technology Workflow Background',
                  'maxHeight' => 60,
                ])
              </div>

              <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Save Settings</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
