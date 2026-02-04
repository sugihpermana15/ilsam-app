@extends('layouts.app')
@section('title', __('website.titles.contact'))

@php
    $ws = \App\Support\WebsiteSettings::all();
    $locale = app()->getLocale();

    $metaDescription = data_get($ws, 'seo.contact.meta_description.' . $locale);
    if (!is_string($metaDescription) || trim($metaDescription) === '') {
        $metaDescription = data_get($ws, 'seo.contact.meta_description.en');
    }
    if (!is_string($metaDescription)) {
        $metaDescription = '';
    }

    $metaImageRaw = data_get($ws, 'seo.contact.meta_image', 'assets/img/img14.jpg');
    $metaImageRaw = is_string($metaImageRaw) && trim($metaImageRaw) !== '' ? trim($metaImageRaw) : 'assets/img/img14.jpg';
    $metaImage = preg_match('~^https?://~i', $metaImageRaw) ? $metaImageRaw : asset(ltrim($metaImageRaw, '/'));

    $breadcrumbBgRaw = data_get($ws, 'contact.page.breadcrumb_bg', 'assets/img/img14.jpg');
    $breadcrumbBgRaw = is_string($breadcrumbBgRaw) && trim($breadcrumbBgRaw) !== '' ? trim($breadcrumbBgRaw) : 'assets/img/img14.jpg';
    $breadcrumbBgUrl = preg_match('~^https?://~i', $breadcrumbBgRaw) ? $breadcrumbBgRaw : asset(ltrim($breadcrumbBgRaw, '/'));

    $letsTalkBgRaw = data_get($ws, 'contact.page.lets_talk_bg', 'assets/img/img8.jpeg');
    $letsTalkBgRaw = is_string($letsTalkBgRaw) && trim($letsTalkBgRaw) !== '' ? trim($letsTalkBgRaw) : 'assets/img/img8.jpeg';
    $letsTalkBgUrl = preg_match('~^https?://~i', $letsTalkBgRaw) ? $letsTalkBgRaw : asset(ltrim($letsTalkBgRaw, '/'));

    $contactEmail = data_get($ws, 'contact.email', 'market.ilsamindonesia@yahoo.com');
    $contactPhoneDisplay = data_get($ws, 'contact.phone_display', '+62 (021) 89830313 / 0314');
    $contactPhoneTel = data_get($ws, 'contact.phone_tel', '02189830313');
    $contactMapUrl = data_get($ws, 'contact.map_url', 'https://maps.app.goo.gl/reUj3juAoQ8NrGLE6');
    $contactAddressText = (string) data_get($ws, 'contact.address_text', '');
    $contactAddressShort = trim(strtok(str_replace("\r", "", $contactAddressText), "\n")) ?: 'Jl. Trans Heksa Artha Industrial Hill Area Block E No.13.';
    $openingHours = data_get($ws, 'contact.opening_hours', __('website.home.contact.opening_hours_value'));
    if (!is_string($openingHours) || trim($openingHours) === '') {
        $openingHours = __('website.home.contact.opening_hours_value');
    }

    $mapEmbedSrc = data_get($ws, 'contact.map_embed_src');
    if (!is_string($mapEmbedSrc) || trim($mapEmbedSrc) === '') {
        $mapEmbedSrc = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.0320185769133!2d107.23779097590075!3d-6.389870062501263!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e699f000ca996c1%3A0x713bcc5addd9fa22!2sPT.%20ILSAM%20GLOBAL%20INDONESIA%20(IG-103)!5e0!3m2!1sid!2sid!4v1768213302522!5m2!1sid!2sid';
    }

    $companies = data_get($ws, 'home.text_slider_companies', []);
    if (!is_array($companies)) {
        $companies = [];
    }
@endphp

@section('meta_description', $metaDescription)
@section('meta_image', $metaImage)
@section('main')
    <!-- Breadcrumb area start  -->
    <div class="breadcrumb__area breadcrumb-space overly theme-bg-heading-primary overflow-hidden">
        <div class="breadcrumb__background" data-background="{{ $breadcrumbBgUrl }}"></div>
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
            <div class="overlay"><img src="{{ $letsTalkBgUrl }}" alt=""></div>
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
                                    <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
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
                                    <a href="tel:{{ $contactPhoneTel }}">{{ $contactPhoneDisplay }}</a>
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
                                    <a href="{{ $contactMapUrl }}">{{ $contactAddressShort }}</a>
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
                                    <span>{{ $openingHours }}</span>
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
            src="{{ $mapEmbedSrc }}"
            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
    <!--contact-map-->

    <!-- text-slider area start -->
    <section class="text-slider text-slider__section-space position-relative theme-bg-primary overflow-hidden">
        <div class="text-slider__slider carouselTicker carouselTicker-nav">
            <ul class="carouselTicker__list">
                @foreach($companies as $company)
                    @php
                        $company = trim((string) $company);
                        if ($company === '') {
                            continue;
                        }
                    @endphp
                    <li>
                        <h3 data-cursor="-opaque" class="title">{{ $company }}</h3>
                    </li>
                    @if(!$loop->last)
                        <li>
                            <h4 class="title bar">//</h4>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </section>
    <!-- text-slider area end -->
@endsection