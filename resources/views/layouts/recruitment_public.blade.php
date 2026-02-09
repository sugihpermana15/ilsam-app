<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Rekrutmen')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- layout setup -->
    <script type="module" src="{{ asset('assets/js/layout-setup.js') }}"></script>

    @php
        $ws = \App\Support\WebsiteSettings::all();

        $toAssetUrl = function ($raw, $fallback) {
            $value = is_string($raw) ? trim($raw) : '';
            if ($value === '') {
                $value = $fallback;
            }

            if (preg_match('~^https?://~i', $value)) {
                $path = (string) parse_url($value, PHP_URL_PATH);
                $query = (string) parse_url($value, PHP_URL_QUERY);
                if ($path !== '' && (str_starts_with($path, '/assets/') || str_starts_with($path, '/storage/'))) {
                    $local = asset(ltrim($path, '/'));
                    return $query !== '' ? ($local . '?' . $query) : $local;
                }
                return $value;
            }

            return asset(ltrim($value, '/'));
        };

        $faviconUrl = $toAssetUrl(data_get($ws, 'brand.favicon'), 'assets/img/favicon.png');

        $logoMinPath = 'assets/img/logo-min.png';
        $logoMinExists = file_exists(public_path($logoMinPath));
        $logoUrl = $logoMinExists
            ? asset($logoMinPath)
            : $toAssetUrl(data_get($ws, 'brand.logo'), 'assets/img/logo.png');
    @endphp

    <link rel="shortcut icon" href="{{ $faviconUrl }}">

    @include('partials.head-css')
    <style>
        body.recruitment-public-bg {
            background:
                radial-gradient(circle at 50% -10%, rgba(var(--bs-primary-rgb, 13, 110, 253), 0.28) 0%, rgba(var(--bs-primary-rgb, 13, 110, 253), 0) 55%),
                radial-gradient(circle at 0% 20%, rgba(var(--bs-primary-rgb, 13, 110, 253), 0.10) 0%, rgba(var(--bs-primary-rgb, 13, 110, 253), 0) 45%),
                radial-gradient(circle at 100% 30%, rgba(var(--bs-primary-rgb, 13, 110, 253), 0.10) 0%, rgba(var(--bs-primary-rgb, 13, 110, 253), 0) 45%),
                var(--bs-body-bg, #f8f9fa);
        }
    </style>
    @yield('css')
</head>

<body class="recruitment-public-bg">
    <main class="min-vh-100 py-4 py-md-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-md-5">
                            <div class="text-center mb-4">
                                <div class="d-flex justify-content-center mb-2">
                                    <img src="{{ $logoUrl }}" alt="Ilsam" style="height: 52px" class="d-block" />
                                </div>
                                <div class="text-muted small text-uppercase">PT Ilsam Global Indonesia</div>
                                <div class="fw-semibold" style="font-size: 1.1rem; line-height: 1.2">@yield('recruitment_heading')</div>
                            </div>

                            @yield('content')
                        </div>
                    </div>

                    @yield('footer')
                </div>
            </div>
        </div>
    </main>

    @include('partials.vendor-scripts')
    @yield('js')
</body>

</html>
