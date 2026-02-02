@extends('technology')

@section('page_title', __('website.titles.technology'))
@section('breadcrumb_title', __('website.nav.menu.technology'))

@section('meta_description', 'Explore the technology and R&D focus of PT ILSAM GLOBAL INDONESIA, including our domains, standards, and innovation approach.')
@section('meta_image', asset('assets/img/img15.jpg'))
@section('rnd_content')
  <section class="ilsam-rnd-hero">
    <div class="ilsam-rnd-hero__bg" data-background="{{ asset('assets/img/img15.jpg') }}"></div>
    <div class="ilsam-rnd-hero__overlay" aria-hidden="true"></div>

    <div class="row align-items-center g-30">
      <div class="col-lg-7">
        <span class="section__subtitle justify-content-start mb-13 ilsam-rnd-hero__subtitle">
          <span data-width="40px" class="left-separetor"></span>
          {{ __('website.nav.menu.technology') }}
        </span>

        <h2 class="section__title title-animation mb-15 rr-br-hidden-md ilsam-rnd-hero__title" data-cursor="-opaque">
          {{ __('website.technology.hero.title') }}
        </h2>

        <p class="des mb-0 ilsam-rnd-hero__desc">
          {{ __('website.technology.hero.desc') }}
        </p>

        <div class="mt-25 d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
          <a href="#domains" class="rr-btn rr-btn__white w-auto">
            <span class="btn-wrap">
              <span class="text-one">{{ __('website.technology.hero.cta_explore') }}</span>
              <span class="text-two">{{ __('website.technology.hero.cta_explore') }}</span>
            </span>
          </a>
          <a href="{{ route('contact') }}" class="rr-btn w-auto ilsam-rnd-btn-ghost">
            <span class="btn-wrap">
              <span class="text-one">{{ __('website.technology.hero.cta_talk') }}</span>
              <span class="text-two">{{ __('website.technology.hero.cta_talk') }}</span>
            </span>
          </a>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="row g-0">
          <div class="col-6 p-2">
            <div class="ilsam-rnd-kpi__item">
              <div class="ilsam-rnd-kpi__icon"><i class="bi bi-grid-1x2" aria-hidden="true"></i></div>
              <div class="ilsam-rnd-kpi__value">4</div>
              <div class="ilsam-rnd-kpi__label">{{ __('website.technology.kpis.product_families') }}</div>
            </div>
          </div>
          <div class="col-6 p-2">
            <div class="ilsam-rnd-kpi__item">
              <div class="ilsam-rnd-kpi__icon"><i class="bi bi-tag" aria-hidden="true"></i></div>
              <div class="ilsam-rnd-kpi__value">15+</div>
              <div class="ilsam-rnd-kpi__label">{{ __('website.technology.kpis.line_codes') }}</div>
            </div>
          </div>
          <div class="col-6 p-2">
            <div class="ilsam-rnd-kpi__item">
              <div class="ilsam-rnd-kpi__icon"><i class="bi bi-file-earmark-text" aria-hidden="true"></i></div>
              <div class="ilsam-rnd-kpi__value">COA</div>
              <div class="ilsam-rnd-kpi__label">{{ __('website.technology.kpis.sds_coa_support') }}</div>
            </div>
          </div>
          <div class="col-6 p-2">
            <div class="ilsam-rnd-kpi__item">
              <div class="ilsam-rnd-kpi__icon"><i class="bi bi-gear" aria-hidden="true"></i></div>
              <div class="ilsam-rnd-kpi__value">Pilot</div>
              <div class="ilsam-rnd-kpi__label">{{ __('website.technology.kpis.scale_up_support') }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="domains" class="pt-60">
    <div class="row align-items-end g-20">
      <div class="col-lg-8">
        <h3 class="mb-10">{{ __('website.technology.capabilities.title') }}</h3>
        <p class="mb-0 ilsam-muted-sm">
          {{ __('website.technology.capabilities.desc') }}
        </p>
      </div>
      <div class="col-lg-4 d-flex justify-content-lg-end">
        <a href="#portfolio" class="rr-btn rr-btn__transparent w-auto">
          <span class="btn-wrap">
            <span class="text-one">{{ __('website.technology.capabilities.cta_portfolio') }}</span>
            <span class="text-two">{{ __('website.technology.capabilities.cta_portfolio') }}</span>
          </span>
        </a>
      </div>
    </div>

    <div class="row g-0 mt-20">
      <div class="col-lg-3 col-sm-6 p-2 p-md-3">
        <div class="ilsam-rnd-card ilsam-rnd-card--process h-100">
          <div class="ilsam-rnd-card__icon"><i class="bi bi-sliders" aria-hidden="true"></i></div>
          <h4 class="mb-10">{{ __('website.technology.capabilities.cards.process.title') }}</h4>
          <p class="mb-0 ilsam-muted-sm">{{ __('website.technology.capabilities.cards.process.desc') }}</p>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6 p-2 p-md-3">
        <div class="ilsam-rnd-card ilsam-rnd-card--dispersion h-100">
          <div class="ilsam-rnd-card__icon"><i class="bi bi-droplet-half" aria-hidden="true"></i></div>
          <h4 class="mb-10">{{ __('website.technology.capabilities.cards.dispersion.title') }}</h4>
          <p class="mb-0 ilsam-muted-sm">{{ __('website.technology.capabilities.cards.dispersion.desc') }}</p>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6 p-2 p-md-3">
        <div class="ilsam-rnd-card ilsam-rnd-card--curing h-100">
          <div class="ilsam-rnd-card__icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></div>
          <h4 class="mb-10">{{ __('website.technology.capabilities.cards.curing.title') }}</h4>
          <p class="mb-0 ilsam-muted-sm">{{ __('website.technology.capabilities.cards.curing.desc') }}</p>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6 p-2 p-md-3">
        <div class="ilsam-rnd-card ilsam-rnd-card--quality h-100">
          <div class="ilsam-rnd-card__icon"><i class="bi bi-shield-check" aria-hidden="true"></i></div>
          <h4 class="mb-10">{{ __('website.technology.capabilities.cards.quality.title') }}</h4>
          <p class="mb-0 ilsam-muted-sm">{{ __('website.technology.capabilities.cards.quality.desc') }}</p>
        </div>
      </div>
    </div>
  </section>

  <section class="pt-60">
    <div class="working-process-2 ilsam-rnd-stage">
      <div class="working-process-2__bg" data-background="{{ asset('assets/img/img4.jpg') }}"></div>
      <div class="working-process-2__bg-overlay" aria-hidden="true"></div>

      <div class="row align-items-center g-30">
        <div class="col-lg-6">
          <div class="section__title-wrapper text-center text-lg-start">
            <span class="section__subtitle justify-content-start mb-13 ilsam-rnd-stage__subtitle">
              <span data-width="40px" class="left-separetor"></span>
                {{ __('website.technology.workflow.subtitle') }}
            </span>
            <h3 class="mb-12 ilsam-rnd-stage__title">{{ __('website.technology.workflow.title') }}</h3>
            <p class="mb-0 ilsam-rnd-stage__desc">
              {{ __('website.technology.workflow.desc') }}
            </p>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="working-process-2__list d-flex flex-column">
            <div class="working-process-2__list-item d-flex active">
              <div class="number">01</div>
              <div>
                <h4 class="title ilsam-rnd-stage__item-title">{{ __('website.technology.workflow.steps.1.title') }}</h4>
                <p class="des mb-0">{{ __('website.technology.workflow.steps.1.desc') }}</p>
              </div>
            </div>
            <div class="working-process-2__list-item d-flex">
              <div class="number">02</div>
              <div>
                <h4 class="title ilsam-rnd-stage__item-title">{{ __('website.technology.workflow.steps.2.title') }}</h4>
                <p class="des mb-0">{{ __('website.technology.workflow.steps.2.desc') }}</p>
              </div>
            </div>
            <div class="working-process-2__list-item d-flex">
              <div class="number">03</div>
              <div>
                <h4 class="title ilsam-rnd-stage__item-title">{{ __('website.technology.workflow.steps.3.title') }}</h4>
                <p class="des mb-0">{{ __('website.technology.workflow.steps.3.desc') }}</p>
              </div>
            </div>
            <div class="working-process-2__list-item d-flex">
              <div class="number">04</div>
              <div>
                <h4 class="title ilsam-rnd-stage__item-title">{{ __('website.technology.workflow.steps.4.title') }}</h4>
                <p class="des mb-0">{{ __('website.technology.workflow.steps.4.desc') }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="portfolio" class="pt-60">
    <div class="row align-items-end g-20">
      <div class="col-lg-8">
        <h3 class="mb-10">{{ __('website.technology.portfolio.title') }}</h3>
        <p class="mb-0 ilsam-muted-sm">
          {{ __('website.technology.portfolio.desc') }}
        </p>
      </div>
      <div class="col-lg-4 d-flex justify-content-lg-end">
        <a href="{{ route('contact') }}" class="rr-btn rr-btn__transparent w-auto">
          <span class="btn-wrap">
            <span class="text-one">{{ __('website.technology.portfolio.cta_request_datasheet') }}</span>
            <span class="text-two">{{ __('website.technology.portfolio.cta_request_datasheet') }}</span>
          </span>
        </a>
      </div>
    </div>

    <div class="row g-0 mt-20">
      @foreach ($portfolio as $cat)
        @php
          $title = strtolower($cat['title'] ?? '');
          $accentClass = 'ilsam-rnd-portfolio-card--default';
          if (str_contains($title, 'color')) {
              $accentClass = 'ilsam-rnd-portfolio-card--colorants';
          } elseif (str_contains($title, 'surface')) {
              $accentClass = 'ilsam-rnd-portfolio-card--surface';
          } elseif (str_contains($title, 'additive')) {
              $accentClass = 'ilsam-rnd-portfolio-card--additive';
          } elseif (str_contains($title, 'resin')) {
              $accentClass = 'ilsam-rnd-portfolio-card--resin';
          }
        @endphp
        <div class="col-lg-6 p-2 p-md-3">
          <div class="ilsam-rnd-portfolio-card {{ $accentClass }} h-100">
            <div class="d-flex align-items-start justify-content-between gap-3">
              <div>
                <h4 class="mb-6">{{ $cat['title'] }}</h4>
                @if (!empty($cat['subtitle']))
                  <p class="mb-0 ilsam-muted-sm">{{ $cat['subtitle'] }}</p>
                @endif
              </div>

              <a href="{{ $cat['route'] }}" class="rr-btn rr-btn__transparent w-auto ilsam-rnd-btn-sm">
                <span class="btn-wrap">
                  <span class="text-one">{{ __('website.common.open_details') }}</span>
                  <span class="text-two">{{ __('website.common.open_details') }}</span>
                </span>
              </a>
            </div>

            <div class="mt-18">
              @foreach ($cat['lines'] as $line)
                <div class="ilsam-rnd-line">
                  <div class="ilsam-rnd-line__title">{{ $line['title'] }}</div>
                  @if (!empty($line['codes']))
                    <div class="ilsam-codes">
                      @foreach ($line['codes'] as $code)
                        <span class="ilsam-code">{{ $code }}</span>
                      @endforeach
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </section>

  <section class="pt-60">
    <div class="ilsam-panel ilsam-panel--dark p-4 p-lg-5">
      <div class="row align-items-center g-20">
        <div class="col-lg-8">
          <h3 class="mb-10">{{ __('website.technology.support.title') }}</h3>
          <p class="mb-0">
            {{ __('website.technology.support.desc') }}
          </p>
        </div>
        <div class="col-lg-4 d-flex justify-content-lg-end">
          <a href="{{ route('contact') }}" class="rr-btn rr-btn__white w-auto">
            <span class="btn-wrap">
              <span class="text-one">{{ __('website.common.contact_us') }}</span>
              <span class="text-two">{{ __('website.common.contact_us') }}</span>
            </span>
          </a>
        </div>
      </div>
    </div>
  </section>
@endsection