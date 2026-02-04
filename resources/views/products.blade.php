@extends('layouts.app')

@section('title')
  @yield('page_title', __('website.titles.products'))
@endsection

@section('main')
  @php
    $ws = \App\Support\WebsiteSettings::all();
    $breadcrumbBgRaw = data_get($ws, 'products.page.breadcrumb_bg', 'assets/img/img4.jpg');
    $breadcrumbBgRaw = is_string($breadcrumbBgRaw) && trim($breadcrumbBgRaw) !== '' ? trim($breadcrumbBgRaw) : 'assets/img/img4.jpg';
    $breadcrumbBgUrl = preg_match('~^https?://~i', $breadcrumbBgRaw)
      ? $breadcrumbBgRaw
      : route('img', ['path' => ltrim($breadcrumbBgRaw, '/'), 'w' => 1920, 'q' => 65]);
  @endphp

  <!-- Breadcrumb area start  -->
  <div class="breadcrumb__area breadcrumb-space overly theme-bg-heading-primary overflow-hidden">
    <div class="breadcrumb__background" data-background="{{ $breadcrumbBgUrl }}"></div>
    <div class="container">
      <div class="row align-items-center justify-content-between">
        <div class="col-12">
          <div class="breadcrumb__content text-center">
            <h1 class="breadcrumb__title color-white title-animation">@yield('breadcrumb_title', __('website.nav.menu.products'))</h1>
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
                  <li class="active"><span>@yield('breadcrumb_title', __('website.nav.menu.products'))</span></li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Breadcrumb area end  -->

  <div class="page-service-details section-space">
    <div class="container">
      <div class="row g-40">
        <div class="col-12">
          <div class="ilsam-products">
            @yield('products_content')
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection