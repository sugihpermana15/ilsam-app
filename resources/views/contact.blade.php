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

    $contactEmails = array_filter([
        data_get($ws, 'contact.emails.marketing', 'marketing@ilsam.co.id'),
        data_get($ws, 'contact.emails.purchasing', 'purchasing@ilsam.co.id'),
    ]);
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
    <div class="breadcrumb__area breadcrumb-space sp-page-hero overly theme-bg-heading-primary overflow-hidden">
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

    @php
        $companyNames = array_values(array_filter(array_map(fn ($c) => trim((string) $c), $companies), fn ($c) => $c !== ''));
        $addressLines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $contactAddressText) ?: [])));
        if (empty($addressLines)) {
            $addressLines = [$contactAddressShort];
        }
    @endphp

    {{-- Pengantar & kartu kontak --}}
    <section class="sp-section sp-contact-page">
        <div class="container">
            <div class="sp-section__head sp-section__head--center">
                <div>
                    <span class="sp-eyebrow">{{ __('website.home.contact.subtitle') }}</span>
                    <h2 class="sp-title">{{ __('website.home.contact.title_line1') }} {{ __('website.home.contact.title_line2') }}</h2>
                    <p class="sp-lead mb-0">{{ __('website.home.contact.desc') }}</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-3 col-md-6">
                    <div class="sp-contact-card wow fadeInUp" data-wow-delay=".05s">
                        <span class="sp-pillar__icon"><i class="bi bi-envelope" aria-hidden="true"></i></span>
                        <h3 class="sp-contact-card__title">{{ __('website.home.contact.email_address') }}</h3>
                        @foreach($contactEmails as $contactEmail)
                            <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                        @endforeach
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="sp-contact-card wow fadeInUp" data-wow-delay=".13s">
                        <span class="sp-pillar__icon"><i class="bi bi-telephone" aria-hidden="true"></i></span>
                        <h3 class="sp-contact-card__title">{{ __('website.home.contact.phone_number') }}</h3>
                        <a href="tel:{{ $contactPhoneTel }}">{{ $contactPhoneDisplay }}</a>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="sp-contact-card wow fadeInUp" data-wow-delay=".21s">
                        <span class="sp-pillar__icon"><i class="bi bi-geo-alt" aria-hidden="true"></i></span>
                        <h3 class="sp-contact-card__title">{{ __('website.home.contact.our_location') }}</h3>
                        <a href="{{ $contactMapUrl }}" target="_blank" rel="noopener">{{ $contactAddressShort }}</a>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="sp-contact-card wow fadeInUp" data-wow-delay=".29s">
                        <span class="sp-pillar__icon"><i class="bi bi-clock" aria-hidden="true"></i></span>
                        <h3 class="sp-contact-card__title">{{ __('website.home.contact.opening_hour') }}</h3>
                        <span>{{ $openingHours }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Formulir & peta --}}
    <section class="sp-section sp-section--alt">
        <div class="container">
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-7">
                    <div class="sp-panel">
                        <h2 class="sp-panel__title">{{ __('website.pro.contact_form_title') }}</h2>
                        <p class="sp-text">{{ __('website.pro.contact_form_desc') }}</p>

                        <form id="contact-form" class="request-a-quote__form home-2 sp-form" method="POST" action="{{ route('contact.send') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="sp-form__label" for="name">{{ __('website.home.contact.form.name') }}</label>
                                    <div class="validation__wrapper-up position-relative">
                                        <input name="name" id="name" type="text" autocomplete="name" value="{{ old('name') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="sp-form__label" for="tel">{{ __('website.home.contact.form.tel') }}</label>
                                    <div class="validation__wrapper-up position-relative">
                                        <input name="tel" id="tel" type="tel" autocomplete="tel" value="{{ old('tel') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="sp-form__label" for="email">{{ __('website.home.contact.form.email') }}</label>
                                    <div class="validation__wrapper-up position-relative">
                                        <input name="email" id="email" type="email" autocomplete="email" value="{{ old('email') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="sp-form__label" for="inquiries">{{ __('website.home.contact.form.inquiries') }}</label>
                                    <div class="validation__wrapper-up position-relative">
                                        <input name="inquiries" id="inquiries" type="text" value="{{ old('inquiries') }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="sp-form__label" for="textarea">{{ __('website.home.contact.form.details') }}</label>
                                    <div class="validation__wrapper-up position-relative">
                                        <textarea name="textarea" id="textarea" rows="6">{{ old('textarea') }}</textarea>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="sp-btn sp-btn--primary">{{ __('website.home.contact.form.send') }} <i class="bi bi-send" aria-hidden="true"></i></button>
                                </div>

                                <div class="col-12">
                                    <div class="ajax-response">
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

                <div class="col-lg-5">
                    <div class="sp-panel sp-map">
                        <div class="sp-map__frame">
                            <iframe src="{{ $mapEmbedSrc }}" title="{{ __('website.home.contact.our_location') }}" allowfullscreen loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <div class="sp-map__body">
                            <h2 class="sp-panel__title">{{ __('website.pro.contact_visit_title') }}</h2>
                            <address class="sp-map__address">
                                @foreach($addressLines as $addressLine)
                                    {{ $addressLine }}@if(!$loop->last)<br>@endif
                                @endforeach
                            </address>
                            <a href="{{ $contactMapUrl }}" target="_blank" rel="noopener" class="sp-btn sp-btn--outline">
                                {{ __('website.pro.contact_map_open') }} <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Pelanggan --}}
    @if(!empty($companyNames))
        <section class="sp-clients text-slider overflow-hidden" aria-label="{{ __('website.pro.clients_label') }}">
            <div class="container">
                <p class="sp-clients__label">{{ __('website.pro.clients_label') }}</p>
            </div>
            <div class="text-slider__slider carouselTicker carouselTicker-nav">
                <ul class="carouselTicker__list">
                    @foreach($companyNames as $company)
                        <li><span class="sp-clients__name">{{ $company }}</span></li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif
@endsection
