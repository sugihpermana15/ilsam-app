@extends('layouts.master_auth')

@section('title', 'Ilsam - ' . __('auth.sign_up'))

@section('content')


    <!-- START -->
    <div>
        <img src="{{ asset('assets/img/auth/login_bg.jpg') }}" alt="Auth Background"
            class="auth-bg light w-full h-full auth-bg-cover opacity-60 position-absolute top-0">
        <img src="{{ asset('assets/img/auth/auth_bg_dark.jpg') }}" alt="Auth Background" class="auth-bg d-none dark">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100 py-10">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card mx-xxl-8">
                        <div class="card-body py-12 px-8">
                            <img src="{{ asset('assets/img/logo.svg') }}" alt="Logo Dark" height="30"
                                class="mb-4 mx-auto d-block">
                            <h6 class="mb-3 mb-8 fw-medium text-center">{{ __('auth.create_account_to_login') }}</h6>
                            <form method="POST" action="{{ route('register.store') }}">
                                @csrf

                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if($errors->any())
                                    <div class="alert alert-danger">{{ __('auth.errors_fix_below') }}</div>
                                @endif

                                <div class="row g-4">
                                    <div class="col-6">
                                        <label for="firstname" class="form-label">{{ __('auth.first_name') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="firstname"
                                            class="form-control @error('firstname') is-invalid @enderror" id="firstname"
                                            placeholder="{{ __('auth.enter_first_name') }}" required value="{{ old('firstname') }}">
                                        @error('firstname')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="lastname" class="form-label">{{ __('auth.last_name') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="lastname"
                                            class="form-control @error('lastname') is-invalid @enderror" id="lastname"
                                            placeholder="{{ __('auth.enter_last_name') }}" required value="{{ old('lastname') }}">
                                        @error('lastname')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="username" class="form-label">{{ __('auth.username') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="username"
                                            class="form-control @error('username') is-invalid @enderror" id="username"
                                            placeholder="{{ __('auth.username') }}" value="{{ old('username') }}">
                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="email" class="form-label">{{ __('auth.email') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror" id="email"
                                            placeholder="{{ __('auth.email') }}" required value="{{ old('email') }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="password" class="form-label">{{ __('auth.password') }} <span
                                                class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror" id="password"
                                                placeholder="{{ __('auth.password') }}" required>
                                            <button type="button"
                                                class="btn btn-link p-0 position-absolute top-50 end-0 translate-middle-y me-2 password-toggle"
                                                data-target="#password" aria-label="Toggle password">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="form-text text-muted">{{ __('auth.min_8_chars') }}</div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="password_confirmation" class="form-label">{{ __('auth.confirm_password') }} <span
                                                class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="password" name="password_confirmation"
                                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                                id="password_confirmation" placeholder="{{ __('auth.confirm_password') }}" required>
                                            <button type="button"
                                                class="btn btn-link p-0 position-absolute top-50 end-0 translate-middle-y me-2 password-toggle"
                                                data-target="#password_confirmation"
                                                aria-label="Toggle password confirmation">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="form-text text-muted">{{ __('auth.must_match_password') }}</div>
                                        @error('password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="form-check">
                                                <input type="checkbox" name="rememberMe" class="form-check-input"
                                                    id="rememberMe">
                                                <label class="form-check-label" for="rememberMe">{{ __('auth.remember_me') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-8">
                                        <button type="submit" class="btn btn-primary w-full mb-4">{{ __('auth.sign_up') }}<i
                                                class="bi bi-box-arrow-in-right ms-1 fs-16"></i></button>
                                    </div>
                                </div>
                                <p class="mb-0 fw-semibold position-relative text-center fs-12">{{ __('auth.already_have_account') }} <a
                                        href="{{ route('auth') }}" class="text-decoration-underline text-primary">{{ __('auth.sign_in_here') }}</a>
                                </p>
                            </form>
                            <div class="text-center">
                            </div>
                        </div>
                    </div>
                    <p class="position-relative text-center fs-12 mb-0">
                        {{ __('common.footer.copyright', ['year' => now()->year]) }}
                        {{ __('common.footer.by_it_team') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.password-toggle');
            if (!btn) return;
            const target = document.querySelector(btn.getAttribute('data-target'));
            if (!target) return;
            const icon = btn.querySelector('i');
            if (target.type === 'password') {
                target.type = 'text';
                if (icon) { icon.classList.remove('bi-eye'); icon.classList.add('bi-eye-slash'); }
            } else {
                target.type = 'password';
                if (icon) { icon.classList.remove('bi-eye-slash'); icon.classList.add('bi-eye'); }
            }
        });
    </script>
@endsection