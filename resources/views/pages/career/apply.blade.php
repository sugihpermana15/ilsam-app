@extends('layouts.app')
@section('title', __('website.titles.career_apply'))

@section('main')
  @php
    $company = $company ?? [];
    $companyEmail = $company['email'] ?? 'hrd@ilsam.co.id';

    $openings = collect($openings ?? []);
    $selectedJob = $selectedJob ?? null;

    $selectedJobId = $selectedJob ? (string) ($selectedJob->id ?? '') : (string) old('job_id', '');
    $selectedJobTitle = $selectedJob ? (string) ($selectedJob->title ?? '') : (string) old('job_title', '');

    $fallbackHero = $company['hero_image'] ?? asset('assets/img/aboutus/img11.jpg');
  @endphp

  <style>
    .apply-required {
      color: #dc3545;
      font-weight: 700;
      margin-left: 2px;
    }

    /*
              Public theme CSS can make Bootstrap form controls look too "flat".
              Scope an admin-like look ONLY for this page.
            */
    .apply-admin-theme {
      --apply-radius: 12px;
      --apply-line: rgba(0, 0, 0, 0.08);
      --apply-muted: rgba(33, 37, 41, 0.65);
    }

    .apply-admin-theme .apply-shell {
      max-width: 920px;
      margin: 0 auto;
    }

    .apply-admin-theme .card {
      border: 1px solid var(--apply-line);
      box-shadow: 0 10px 26px rgba(0, 0, 0, 0.06);
      border-radius: var(--apply-radius);
      overflow: hidden;
      background: #fff;
    }

    .apply-admin-theme .card-header {
      background: #fff;
      border-bottom: 1px solid var(--apply-line);
      padding: 16px 20px;
    }

    .apply-admin-theme .card-footer {
      background: #fff;
      border-top: 1px solid var(--apply-line);
      padding: 14px 20px;
    }

    .apply-admin-theme .card-body {
      padding: 20px;
    }

    .apply-admin-theme .card-title {
      font-weight: 800;
      letter-spacing: -0.01em;
    }

    .apply-admin-theme .form-label {
      font-weight: 700;
      font-size: 0.9rem;
      color: #212529;
      margin-bottom: 0.35rem;
    }

    .apply-admin-theme .form-control,
    .apply-admin-theme .form-select {
      background-color: #fff;
      border: 1px solid rgba(0, 0, 0, 0.15);
      border-radius: 10px;
      padding: 0.48rem 0.8rem;
      font-size: 0.92rem;
      line-height: 1.35;
      min-height: 40px;
      box-shadow: none;
    }

    .apply-admin-theme textarea.form-control {
      min-height: 96px;
    }

    .apply-admin-theme .form-control:focus,
    .apply-admin-theme .form-select:focus {
      border-color: rgba(13, 110, 253, 0.7);
      box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
      outline: 0;
    }

    .apply-admin-theme .text-muted {
      color: var(--apply-muted) !important;
    }

    .apply-admin-theme small.text-muted {
      font-size: 0.8rem;
    }

    .apply-admin-theme .alert {
      border-radius: 12px;
      font-size: 0.92rem;
    }

    .apply-admin-theme .alert-info {
      background: rgba(13, 202, 240, 0.12);
      border-color: rgba(13, 202, 240, 0.25);
    }

    .apply-admin-theme .btn {
      border-radius: 10px;
      font-weight: 700;
      padding: 0.48rem 0.9rem;
    }

    .apply-admin-theme .apply-section-title {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 800;
      font-size: 0.95rem;
      letter-spacing: -0.01em;
      margin-top: 2px;
      color: #212529;
    }

    .apply-admin-theme .apply-section-rule {
      border-top: 1px solid var(--apply-line);
      margin: 6px 0 6px;
      opacity: 1;
    }

    @media (max-width: 576px) {

      .apply-admin-theme .card-header,
      .apply-admin-theme .card-body,
      .apply-admin-theme .card-footer {
        padding-left: 14px;
        padding-right: 14px;
      }
    }
  </style>

  <!-- Breadcrumb area start  -->
  <div class="breadcrumb__area breadcrumb-space overly theme-bg-heading-primary overflow-hidden">
    <div class="breadcrumb__background" data-background="{{ $fallbackHero }}"></div>
    <div class="container">
      <div class="row align-items-center justify-content-between">
        <div class="col-12">
          <div class="breadcrumb__content text-center">
            <h1 class="breadcrumb__title color-white title-animation">{{ __('website.career.apply.breadcrumb_title') }}</h1>
            <div class="breadcrumb__menu d-inline-flex justify-content-center">
              <nav>
                <ul>
                  <li>
                    <span>
                      <a href="{{ route('home') }}">{{ __('website.common.home') }}</a>
                    </span>
                  </li>
                  <li>
                    <span>
                      <a href="{{ route('career') }}">{{ __('website.career.breadcrumb_title') }}</a>
                    </span>
                  </li>
                  <li class="active"><span>{{ __('website.career.apply.breadcrumb_title') }}</span></li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Breadcrumb area end  -->

  <section class="pt-60 pb-40" style="background:#f6f8fb;">
    <div class="container">
      {{-- SweetAlert2 notification (match admin style) --}}
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          @if(session('success'))
            Swal.fire({
              icon: 'success',
              title: @json(__('website.common.success')),
              text: @json(session('success')),
              timer: 2200,
              showConfirmButton: false
            });
          @endif
          @if(session('error'))
            Swal.fire({
              icon: 'error',
              title: @json(__('website.common.error')),
              text: @json(session('error')),
              timer: 2600,
              showConfirmButton: false
            });
          @endif
          });
      </script>

      <div class="row justify-content-center">
        <div class="col-12">
          @if($errors->any())
            <div class="alert alert-danger" role="alert">
              <div class="fw-semibold mb-1">{{ __('website.career.apply.errors_required') }}</div>
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="apply-admin-theme">
            <div class="apply-shell">
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title mb-1">{{ __('website.career.apply.card.title') }}</h5>
                  <div class="text-muted small">{{ __('website.career.apply.card.subtitle') }}</div>
                </div>

                <form id="apply_form" method="POST" action="{{ route('career.apply.submit') }}"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="card-body">
                    <div class="alert alert-info mb-3">
                      {!! __('website.career.apply.help_hrd_html', ['mailto' => 'mailto:' . $companyEmail, 'email' => $companyEmail]) !!}
                    </div>

                    <div class="row g-3">
                      <div class="col-12">
                        <div class="apply-section-title">{{ __('website.career.apply.sections.personal') }}</div>
                        <hr class="apply-section-rule">
                      </div>

                      <div class="col-md-12">
                        <label class="form-label">{{ __('website.career.apply.fields.position') }} <span class="apply-required">*</span></label>
                        @if($selectedJob)
                          <input type="hidden" name="job_id" value="{{ $selectedJobId }}">
                          <input type="hidden" name="job_title" value="{{ $selectedJobTitle }}">
                          <input class="form-control" type="text" value="{{ $selectedJobTitle }}" readonly>
                        @else
                          <select class="form-select @error('job_id') is-invalid @enderror" name="job_id" id="job_id_select"
                            required>
                            <option value="">{{ __('website.career.apply.fields.select_position') }}</option>
                            @foreach($openings as $job)
                              @php
                                $jid = (string) ($job->id ?? '');
                                $jtitle = (string) ($job->title ?? '');
                              @endphp
                              <option value="{{ $jid }}" data-title="{{ $jtitle }}" @selected(old('job_id') === $jid)>
                                {{ $jtitle }}
                              </option>
                            @endforeach
                          </select>
                          <input type="hidden" name="job_title" id="job_title_input" value="{{ old('job_title', '') }}">
                          @error('job_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                          @enderror
                        @endif
                        <small class="text-muted">{{ __('website.career.apply.fields.position_help') }}</small>
                      </div>

                      <div class="col-md-6">
                        <label class="form-label">{{ __('website.career.apply.fields.full_name') }} <span class="apply-required">*</span></label>
                        <input type="text" class="form-control @error('full_name') is-invalid @enderror" name="full_name"
                          value="{{ old('full_name') }}" required>
                        @error('full_name')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>

                      <div class="col-md-6">
                        <label class="form-label">{{ __('website.career.apply.fields.domicile') }}</label>
                        <input type="text" class="form-control @error('domicile') is-invalid @enderror" name="domicile"
                          value="{{ old('domicile') }}" placeholder="{{ __('website.career.apply.placeholders.domicile') }}">
                        @error('domicile')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>

                      <div class="col-12 pt-1">
                        <div class="apply-section-title">{{ __('website.career.apply.sections.contact') }}</div>
                        <hr class="apply-section-rule">
                      </div>

                      <div class="col-md-6">
                        <label class="form-label">{{ __('website.career.apply.fields.email') }} <span class="apply-required">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                          value="{{ old('email') }}" required>
                        @error('email')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>

                      <div class="col-md-6">
                        <label class="form-label">{{ __('website.career.apply.fields.phone_whatsapp') }} <span class="apply-required">*</span></label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone"
                          value="{{ old('phone') }}" required>
                        @error('phone')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>

                      <div class="col-md-6">
                        <label class="form-label">{{ __('website.career.apply.fields.linkedin_optional') }}</label>
                        <input type="url" class="form-control @error('linkedin_url') is-invalid @enderror"
                          name="linkedin_url" value="{{ old('linkedin_url') }}" placeholder="{{ __('website.career.apply.placeholders.linkedin') }}">
                        @error('linkedin_url')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>

                      <div class="col-md-6">
                        <label class="form-label">{{ __('website.career.apply.fields.portfolio_optional') }}</label>
                        <input type="url" class="form-control @error('portfolio_url') is-invalid @enderror"
                          name="portfolio_url" value="{{ old('portfolio_url') }}" placeholder="{{ __('website.career.apply.placeholders.portfolio') }}">
                        @error('portfolio_url')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>

                      <div class="col-md-12">
                        <label class="form-label">{{ __('website.career.apply.fields.message_optional') }}</label>
                        <textarea class="form-control @error('message') is-invalid @enderror" name="message" rows="3"
                          placeholder="{{ __('website.career.apply.placeholders.message') }}">{{ old('message') }}</textarea>
                        @error('message')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>

                      <div class="col-12 pt-1">
                        <div class="apply-section-title">{{ __('website.career.apply.sections.documents') }}</div>
                        <hr class="apply-section-rule">
                      </div>

                      <div class="col-md-12">
                        <label class="form-label">{{ __('website.career.apply.fields.upload_cv') }} <span class="apply-required">*</span></label>
                        <input type="file" class="form-control @error('cv') is-invalid @enderror" name="cv"
                          accept=".pdf,application/pdf" required>
                        <small class="text-muted">{{ __('website.career.apply.cv_help', ['type' => __('website.common.pdf'), 'max' => '2MB']) }}</small>
                        @error('cv')
                          <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                      </div>

                      <div class="col-md-12">
                        <div class="form-check">
                          <input class="form-check-input @error('consent') is-invalid @enderror" type="checkbox"
                            name="consent" id="consent" value="1" required @checked(old('consent'))>
                          <label class="form-check-label" for="consent">
                            {{ __('website.career.apply.fields.consent_label') }}
                          </label>
                          @error('consent')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                          @enderror
                        </div>
                        <small class="text-muted">{{ __('website.career.apply.fields.consent_help') }}</small>
                      </div>

                      @php
                        $recaptchaEnabled = (bool) config('career_security.recaptcha.enabled');
                        $recaptchaSiteKey = (string) config('services.recaptcha.site_key');
                        $showRecaptcha = $recaptchaEnabled && $recaptchaSiteKey !== '';
                      @endphp

                      @if($showRecaptcha)
                        <div class="col-md-12">
                          <label class="form-label">{{ __('website.career.apply.fields.verification') }} <span class="apply-required">*</span></label>
                          <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
                          @error('g-recaptcha-response')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                          @enderror
                        </div>
                        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                      @endif
                    </div>
                  </div>

                  <div class="card-footer d-flex justify-content-between align-items-center">
                    <a class="btn btn-secondary" href="{{ route('career') }}">{{ __('website.common.back') }}</a>
                    <button class="btn btn-primary" id="apply_submit" type="submit">
                      {{ __('website.career.apply.actions.submit_application') }}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    (function () {
      var form = document.getElementById('apply_form');
      var submitBtn = document.getElementById('apply_submit');

      var uploadingText = @json(__('website.career.apply.uploading'));

      var select = document.getElementById('job_id_select');
      var titleInput = document.getElementById('job_title_input');

      if (form && submitBtn) {
        form.addEventListener('submit', function () {
          if (submitBtn.disabled) return;
          submitBtn.disabled = true;
          submitBtn.setAttribute('aria-busy', 'true');
          submitBtn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + uploadingText;
        });
      }

      if (!select || !titleInput) return;

      function syncTitle() {
        var opt = select.options[select.selectedIndex];
        var title = opt ? (opt.getAttribute('data-title') || '') : '';
        titleInput.value = title;
      }

      select.addEventListener('change', syncTitle);
      syncTitle();
    })();
  </script>
@endsection