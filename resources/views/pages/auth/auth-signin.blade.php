@extends('layouts.master_auth')

@section('title', 'Ilsam - Sign In')

@section('content')

    <!-- START -->
    <div>
        <img src="{{ asset('assets/img/auth/login_bg.jpg') }}" alt="Auth Background"
            class="auth-bg light w-full h-full opacity-60 position-absolute top-0">
        <img src="{{ asset('assets/img/auth/auth_bg_dark.jpg') }}" alt="Auth Background" class="auth-bg d-none dark">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100 py-10">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card mx-xxl-8">
                        <div class="card-body py-12 px-8">
                            <img src="{{ asset('assets/img/logo.svg') }}" alt="Logo Dark" height="30"
                                class="mb-4 mx-auto d-block">
                            <h6 class="mb-3 mb-8 fw-medium text-center">Sign In to Your Account</h6>
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if(session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif

                                @if($errors->any())
                                    <div class="alert alert-danger">Please correct the errors below.</div>
                                @endif

                                <div class="row g-4">
                                    <div class="col-12">
                                        <label for="username" class="form-label">Username <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="username"
                                            class="form-control @error('username') is-invalid @enderror" id="username"
                                            placeholder="Enter your username" required value="{{ old('username') }}">
                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="password" class="form-label">Password <span
                                                class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror" id="password"
                                                placeholder="Enter your password" required>
                                            <button type="button"
                                                class="btn btn-link p-0 position-absolute top-50 end-0 translate-middle-y me-2 password-toggle"
                                                data-target="#password" aria-label="Toggle password">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="form-text text-muted">Minimum 8 characters.</div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="form-check">
                                                <input type="checkbox" name="rememberMe" class="form-check-input"
                                                    id="rememberMe">
                                                <label class="form-check-label" for="rememberMe">Remember me</label>
                                            </div>
                                            {{-- <div class="form-text">
                                                <a href="{{ route('forgot-password') }}"
                                                    class="link link-primary text-muted text-decoration-underline">Forgot
                                                    password?</a>
                                            </div> --}}
                                        </div>
                                    </div>
                                    <div class="col-12 mt-8">
                                        <button type="submit" class="btn btn-primary w-full mb-4">Sign In<i
                                                class="bi bi-box-arrow-in-right ms-1 fs-16"></i></button>
                                    </div>
                                </div>
                                <p class="mb-0 fw-semibold position-relative text-center fs-12">Don't have an account? <a
                                        href="{{ route('register') }}" class="text-decoration-underline text-primary">Sign
                                        up here</a>
                                </p>
                            </form>
                            <div class="text-center">
                            </div>
                        </div>
                    </div>
                    <p class="position-relative text-center fs-12 mb-0">Copyright ©<span
                            class="current-year">{{ now()->year }}</span> ILSAM
                        INDONESIA by IT Team.
                        All rights reserved.</p>
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