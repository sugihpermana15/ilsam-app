@extends('about')
@section('title', __('website.titles.ceo_message'))
@section('breadcrumb_title', __('website.about.ceo_message.title'))
@php
  $ws = \App\Support\WebsiteSettings::all();
  $locale = app()->getLocale();

  $metaDescription = data_get($ws, 'seo.about.ceo.meta_description.' . $locale);
  if (!is_string($metaDescription) || trim($metaDescription) === '') {
    $metaDescription = data_get($ws, 'seo.about.ceo.meta_description.en');
  }
  if (!is_string($metaDescription)) {
    $metaDescription = '';
  }

  $metaImageRaw = data_get($ws, 'seo.about.ceo.meta_image', 'assets/img/img6.jpg');
  $metaImageRaw = is_string($metaImageRaw) && trim($metaImageRaw) !== '' ? trim($metaImageRaw) : 'assets/img/img6.jpg';
  $metaImage = preg_match('~^https?://~i', $metaImageRaw) ? $metaImageRaw : asset(ltrim($metaImageRaw, '/'));

  $portraitRaw = data_get($ws, 'about.ceo.portrait_image', 'assets/img/aboutus/ceo.jpg');
  $portraitRaw = is_string($portraitRaw) && trim($portraitRaw) !== '' ? trim($portraitRaw) : 'assets/img/aboutus/ceo.jpg';
  $portraitUrl = preg_match('~^https?://~i', $portraitRaw)
    ? $portraitRaw
    : route('img', ['path' => ltrim($portraitRaw, '/'), 'w' => 700, 'q' => 75]);
@endphp

@section('meta_description', $metaDescription)
@section('meta_image', $metaImage)
@section('aboutus')
  <section class="sp-section">
    <div class="container">
      <div class="row gy-5 gx-xl-5 align-items-start">
        <div class="col-lg-4">
          <aside class="sp-ceo wow fadeInUp" data-wow-delay=".1s">
            <div class="sp-ceo__portrait">
              <img src="{{ $portraitUrl }}" width="300" height="400" loading="lazy" decoding="async"
                alt="{{ __('website.about.ceo_message.portrait_alt') }}">
            </div>
            <div class="sp-ceo__caption">
              <span class="sp-ceo__org">{{ __('website.about.ceo_message.signature') }}</span>
            </div>
          </aside>
        </div>

        <div class="col-lg-8">
          <article class="sp-letter wow fadeInUp" data-wow-delay=".15s">
            <i class="bi bi-quote sp-letter__mark" aria-hidden="true"></i>
            <span class="sp-eyebrow">{{ __('website.pro.letter_label') }}</span>
            <h2 class="sp-title">{{ __('website.about.ceo_message.title') }}</h2>

            <p class="sp-letter__greeting">{{ __('website.about.ceo_message.greetings') }}</p>
            <p class="sp-lead">{{ __('website.about.ceo_message.thanks') }}</p>

            <p class="sp-text">{!! __('website.about.ceo_message.p1_html') !!}</p>
            <p class="sp-text">{{ __('website.about.ceo_message.p2') }}</p>
            <p class="sp-text">{{ __('website.about.ceo_message.p3') }}</p>
            <p class="sp-text">{{ __('website.about.ceo_message.p4') }}</p>
            <p class="sp-text">{{ __('website.about.ceo_message.p5') }}</p>

            <blockquote class="sp-letter__quote">{!! __('website.about.ceo_message.p6_html') !!}</blockquote>

            <p class="sp-letter__greeting">{{ __('website.about.ceo_message.to_customers') }}</p>
            <p class="sp-text">{{ __('website.about.ceo_message.wish') }}</p>
            <p class="sp-text">{{ __('website.about.ceo_message.thank_you') }}</p>

            <footer class="sp-letter__sign">
              <span class="sp-letter__line" aria-hidden="true"></span>
              {{ __('website.about.ceo_message.signature') }}
            </footer>
          </article>
        </div>
      </div>
    </div>
  </section>
@endsection
