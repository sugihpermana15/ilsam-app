@extends('layouts.app')

@section('title', 'Ilsam')

@section('main')
  <!-- Banner area start -->
  <section class="banner overflow-hidden">
    <div class="swiper banner__slider">
      <div class="swiper-wrapper">
        <div class="swiper-slide">
          <div class="banner__item banner__space theme-bg-heading-primary">
            <div class="banner__item-bg" data-background="{{ asset('assets/img/img1.jpg') }}"></div>
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
                      Your Partner in Reliable<br>
                      Chemical Solutions.
                    </h1>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="swiper-slide">
          <div class="banner__item banner__space theme-bg-heading-primary">
            <div class="banner__item-bg" data-background="{{ asset('assets/img/img4.jpg') }}"></div>
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
                      Your Partner in Reliable<br>
                      Chemical Solutions.
                    </h1>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="swiper-slide">
          <div class="banner__item banner__space theme-bg-heading-primary">
            <div class="banner__item-bg" data-background="{{ asset('assets/img/img3.jpg') }}"></div>
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
                      Your Partner in Reliable<br>
                      Chemical Solutions.
                    </h1>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="swiper-slide">
          <div class="banner__item banner__space theme-bg-heading-primary">
            <div class="banner__item-bg" data-background="{{ asset('assets/img/img10.jpg') }}"></div>
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
                      Your Partner in Reliable<br>
                      Chemical Solutions.
                    </h1>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="swiper-slide">
          <div class="banner__item banner__space theme-bg-heading-primary">
            <div class="banner__item-bg" data-background="{{ asset('assets/img/img9.jpg') }}"></div>
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
                      Your Partner in Reliable<br>
                      Chemical Solutions.
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
                class="left-separetor"></span>Who we are</span>
            <h2 class="section__title title-animation mb-15 mb-xs-10 rr-br-hidden-md" data-cursor="-opaque">Perfect
              Solution <br> for your Business.</h2>
          </div>
        </div>
        <div class="col-xl-6">
          <div class="about-us--ilsam__figure">
            <div class="about-us--ilsam__media wow clip-a-z">
              <img src="{{ asset('assets/img/main_who_triangle.png') }}" alt="About Ilsam">
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- about-us area end -->

  <!-- Experience -->
  <section class="experience theme-bg-heading-primary section-space-100 position-relative z-1 overflow-hidden">
    <div class="experience__bg" data-background="{{ asset('assets/img/img6.jpg') }}"></div>
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
                <h4 class="color-white rr-fw-sbold mb-0">Establishment</h4>
              </div>
            </div>

            <div class="experience__item d-flex flex-wrap align-items-center">
              <div class="experience__item-icon">
                <i class="bi bi-people" aria-hidden="true"></i>
              </div>
              <div class="experience__item-text">
                <h2 class="experience__item-text-title color-white">
                  <span class="odometer" data-count="51">0</span>
                </h2>
                <h4 class="color-white rr-fw-sbold mb-0">Clients</h4>
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
                <h4 class="color-white rr-fw-sbold mb-0">Years Experience</h4>
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
        <div class="what-we-do-2__text rr-shape-p-s_1">Products</div>
      </div>
      <div class="what-we-do-2__shape-1 rr-shape-p-s_1 rr-upDown"><img
          src="{{ asset('assets/img/style2/what-we-do-2/shape-2.png') }}" alt="">
      </div>
      <div class="what-we-do-2__shape-2 rr-shape-p-s_1 rr-downUp"><img
          src="{{ asset('assets/img/style2/what-we-do-2/shape-3.png') }}" alt="">
      </div>
      <div class="what-we-do-2__shape-3 rr-shape-p-s_1 rr-downUp"><img
          src="{{ asset('assets/img/style2/what-we-do-2/shape-4.png') }}" alt="">
      </div>
      <div class="row">
        <div class="col-12">
          <div class="section__title-wrapper text-center mb-60 mb-sm-40 mb-xs-35">
            <span class="section__subtitle justify-content-center mb-13 ml-0"><span data-width="40px"
                class="left-separetor"></span>Products<span data-width="40px" class="right-separetor"></span></span>
            <h2 class="section__title title-animation text-capitalize rr-br-hidden-md" data-cursor="-opaque">Powering
              Industries with Quality Chemicals</h2>
          </div>
        </div>
      </div>

      <div class="row mb-minus-30">
        <div class="col-xl-6 col-md-6">
          <div class="what-we-do-2__item d-flex flex-column mb-30">
            <div class="what-we-do-2__item-bg" data-background="{{ asset('assets/img/img4.jpg') }}"></div>
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
              <h4 class="title mb-15"><a href="{{ route('products.colorants') }}">Colorants</a></h4>
              <p class="mb-0 rr-p-16">There are many variations of passages <br>
                of Lorem Ipsum available.</p>
            </div>
          </div>
        </div>
        <div class="col-xl-6 col-md-6">
          <div class="what-we-do-2__item d-flex flex-column mb-30">
            <div class="what-we-do-2__item-bg" data-background="{{ asset('assets/img/img6.jpg') }}"></div>
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
              <h4 class="title mb-15"><a href="{{ route('products.surface-coating-agents') }}">Surface Coating Agents</a>
              </h4>
              <p class="mb-0 rr-p-16">There are many variations of passages <br>
                of Lorem Ipsum available.</p>
            </div>
          </div>
        </div>
        <div class="col-xl-6 col-md-6">
          <div class="what-we-do-2__item d-flex flex-column mb-30">
            <div class="what-we-do-2__item-bg" data-background="{{ asset('assets/img/img3.jpg') }}"></div>
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
              <h4 class="title mb-15"><a href="{{ route('products.additive-coating') }}">Additive Coating</a></h4>
              <p class="mb-0 rr-p-16">There are many variations of passages <br>
                of Lorem Ipsum available.</p>
            </div>
          </div>
        </div>
        <div class="col-xl-6 col-md-6">
          <div class="what-we-do-2__item d-flex flex-column mb-30">
            <div class="what-we-do-2__item-bg" data-background="{{ asset('assets/img/img1.jpg') }}"></div>
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
              <h4 class="title mb-15"><a href="{{ route('products.pu-resin') }}">PU RESIN </a></h4>
              <p class="mb-0 rr-p-16">There are many variations of passages <br>
                of Lorem Ipsum available.</p>
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
      <div class="overlay"><img src="{{ asset('assets/img/img8.jpeg') }}" alt=""></div>
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
                  <h4 class="title">Email Address</h4>
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
                  <h4 class="title">Phone Number</h4>
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
                  <h4 class="title">Our Location</h4>
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
                  <h4 class="title">Opening Hour </h4>
                  <span>Mon - Sat: 08am - 05pm</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-6">
          <div class="section__title-wrapper text-center text-xl-start mb-40 mb-xs-35 rr-mt-60-lg">
            <span class="section__subtitle justify-content-start mb-13"><span data-width="40px"
                class="left-separetor"></span>Lets Talk</span>
            <h2 class="section__title title-animation text-capitalize mb-15 rr-br-hidden-md" data-cursor="-opaque">Need a
              Trusted Chemical Partner? Get in Touch</h2>
            <p class="des mb-0">We offer consistent, high-quality chemical solutions designed for efficiency, safety, and
              long-term industrial performance.</p>
          </div>

          <form id="request-a-quote__form" class="request-a-quote__form home-2" method="POST">
            <div class="row">
              <div class="col-xl-6">
                <div class="request-a-quote__form-input">
                  <div class="validation__wrapper-up position-relative">
                    <input name="name" id="name" type="text" placeholder="Name">
                  </div>
                </div>
              </div>
              <div class="col-xl-6">
                <div class="request-a-quote__form-input">
                  <div class="validation__wrapper-up position-relative">
                    <input name="tel" id="tel" type="tel" placeholder="Phone Number">
                  </div>
                </div>
              </div>
              <div class="col-xl-6">
                <div class="request-a-quote__form-input">
                  <div class="validation__wrapper-up position-relative">
                    <input name="email" id="email" type="email" placeholder="Email Address">
                  </div>
                </div>
              </div>
              <div class="col-xl-6">
                <div class="request-a-quote__form-input">
                  <div class="validation__wrapper-up position-relative">
                    <input name="inquiries" id="inquiries" type="text" placeholder="Work Inquiries">
                  </div>
                </div>
              </div>
              <div class="col-12">
                <div class="request-a-quote__form-input textarea">
                  <div class="validation__wrapper-up position-relative">
                    <textarea name="textarea" id="textarea" placeholder="Project Details"></textarea>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <button type="submit" class="rr-btn w-auto">
                  <span class="btn-wrap">
                    <span class="text-one">Send Message</span>
                    <span class="text-two">Send Message</span>
                  </span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
  <!-- lets-talk area end -->

@endsection