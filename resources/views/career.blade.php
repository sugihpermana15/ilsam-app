@extends('layouts.app')
@section('title', __('website.titles.career'))

@php
  $settings = \App\Support\WebsiteSettings::all();
  $locale = app()->getLocale();
  $fallbackLocale = config('app.fallback_locale', 'en');
  $metaDescription = data_get($settings, "seo.career.meta_description.$locale")
    ?: data_get($settings, "seo.career.meta_description.$fallbackLocale")
    ?: 'Explore career opportunities at PT ILSAM GLOBAL INDONESIA. Browse open positions, filter by department and location, and apply online.';
  $metaImage = data_get($settings, 'seo.career.meta_image') ?: asset('assets/img/img9.jpg');
@endphp
@section('meta_description', $metaDescription)
@section('meta_image', str_starts_with($metaImage, 'http') ? $metaImage : asset($metaImage))
@section('main')
  @php
    $openings = collect($openings ?? []);

    $company = $company ?? [];
    $companyEmail = $company['email'] ?? 'career@ilsam.co.id';

    $filters = $filters ?? [
      'q' => request('q', ''),
      'department' => request('department', ''),
      'location' => request('location', ''),
      'type' => request('type', ''),
      'work_mode' => request('work_mode', ''),
      'sort' => request('sort', 'title_asc'),
    ];

    $filterOptions = $filterOptions ?? [
      'departments' => $openings->map(fn($j) => data_get($j, 'department'))->filter()->unique()->sort()->values()->all(),
      'locations' => $openings->map(fn($j) => data_get($j, 'location'))->filter()->unique()->sort()->values()->all(),
      'types' => $openings->map(fn($j) => data_get($j, 'type'))->filter()->unique()->sort()->values()->all(),
      'work_modes' => $openings->map(fn($j) => data_get($j, 'work_mode'))->filter()->unique()->sort()->values()->all(),
      'sorts' => [
        ['value' => 'title_asc', 'label' => __('website.career.sorts.title_asc')],
        ['value' => 'title_desc', 'label' => __('website.career.sorts.title_desc')],
        ['value' => 'dept_asc', 'label' => __('website.career.sorts.dept_asc')],
        ['value' => 'loc_asc', 'label' => __('website.career.sorts.loc_asc')],
      ],
    ];

    $openingsAllCount = $openingsAllCount ?? $openings->count();

    $resultsCount = $openings->count();
    $resultsLabel = trans_choice('website.career.meta.results_word', $resultsCount);

    $normalizeLines = function (?string $text) {
      $text = trim((string) $text);
      if ($text === '') {
        return [];
      }
      $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];
      $lines = array_map(fn($line) => trim((string) $line), $lines);
      $lines = array_filter($lines, fn($line) => $line !== '');
      $lines = array_map(fn($line) => preg_replace('/^\s*(?:[-*•]+|\d+[\.)])\s*/u', '', $line), $lines);
      return array_values(array_filter($lines, fn($line) => $line !== ''));
    };
  @endphp

  @php
    $hasActiveFilters = collect($filters)->except('sort')->filter(fn ($value) => trim((string) $value) !== '')->isNotEmpty();
    $filterSelects = [
      ['name' => 'department', 'label' => __('website.career.filters.department'), 'all' => __('website.career.filters.all_departments'), 'options' => $filterOptions['departments'] ?? []],
      ['name' => 'location', 'label' => __('website.career.filters.location'), 'all' => __('website.career.filters.all_locations'), 'options' => $filterOptions['locations'] ?? []],
      ['name' => 'type', 'label' => __('website.career.filters.type'), 'all' => __('website.career.filters.all_types'), 'options' => $filterOptions['types'] ?? []],
      ['name' => 'work_mode', 'label' => __('website.career.filters.work_mode'), 'all' => __('website.career.filters.all_work_modes'), 'options' => $filterOptions['work_modes'] ?? []],
    ];
  @endphp

  {{-- Notifikasi hasil lamaran --}}
  @if(session('success') || session('error'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swal === 'undefined') return;
        @if(session('success'))
          Swal.fire({
            icon: 'success',
            title: @json(__('website.common.success')),
            text: @json(session('success') . (session('candidate_code') ? ' Candidate code: ' . session('candidate_code') : '')),
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
  @endif

  <!-- Breadcrumb area start  -->
  <div class="breadcrumb__area breadcrumb-space sp-page-hero overly theme-bg-heading-primary overflow-hidden">
    <div class="breadcrumb__background"
      data-background="{{ $company['hero_image'] ?? asset('assets/img/aboutus/img11.jpg') }}"></div>
    <div class="container">
      <div class="row align-items-center justify-content-between">
        <div class="col-12">
          <div class="breadcrumb__content text-center">
            <h1 class="breadcrumb__title color-white title-animation">{{ __('website.career.breadcrumb_title') }}</h1>
            <div class="breadcrumb__menu d-inline-flex justify-content-center">
              <nav>
                <ul>
                  <li>
                    <span>
                      <a href="{{ route('home') }}">
                        <svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M1 5.9L7.5 1L14 5.9V13.6C14 13.9713 13.8478 14.3274 13.5769 14.5899C13.306 14.8525 12.9386 15 12.5556 15H2.44444C2.06135 15 1.69395 14.8525 1.42307 14.5899C1.15218 14.3274 1 13.9713 1 13.6V5.9Z"
                            stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                          <path d="M5.33398 15V8H9.66732V15" stroke="white" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        </svg>
                        {{ __('website.common.home') }}
                      </a>
                    </span>
                  </li>
                  <li class="active"><span>{{ __('website.career.breadcrumb_title') }}</span></li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Breadcrumb area end  -->

  <section class="sp-section sp-section--alt sp-career">
    <div class="container">
      <div class="sp-section__head">
        <div>
          <span class="sp-eyebrow">{{ __('website.pro.career_label') }}</span>
          <h2 class="sp-title">{{ __('website.career.open_positions') }}</h2>
          <p class="sp-lead mb-0">{{ __('website.career.subtitle') }}</p>
        </div>
        <p class="sp-career__count" aria-live="polite">
          <strong>{{ $resultsCount }}</strong> {{ $resultsLabel }}
          @if($resultsCount !== $openingsAllCount)
            {{ __('website.common.of') }} {{ $openingsAllCount }}
          @endif
        </p>
      </div>

      {{-- Pencarian & filter --}}
      <form method="GET" action="{{ route('career') }}" class="sp-filters">
        <div class="sp-filters__search">
          <label class="sp-form__label" for="career-q">{{ __('website.career.filters.search') }}</label>
          <div class="sp-toolbar__search">
            <i class="bi bi-search" aria-hidden="true"></i>
            <input type="search" id="career-q" name="q" value="{{ $filters['q'] }}"
              placeholder="{{ __('website.career.filters.search_placeholder') }}">
          </div>
        </div>

        @foreach($filterSelects as $select)
          <div>
            <label class="sp-form__label" for="career-{{ $select['name'] }}">{{ $select['label'] }}</label>
            <select class="sp-select" id="career-{{ $select['name'] }}" name="{{ $select['name'] }}">
              <option value="">{{ $select['all'] }}</option>
              @foreach($select['options'] as $opt)
                <option value="{{ $opt }}" @selected(($filters[$select['name']] ?? '') === $opt)>{{ $opt }}</option>
              @endforeach
            </select>
          </div>
        @endforeach

        <div>
          <label class="sp-form__label" for="career-sort">{{ __('website.career.filters.sort') }}</label>
          <select class="sp-select" id="career-sort" name="sort">
            @foreach(($filterOptions['sorts'] ?? []) as $opt)
              <option value="{{ $opt['value'] }}" @selected(($filters['sort'] ?? 'title_asc') === $opt['value'])>{{ $opt['label'] }}</option>
            @endforeach
          </select>
        </div>

        <div class="sp-filters__actions">
          <button class="sp-btn sp-btn--primary" type="submit">{{ __('website.career.actions.apply_filters') }}</button>
          @if($hasActiveFilters)
            <a class="sp-btn sp-btn--outline" href="{{ route('career') }}">{{ __('website.career.actions.reset') }}</a>
          @endif
        </div>
      </form>

      {{-- Daftar lowongan --}}
      @if($openings->isEmpty())
        <div class="sp-empty">
          <i class="bi bi-briefcase" aria-hidden="true"></i>
          <h3>{{ __('website.career.empty.title') }}</h3>
          <p>{!! __('website.career.empty.desc_html', ['mailto' => 'mailto:' . $companyEmail, 'email' => e($companyEmail)]) !!}</p>
        </div>
      @else
        <div class="sp-jobs">
          @foreach($openings as $job)
            @php
              $title = data_get($job, 'title', __('website.career.default_position'));
              $jobId = (string) data_get($job, 'id', '');
              $applyUrl = route('career.apply', ['job' => $jobId !== '' ? $jobId : null]);

              $jobTags = array_filter([
                'bi-briefcase' => data_get($job, 'type', ''),
                'bi-laptop' => data_get($job, 'work_mode', ''),
                'bi-diagram-3' => data_get($job, 'department', ''),
                'bi-geo-alt' => data_get($job, 'location', ''),
              ]);

              $experience = trim((string) data_get($job, 'experience', ''));
              $deadlineRaw = data_get($job, 'deadline');
              $deadlineFormatted = '';
              $closingSoon = false;
              if (!empty($deadlineRaw)) {
                try {
                  $deadlineDate = \Carbon\Carbon::parse($deadlineRaw);
                  $deadlineFormatted = $deadlineDate->format('d M Y');
                  $closingSoon = $deadlineDate->startOfDay()->lte(today()->addDays(7));
                } catch (\Throwable $e) {
                  $deadlineFormatted = (string) $deadlineRaw;
                }
              }

              $responsibilityLines = $normalizeLines(data_get($job, 'responsibilities', ''));
              $requirementLines = $normalizeLines(data_get($job, 'requirements', ''));
              $hasLists = !empty($responsibilityLines) || !empty($requirementLines);
            @endphp
            <article class="sp-job">
              <div class="sp-job__head">
                <div class="sp-job__main">
                  <h3 class="sp-job__title">{{ $title }}</h3>
                  <ul class="sp-job__tags">
                    @foreach($jobTags as $tagIcon => $tagText)
                      <li><i class="bi {{ $tagIcon }}" aria-hidden="true"></i> {{ $tagText }}</li>
                    @endforeach
                  </ul>
                </div>
                <a class="sp-btn sp-btn--primary" href="{{ $applyUrl }}">{{ __('website.career.actions.apply') }} <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
              </div>

              @if($experience !== '' || $deadlineFormatted !== '')
                <dl class="sp-job__facts">
                  @if($experience !== '')
                    <div>
                      <dt>{{ __('website.career.details.experience') }}</dt>
                      <dd>{{ $experience }}</dd>
                    </div>
                  @endif
                  @if($deadlineFormatted !== '')
                    <div class="{{ $closingSoon ? 'is-soon' : '' }}">
                      <dt>{{ __('website.career.details.deadline') }}</dt>
                      <dd>
                        {{ $deadlineFormatted }}
                        @if($closingSoon)
                          <span class="sp-job__soon">{{ __('website.pro.career_closing_soon') }}</span>
                        @endif
                      </dd>
                    </div>
                  @endif
                </dl>
              @endif

              @if($hasLists)
                <details class="sp-job__details" @if($loop->first) open @endif>
                  <summary>{{ __('website.career.details.summary_toggle') }} <i class="bi bi-chevron-down" aria-hidden="true"></i></summary>
                  <div class="sp-job__lists">
                    @if(!empty($requirementLines))
                      <div>
                        <h4>{{ __('website.career.details.requirements') }}</h4>
                        <ul>
                          @foreach($requirementLines as $line)
                            <li>{{ $line }}</li>
                          @endforeach
                        </ul>
                      </div>
                    @endif
                    @if(!empty($responsibilityLines))
                      <div>
                        <h4>{{ __('website.career.details.responsibilities') }}</h4>
                        <ul>
                          @foreach($responsibilityLines as $line)
                            <li>{{ $line }}</li>
                          @endforeach
                        </ul>
                      </div>
                    @endif
                  </div>
                </details>
              @endif
            </article>
          @endforeach
        </div>
      @endif
    </div>
  </section>

  {{-- Kirim CV umum --}}
  <section class="sp-cta">
    <div class="container">
      <div class="sp-cta__inner">
        <div>
          <h2 class="sp-title sp-title--light">{{ __('website.pro.career_cta_title') }}</h2>
          <p class="sp-cta__text">{!! __('website.career.note_html', ['mailto' => 'mailto:' . $companyEmail, 'email' => e($companyEmail)]) !!}</p>
        </div>
        <a href="mailto:{{ $companyEmail }}" class="sp-btn sp-btn--light">{{ __('website.career.send_cv') }} <i class="bi bi-envelope" aria-hidden="true"></i></a>
      </div>
    </div>
  </section>
@endsection
