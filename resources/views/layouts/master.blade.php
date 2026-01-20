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
<link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

@yield('css')
@include('partials.head-css')

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