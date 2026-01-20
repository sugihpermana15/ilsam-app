@extends('layouts.master_auth')

@section('title', 'Ilsam - Sign Up')

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
                            <h6 class="mb-3 mb-8 fw-medium text-center">Create your account to login</h6>
                            <form method="POST" action="{{ route('register.store') }}">
                                @csrf

                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if($errors->any())
                                    <div class="alert alert-danger">Please correct the errors below.</div>
                                @endif

                                <div class="row g-4">
                                    <div class="col-6">
                                        <label for="firstname" class="form-label">First Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="firstname"
                                            class="form-control @error('firstname') is-invalid @enderror" id="firstname"
                                            placeholder="Enter your First Name" required value="{{ old('firstname') }}">
                                        @error('firstname')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="lastname" class="form-label">Last Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="lastname"
                                            class="form-control @error('lastname') is-invalid @enderror" id="lastname"
                                            placeholder="Last Name" required value="{{ old('lastname') }}">
                                        @error('lastname')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="username" class="form-label">Username <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="username"
                                            class="form-control @error('username') is-invalid @enderror" id="username"
                                            placeholder="Username" value="{{ old('username') }}">
                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror" id="email"
                                            placeholder="Email" required value="{{ old('email') }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="password" class="form-label">Password <span
                                                class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror" id="password"
                                                placeholder="Password" required>
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
                                        <label for="password_confirmation" class="form-label">Confirm Password <span
                                                class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="password" name="password_confirmation"
                                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                                id="password_confirmation" placeholder="Confirm Password" required>
                                            <button type="button"
                                                class="btn btn-link p-0 position-absolute top-50 end-0 translate-middle-y me-2 password-toggle"
                                                data-target="#password_confirmation"
                                                aria-label="Toggle password confirmation">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="form-text text-muted">Must match the password above.</div>
                                        @error('password_confirmation')
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
                                        </div>
                                    </div>
                                    <div class="col-12 mt-8">
                                        <button type="submit" class="btn btn-primary w-full mb-4">Sign Up<i
                                                class="bi bi-box-arrow-in-right ms-1 fs-16"></i></button>
                                    </div>
                                </div>
                                <p class="mb-0 fw-semibold position-relative text-center fs-12">Already have an account? <a
                                        href="{{ route('auth') }}" class="text-decoration-underline text-primary">Sign In
                                        here</a>
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