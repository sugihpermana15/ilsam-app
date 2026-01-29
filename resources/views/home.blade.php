@extends('layouts.app')

@section('title', 'Ilsam')

@section('main')
  <!-- Banner area start -->
  <section class="banner overflow-hidden">
    <div class="swiper banner__slider">
      <div class="swiper-wrapper">
        <div class="swiper-slide">
          <div class="banner__item banner__space theme-bg-heading-primary">
            <div class="banner__item-bg" data-background="{{ route('img', ['path' => 'assets/img/img1.jpg', 'w' => 1600, 'q' => 65]) }}"></div>
            <div class="container rr-shape-p-c_1">
              <div class="banner__item-shape-2 rr-shape-p-s_1 rr-upDown">
                <img src="{{ asset('assets/img/style2/banner/shape-2.png') }}" alt="">
              </div>
              <div class="banner__item-shape-3 rr-shape-p-s_1 rr-upDown">
                <img src="{{ asset('assets/img/style2/banner/shape-3.png') }}" alt="">
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="banner__item-content">
                    <h4 class="banner__item-sub-title color-white rr-fw-medium text-decoration-underline mb-25">
                      PT ILSAM GLOBAL INDONESIA
                    </h4>
                    <h1 class="banner__item-title h1-70 rr-fw-bold color-white mb-10 rr-br-hidden-md">
                      {{ __('website.home.hero.title_line1') }}<br>
                      {{ __('website.home.hero.title_line2') }}
                    </h1>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="swiper-slide">
          <div class="banner__item banner__space theme-bg-heading-primary">
            <div class="banner__item-bg" data-background="{{ route('img', ['path' => 'assets/img/img4.jpg', 'w' => 1600, 'q' => 65]) }}"></div>
            <div class="container rr-shape-p-c_1">
              <div class="banner__item-shape-2 rr-shape-p-s_1 rr-upDown">
                <img src="{{ asset('assets/img/style2/banner/shape-2.png') }}" alt="">
              </div>
              <div class="banner__item-shape-3 rr-shape-p-s_1 rr-upDown">
                <img src="{{ asset('assets/img/style2/banner/shape-3.png') }}" alt="">
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="banner__item-content">
                    <h4 class="banner__item-sub-title color-white rr-fw-sbold text-decoration-underline mb-25">
                      PT ILSAM GLOBAL INDONESIA
                    </h4>
                    <h1 class="banner__item-title h1-70 rr-fw-bold color-white mb-10 rr-br-hidden-lg">
                      {{ __('website.home.hero.title_line1') }}<br>
                      {{ __('website.home.hero.title_line2') }}
                    </h1>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="swiper-slide">
          <div class="banner__item banner__space theme-bg-heading-primary">
            <div class="banner__item-bg" data-background="{{ route('img', ['path' => 'assets/img/img3.jpg', 'w' => 1600, 'q' => 65]) }}"></div>
            <div class="container rr-shape-p-c_1">
              <div class="banner__item-shape-2 rr-shape-p-s_1 rr-upDown">
                <img src="{{ asset('assets/img/style2/banner/shape-2.png') }}" alt="">
              </div>
              <div class="banner__item-shape-3 rr-shape-p-s_1 rr-upDown">
                <img src="{{ asset('assets/img/style2/banner/shape-3.png') }}" alt="">
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="banner__item-content">
                    <h4 class="banner__item-sub-title color-white rr-fw-sbold text-decoration-underline mb-25">
                      PT ILSAM GLOBAL INDONESIA
                    </h4>
                    <h1 class="banner__item-title h1-70 rr-fw-bold color-white mb-10 rr-br-hidden-lg">
                      {{ __('website.home.hero.title_line1') }}<br>
                      {{ __('website.home.hero.title_line2') }}
                    </h1>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="swiper-slide">
          <div class="banner__item banner__space theme-bg-heading-primary">
            <div class="banner__item-bg" data-background="{{ route('img', ['path' => 'assets/img/img10.jpg', 'w' => 1600, 'q' => 65]) }}"></div>
            <div class="container rr-shape-p-c_1">
              <div class="banner__item-shape-2 rr-shape-p-s_1 rr-upDown">
                <img src="{{ asset('assets/img/style2/banner/shape-2.png') }}" alt="">
              </div>
              <div class="banner__item-shape-3 rr-shape-p-s_1 rr-upDown">
                <img src="{{ asset('assets/img/style2/banner/shape-3.png') }}" alt="">
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="banner__item-content">
                    <h4 class="banner__item-sub-title color-white rr-fw-sbold text-decoration-underline mb-25">
                      PT ILSAM GLOBAL INDONESIA
                    </h4>
                    <h1 class="banner__item-title h1-70 rr-fw-bold color-white mb-10 rr-br-hidden-lg">
                      {{ __('website.home.hero.title_line1') }}<br>
                      {{ __('website.home.hero.title_line2') }}
                    </h1>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="swiper-slide">
          <div class="banner__item banner__space theme-bg-heading-primary">
            <div class="banner__item-bg" data-background="{{ route('img', ['path' => 'assets/img/img9.jpg', 'w' => 1600, 'q' => 65]) }}"></div>
            <div class="container rr-shape-p-c_1">
              <div class="banner__item-shape-2 rr-shape-p-s_1 rr-upDown">
                <img src="{{ asset('assets/img/style2/banner/shape-2.png') }}" alt="">
              </div>
              <div class="banner__item-shape-3 rr-shape-p-s_1 rr-upDown">
                <img src="{{ asset('assets/img/style2/banner/shape-3.png') }}" alt="">
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="banner__item-content">
                    <h4 class="banner__item-sub-title color-white rr-fw-sbold text-decoration-underline mb-25">
                      PT ILSAM GLOBAL INDONESIA
                    </h4>
                    <h1 class="banner__item-title h1-70 rr-fw-bold color-white mb-10 rr-br-hidden-lg">
                      {{ __('website.home.hero.title_line1') }}<br>
                      {{ __('website.home.hero.title_line2') }}
                    </h1>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="banner__slider__controller-view">
        <div class="swiper-pagination"></div>
        <div class="timer-swiper">
          <div class="timer-swiperAfter"></div>
        </div>
      </div>

      <div class="banner__slider__arrow">
        <button class="banner__slider__arrow-prev">
          <svg width="20" height="11" viewBox="0 0 20 11" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M19 5.5L1 5.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M6.25 1L1 5.5L6.25 10" stroke="white" stroke-width="1.5" stroke-linecap="round"
              stroke-linejoin="round" />
          </svg>
        </button>
        <button class="banner__slider__arrow-next">
          <svg width="20" height="11" viewBox="0 0 20 11" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 5.5L19 5.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M13.75 1L19 5.5L13.75 10" stroke="white" stroke-width="1.5" stroke-linecap="round"
              stroke-linejoin="round" />
          </svg>
        </button>
      </div>
    </div>
  </section>
  <!-- Banner area end -->

  <!-- about-us area start -->
  <section class="about-us about-us--ilsam section-space overflow-hidden position-relative">
    <div class="about-us--ilsam__bg" aria-hidden="true"></div>
    <div class="container rr-shape-p-c_1 position-relative">
      <div class="row align-items-center">
        <div class="col-xl-6">
          <div class="section__title-wrapper text-center text-xl-start rr-mb-60-lg">
            <span class="section__subtitle justify-content-start mb-13"><span data-width="40px"
                class="left-separetor"></span>{{ __('website.home.about.subtitle') }}</span>
            <h2 class="section__title title-animation mb-15 mb-xs-10 rr-br-hidden-md" data-cursor="-opaque">Perfect
              {{ __('website.home.about.title_line1') }} <br> {{ __('website.home.about.title_line2') }}</h2>
          </div>
        </div>
        <div class="col-xl-6">
          <div class="about-us--ilsam__figure">
            <div class="about-us--ilsam__media wow clip-a-z">
              <img loading="lazy" decoding="async" src="{{ route('img', ['path' => 'assets/img/main_who_triangle.png', 'w' => 900, 'q' => 80, 'fm' => 'png', 'v' => 3]) }}" alt="{{ __('website.home.about.image_alt') }}">
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- about-us area end -->

  <!-- Experience -->
  <section class="experience theme-bg-heading-primary section-space-100 position-relative z-1 overflow-hidden">
    <div class="experience__bg" data-background="{{ route('img', ['path' => 'assets/img/img6.jpg', 'w' => 1600, 'q' => 65]) }}"></div>
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="experience__box d-flex flex-wrap justify-content-sm-between">
            <div class="experience__item d-flex flex-wrap align-items-center">
              <div class="experience__item-icon">
                <i class="bi bi-calendar3" aria-hidden="true"></i>
              </div>
              <div class="experience__item-text">
                <h2 class="experience__item-text-title color-white">
                  <span class="odometer" data-count="1999">0</span>
                </h2>
                <h4 class="color-white rr-fw-sbold mb-0">{{ __('website.home.experience.established') }}</h4>
              </div>
            </div>

            <div class="experience__item d-flex flex-wrap align-items-center">
              <div class="experience__item-icon">
                <i class="bi bi-people" aria-hidden="true"></i>
              </div>
              <div class="experience__item-text">
                <h2 class="experience__item-text-title color-white">
                  <span class="odometer" data-count="20">0</span>
                </h2>
                <h4 class="color-white rr-fw-sbold mb-0">{{ __('website.home.experience.clients') }}</h4>
              </div>
            </div>

            <div class="experience__item d-flex flex-wrap align-items-center">
              <div class="experience__item-icon">
                <i class="bi bi-award" aria-hidden="true"></i>
              </div>
              <div class="experience__item-text">
                <h2 class="experience__item-text-title color-white">
                  <span class="odometer" data-count="27">0</span>
                </h2>
                <h4 class="color-white rr-fw-sbold mb-0">{{ __('website.home.experience.years_experience') }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Experience -->

  <!-- what-we-do-2 area start -->
  <section class="what-we-do-2 section-space section-bg-2 overflow-hidden">
    <div class="container rr-shape-p-c_1">
      <div class="rr-upDown">
        <div class="what-we-do-2__text rr-shape-p-s_1">{{ __('website.home.products.label') }}</div>
      </div>
      <div class="what-we-do-2__shape-1 rr-shape-p-s_1 rr-upDown"><img
          loading="lazy" decoding="async" src="{{ asset('assets/img/style2/what-we-do-2/shape-2.png') }}" alt="">
      </div>
      <div class="what-we-do-2__shape-2 rr-shape-p-s_1 rr-downUp"><img
          loading="lazy" decoding="async" src="{{ asset('assets/img/style2/what-we-do-2/shape-3.png') }}" alt="">
      </div>
      <div class="what-we-do-2__shape-3 rr-shape-p-s_1 rr-downUp"><img
          loading="lazy" decoding="async" src="{{ asset('assets/img/style2/what-we-do-2/shape-4.png') }}" alt="">
      </div>
      <div class="row">
        <div class="col-12">
          <div class="section__title-wrapper text-center mb-60 mb-sm-40 mb-xs-35">
            <span class="section__subtitle justify-content-center mb-13 ml-0"><span data-width="40px"
                class="left-separetor"></span>{{ __('website.home.products.label') }}<span data-width="40px" class="right-separetor"></span></span>
            <h2 class="section__title title-animation text-capitalize rr-br-hidden-md" data-cursor="-opaque">Powering
              {{ __('website.home.products.title') }}</h2>
          </div>
        </div>
      </div>

      <div class="row mb-minus-30">
        <div class="col-xl-6 col-md-6">
          <div class="what-we-do-2__item d-flex flex-column mb-30">
            <div class="what-we-do-2__item-bg" data-background="{{ route('img', ['path' => 'assets/img/img4.jpg', 'w' => 1200, 'q' => 65]) }}"></div>
            <div class="what-we-do-2__item-shape-1 rr-upDown">
              <svg width="218" height="226" viewBox="0 0 218 226" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M81.7719 23.7964L209.283 113.722L67.6499 179.187L81.7719 23.7964Z" fill="var(--rr-theme-primary)"
                  fill-opacity="0.5" />
              </svg>
            </div>
            <div class="what-we-do-2__item-img mb-20">
              <i class="bi bi-palette" aria-hidden="true"></i>
            </div>
            <div class="mt-auto">
              <h4 class="title mb-15"><a href="{{ route('products.colorants') }}">{{ __('website.home.products.items.colorants') }}</a></h4>
              <p class="mb-0 rr-p-16">{{ __('website.home.products.teaser_line1') }} <br>
                {{ __('website.home.products.teaser_line2') }}</p>
            </div>
          </div>
        </div>
        <div class="col-xl-6 col-md-6">
          <div class="what-we-do-2__item d-flex flex-column mb-30">
            <div class="what-we-do-2__item-bg" data-background="{{ route('img', ['path' => 'assets/img/img6.jpg', 'w' => 1200, 'q' => 65]) }}"></div>
            <div class="what-we-do-2__item-shape-1 rr-upDown">
              <svg width="218" height="226" viewBox="0 0 218 226" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M81.7719 23.7964L209.283 113.722L67.6499 179.187L81.7719 23.7964Z" fill="var(--rr-theme-primary)"
                  fill-opacity="0.5" />
              </svg>
            </div>
            <div class="what-we-do-2__item-img mb-20">
              <i class="bi bi-brush" aria-hidden="true"></i>
            </div>
            <div class="mt-auto">
              <h4 class="title mb-15"><a href="{{ route('products.surface-coating-agents') }}">{{ __('website.home.products.items.surface_coating_agents') }}</a>
              </h4>
              <p class="mb-0 rr-p-16">{{ __('website.home.products.teaser_line1') }} <br>
                {{ __('website.home.products.teaser_line2') }}</p>
            </div>
          </div>
        </div>
        <div class="col-xl-6 col-md-6">
          <div class="what-we-do-2__item d-flex flex-column mb-30">
            <div class="what-we-do-2__item-bg" data-background="{{ route('img', ['path' => 'assets/img/img3.jpg', 'w' => 1200, 'q' => 65]) }}"></div>
            <div class="what-we-do-2__item-shape-1 rr-upDown">
              <svg width="218" height="226" viewBox="0 0 218 226" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M81.7719 23.7964L209.283 113.722L67.6499 179.187L81.7719 23.7964Z" fill="var(--rr-theme-primary)"
                  fill-opacity="0.5" />
              </svg>
            </div>
            <div class="what-we-do-2__item-img mb-20">
              <i class="bi bi-layers" aria-hidden="true"></i>
            </div>
            <div class="mt-auto">
              <h4 class="title mb-15"><a href="{{ route('products.additive-coating') }}">{{ __('website.home.products.items.additive_coating') }}</a></h4>
              <p class="mb-0 rr-p-16">{{ __('website.home.products.teaser_line1') }} <br>
                {{ __('website.home.products.teaser_line2') }}</p>
            </div>
          </div>
        </div>
        <div class="col-xl-6 col-md-6">
          <div class="what-we-do-2__item d-flex flex-column mb-30">
            <div class="what-we-do-2__item-bg" data-background="{{ route('img', ['path' => 'assets/img/img1.jpg', 'w' => 1200, 'q' => 65]) }}"></div>
            <div class="what-we-do-2__item-shape-1 rr-upDown">
              <svg width="218" height="226" viewBox="0 0 218 226" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M81.7719 23.7964L209.283 113.722L67.6499 179.187L81.7719 23.7964Z" fill="var(--rr-theme-primary)"
                  fill-opacity="0.5" />
              </svg>
            </div>
            <div class="what-we-do-2__item-img mb-20">
              <i class="bi bi-droplet" aria-hidden="true"></i>
            </div>
            <div class="mt-auto">
              <h4 class="title mb-15"><a href="{{ route('products.pu-resin') }}">{{ __('website.home.products.items.pu_resin') }}</a></h4>
              <p class="mb-0 rr-p-16">{{ __('website.home.products.teaser_line1') }} <br>
                {{ __('website.home.products.teaser_line2') }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- what-we-do-2 area end -->

  <!-- text-slider area start -->
  <section class="text-slider text-slider__section-space position-relative theme-bg-primary overflow-hidden">
    <div class="text-slider__slider carouselTicker carouselTicker-nav">
      <ul class="carouselTicker__list">
        <li>
          <h3 data-cursor="-opaque" class="title">PT. KINDO MAKMUR JAYA</h3>
        </li>
        <li>
          <h4 class="title bar">//</h4>
        </li>
        <li>
          <h3 data-cursor="-opaque" class="title">PT. SINYOUNG ABADI</h3>
        </li>
        <li>
          <h4 class="title bar">//</h4>
        </li>
        <li>
          <h3 data-cursor="-opaque" class="title">PT. KONES TAEYA INDUSTRY</h3>
        </li>
        <li>
          <h4 class="title bar">//</h4>
        </li>
        <li>
          <h3 data-cursor="-opaque" class="title">PT. BINTANG FAMILY INDONESIA</h3>
        </li>
        <li>
          <h4 class="title bar">//</h4>
        </li>
        <li>
          <h3 data-cursor="-opaque" class="title">PT. SINAR CONTINENTAL</h3>
        </li>
        <li>
          <h4 class="title bar">//</h4>
        </li>
        <li>
          <h3 data-cursor="-opaque" class="title">PT. SUN LEE JAYA</h3>
        </li>
        <li>
          <h4 class="title bar">//</h4>
        </li>
        <li>
          <h3 data-cursor="-opaque" class="title">PT. SEMPURNAINDAH MULTINUSANTARA</h3>
        </li>
        <li>
          <h4 class="title bar">//</h4>
        </li>
        <li>
          <h3 data-cursor="-opaque" class="title">PT. BEN TECH ABADI</h3>
        </li>
        <li>
          <h4 class="title bar">//</h4>
        </li>
        <li>
          <h3 data-cursor="-opaque" class="title">PT. BAIKSAN INDONESIA</h3>
        </li>
        <li>
          <h4 class="title bar">//</h4>
        </li>
        <li>
          <h3 data-cursor="-opaque" class="title">PT. DAEHWA LEATHER LESTARI</h3>
        </li>
        <li>
          <h4 class="title bar">//</h4>
        </li>
        <li>
          <h3 data-cursor="-opaque" class="title">PT. CIPTA HARMONI JAYA</h3>
        </li>
        <li>
          <h4 class="title bar">//</h4>
        </li>
        <li>
          <h3 data-cursor="-opaque" class="title">PT. YOUNGIL LEATHER INDONESIA</h3>
        </li>
        <li>
          <h4 class="title bar">//</h4>
        </li>
        <li>
          <h3 data-cursor="-opaque" class="title">PT. JOIL INNI INDONESIA</h3>
        </li>
        <li>
          <h4 class="title bar">//</h4>
        </li>
        <li>
          <h3 data-cursor="-opaque" class="title">PT. DAEWON ECO INDONESIA</h3>
        </li>
      </ul>
    </div>
  </section>
  <!-- text-slider area end -->

  <!-- lets-talk area start -->
  <section class="lets-talk section-space-115 section-bg-2 overflow-hidden">
    <div class="lets-talk-bg">
      <div class="overlay"><img loading="lazy" decoding="async" src="{{ route('img', ['path' => 'assets/img/img8.jpeg', 'w' => 1600, 'q' => 70]) }}" alt=""></div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-4">
          <div class="row pr-30 rr-pr-none-lg pl-80 rr-pl-none-xl mb-minus-30">
            <div class="col-xl-12 col-lg-4 col-md-6">
              <div
                class="contact-list__item contact-list__item-home2 d-flex align-items-center justify-content-center mb-30">
                <div class="contact-list__item-icon">
                  <i class="bi bi-envelope" aria-hidden="true"></i>
                </div>
                <div class="contact-list__item-text">
                  <h4 class="title">{{ __('website.home.contact.email_address') }}</h4>
                  <a href="mailto:market.ilsamindonesia@yahoo.com">market.ilsamindonesia@yahoo.com</a>
                </div>
              </div>
            </div>
            <div class="col-xl-12 col-lg-4 col-md-6">
              <div
                class="contact-list__item contact-list__item-home2 d-flex align-items-center justify-content-center mb-30">
                <div class="contact-list__item-icon">
                  <i class="bi bi-telephone" aria-hidden="true"></i>
                </div>
                <div class="contact-list__item-text">
                  <h4 class="title">{{ __('website.home.contact.phone_number') }}</h4>
                  <a href="tel:+622674868013">+62 (267) 4868013 / 313</a>
                </div>
              </div>
            </div>
            <div class="col-xl-12 col-lg-4 col-md-6">
              <div
                class="contact-list__item contact-list__item-home2 d-flex align-items-center justify-content-center mb-30">
                <div class="contact-list__item-icon">
                  <i class="bi bi-geo-alt" aria-hidden="true"></i>
                </div>
                <div class="contact-list__item-text">
                  <h4 class="title">{{ __('website.home.contact.our_location') }}</h4>
                  <a href="https://maps.app.goo.gl/reUj3juAoQ8NrGLE6">Jl. Trans Heksa Artha Industrial Hill Area Block E
                    No.13.</a>
                </div>
              </div>
            </div>
            <div class="col-xl-12 col-lg-4 col-md-6">
              <div
                class="contact-list__item contact-list__item-home2 d-flex align-items-center justify-content-center mb-30">
                <div class="contact-list__item-icon">
                  <i class="bi bi-clock" aria-hidden="true"></i>
                </div>
                <div class="contact-list__item-text">
                  <h4 class="title">{{ __('website.home.contact.opening_hour') }}</h4>
                  <span>{{ __('website.home.contact.opening_hours_value') }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-6">
          <div class="section__title-wrapper text-center text-xl-start mb-40 mb-xs-35 rr-mt-60-lg">
            <span class="section__subtitle justify-content-start mb-13"><span data-width="40px"
                class="left-separetor"></span>{{ __('website.home.contact.subtitle') }}</span>
            <h2 class="section__title title-animation text-capitalize mb-15 rr-br-hidden-md" data-cursor="-opaque">{{ __('website.home.contact.title_line1') }}
              {{ __('website.home.contact.title_line2') }}</h2>
            <p class="des mb-0">{{ __('website.home.contact.desc') }}</p>
          </div>

          <form id="contact-form" class="request-a-quote__form home-2" method="POST" action="{{ route('contact.send') }}">
            @csrf
            <div class="row">
              <div class="col-xl-6">
                <div class="request-a-quote__form-input">
                  <div class="validation__wrapper-up position-relative">
                    <input name="name" id="name" type="text" placeholder="{{ __('website.home.contact.form.name') }}">
                  </div>
                </div>
              </div>
              <div class="col-xl-6">
                <div class="request-a-quote__form-input">
                  <div class="validation__wrapper-up position-relative">
                    <input name="tel" id="tel" type="tel" placeholder="{{ __('website.home.contact.form.tel') }}">
                  </div>
                </div>
              </div>
              <div class="col-xl-6">
                <div class="request-a-quote__form-input">
                  <div class="validation__wrapper-up position-relative">
                    <input name="email" id="email" type="email" placeholder="{{ __('website.home.contact.form.email') }}">
                  </div>
                </div>
              </div>
              <div class="col-xl-6">
                <div class="request-a-quote__form-input">
                  <div class="validation__wrapper-up position-relative">
                    <input name="inquiries" id="inquiries" type="text" placeholder="{{ __('website.home.contact.form.inquiries') }}">
                  </div>
                </div>
              </div>
              <div class="col-12">
                <div class="request-a-quote__form-input textarea">
                  <div class="validation__wrapper-up position-relative">
                    <textarea name="textarea" id="textarea" placeholder="{{ __('website.home.contact.form.details') }}"></textarea>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <button type="submit" class="rr-btn w-auto">
                  <span class="btn-wrap">
                    <span class="text-one">{{ __('website.home.contact.form.send') }}</span>
                    <span class="text-two">{{ __('website.home.contact.form.send') }}</span>
                  </span>
                </button>
              </div>

              <div class="col-12">
                <div class="ajax-response mt-3">
                  @if (session('success'))
                    <div class="success">{{ session('success') }}</div>
                  @endif

                  @if ($errors->any())
                    <ul class="error mb-0">
                      @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  @endif
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
  <!-- lets-talk area end -->

@endsection