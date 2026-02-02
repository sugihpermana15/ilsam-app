@extends('layouts.app')
@section('title', __('website.titles.career'))

@section('meta_description', 'Explore career opportunities at PT ILSAM GLOBAL INDONESIA. Browse open positions, filter by department and location, and apply online.')
@section('meta_image', asset('assets/img/img9.jpg'))
@section('main')
  @php
    $openings = collect($openings ?? []);

    $company = $company ?? [];
    $companyEmail = $company['email'] ?? 'hrd@ilsam.co.id';

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

  <style>
    .ilsam-career-board {
      --cb-ink: #0b1220;
      --cb-muted: rgba(11, 18, 32, 0.72);
      --cb-muted-2: rgba(11, 18, 32, 0.56);
      --cb-line: rgba(11, 18, 32, 0.14);
      --cb-surface: rgba(255, 255, 255, 0.96);
      --cb-surface-2: rgba(248, 250, 252, 0.98);
      --cb-shadow: 0 22px 55px rgba(11, 18, 32, 0.14);
      --cb-shadow-soft: 0 14px 34px rgba(11, 18, 32, 0.12);

      /* Industrial palette */
      --cb-navy: #0b1220;
      --cb-steel: #1f2a44;
      --cb-accent: #ffb020;
      /* safety amber */
      --cb-accent-2: #00d18f;
      /* chemical green */
      --cb-radius: 14px;

      --cb-bg: #f4f7fb;
    }

    .ilsam-career-board {
      padding: 56px 0 28px;
      position: relative;
      background-color: var(--cb-bg);
      background-image:
        radial-gradient(1200px 500px at 20% -10%, rgba(0, 209, 143, 0.10), transparent 55%),
        radial-gradient(1100px 520px at 85% 0%, rgba(255, 176, 32, 0.10), transparent 52%),
        linear-gradient(180deg, rgba(11, 18, 32, 0.06) 0%, rgba(11, 18, 32, 0.00) 40%, rgba(11, 18, 32, 0.05) 100%);
    }

    .ilsam-career-board:before {
      content: "";
      position: absolute;
      inset: 0;
      pointer-events: none;
      background-image:
        linear-gradient(rgba(11, 18, 32, 0.06) 1px, transparent 1px),
        linear-gradient(90deg, rgba(11, 18, 32, 0.06) 1px, transparent 1px);
      background-size: 34px 34px;
      mask-image: radial-gradient(ellipse at top, rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.08) 60%, rgba(0, 0, 0, 0) 76%);
      opacity: 0.45;
    }

    .ilsam-career-board .container {
      position: relative;
      z-index: 1;
    }

    .ilsam-career-board .board-header {
      display: flex;
      flex-wrap: wrap;
      align-items: flex-start;
      justify-content: space-between;
      gap: 14px;
      margin-bottom: 18px;
    }


    .ilsam-career-board .board-title {
      margin: 0;
      font-size: 30px;
      line-height: 1.2;
      font-weight: 800;
      color: var(--cb-ink);
      letter-spacing: -0.02em;
    }

    .ilsam-career-board .board-title .accent {
      color: var(--cb-navy);
    }

    @@supports (-webkit-background-clip: text) or (background-clip: text) {
      .ilsam-career-board .board-title .accent {
        background: linear-gradient(90deg, var(--cb-navy), var(--cb-steel));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
      }
    }

    .ilsam-career-board .board-subtitle {
      margin: 8px 0 0;
      color: var(--cb-muted);
      font-size: 14px;
    }

    .ilsam-career-board .board-top {
      display: grid;
      grid-template-columns: 1fr auto;
      align-items: center;
      gap: 12px;
      margin-bottom: 18px;
    }

    @media (max-width: 576px) {
      .ilsam-career-board .board-top {
        grid-template-columns: 1fr;
        align-items: stretch;
      }
    }

    .ilsam-career-board .pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 10px;
      border-radius: 999px;
      border: 1px solid rgba(11, 18, 32, 0.16);
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.96));
      box-shadow: 0 14px 30px rgba(11, 18, 32, 0.10);
      color: rgba(11, 18, 32, 0.84);
      font-size: 13px;
    }

    .ilsam-career-board .pill.pill-results {
      width: fit-content;
      box-shadow: 0 10px 20px rgba(11, 18, 32, 0.08);
    }

    @media (max-width: 576px) {
      .ilsam-career-board .pill.pill-results {
        width: 100%;
        justify-content: center;
      }
    }

    .ilsam-career-board .board-controls {
      display: grid;
      grid-template-columns: 1fr auto;
      align-items: center;
      gap: 10px;
    }

    @media (max-width: 576px) {
      .ilsam-career-board .board-controls {
        grid-template-columns: 1fr;
      }

      .ilsam-career-board .board-controls .btn {
        width: 100%;
      }
    }

    .ilsam-career-board .input {
      width: 100%;
      border-radius: 12px;
      border: 1px solid rgba(11, 18, 32, 0.18);
      background: rgba(255, 255, 255, 0.99);
      padding: 10px 12px;
      outline: none;
      transition: box-shadow 160ms ease, border-color 160ms ease;
    }

    .ilsam-career-board .input:focus {
      border-color: rgba(0, 209, 143, 0.70);
      box-shadow: 0 0 0 4px rgba(0, 209, 143, 0.18);
    }

    .ilsam-career-board .label {
      font-size: 12px;
      color: rgba(2, 6, 23, 0.58);
      margin-bottom: 6px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .02em;
    }

    .ilsam-career-board .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      height: 42px;
      padding: 0 16px;
      border-radius: 12px;
      border: 1px solid rgba(11, 18, 32, 0.18);
      background: rgba(255, 255, 255, 0.98);
      color: rgba(11, 18, 32, 0.90);
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
      white-space: nowrap;
      transition: transform 120ms ease, box-shadow 160ms ease, border-color 160ms ease, background 160ms ease;
    }

    .ilsam-career-board .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 14px 26px rgba(11, 18, 32, 0.12);
      border-color: rgba(11, 18, 32, 0.28);
    }

    .ilsam-career-board .btn-primary {
      background: linear-gradient(180deg, var(--cb-accent), #ff9a1f);
      border-color: rgba(255, 154, 31, 0.95);
      color: #1f2937;
      box-shadow: 0 18px 34px rgba(255, 154, 31, 0.22);
    }

    .ilsam-career-board .btn-primary:hover {
      background: linear-gradient(180deg, #ffb020, #ff8c1a);
      border-color: rgba(255, 140, 26, 0.95);
      color: #111827;
    }

    .ilsam-career-board .btn-outline:hover {
      border-color: rgba(2, 6, 23, 0.24);
    }

    .ilsam-career-board .btn-secondary {
      background: linear-gradient(180deg, rgba(11, 18, 32, 0.92), rgba(31, 42, 68, 0.92));
      border-color: rgba(11, 18, 32, 0.85);
      color: rgba(255, 255, 255, 0.92);
      box-shadow: 0 18px 36px rgba(11, 18, 32, 0.22);
    }

    .ilsam-career-board .btn-secondary:hover {
      background: linear-gradient(180deg, rgba(11, 18, 32, 0.98), rgba(31, 42, 68, 0.98));
      border-color: rgba(11, 18, 32, 0.92);
      color: rgba(255, 255, 255, 0.96);
    }

    .ilsam-career-board .board-layout {
      display: grid;
      grid-template-columns: 360px 1fr;
      gap: 22px;
      align-items: start;
    }

    @media (max-width: 992px) {
      .ilsam-career-board .board-layout {
        grid-template-columns: 1fr;
      }

      .ilsam-career-board .filter-card {
        margin-bottom: 14px;
      }
    }

    .ilsam-career-board .filter-card {
      border: 1px solid rgba(11, 18, 32, 0.14);
      border-radius: var(--cb-radius);
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.96));
      box-shadow: var(--cb-shadow);
      padding: 18px;
      position: sticky;
      top: 110px;
      overflow: hidden;
    }

    .ilsam-career-board .filter-card:before {
      content: "";
      position: absolute;
      inset: 0;
      pointer-events: none;
      background:
        linear-gradient(90deg, rgba(0, 209, 143, 0.28), rgba(255, 176, 32, 0.18)) 0 0/100% 4px no-repeat,
        radial-gradient(circle at 20% 0%, rgba(0, 209, 143, 0.14), transparent 40%) 0 0/100% 100% no-repeat;
    }

    @media (max-width: 992px) {
      .ilsam-career-board .filter-card {
        position: static;
      }
    }

    .ilsam-career-board .filter-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 12px;
      position: relative;
      z-index: 1;
    }

    .ilsam-career-board .filter-title {
      margin: 0;
      font-size: 18px;
      font-weight: 800;
      color: var(--cb-ink);
    }

    .ilsam-career-board .filter-form {
      display: grid;
      gap: 12px;
      position: relative;
      z-index: 1;
    }

    .ilsam-career-board .filter-actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 2px;
    }

    .ilsam-career-board .filter-note {
      margin-top: 16px;
      font-size: 13px;
      color: rgba(11, 18, 32, 0.74);
      position: relative;
      z-index: 1;
    }

    .ilsam-career-board .jobs {
      display: grid;
      gap: 14px;
    }

    .ilsam-career-board .job-card {
      border: 1px solid rgba(11, 18, 32, 0.14);
      border-radius: var(--cb-radius);
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.96));
      padding: 16px 16px 14px;
      box-shadow: var(--cb-shadow-soft);
      position: relative;
      overflow: hidden;
    }

    .ilsam-career-board .job-card:before {
      content: "";
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
      width: 4px;
      background: linear-gradient(180deg, var(--cb-accent-2), var(--cb-accent));
      opacity: 0.90;
    }

    .ilsam-career-board .job-head {
      display: flex;
      flex-wrap: wrap;
      align-items: flex-start;
      justify-content: space-between;
      gap: 10px;
    }

    .ilsam-career-board .job-title {
      font-weight: 800;
      font-size: 16px;
      color: var(--cb-ink);
      letter-spacing: -0.01em;
    }

    .ilsam-career-board .job-meta {
      margin-top: 10px;
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .ilsam-career-board .tag {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 10px;
      border-radius: 999px;
      border: 1px solid rgba(11, 18, 32, 0.16);
      background: rgba(255, 255, 255, 0.92);
      color: rgba(11, 18, 32, 0.86);
      font-size: 13px;
      font-weight: 700;
    }

    .ilsam-career-board .tag .t-dot {
      width: 7px;
      height: 7px;
      border-radius: 999px;
      background: rgba(11, 18, 32, 0.42);
    }

    .ilsam-career-board .tag.tag-type {
      background: rgba(255, 176, 32, 0.12);
      border-color: rgba(255, 176, 32, 0.30);
      color: rgba(106, 64, 0, 0.95);
    }

    .ilsam-career-board .tag.tag-mode {
      background: rgba(0, 209, 143, 0.12);
      border-color: rgba(0, 209, 143, 0.30);
      color: rgba(0, 92, 61, 0.95);
    }

    .ilsam-career-board .tag.tag-dept {
      background: rgba(14, 165, 233, 0.10);
      border-color: rgba(14, 165, 233, 0.26);
      color: rgba(3, 87, 126, 0.95);
    }

    .ilsam-career-board .tag.tag-loc {
      background: rgba(99, 102, 241, 0.10);
      border-color: rgba(99, 102, 241, 0.26);
      color: rgba(46, 48, 140, 0.95);
    }

    .ilsam-career-board .tag.tag-type .t-dot {
      background: rgba(255, 176, 32, 0.95);
      box-shadow: 0 0 0 4px rgba(255, 176, 32, 0.14);
    }

    .ilsam-career-board .tag.tag-mode .t-dot {
      background: rgba(0, 209, 143, 0.95);
      box-shadow: 0 0 0 4px rgba(0, 209, 143, 0.14);
    }

    .ilsam-career-board .tag.tag-dept .t-dot {
      background: rgba(14, 165, 233, 0.95);
      box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.14);
    }

    .ilsam-career-board .tag.tag-loc .t-dot {
      background: rgba(99, 102, 241, 0.95);
      box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.14);
    }

    .ilsam-career-board .job-snippet {
      margin-top: 10px;
      color: rgba(11, 18, 32, 0.78);
      font-size: 14px;
      line-height: 1.55;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .ilsam-career-board .job-details {
      margin-top: 12px;
      border-top: 1px solid rgba(11, 18, 32, 0.12);
      padding-top: 10px;
    }

    .ilsam-career-board .job-details summary {
      cursor: pointer;
      font-weight: 700;
      color: rgba(2, 6, 23, 0.84);
      user-select: none;
      list-style: none;
    }

    .ilsam-career-board .job-details summary::-webkit-details-marker {
      display: none;
    }

    .ilsam-career-board .job-details summary:before {
      content: "▸";
      display: inline-block;
      margin-right: 8px;
      transform: translateY(-1px);
      transition: transform 160ms ease;
      color: rgba(2, 6, 23, 0.55);
    }

    .ilsam-career-board .job-details[open] summary:before {
      transform: rotate(90deg) translateX(2px);
    }

    .ilsam-career-board .job-details-body {
      margin-top: 10px;
      display: grid;
      gap: 12px;
      color: rgba(2, 6, 23, 0.76);
      font-size: 14px;
      line-height: 1.55;
    }

    .ilsam-career-board .job-section-title {
      font-weight: 800;
      color: rgba(2, 6, 23, 0.88);
      margin-bottom: 6px;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: .02em;
    }

    .ilsam-career-board .job-list {
      margin: 0;
      padding-left: 18px;
    }

    .ilsam-career-board .job-kv {
      display: flex;
      flex-wrap: wrap;
      gap: 8px 12px;
    }

    .ilsam-career-board .kv {
      display: inline-flex;
      gap: 8px;
      align-items: baseline;
      padding: 6px 10px;
      border: 1px solid rgba(2, 6, 23, 0.10);
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.70);
    }

    .ilsam-career-board .kv b {
      font-weight: 800;
      color: rgba(2, 6, 23, 0.88);
    }

    .ilsam-career-board .empty-state {
      border: 1px dashed rgba(2, 6, 23, 0.24);
      border-radius: var(--cb-radius);
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.90), rgba(248, 250, 252, 0.90));
      padding: 26px;
    }

    .ilsam-career-board .empty-title {
      margin: 0 0 8px;
      font-size: 22px;
      font-weight: 800;
      color: rgba(2, 6, 23, 0.92);
    }

    .ilsam-career-board .empty-sub {
      margin: 0;
      color: rgba(2, 6, 23, 0.68);
    }
  </style>

  <!-- Breadcrumb area start  -->
  <div class="breadcrumb__area breadcrumb-space overly theme-bg-heading-primary overflow-hidden">
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

  <section class="ilsam-career-board">
    <div class="container">
      <div class="board-header">
        <div>
          <h2 class="board-title"><span class="accent">{{ __('website.career.open_positions') }}</span></h2>
          <p class="board-subtitle">{{ __('website.career.subtitle') }}</p>
        </div>
        <a class="btn btn-secondary" href="mailto:{{ $companyEmail }}">{{ __('website.career.send_cv') }}</a>
      </div>

      <div class="board-top">
        <span class="pill pill-results">
          {{ $resultsCount }} {{ $resultsLabel }} <span style="color: rgba(2, 6, 23, 0.48);">{{ __('website.common.of') }}</span>
          {{ $openingsAllCount }}
        </span>

        <form class="board-controls" method="GET" action="{{ route('career') }}">
          <input type="hidden" name="q" value="{{ $filters['q'] }}">
          <input type="hidden" name="department" value="{{ $filters['department'] }}">
          <input type="hidden" name="location" value="{{ $filters['location'] }}">
          <input type="hidden" name="type" value="{{ $filters['type'] }}">
          <input type="hidden" name="work_mode" value="{{ $filters['work_mode'] }}">

          <select name="sort" class="input" style="min-width: 220px; height: 42px;">
            @foreach(($filterOptions['sorts'] ?? []) as $opt)
              <option value="{{ $opt['value'] }}" @selected(($filters['sort'] ?? 'title_asc') === $opt['value'])>
                {{ $opt['label'] }}
              </option>
            @endforeach
          </select>

          <button class="btn btn-primary" type="submit">{{ __('website.career.actions.sort') }}</button>
        </form>
      </div>

      <div class="board-layout">
        <aside class="filter-card">
          <div class="filter-head">
            <h3 class="filter-title">{{ __('website.career.filters.title') }}</h3>
            <span class="pill" style="box-shadow:none; background:rgba(255,255,255,.75);">{{ __('website.career.filters.refine') }}</span>
          </div>

          <form method="GET" action="{{ route('career') }}" class="filter-form">
            <div>
              <div class="label">{{ __('website.career.filters.search') }}</div>
              <input class="input" type="text" name="q" value="{{ $filters['q'] }}"
                placeholder="{{ __('website.career.filters.search_placeholder') }}">
            </div>

            <div>
              <div class="label">{{ __('website.career.filters.department') }}</div>
              <select class="input" name="department" style="height: 42px;">
                <option value="">{{ __('website.career.filters.all_departments') }}</option>
                @foreach(($filterOptions['departments'] ?? []) as $opt)
                  <option value="{{ $opt }}" @selected(($filters['department'] ?? '') === $opt)>{{ $opt }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <div class="label">{{ __('website.career.filters.location') }}</div>
              <select class="input" name="location" style="height: 42px;">
                <option value="">{{ __('website.career.filters.all_locations') }}</option>
                @foreach(($filterOptions['locations'] ?? []) as $opt)
                  <option value="{{ $opt }}" @selected(($filters['location'] ?? '') === $opt)>{{ $opt }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <div class="label">{{ __('website.career.filters.type') }}</div>
              <select class="input" name="type" style="height: 42px;">
                <option value="">{{ __('website.career.filters.all_types') }}</option>
                @foreach(($filterOptions['types'] ?? []) as $opt)
                  <option value="{{ $opt }}" @selected(($filters['type'] ?? '') === $opt)>{{ $opt }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <div class="label">{{ __('website.career.filters.work_mode') }}</div>
              <select class="input" name="work_mode" style="height: 42px;">
                <option value="">{{ __('website.career.filters.all_work_modes') }}</option>
                @foreach(($filterOptions['work_modes'] ?? []) as $opt)
                  <option value="{{ $opt }}" @selected(($filters['work_mode'] ?? '') === $opt)>{{ $opt }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <div class="label">{{ __('website.career.filters.sort') }}</div>
              <select class="input" name="sort" style="height: 42px;">
                @foreach(($filterOptions['sorts'] ?? []) as $opt)
                  <option value="{{ $opt['value'] }}" @selected(($filters['sort'] ?? 'title_asc') === $opt['value'])>
                    {{ $opt['label'] }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="filter-actions">
              <button class="btn btn-primary" type="submit">{{ __('website.career.actions.apply_filters') }}</button>
              <a class="btn btn-outline" href="{{ route('career') }}">{{ __('website.career.actions.reset') }}</a>
            </div>
          </form>

          <div class="filter-note">
            {!! __('website.career.note_html', ['mailto' => 'mailto:' . $companyEmail, 'email' => e($companyEmail)]) !!}
          </div>
        </aside>

        <div>
          @if($openings->isEmpty())
            <div class="empty-state">
              <div class="empty-title">{{ __('website.career.empty.title') }}</div>
              <p class="empty-sub">{!! __('website.career.empty.desc_html', ['mailto' => 'mailto:' . $companyEmail, 'email' => e($companyEmail)]) !!}</p>
            </div>
          @else
            <div class="jobs">
              @foreach($openings as $job)
                @php
                  $title = data_get($job, 'title', __('website.career.default_position'));
                  $dept = data_get($job, 'department', '');
                  $loc = data_get($job, 'location', '');
                  $type = data_get($job, 'type', '');
                  $mode = data_get($job, 'work_mode', '');
                  $jobId = (string) data_get($job, 'id', '');
                  $applyUrl = route('career.apply', ['job' => $jobId !== '' ? $jobId : null]);

                  $experience = trim((string) data_get($job, 'experience', ''));
                  $summary = trim((string) data_get($job, 'summary', ''));
                  $responsibilities = trim((string) data_get($job, 'responsibilities', ''));
                  $requirements = trim((string) data_get($job, 'requirements', ''));
                  $deadlineRaw = data_get($job, 'deadline');
                  $deadlineFormatted = '';
                  if (!empty($deadlineRaw)) {
                    try {
                      $deadlineFormatted = \Carbon\Carbon::parse($deadlineRaw)->format('d M Y');
                    } catch (\Throwable $e) {
                      $deadlineFormatted = (string) $deadlineRaw;
                    }
                  }

                  $responsibilityLines = $normalizeLines($responsibilities);
                  $requirementLines = $normalizeLines($requirements);

                  $hasDetails = ($summary !== '') || !empty($responsibilityLines) || !empty($requirementLines) || ($experience !== '') || ($deadlineFormatted !== '');

                  $summarySnippet = '';
                  if ($summary !== '') {
                    $summarySnippet = \Illuminate\Support\Str::limit($summary, 180);
                  }
                @endphp
                <div class="job-card">
                  <div class="job-head">
                    <div class="job-title">{{ $title }}</div>
                    <a class="btn btn-primary" href="{{ $applyUrl }}">{{ __('website.career.actions.apply') }}</a>
                  </div>
                  <div class="job-meta">
                    @if($type)
                      <span class="tag tag-type"><span class="t-dot"></span>{{ $type }}</span>
                    @endif
                    @if($mode)
                      <span class="tag tag-mode"><span class="t-dot"></span>{{ $mode }}</span>
                    @endif
                    @if($dept)
                      <span class="tag tag-dept"><span class="t-dot"></span>{{ $dept }}</span>
                    @endif
                    @if($loc)
                      <span class="tag tag-loc"><span class="t-dot"></span>{{ $loc }}</span>
                    @endif
                  </div>

                  @if($summarySnippet !== '')
                    <div class="job-snippet">{{ $summarySnippet }}</div>
                  @endif

                  @if($hasDetails)
                    <details class="job-details">
                      <summary>{{ __('website.career.details.summary_toggle') }}</summary>
                      <div class="job-details-body">
                        @if($experience !== '' || $deadlineFormatted !== '')
                          <div class="job-kv">
                            @if($experience !== '')
                              <span class="kv"><b>{{ __('website.career.details.experience') }}</b> <span>{{ $experience }}</span></span>
                            @endif
                            @if($deadlineFormatted !== '')
                              <span class="kv"><b>{{ __('website.career.details.deadline') }}</b> <span>{{ $deadlineFormatted }}</span></span>
                            @endif
                          </div>
                        @endif

                        @if($summary !== '')
                          <div>
                            <div class="job-section-title">{{ __('website.career.details.summary') }}</div>
                            <div>{!! nl2br(e($summary)) !!}</div>
                          </div>
                        @endif

                        @if(!empty($responsibilityLines))
                          <div>
                            <div class="job-section-title">{{ __('website.career.details.responsibilities') }}</div>
                            <ul class="job-list">
                              @foreach($responsibilityLines as $line)
                                <li>{{ $line }}</li>
                              @endforeach
                            </ul>
                          </div>
                        @endif

                        @if(!empty($requirementLines))
                          <div>
                            <div class="job-section-title">{{ __('website.career.details.requirements') }}</div>
                            <ul class="job-list">
                              @foreach($requirementLines as $line)
                                <li>{{ $line }}</li>
                              @endforeach
                            </ul>
                          </div>
                        @endif
                      </div>
                    </details>
                  @endif
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </section>

@endsection