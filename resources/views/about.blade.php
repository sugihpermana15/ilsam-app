@extends('layouts.app')

@section('title', __('website.titles.about_us'))

@section('main')
  <!-- Breadcrumb area start  -->
  <div class="breadcrumb__area breadcrumb-space sp-page-hero overly theme-bg-heading-primary overflow-hidden">
    <div class="breadcrumb__background" data-background="{{ asset('assets/img/aboutus/img11.jpg') }}"></div>
    <div class="container">
      <div class="row align-items-center justify-content-between">
        <div class="col-12">
          <div class="breadcrumb__content text-center">
            <h1 class="breadcrumb__title color-white title-animation">@yield('breadcrumb_title', __('website.nav.menu.about_us'))</h1>
            <div class="breadcrumb__menu d-inline-flex justify-content-center">
              <nav>
                <ul>
                  <li>
                    <span>
                      <a href="{{ route('home') }}">
                        <svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M1 5.9L7.5 1L14 5.9V13.6C14 13.9713 13.8478 14.3274 13.5769 14.5899C13.306 14.8525 12.9386 15 12.5556 15H2.44444C2.06135 15 1.69395 14.8525 1.42307 14.5899C1.15218 14.3274 1 13.9713 1 13.6V5.9Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                          <path d="M5.33398 15V8H9.66732V15" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ __('website.common.home') }}
                      </a>
                    </span>
                  </li>
                  <li class="active"><span>@yield('breadcrumb_title', __('website.nav.menu.about_us'))</span></li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Breadcrumb area end  -->

  <!-- Sub-navigasi About Us -->
  @php
    $aboutTabs = [
      ['route' => 'aboutus', 'label' => __('website.nav.menu.company_overview'), 'icon' => 'bi-building'],
      ['route' => 'ceo', 'label' => __('website.nav.menu.ceo_message'), 'icon' => 'bi-chat-quote'],
      ['route' => 'philosophy', 'label' => __('website.nav.menu.management_philosophy'), 'icon' => 'bi-compass'],
    ];
  @endphp
  <nav class="sp-subnav" aria-label="{{ __('website.pro.about_nav') }}">
    <div class="container">
      <ul class="sp-subnav__list">
        @foreach($aboutTabs as $tab)
          <li>
            <a href="{{ route($tab['route']) }}" class="sp-subnav__link{{ request()->routeIs($tab['route']) ? ' is-active' : '' }}"
              @if(request()->routeIs($tab['route'])) aria-current="page" @endif>
              <i class="bi {{ $tab['icon'] }}" aria-hidden="true"></i> {{ $tab['label'] }}
            </a>
          </li>
        @endforeach
      </ul>
    </div>
  </nav>

  <div class="sp-about-page">
    @yield('aboutus')
  </div>
@endsection
