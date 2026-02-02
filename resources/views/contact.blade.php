@extends('layouts.app')
@section('title', __('website.titles.contact'))

@section('meta_description', 'Contact PT ILSAM GLOBAL INDONESIA in Karawang, West Java for chemical colorants and coating solutions (PU/PVC synthetic leather & footwear manufacturing). Serving Cikarang, Bekasi, Karawang, Jakarta, and across Java & Indonesia.')
@section('meta_image', asset('assets/img/img14.jpg'))
@section('main')
    <!-- Breadcrumb area start  -->
    <div class="breadcrumb__area breadcrumb-space overly theme-bg-heading-primary overflow-hidden">
        <div class="breadcrumb__background" data-background="{{ asset('assets/img/img14.jpg') }}"></div>
        <div class="container">
            <div class="row align-items-center justify-content-between">
                <div class="col-12">
                    <div class="breadcrumb__content text-center">
                        <h1 class="breadcrumb__title color-white title-animation">@yield('breadcrumb_title', __('website.contact.breadcrumb'))
                        </h1>
                        <div class="breadcrumb__menu d-inline-flex justify-content-center">
                            <nav>
                                <ul>
                                    <li>
                                        <span>
                                            <a href="{{ route('home') }}">
                                                <svg width="15" height="16" viewBox="0 0 15 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M1 5.9L7.5 1L14 5.9V13.6C14 13.9713 13.8478 14.3274 13.5769 14.5899C13.306 14.8525 12.9386 15 12.5556 15H2.44444C2.06135 15 1.69395 14.8525 1.42307 14.5899C1.15218 14.3274 1 13.9713 1 13.6V5.9Z"
                                                        stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path d="M5.33398 15V8H9.66732V15" stroke="white" stroke-width="1.5"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                {{ __('website.common.home') }}
                                            </a>
                                        </span>
                                    </li>
                                    <li class="active"><span>@yield('breadcrumb_title', __('website.contact.breadcrumb'))</span></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb area end  -->

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
                                    <a href="https://maps.app.goo.gl/reUj3juAoQ8NrGLE6">Jl. Trans Heksa Artha Industrial
                                        Hill Area Block E No.13.</a>
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
                        <h2 class="section__title title-animation text-capitalize mb-15 rr-br-hidden-md"
                            data-cursor="-opaque">{{ __('website.home.contact.title_line1') }} {{ __('website.home.contact.title_line2') }}</h2>
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

    <!--contact-map-->
    <div class="contact-map">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.0320185769133!2d107.23779097590075!3d-6.389870062501263!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e699f000ca996c1%3A0x713bcc5addd9fa22!2sPT.%20ILSAM%20GLOBAL%20INDONESIA%20(IG-103)!5e0!3m2!1sid!2sid!4v1768213302522!5m2!1sid!2sid"
            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
    <!--contact-map-->

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
@endsection