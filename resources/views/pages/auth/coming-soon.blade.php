@extends('partials.layouts.master_auth')

@section('title', __('common.pages.coming_soon.title'))

@section('content')


    <!-- START -->
    <div>
        <img src="assets/images/auth/auth_bg.jpeg" alt="{{ __('errors.pages.common.auth_bg_alt') }}"
            class="auth-bg light w-full h-full position-absolute top-0">
        <img src="assets/images/auth/auth_bg_dark.jpg" alt="{{ __('errors.pages.common.auth_bg_alt') }}" class="auth-bg d-none dark">
        <div class="container">
            <div class="justify-content-center align-items-center min-vh-100 row gy-0">
                <div class="col-lg-8">
                    <div class="position-relative justify-content-center py-10">
                        <div class="mb-5 text-center">
                            <h2 class="fw-normal">{!! __('common.pages.coming_soon.welcome_html', ['brand' => 'Ilsam']) !!}</h2>
                            <p class="text-muted mb-0">{{ __('common.pages.coming_soon.desc') }}</p>
                        </div>
                        <img class="max-h-320px mx-auto d-block" src="assets/images/vector/coming-soon.svg"
                            alt="{{ __('errors.pages.common.vector_alt') }}">
                        <form class="form-custom mt-16">
                            <div id="countdown">
                                <ul class="list-inline text-center d-flex flex-column flex-md-row gap-1">
                                    <li class="list-inline-item p-4 w-100 min-w-100px rounded-2">
                                        <h2 class="mb-1 fs-45" id="days"></h2>
                                        <span>{{ __('common.pages.coming_soon.labels.days') }}</span>
                                    </li>
                                    <li class="list-inline-item p-4 w-100 min-w-100px rounded-2">
                                        <h2 class="mb-1 fs-45" id="hours"></h2>
                                        <span>{{ __('common.pages.coming_soon.labels.hours') }}</span>
                                    </li>
                                    <li class="list-inline-item p-4 w-100 min-w-100px rounded-2">
                                        <h2 class="mb-1 fs-45" id="minutes"></h2>
                                        <span>{{ __('common.pages.coming_soon.labels.minutes') }}</span>
                                    </li>
                                    <li class="list-inline-item p-4 w-100 min-w-100px rounded-2">
                                        <h2 class="mb-1 fs-45" id="seconds"></h2>
                                        <span>{{ __('common.pages.coming_soon.labels.seconds') }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="position-relative subscribe-btn max-w-500px mt-10 mx-auto">
                                <input type="text"
                                    class="form-control form-control-lg fs-14 min-h-56px form-control-icon"
                                    id="companyNameLayout4" placeholder="{{ __('common.pages.coming_soon.email_placeholder') }}" required>
                                <button class="btn btn-primary ">{{ __('common.pages.coming_soon.subscribe') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <script src="assets/js/auth/coming-soon.init.js"></script>
    <script type="module" src="assets/js/app.js"></script>
@endsection
