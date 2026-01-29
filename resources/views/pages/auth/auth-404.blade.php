@extends('partials.layouts.master_auth')

@section('title', __('errors.titles.404'))

@section('content')


    <!-- START -->
    <div>
        <img src="{{ asset('assets/images/auth/auth_bg.jpeg') }}" alt="{{ __('errors.pages.common.auth_bg_alt') }}"
            class="auth-bg light w-full h-full opacity-60 position-absolute top-0">
        <img src="{{ asset('assets/images/auth/auth_bg_dark.jpg') }}" alt="{{ __('errors.pages.common.auth_bg_alt') }}" class="auth-bg d-none dark">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100 py-10">
                <div class="col-lg-8">
                    <div class="position-relative text-center">
                        <img class="img-fluid min-h-320px" src="{{ asset('assets/images/vector/404.svg') }}"
                            alt="{{ __('errors.pages.common.vector_alt') }}">
                        <div class="mt-4 mt-md-16 text-center">
                            <h1 class="fw-semibold fs-45"><span class="fw-bold text-primary">{{ __('errors.pages.404.headline_prefix') }} </span>{{ __('errors.pages.404.headline') }}</h1>
                            <p class="text-muted fs-16 mb-8">{{ __('errors.pages.404.desc') }}</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">{{ __('errors.pages.actions.back_to_home') }}<i
                                    class="bi bi-send-fill ms-2"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
@endsection