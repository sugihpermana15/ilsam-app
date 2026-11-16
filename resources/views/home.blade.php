@extends('layouts.app')

@section('title', 'PT ILSAM Global Indonesia - Chemical Colorants & Coating Solutions')

@php
  $ws = \App\Support\WebsiteSettings::all();
  $locale = app()->getLocale();

  $getLocalized = function (string $baseKey, string $fallback = '') use ($ws, $locale) {
    $value = data_get($ws, $baseKey . '.' . $locale);
    if (is_string($value) && trim($value) !== '') {
      return trim($value);
    }

    $value = data_get($ws, $baseKey . '.en');
    if (is_string($value) && trim($value) !== '') {
      return trim($value);
    }

    return $fallback;
  };

  $imgUrl = function ($raw, $w, $q) {
    $value = is_string($raw) ? trim($raw) : '';
    if ($value === '') {
      return '';
    }
    return preg_match('~^https?://~i', $value)
      ? $value
      : route('img', ['path' => ltrim($value, '/'), 'w' => $w, 'q' => $q]);
  };

  $metaDescription = data_get($ws, 'seo.home.meta_description.' . $locale);
  if (!is_string($metaDescription) || trim($metaDescription) === '') {
    $metaDescription = data_get($ws, 'seo.home.meta_description.en');
  }
  if (!is_string($metaDescription)) {
    $metaDescription = '';
  }
  if (trim($metaDescription) === '') {
    $metaDescription = 'PT ILSAM Global Indonesia supplies chemical colorants, PU resin, additives, and surface coating solutions for PU/PVC synthetic leather and footwear manufacturers in Indonesia.';
  }

  $metaImageRaw = data_get($ws, 'seo.home.meta_image', 'assets/img/img1.jpg');
  $metaImageRaw = is_string($metaImageRaw) && trim($metaImageRaw) !== '' ? trim($metaImageRaw) : 'assets/img/img1.jpg';
  $metaImage = preg_match('~^https?://~i', $metaImageRaw) ? $metaImageRaw : asset(ltrim($metaImageRaw, '/'));

  $heroSlides = data_get($ws, 'home.hero_slides', []);
  if (!is_array($heroSlides) || count($heroSlides) === 0) {
    $heroSlides = [
      'assets/img/img1.jpg',
      'assets/img/img4.jpg',
      'assets/img/img3.jpg',
      'assets/img/img10.jpg',
      'assets/img/img9.jpg',
    ];
  }

  $companies = data_get($ws, 'home.text_slider_companies', []);
  if (!is_array($companies)) {
    $companies = [];
  }

  $letsTalkBgRaw = data_get($ws, 'contact.page.lets_talk_bg', 'assets/img/img8.jpeg');
  $letsTalkBgRaw = is_string($letsTalkBgRaw) && trim($letsTalkBgRaw) !== '' ? trim($letsTalkBgRaw) : 'assets/img/img8.jpeg';
  $letsTalkBgUrl = preg_match('~^https?://~i', $letsTalkBgRaw)
    ? $letsTalkBgRaw
    : route('img', ['path' => ltrim($letsTalkBgRaw, '/'), 'w' => 1600, 'q' => 70]);

  $contactEmails = array_filter([
    data_get($ws, 'contact.emails.marketing', 'marketing@ilsam.co.id'),
    data_get($ws, 'contact.emails.purchasing', 'purchasing@ilsam.co.id'),
  ]);
  $contactPhoneDisplay = data_get($ws, 'contact.phone_display', '+62 (021) 89830313 / 0314');
  $contactPhoneTel = data_get($ws, 'contact.phone_tel', '02189830313');
  $contactMapUrl = data_get($ws, 'contact.map_url', 'https://maps.app.goo.gl/reUj3juAoQ8NrGLE6');
  $contactAddressText = (string) data_get($ws, 'contact.address_text', '');
  $contactAddressShort = trim(strtok(str_replace("\r", "", $contactAddressText), "\n")) ?: __('website.nav.top.address');

  $openingHours = data_get($ws, 'contact.opening_hours', __('website.home.contact.opening_hours_value'));
  if (!is_string($openingHours) || trim($openingHours) === '') {
    $openingHours = __('website.home.contact.opening_hours_value');
  }

  $homeAboutImageRaw = data_get($ws, 'home.sections.about_image', 'assets/img/main_who_triangle.png');
  $homeAboutImageRaw = is_string($homeAboutImageRaw) && trim($homeAboutImageRaw) !== '' ? trim($homeAboutImageRaw) : 'assets/img/main_who_triangle.png';
  $homeAboutImageUrl = $imgUrl($homeAboutImageRaw, 900, 80);

  $assetUrl = function ($raw, $fallback) {
    $value = is_string($raw) ? trim($raw) : '';
    if ($value === '') {
      $value = $fallback;
    }
    return preg_match('~^https?://~i', $value) ? $value : asset(ltrim($value, '/'));
  };

  $bannerShape2Url = $assetUrl(data_get($ws, 'home.decorations.banner_shape_2'), 'assets/img/style2/banner/shape-2.png');
  $bannerShape3Url = $assetUrl(data_get($ws, 'home.decorations.banner_shape_3'), 'assets/img/style2/banner/shape-3.png');
  $productsShape2Url = $assetUrl(data_get($ws, 'home.decorations.products_shape_2'), 'assets/img/style2/what-we-do-2/shape-2.png');
  $productsShape3Url = $assetUrl(data_get($ws, 'home.decorations.products_shape_3'), 'assets/img/style2/what-we-do-2/shape-3.png');
  $productsShape4Url = $assetUrl(data_get($ws, 'home.decorations.products_shape_4'), 'assets/img/style2/what-we-do-2/shape-4.png');

  $experienceBgRaw = data_get($ws, 'home.sections.experience_bg', 'assets/img/img6.jpg');
  $experienceBgRaw = is_string($experienceBgRaw) && trim($experienceBgRaw) !== '' ? trim($experienceBgRaw) : 'assets/img/img6.jpg';
  $experienceBgUrl = $imgUrl($experienceBgRaw, 1600, 65);

  $cardColorantsRaw = data_get($ws, 'home.sections.products_cards.colorants_bg', 'assets/img/img4.jpg');
  $cardSurfaceRaw = data_get($ws, 'home.sections.products_cards.surface_coating_agents_bg', 'assets/img/img6.jpg');
  $cardAdditiveRaw = data_get($ws, 'home.sections.products_cards.additive_coating_bg', 'assets/img/img3.jpg');
  $cardPuResinRaw = data_get($ws, 'home.sections.products_cards.pu_resin_bg', 'assets/img/img1.jpg');

  $cardColorantsUrl = $imgUrl($cardColorantsRaw, 1200, 65);
  $cardSurfaceUrl = $imgUrl($cardSurfaceRaw, 1200, 65);
  $cardAdditiveUrl = $imgUrl($cardAdditiveRaw, 1200, 65);
  $cardPuResinUrl = $imgUrl($cardPuResinRaw, 1200, 65);

  $heroSubtitle = $getLocalized('home.text.hero.subtitle', 'PT ILSAM GLOBAL INDONESIA');
  $heroTitleLine1 = $getLocalized('home.text.hero.title_line1', __('website.home.hero.title_line1'));
  $heroTitleLine2 = $getLocalized('home.text.hero.title_line2', __('website.home.hero.title_line2'));

  $aboutSubtitle = $getLocalized('home.text.about.subtitle', __('website.home.about.subtitle'));
  $aboutTitleLine1 = $getLocalized('home.text.about.title_line1', __('website.home.about.title_line1'));
  $aboutTitleLine2 = $getLocalized('home.text.about.title_line2', __('website.home.about.title_line2'));

  $establishedValue = (int) data_get($ws, 'home.text.experience.established_value', 1999);
  $clientsValue = (int) data_get($ws, 'home.text.experience.clients_value', 20);
  $yearsExperienceValue = (int) data_get($ws, 'home.text.experience.years_experience_value', 27);

  $establishedLabel = $getLocalized('home.text.experience.established_label', __('website.home.experience.established'));
  $clientsLabel = $getLocalized('home.text.experience.clients_label', __('website.home.experience.clients'));
  $yearsExperienceLabel = $getLocalized('home.text.experience.years_experience_label', __('website.home.experience.years_experience'));

  $productsLabel = $getLocalized('home.text.products.label', __('website.home.products.label'));
  $productsTitle = $getLocalized('home.text.products.title', __('website.home.products.title'));
  $productsTeaserLine1 = $getLocalized('home.text.products.teaser_line1', __('website.home.products.teaser_line1'));
  $productsTeaserLine2 = $getLocalized('home.text.products.teaser_line2', __('website.home.products.teaser_line2'));

  $productsItemColorants = $getLocalized('home.text.products.items.colorants', __('website.home.products.items.colorants'));
  $productsItemSurface = $getLocalized('home.text.products.items.surface_coating_agents', __('website.home.products.items.surface_coating_agents'));
  $productsItemAdditive = $getLocalized('home.text.products.items.additive_coating', __('website.home.products.items.additive_coating'));
  $productsItemPu = $getLocalized('home.text.products.items.pu_resin', __('website.home.products.items.pu_resin'));
