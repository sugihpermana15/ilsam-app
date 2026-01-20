<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title')</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}" type="image/x-icon">

    <!-- meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="view-transition" content="same-origin">
    <meta name="theme-color" content="Red">
    <meta property="og:site_name" content="Consulo">
    <meta property="og:url" content="https://themeforest.net/user/spreethemes/portfolio">
    <meta property="og:title" content="Creative Business Consulting Template">
    <meta property="og:description"
        content="A versatile HTML template designed for corporate entities and professional businesses.">
    <meta name="description"
        content="Consulo is a creative business consulting Bootstrap 5 template designed for corporate entities and professional businesses.">

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
    <script src="{{ asset('assets/js/plugins/magiccursor.js') }}"></script>
    <script src="{{ asset('assets/js/image-reveal-hover.js') }}"></script>
</body>

</html>