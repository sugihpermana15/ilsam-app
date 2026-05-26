@extends('about')
@section('title', __('website.titles.business_philosophy'))
@section('breadcrumb_title', __('website.about.business_philosophy.title'))
@php
    $ws = \App\Support\WebsiteSettings::all();
    $locale = app()->getLocale();

    $metaDescription = data_get($ws, 'seo.about.philosophy.meta_description.' . $locale);
    if (!is_string($metaDescription) || trim($metaDescription) === '') {
        $metaDescription = data_get($ws, 'seo.about.philosophy.meta_description.en');
    }
    if (!is_string($metaDescription)) {
        $metaDescription = '';
    }

    $metaImageRaw = data_get($ws, 'seo.about.philosophy.meta_image', 'assets/img/img3.jpg');
    $metaImageRaw = is_string($metaImageRaw) && trim($metaImageRaw) !== '' ? trim($metaImageRaw) : 'assets/img/img3.jpg';
    $metaImage = preg_match('~^https?://~i', $metaImageRaw) ? $metaImageRaw : asset(ltrim($metaImageRaw, '/'));

    $heroRaw = data_get($ws, 'about.philosophy.hero_bg', 'assets/img/aboutus/img12.jpg');
    $heroRaw = is_string($heroRaw) && trim($heroRaw) !== '' ? trim($heroRaw) : 'assets/img/aboutus/img12.jpg';
    $heroUrl = preg_match('~^https?://~i', $heroRaw)
        ? $heroRaw
        : asset(ltrim($heroRaw, '/'));
@endphp

@section('meta_description', $metaDescription)
@section('meta_image', $metaImage)
@section('aboutus')

    <!-- Working Process area start -->
    <section class="working-process section-space overflow-hidden">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section__title-wrapper text-center mb-60 mb-sm-40 mb-xs-35">
                        <h2 class="section__title title-animation text-capitalize rr-br-hidden-md" data-cursor="-opaque">{{ __('website.about.business_philosophy.motto_title') }}</h2>
                        <p class="mt-20 text-muted fs-5">{{ __('website.about.business_philosophy.motto_desc') }}</p>
                    </div>
                </div>
            </div>

            <div class="row mb-minus-30 rr-shape-p-c_1">
                <div class="working-process__shape-1 rr-shape-p-s_1 leftRight">
                    <img src="{{ asset('assets/img/style2/working-process/shape.png') }}" alt="">
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="working-process__item text-center mb-30 mt-30">
                        <div class="working-process__item-icon mb-40">
                            <div class="working-process__item-icon-img" aria-hidden="true">
                                <i class="fa-solid fa-circle-plus fa-2x"></i>
                            </div>
                        </div>
                        <h4 class="title mb-10">{{ __('website.about.business_philosophy.motto.good_health.title') }}</h4>
                        <p class="des mb-0">
                            {{ __('website.about.business_philosophy.motto.good_health.desc') }}
                        </p>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="working-process__item text-center mb-30">
                        <div class="working-process__item-icon mb-40">
                            <div class="working-process__item-icon-img" aria-hidden="true">
                                <i class="fa-solid fa-handshake fa-2x"></i>
                            </div>
                        </div>
                        <h4 class="title mb-10">{{ __('website.about.business_philosophy.motto.loyalty.title') }}</h4>
                        <p class="des mb-0">
                            {{ __('website.about.business_philosophy.motto.loyalty.desc') }}
                        </p>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="working-process__item text-center mb-30 mt-30">
                        <div class="working-process__item-icon mb-40">
                            <div class="working-process__item-icon-img" aria-hidden="true">
                                <i class="fa-solid fa-gavel fa-2x"></i>
                            </div>
                        </div>
                        <h4 class="title mb-10">{{ __('website.about.business_philosophy.motto.justice.title') }}</h4>
                        <p class="des mb-0">{{ __('website.about.business_philosophy.motto.justice.desc') }}</p>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="working-process__item text-center mb-30">
                        <div class="working-process__item-icon mb-40">
                            <div class="working-process__item-icon-img" aria-hidden="true">
                                <i class="fa-solid fa-hand-holding-heart fa-2x"></i>
                            </div>
                        </div>
                        <h4 class="title mb-10">{{ __('website.about.business_philosophy.motto.morality.title') }}</h4>
                        <p class="des mb-0">
                            {{ __('website.about.business_philosophy.motto.morality.desc') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- working-process area end -->

    <section
    class="about-hero about-hero--bleed wow fadeInUp"
    data-wow-delay=".1s"
    style="--about-hero-bg: url('{{ $heroUrl }}');"
        aria-label="{{ __('website.about.business_philosophy.aria') }}">
    <div class="about-hero__inner">
            <h2 class="about-hero__title">{{ __('website.about.business_philosophy.title') }}</h2>
      <p class="about-hero__lead mb-0">
                <span class="about-hero__quote">{{ __('website.about.business_philosophy.quote') }}</span>.
      </p>
    </div>
    </section>

        <!-- Company Slogan area start -->
        <section class="company-slogan section-space">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="section__title-wrapper text-center mb-60 mb-sm-40 mb-xs-35">
                            <h2 class="section__title title-animation text-capitalize rr-br-hidden-md" data-cursor="-opaque">{{ __('website.about.business_philosophy.slogans_title') }}</h2>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10">
                        <ul class="company-slogan__list list-unstyled mb-0 wow fadeInUp" data-wow-delay=".15s">
                            <li class="company-slogan__item p-4 mb-4 shadow-sm rounded-4 bg-white d-flex align-items-center" style="border-left: 5px solid var(--rr-theme-primary, #0056b3);">
                                <i class="fa-solid fa-check-circle text-primary me-3 fs-3"></i>
                                <span class="fs-5 fw-medium text-dark">{{ __('website.about.business_philosophy.slogans.1') }}</span>
                            </li>
                            <li class="company-slogan__item p-4 mb-4 shadow-sm rounded-4 bg-white d-flex align-items-center" style="border-left: 5px solid var(--rr-theme-primary, #0056b3);">
                                <i class="fa-solid fa-check-circle text-primary me-3 fs-3"></i>
                                <span class="fs-5 fw-medium text-dark">{{ __('website.about.business_philosophy.slogans.2') }}</span>
                            </li>
                            <li class="company-slogan__item p-4 mb-4 shadow-sm rounded-4 bg-white d-flex align-items-center" style="border-left: 5px solid var(--rr-theme-primary, #0056b3);">
                                <i class="fa-solid fa-check-circle text-primary me-3 fs-3"></i>
                                <span class="fs-5 fw-medium text-dark">{{ __('website.about.business_philosophy.slogans.3') }}</span>
                            </li>
                            <li class="company-slogan__item p-4 shadow-sm rounded-4 bg-white d-flex align-items-center" style="border-left: 5px solid var(--rr-theme-primary, #0056b3);">
                                <i class="fa-solid fa-check-circle text-primary me-3 fs-3"></i>
                                <span class="fs-5 fw-medium text-dark">{{ __('website.about.business_philosophy.slogans.4') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!-- Company Slogan area end -->

@endsection