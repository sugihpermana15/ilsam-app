<!DOCTYPE html>
<html lang="en">

<meta charset="utf-8" />
<title>@yield('title', ' Ilsam ')</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta content="Admin & Dashboards Ilsam" name="description" />
<meta content="Sugih Permana Sejati" name="author" />

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

@yield('css')
@include('partials.head-css')

<body>

  @yield('content')

  @include('partials.vendor-scripts')

  @yield('js')

</body>

</html>