@endphp

@section('meta_description', $metaDescription)
@section('canonical', route('home'))
@section('meta_image', $metaImage)

@php
  // Deskripsi kartu produk: pakai teaser dari Website Settings bila diisi admin,
  // jika tidak pakai deskripsi bawaan per produk.
  $adminTeaser = trim($getLocalized('home.text.products.teaser_line1') . ' ' . $getLocalized('home.text.products.teaser_line2'));
  $productCards = [
    ['title' => $productsItemColorants, 'desc' => __('website.pro.products.colorants'), 'img' => $cardColorantsUrl, 'icon' => 'bi-palette', 'url' => route('products.colorants')],
    ['title' => $productsItemSurface, 'desc' => __('website.pro.products.surface_coating_agents'), 'img' => $cardSurfaceUrl, 'icon' => 'bi-brush', 'url' => route('products')],
    ['title' => $productsItemAdditive, 'desc' => __('website.pro.products.additive_coating'), 'img' => $cardAdditiveUrl, 'icon' => 'bi-layers', 'url' => route('products')],
    ['title' => $productsItemPu, 'desc' => __('website.pro.products.pu_resin'), 'img' => $cardPuResinUrl, 'icon' => 'bi-droplet', 'url' => route('products')],
  ];
  $pillars = [
    ['icon' => 'bi-patch-check', 'key' => 'quality'],
    ['icon' => 'bi-truck', 'key' => 'supply'],
    ['icon' => 'bi-headset', 'key' => 'support'],
  ];
  $stats = [
    ['icon' => 'bi-calendar3', 'value' => $establishedValue, 'label' => $establishedLabel, 'suffix' => ''],
    ['icon' => 'bi-people', 'value' => $clientsValue, 'label' => $clientsLabel, 'suffix' => '+'],
    ['icon' => 'bi-award', 'value' => $yearsExperienceValue, 'label' => $yearsExperienceLabel, 'suffix' => ''],
  ];
  $companyNames = array_values(array_filter(array_map(fn ($c) => trim((string) $c), $companies), fn ($c) => $c !== ''));
  $contactItems = [
    ['icon' => 'bi-envelope', 'label' => __('website.home.contact.email_address')],
    ['icon' => 'bi-telephone', 'label' => __('website.home.contact.phone_number')],
    ['icon' => 'bi-geo-alt', 'label' => __('website.home.contact.our_location')],
    ['icon' => 'bi-clock', 'label' => __('website.home.contact.opening_hour')],
  ];
