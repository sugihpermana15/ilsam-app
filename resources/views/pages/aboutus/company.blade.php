@extends('about')
@section('title', __('website.titles.company_overview'))
@section('breadcrumb_title', __('website.about.company_overview.title'))
@php
  $ws = \App\Support\WebsiteSettings::all();
  $locale = app()->getLocale();
  $fallbackLocale = config('app.fallback_locale', 'en');

  $metaDescription = data_get($ws, 'seo.about.company.meta_description.' . $locale);
  if (!is_string($metaDescription) || trim($metaDescription) === '') {
    $metaDescription = data_get($ws, 'seo.about.company.meta_description.' . $fallbackLocale);
  }
  if (!is_string($metaDescription) || trim($metaDescription) === '') {
    $metaDescription = 'Learn about PT ILSAM GLOBAL INDONESIA: our company overview, values, and journey. Explore our profile and milestones.';
  }

  $metaImageRaw = data_get($ws, 'seo.about.company.meta_image', 'assets/img/img8.jpeg');
  $metaImageRaw = is_string($metaImageRaw) && trim($metaImageRaw) !== '' ? trim($metaImageRaw) : 'assets/img/img8.jpeg';
  $metaImage = preg_match('~^https?://~i', $metaImageRaw) ? $metaImageRaw : asset(ltrim($metaImageRaw, '/'));

  $mainImageRaw = data_get($ws, 'about.company.image', 'assets/img/img8.jpeg');
  $mainImageRaw = is_string($mainImageRaw) && trim($mainImageRaw) !== '' ? trim($mainImageRaw) : 'assets/img/img8.jpeg';
  $mainImageUrl = preg_match('~^https?://~i', $mainImageRaw) ? $mainImageRaw : asset(ltrim($mainImageRaw, '/'));
@endphp

@section('meta_description', $metaDescription)
@section('meta_image', $metaImage)
@section('aboutus')
  <div class="service-details__content-media company-overview__media wow fadeInUp" data-wow-delay=".1s">
    <img
      src="{{ $mainImageUrl }}"
      loading="lazy"
      decoding="async"
      alt="{{ __('website.about.company_overview.image_alt') }}"
    >
  </div>

  <h2 class="heading text-50 wow fadeInUp" data-wow-delay=".2s">{{ __('website.about.company_overview.title') }}</h2>

  <p class="text text-18 wow fadeInUp" data-wow-delay=".25s">{{ __('website.about.company_overview.p1') }}</p>

  <p class="text text-18 wow fadeInUp" data-wow-delay=".3s">{{ __('website.about.company_overview.p2') }}</p>

  <p class="text text-18 wow fadeInUp" data-wow-delay=".35s">{{ __('website.about.company_overview.p3') }}</p>

  <p class="text text-18 mb-0 wow fadeInUp" data-wow-delay=".4s">{{ __('website.about.company_overview.p4') }}</p>

  <div class="mt-4 wow fadeInUp" data-wow-delay=".45s">
    <div class="about-timeline" role="list" aria-label="{{ __('website.about.company_overview.timeline_aria') }}">
      <div class="about-timeline__item is-active" role="listitem">
        <div class="about-timeline__node" aria-hidden="true">
          <span class="about-timeline__number">01</span>
        </div>
        <div class="about-timeline__body">
          <div class="about-timeline__meta">1999</div>
          <h3 class="about-timeline__title">{{ __('website.about.company_overview.timeline.1999.title') }}</h3>
          <p class="about-timeline__text mb-0">{{ __('website.about.company_overview.timeline.1999.desc') }}</p>
        </div>
      </div>

      <div class="about-timeline__item" role="listitem">
        <div class="about-timeline__node" aria-hidden="true">
          <span class="about-timeline__number">02</span>
        </div>
        <div class="about-timeline__body">
          <div class="about-timeline__meta">2001</div>
          <h3 class="about-timeline__title">{{ __('website.about.company_overview.timeline.2001.title') }}</h3>
          <p class="about-timeline__text mb-0">{{ __('website.about.company_overview.timeline.2001.desc') }}</p>
        </div>
      </div>

      <div class="about-timeline__item" role="listitem">
        <div class="about-timeline__node" aria-hidden="true">
          <span class="about-timeline__number">03</span>
        </div>
        <div class="about-timeline__body">
          <div class="about-timeline__meta">2005</div>
          <h3 class="about-timeline__title">{{ __('website.about.company_overview.timeline.2005.title') }}</h3>
          <p class="about-timeline__text mb-0">{{ __('website.about.company_overview.timeline.2005.desc') }}</p>
        </div>
      </div>

      <div class="about-timeline__item" role="listitem">
        <div class="about-timeline__node" aria-hidden="true">
          <span class="about-timeline__number">04</span>
        </div>
        <div class="about-timeline__body">
          <div class="about-timeline__meta">2024</div>
          <h3 class="about-timeline__title">{{ __('website.about.company_overview.timeline.2024.title') }}</h3>
          <p class="about-timeline__text mb-0">{{ __('website.about.company_overview.timeline.2024.desc') }}</p>
        </div>
      </div>
    </div>
  </div>
@endsection