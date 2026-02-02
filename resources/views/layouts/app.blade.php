<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @php
        $rawTitle = strip_tags(trim($__env->yieldContent('title')));
        $seoTitle = preg_replace('/\s+/', ' ', $rawTitle);

        $brandName = config('app.name', 'Ilsam Global Indonesia');
        $seoTitle = $seoTitle !== '' ? $seoTitle : $brandName;
        if ($brandName !== '' && !str_contains(mb_strtolower($seoTitle), mb_strtolower($brandName))) {
            $seoTitle = rtrim($seoTitle) . ' | ' . $brandName;
        }

        $rawDescription = strip_tags(trim($__env->yieldContent('meta_description')));
        $seoDescription = preg_replace('/\s+/', ' ', $rawDescription);
        $seoDescription = $seoDescription !== ''
            ? $seoDescription
            : 'PT ILSAM GLOBAL INDONESIA (Ilsam) — chemical colorants & coating solutions for PU/PVC synthetic leather and footwear manufacturing. Based in Karawang, West Java, serving Cikarang, Bekasi, Karawang, Jakarta, and across Java & Indonesia.';

        $rawCanonical = trim($__env->yieldContent('canonical'));
        $seoUrl = $rawCanonical !== '' ? $rawCanonical : url()->current();

        $rawImage = trim($__env->yieldContent('meta_image'));
        $seoImage = $rawImage !== '' ? $rawImage : asset('assets/img/logo.png');

        $rawImageAlt = strip_tags(trim($__env->yieldContent('meta_image_alt')));
        $seoImageAlt = preg_replace('/\s+/', ' ', $rawImageAlt);
        $seoImageAlt = $seoImageAlt !== '' ? $seoImageAlt : (config('app.name', 'Ilsam Global Indonesia') . ' logo');

        $rawRobots = trim($__env->yieldContent('meta_robots'));
        $seoRobots = $rawRobots !== '' ? $rawRobots : 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1';

        $companyEmail = 'market.ilsamindonesia@yahoo.com';
        $companyPhone = '+62 21 89830313';
        $companyPhoneAlt = '+62 21 89830314';
        $companyMapUrl = 'https://maps.app.goo.gl/reUj3juAoQ8NrGLE6';
        $hqWebsite = 'https://www.ilsam.com';

        $companyAddress = [
            'streetAddress' => 'Jl. Trans Heksa Artha Industrial Hill Area Block E No.13, Wanajaya Village, District Telukjambe Barat',
            'addressLocality' => 'Karawang',
            'addressRegion' => 'West Java',
            'postalCode' => '41361',
            'addressCountry' => 'ID',
        ];

        // Coordinates from the embedded Google Maps (Contact page)
        $companyGeo = [
            'latitude' => -6.389870062501263,
            'longitude' => 107.23779097590075,
        ];

        $locale = app()->getLocale();
        $ogLocaleMap = [
            'en' => 'en_US',
            'id' => 'id_ID',
            'ko' => 'ko_KR',
        ];
        $ogLocale = $ogLocaleMap[$locale] ?? str_replace('-', '_', $locale);
    @endphp

    <title>{{ $seoTitle }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}" type="image/x-icon">

    <!-- meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="view-transition" content="same-origin">
    <meta name="theme-color" content="Red">
    <meta name="robots" content="{{ $seoRobots }}">
    <link rel="canonical" href="{{ $seoUrl }}">

    <meta name="description" content="{{ $seoDescription }}">

    <!-- Location hints (not ranking factors, but useful for parsers) -->
    <meta name="geo.region" content="ID-JB">
    <meta name="geo.placename" content="Karawang, West Java, Indonesia">
    <meta name="geo.position" content="{{ $companyGeo['latitude'] }};{{ $companyGeo['longitude'] }}">
    <meta name="ICBM" content="{{ $companyGeo['latitude'] }}, {{ $companyGeo['longitude'] }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:site_name" content="{{ config('app.name', 'Ilsam Global Indonesia') }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="{{ $ogLocale }}">
    <meta property="og:url" content="{{ $seoUrl }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:image:alt" content="{{ $seoImageAlt }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">

    <!-- Performance hints -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">

    <!-- Structured data (JSON-LD) -->
    @php
        $baseUrl = rtrim(config('app.url', url('/')), '/');
        $schemaGraph = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    '@id' => $baseUrl . '/#organization',
                    'name' => config('app.name', 'Ilsam Global Indonesia'),
                    'url' => $baseUrl,
                    'sameAs' => [$hqWebsite],
                    'description' => $seoDescription,
                    'industry' => 'Chemical manufacturing',
                    'knowsAbout' => [
                        'Chemical colorants',
                        'Colorants for PU synthetic leather',
                        'Colorants for PVC synthetic leather',
                        'Colorants for printing',
                        'Water-based colorants',
                        'Surface coating agents',
                        'Additive coating',
                        'PU resin',
                    ],
                    'email' => $companyEmail,
                    'telephone' => [$companyPhone, $companyPhoneAlt],
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => $companyAddress['streetAddress'],
                        'addressLocality' => $companyAddress['addressLocality'],
                        'addressRegion' => $companyAddress['addressRegion'],
                        'postalCode' => $companyAddress['postalCode'],
                        'addressCountry' => $companyAddress['addressCountry'],
                    ],
                    'areaServed' => [
                        ['@type' => 'City', 'name' => 'Cikarang'],
                        ['@type' => 'City', 'name' => 'Bekasi'],
                        ['@type' => 'City', 'name' => 'Karawang'],
                        ['@type' => 'City', 'name' => 'Jakarta'],
                        ['@type' => 'AdministrativeArea', 'name' => 'Java'],
                        ['@type' => 'Country', 'name' => 'Indonesia'],
                    ],
                    'contactPoint' => [
                        [
                            '@type' => 'ContactPoint',
                            'contactType' => 'sales',
                            'telephone' => $companyPhone,
                            'email' => $companyEmail,
                            'areaServed' => 'ID',
                            'availableLanguage' => ['id', 'en'],
                        ],
                    ],
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => asset('assets/img/logo.png'),
                    ],
                ],
                [
                    '@type' => 'LocalBusiness',
                    '@id' => $baseUrl . '/#localbusiness',
                    'name' => config('app.name', 'Ilsam Global Indonesia'),
                    'url' => $baseUrl,
                    'sameAs' => [$hqWebsite],
                    'image' => $seoImage,
                    'description' => $seoDescription,
                    'telephone' => [$companyPhone, $companyPhoneAlt],
                    'email' => $companyEmail,
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => $companyAddress['streetAddress'],
                        'addressLocality' => $companyAddress['addressLocality'],
                        'addressRegion' => $companyAddress['addressRegion'],
                        'postalCode' => $companyAddress['postalCode'],
                        'addressCountry' => $companyAddress['addressCountry'],
                    ],
                    'geo' => [
                        '@type' => 'GeoCoordinates',
                        'latitude' => $companyGeo['latitude'],
                        'longitude' => $companyGeo['longitude'],
                    ],
                    'hasMap' => $companyMapUrl,
                    'areaServed' => [
                        ['@type' => 'City', 'name' => 'Cikarang'],
                        ['@type' => 'City', 'name' => 'Bekasi'],
                        ['@type' => 'City', 'name' => 'Karawang'],
                        ['@type' => 'City', 'name' => 'Jakarta'],
                        ['@type' => 'AdministrativeArea', 'name' => 'Java'],
                        ['@type' => 'Country', 'name' => 'Indonesia'],
                    ],
                    'parentOrganization' => ['@id' => $baseUrl . '/#organization'],
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => $baseUrl . '/#website',
                    'url' => $baseUrl,
                    'name' => config('app.name', 'Ilsam Global Indonesia'),
                    'publisher' => ['@id' => $baseUrl . '/#organization'],
                    'inLanguage' => $locale,
                ],
                [
                    '@type' => 'WebPage',
                    '@id' => $seoUrl . '#webpage',
                    'url' => $seoUrl,
                    'name' => $seoTitle,
                    'description' => $seoDescription,
                    'isPartOf' => ['@id' => $baseUrl . '/#website'],
                    'about' => ['@id' => $baseUrl . '/#organization'],
                    'inLanguage' => $locale,
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($schemaGraph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

    <!-- CSS here -->
    <link rel="stylesheet" href="{{ asset('assets/css/vendor/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/vendor/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/vendor/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/vendor/fontawesome-pro.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/vendor/spacing.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/odometer-theme-default.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/carouselTicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/image-reveal-hover.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    @stack('head')
</head>

<body>

    @php
        $disablePreloader = request()->routeIs('career') || request()->is('career');
    @endphp

    <!-- preloader start -->
    <div class="preloader" data-preloader="{{ $disablePreloader ? 'disabled' : 'active' }}" data-loaded="progress"
        @if($disablePreloader) style="display:none" @endif>
        <div class="preloader-close">x</div>
        <div class="wrapper w-100 text-center">
            <div id="progress-bar" class="preloader-text" data-text="ILSAM"></div>
            <div class="progress-bar">
                <div class="progress"></div>
            </div>
        </div>
    </div>
    <!-- preloader end -->

    <!-- preloader start -->
    <div class="loading-form" @if($disablePreloader) style="display:none" @endif>
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <!-- preloader end -->

    <!-- Backtotop start -->
    <div id="scroll-percentage">
        <span id="scroll-percentage-value"></span>
    </div>
    <!-- Backtotop end -->

    <!-- cursorAnimation start -->
    <div class="cursor-wrapper relative">
        <div class="cursor"></div>
        <div class="cursor-follower"></div>
    </div>
    <!-- cursorAnimation end -->

    @include('partials.navbar')

    <main>
        @yield('main')
    </main>

    @include('partials.footer')

    <!-- JS here -->
    <script src="{{ asset('assets/js/vendor/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/meanmenu.min.js') }}"></script>
    <script>
        // Odometer default format uses thousands separators (e.g. 1,999).
        // Force plain digits for all counters.
        window.odometerOptions = { format: "d" };
    </script>
    <script src="{{ asset('assets/js/plugins/odometer.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/swiper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/wow.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/type.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/nice-select.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/jquery.appear.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/parallax.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/parallax-scroll.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/gsap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/ScrollTrigger.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/SplitText.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/tween-max.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/draggable.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/jquery.carouselTicker.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/ajax-form.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/TextPlugin.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/lang-switch.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/magiccursor.js') }}"></script>
    <script src="{{ asset('assets/js/image-reveal-hover.js') }}"></script>
</body>

</html>