@endphp

@section('main')
  <!-- Hero -->
  <section class="banner sp-hero overflow-hidden">
    <div class="swiper banner__slider">
      <div class="swiper-wrapper">
        @foreach($heroSlides as $idx => $slidePath)
          @php
            $slidePath = is_string($slidePath) ? trim($slidePath) : '';
            if ($slidePath === '') {
              continue;
            }
            $slideBgUrl = $imgUrl($slidePath, 1600, 65);
            $isFirst = (int) $idx === 0;
          @endphp
          <div class="swiper-slide">
            <div class="banner__item banner__space theme-bg-heading-primary">
              <div class="banner__item-bg" data-background="{{ $slideBgUrl }}"></div>
              <div class="container rr-shape-p-c_1">
                <div class="row">
                  <div class="col-12 col-xl-9">
                    <div class="banner__item-content sp-hero__content">
                      <span class="sp-eyebrow sp-eyebrow--light">{{ $heroSubtitle }}</span>
                      @if($isFirst)
                        <h1 class="sp-hero__title">{{ $heroTitleLine1 }}<br>{{ $heroTitleLine2 }}</h1>
                      @else
                        <p class="sp-hero__title" role="heading" aria-level="2">{{ $heroTitleLine1 }}<br>{{ $heroTitleLine2 }}</p>
                      @endif
                      <p class="sp-hero__lead">{{ __('website.pro.hero_lead') }}</p>
                      <div class="sp-hero__actions">
                        <a href="{{ route('products') }}" class="sp-btn sp-btn--primary">{{ __('website.pro.cta_products') }} <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                        <a href="{{ route('contact') }}" class="sp-btn sp-btn--glass">{{ __('website.pro.cta_contact') }}</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="banner__slider__controller-view">
        <div class="swiper-pagination"></div>
        <div class="timer-swiper">
          <div class="timer-swiperAfter"></div>
        </div>
      </div>

      <div class="banner__slider__arrow">
        <button class="banner__slider__arrow-prev" aria-label="Previous slide">
          <svg width="20" height="11" viewBox="0 0 20 11" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M19 5.5L1 5.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M6.25 1L1 5.5L6.25 10" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
        <button class="banner__slider__arrow-next" aria-label="Next slide">
          <svg width="20" height="11" viewBox="0 0 20 11" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 5.5L19 5.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M13.75 1L19 5.5L13.75 10" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
      </div>
    </div>
  </section>

  <!-- Statistik -->
  <section class="sp-stats" aria-label="{{ $yearsExperienceLabel }}">
    <div class="container">
      <div class="sp-stats__card">
        @foreach($stats as $stat)
          <div class="sp-stat">
            <span class="sp-stat__icon"><i class="bi {{ $stat['icon'] }}" aria-hidden="true"></i></span>
            <div>
              <div class="sp-stat__value"><span class="odometer" data-count="{{ $stat['value'] }}">0</span>{{ $stat['suffix'] }}</div>
              <div class="sp-stat__label">{{ $stat['label'] }}</div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- Tentang -->
  <section class="sp-section sp-about">
    <div class="container">
      <div class="row align-items-center gy-5">
        <div class="col-lg-6">
          <span class="sp-eyebrow">{{ $aboutSubtitle }}</span>
          <h2 class="sp-title">{{ $aboutTitleLine1 }} {{ $aboutTitleLine2 }}</h2>
          <p class="sp-lead">{{ __('website.about.company_overview.p1') }}</p>
          <p class="sp-text">{{ __('website.about.company_overview.p3') }}</p>
          <a href="{{ route('aboutus') }}" class="sp-btn sp-btn--outline">{{ __('website.pro.about_cta') }} <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>
        <div class="col-lg-6">
          <div class="sp-about__figure wow fadeInUp" data-wow-delay=".1s">
            <img loading="lazy" decoding="async" src="{{ $homeAboutImageUrl }}" alt="{{ __('website.home.about.image_alt') }}">
          </div>
        </div>
      </div>

      <div class="row g-4 sp-pillars">
        @foreach($pillars as $i => $pillar)
          <div class="col-md-4">
            <div class="sp-pillar wow fadeInUp" data-wow-delay="{{ 0.1 + $i * 0.1 }}s">
              <span class="sp-pillar__icon"><i class="bi {{ $pillar['icon'] }}" aria-hidden="true"></i></span>
              <h3 class="sp-pillar__title">{{ __('website.pro.pillars.' . $pillar['key'] . '.title') }}</h3>
              <p class="sp-pillar__desc">{{ __('website.pro.pillars.' . $pillar['key'] . '.desc') }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- Produk -->
  <section class="sp-section sp-section--alt">
    <div class="container">
      <div class="sp-section__head">
        <div>
          <span class="sp-eyebrow">{{ $productsLabel }}</span>
          <h2 class="sp-title mb-0">{{ $productsTitle }}</h2>
        </div>
        <a href="{{ route('products') }}" class="sp-link">{{ __('website.pro.all_products') }} <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
      </div>

      <div class="row g-4">
        @foreach($productCards as $i => $card)
          <div class="col-xl-3 col-md-6">
            <a href="{{ $card['url'] }}" class="sp-product wow fadeInUp" data-wow-delay="{{ 0.05 + $i * 0.08 }}s">
              <span class="sp-product__media">
                @if($card['img'] !== '')
                  <img loading="lazy" decoding="async" src="{{ $card['img'] }}" alt="">
                @endif
                <span class="sp-product__icon"><i class="bi {{ $card['icon'] }}" aria-hidden="true"></i></span>
              </span>
              <span class="sp-product__body">
                <span class="sp-product__title">{{ $card['title'] }}</span>
                <span class="sp-product__desc">{{ $adminTeaser !== '' ? $adminTeaser : $card['desc'] }}</span>
                <span class="sp-product__more">{{ __('website.pro.learn_more') }} <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
              </span>
            </a>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- Pelanggan -->
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

  <!-- Kontak -->
  <section class="sp-section sp-contact" id="contact">
    <div class="container">
      <div class="sp-contact__wrap">
        <div class="sp-contact__info" style="--sp-contact-bg: url('{{ $letsTalkBgUrl }}');">
          <span class="sp-eyebrow sp-eyebrow--light">{{ __('website.home.contact.subtitle') }}</span>
          <h2 class="sp-title sp-title--light">{{ __('website.home.contact.title_line1') }} {{ __('website.home.contact.title_line2') }}</h2>
          <p class="sp-contact__desc">{{ __('website.home.contact.desc') }}</p>

          <ul class="sp-contact__list">
            <li>
              <span class="sp-contact__icon"><i class="bi {{ $contactItems[0]['icon'] }}" aria-hidden="true"></i></span>
              <div>
                <span class="sp-contact__label">{{ $contactItems[0]['label'] }}</span>
                @foreach($contactEmails as $contactEmail)
                  <a class="d-block" href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                @endforeach
              </div>
            </li>
            <li>
              <span class="sp-contact__icon"><i class="bi {{ $contactItems[1]['icon'] }}" aria-hidden="true"></i></span>
              <div>
                <span class="sp-contact__label">{{ $contactItems[1]['label'] }}</span>
                <a href="tel:{{ $contactPhoneTel }}">{{ $contactPhoneDisplay }}</a>
              </div>
            </li>
            <li>
              <span class="sp-contact__icon"><i class="bi {{ $contactItems[2]['icon'] }}" aria-hidden="true"></i></span>
              <div>
                <span class="sp-contact__label">{{ $contactItems[2]['label'] }}</span>
                <a href="{{ $contactMapUrl }}" target="_blank" rel="noopener">{{ $contactAddressShort }}</a>
              </div>
            </li>
            <li>
              <span class="sp-contact__icon"><i class="bi {{ $contactItems[3]['icon'] }}" aria-hidden="true"></i></span>
              <div>
                <span class="sp-contact__label">{{ $contactItems[3]['label'] }}</span>
                <span>{{ $openingHours }}</span>
              </div>
            </li>
          </ul>
        </div>

        <div class="sp-contact__form">
          <form id="contact-form" class="request-a-quote__form home-2 sp-form" method="POST" action="{{ route('contact.send') }}">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label class="sp-form__label" for="name">{{ __('website.home.contact.form.name') }}</label>
                <div class="validation__wrapper-up position-relative">
                  <input name="name" id="name" type="text" autocomplete="name">
                </div>
              </div>
              <div class="col-md-6">
                <label class="sp-form__label" for="tel">{{ __('website.home.contact.form.tel') }}</label>
                <div class="validation__wrapper-up position-relative">
                  <input name="tel" id="tel" type="tel" autocomplete="tel">
                </div>
              </div>
              <div class="col-md-6">
                <label class="sp-form__label" for="email">{{ __('website.home.contact.form.email') }}</label>
                <div class="validation__wrapper-up position-relative">
                  <input name="email" id="email" type="email" autocomplete="email">
                </div>
              </div>
              <div class="col-md-6">
                <label class="sp-form__label" for="inquiries">{{ __('website.home.contact.form.inquiries') }}</label>
                <div class="validation__wrapper-up position-relative">
                  <input name="inquiries" id="inquiries" type="text">
                </div>
              </div>
              <div class="col-12">
                <label class="sp-form__label" for="textarea">{{ __('website.home.contact.form.details') }}</label>
                <div class="validation__wrapper-up position-relative">
                  <textarea name="textarea" id="textarea" rows="5"></textarea>
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
    </div>
  </section>
@endsection
