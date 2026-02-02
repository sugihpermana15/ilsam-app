@extends('about')
@section('title', __('website.titles.ceo_message'))
@section('breadcrumb_title', __('website.about.ceo_message.title'))
@section('meta_description', 'Read the CEO message from PT ILSAM GLOBAL INDONESIA—our commitment, direction, and vision for sustainable growth.')
@section('meta_image', asset('assets/img/img6.jpg'))
@section('aboutus')
  <div class="row g-40 align-items-start">
    <div class="col-12 col-lg-7 order-2 order-lg-1">
      <h2 class="heading text-50 mb-4 wow fadeInUp" data-wow-delay=".1s">{{ __('website.about.ceo_message.title') }}</h2>

      <p class="text text-18 mb-3 wow fadeInUp" data-wow-delay=".15s"><strong>{{ __('website.about.ceo_message.greetings') }}</strong></p>
      <p class="text text-18 mb-3 wow fadeInUp" data-wow-delay=".2s">{{ __('website.about.ceo_message.thanks') }}</p>

      <p class="text text-18 mb-3 wow fadeInUp" data-wow-delay=".25s">{!! __('website.about.ceo_message.p1_html') !!}</p>

      <p class="text text-18 mb-3 wow fadeInUp" data-wow-delay=".3s">{{ __('website.about.ceo_message.p2') }}</p>

      <p class="text text-18 mb-3 wow fadeInUp" data-wow-delay=".35s">{{ __('website.about.ceo_message.p3') }}</p>

      <p class="text text-18 mb-3 wow fadeInUp" data-wow-delay=".4s">{{ __('website.about.ceo_message.p4') }}</p>

      <p class="text text-18 mb-3 wow fadeInUp" data-wow-delay=".45s">{{ __('website.about.ceo_message.p5') }}</p>

      <p class="text text-18 mb-4 wow fadeInUp" data-wow-delay=".5s">{!! __('website.about.ceo_message.p6_html') !!}</p>

      <p class="text text-18 mb-3 wow fadeInUp" data-wow-delay=".55s"><strong>{{ __('website.about.ceo_message.to_customers') }}</strong></p>
      <p class="text text-18 mb-3 wow fadeInUp" data-wow-delay=".6s">{{ __('website.about.ceo_message.wish') }}</p>
      <p class="text text-18 mb-4 wow fadeInUp" data-wow-delay=".65s">{{ __('website.about.ceo_message.thank_you') }}</p>

      <div class="mt-5 wow fadeInUp" data-wow-delay=".7s">
        <p class="fw-bold mb-0">{{ __('website.about.ceo_message.signature') }}</p>
      </div>
    </div>
    <div class="col-12 col-lg-5 order-1 order-lg-2">
      <div class="ceo-media wow fadeInUp" data-wow-delay=".1s">
        <div class="ceo-media__watermark" aria-hidden="true">
          <span>{{ __('website.about.ceo_message.watermark.ceo') }}</span>
          <span>{{ __('website.about.ceo_message.watermark.greeting') }}</span>
        </div>

        <div class="service-details__content-media">
          <div class="ceo-portrait">
            <img src="{{ asset('assets/img/aboutus/ceo.jpg') }}" width="300" height="400" loading="lazy" decoding="async"
              alt="{{ __('website.about.ceo_message.portrait_alt') }}">
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection