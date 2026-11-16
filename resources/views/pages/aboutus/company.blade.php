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
@php
  $aboutStats = [
    ['value' => (int) data_get($ws, 'home.text.experience.established_value', 1999), 'label' => __('website.home.experience.established'), 'suffix' => ''],
    ['value' => (int) data_get($ws, 'home.text.experience.clients_value', 20), 'label' => __('website.home.experience.clients'), 'suffix' => '+'],
    ['value' => (int) data_get($ws, 'home.text.experience.years_experience_value', 27), 'label' => __('website.home.experience.years_experience'), 'suffix' => ''],
  ];
  $timelineYears = ['1999', '2001', '2005', '2024'];
@endphp
@section('aboutus')
  <section class="sp-section">
    <div class="container">
      <div class="row align-items-center gy-5 gx-xl-5">
        <div class="col-lg-6">
          <div class="sp-figure wow fadeInUp" data-wow-delay=".1s">
            <img src="{{ $mainImageUrl }}" loading="lazy" decoding="async" alt="{{ __('website.about.company_overview.image_alt') }}">
          </div>
        </div>
        <div class="col-lg-6">
          <span class="sp-eyebrow">{{ __('website.pro.profile_label') }}</span>
          <h2 class="sp-title">{{ __('website.about.company_overview.title') }}</h2>
          <p class="sp-lead">{{ __('website.about.company_overview.p1') }}</p>
          <p class="sp-text">{{ __('website.about.company_overview.p2') }}</p>
          <p class="sp-text">{{ __('website.about.company_overview.p3') }}</p>
          <p class="sp-text mb-0">{{ __('website.about.company_overview.p4') }}</p>
        </div>
      </div>

      <div class="sp-stats__card sp-stats__card--inline">
        @foreach($aboutStats as $stat)
          <div class="sp-stat">
            <div>
              <div class="sp-stat__value"><span class="odometer" data-count="{{ $stat['value'] }}">0</span>{{ $stat['suffix'] }}</div>
              <div class="sp-stat__label">{{ $stat['label'] }}</div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <section class="sp-section sp-section--alt">
    <div class="container">
      <div class="sp-section__head sp-section__head--center">
        <div>
          <span class="sp-eyebrow">{{ __('website.pro.history_label') }}</span>
          <h2 class="sp-title mb-0">{{ __('website.pro.history_title') }}</h2>
        </div>
      </div>

      <ol class="sp-timeline" aria-label="{{ __('website.about.company_overview.timeline_aria') }}">
        @foreach($timelineYears as $i => $year)
          <li class="sp-timeline__item wow fadeInUp" data-wow-delay="{{ 0.1 + $i * 0.1 }}s">
            <span class="sp-timeline__year">{{ $year }}</span>
            <span class="sp-timeline__dot" aria-hidden="true"></span>
            <div class="sp-timeline__card">
              <h3 class="sp-timeline__title">{{ __('website.about.company_overview.timeline.' . $year . '.title') }}</h3>
              <p class="sp-timeline__text">{{ __('website.about.company_overview.timeline.' . $year . '.desc') }}</p>
            </div>
          </li>
        @endforeach
      </ol>
    </div>
  </section>
@endsection
