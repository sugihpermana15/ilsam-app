<!DOCTYPE html>
<html lang="en">

<meta charset="utf-8" />
<title>@yield('title')</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta content="Ilsam - Admin & Dashboards" name="description" />
<meta content="Sugih Permana Sejati" name="author" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- layout setup -->
<script type="module" src="{{ asset('assets/js/layout-setup.js') }}"></script>

<!-- App favicon -->
@php
    $ws = \App\Support\WebsiteSettings::all();
    $faviconRaw = data_get($ws, 'brand.favicon', 'assets/img/favicon.png');
    $faviconRaw = is_string($faviconRaw) && trim($faviconRaw) !== '' ? trim($faviconRaw) : 'assets/img/favicon.png';
    $faviconUrl = preg_match('~^https?://~i', $faviconRaw) ? $faviconRaw : asset(ltrim($faviconRaw, '/'));
@endphp
<link rel="shortcut icon" href="{{ $faviconUrl }}">
@include('partials.head-css')

@yield('css')

<!-- Font Awesome (global) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<body>

    @include('partials.header')
    @include('partials.sidebar')

    <main class="app-wrapper">
        <div class="container-fluid">

            @include('partials.page-title')

            @yield('content')
            @include('partials.scroll-to-top')

        </div>
    </main>

    @include('partials.footer_admin')

    @include('partials.vendor-scripts')

    @yield('js')

</body>

</html>