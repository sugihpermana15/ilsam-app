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
<link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

@yield('css')
@include('partials.head-css')

<body>

  @yield('content')

  @include('partials.vendor-scripts')

  @yield('js')

</body>

</html>