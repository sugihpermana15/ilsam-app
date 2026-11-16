@extends('technology')

@section('page_title', __('website.titles.technology'))
@section('breadcrumb_title', __('website.nav.menu.technology'))

@php
  $settings = \App\Support\WebsiteSettings::all();
  $locale = app()->getLocale();
  $fallbackLocale = config('app.fallback_locale', 'en');
  $metaDescription = data_get($settings, "seo.technology.meta_description.$locale")
    ?: data_get($settings, "seo.technology.meta_description.$fallbackLocale")
    ?: 'Explore the technology and R&D focus of PT ILSAM GLOBAL INDONESIA, including our domains, standards, and innovation approach.';
  $metaImage = data_get($settings, 'seo.technology.meta_image') ?: asset('assets/img/img15.jpg');

  $heroBgRaw = data_get($settings, 'technology.page.hero_bg') ?: 'assets/img/img15.jpg';
  $heroBgUrl = preg_match('~^https?://~i', (string) $heroBgRaw) ? (string) $heroBgRaw : asset(ltrim((string) $heroBgRaw, '/'));

  $workflowBgRaw = data_get($settings, 'technology.page.workflow_bg') ?: 'assets/img/img4.jpg';
  $workflowBgUrl = preg_match('~^https?://~i', (string) $workflowBgRaw) ? (string) $workflowBgRaw : asset(ltrim((string) $workflowBgRaw, '/'));
@endphp
@section('meta_description', $metaDescription)
@section('meta_image', str_starts_with($metaImage, 'http') ? $metaImage : asset($metaImage))
@php
  $rndKpis = [
    ['icon' => 'bi-grid-1x2', 'value' => '4', 'label' => __('website.technology.kpis.product_families')],
    ['icon' => 'bi-tag', 'value' => '15+', 'label' => __('website.technology.kpis.line_codes')],
    ['icon' => 'bi-file-earmark-text', 'value' => 'COA', 'label' => __('website.technology.kpis.sds_coa_support')],
    ['icon' => 'bi-gear', 'value' => 'Pilot', 'label' => __('website.technology.kpis.scale_up_support')],
  ];
  $rndCapabilities = [
    ['key' => 'process', 'icon' => 'bi-sliders'],
    ['key' => 'dispersion', 'icon' => 'bi-droplet-half'],
    ['key' => 'curing', 'icon' => 'bi-speedometer2'],
    ['key' => 'quality', 'icon' => 'bi-shield-check'],
  ];
  $portfolioIcon = function (string $title): string {
    $title = strtolower($title);
    return match (true) {
      str_contains($title, 'color') => 'bi-palette',
      str_contains($title, 'surface') => 'bi-brush',
      str_contains($title, 'additive') => 'bi-layers',
      str_contains($title, 'resin') => 'bi-droplet',
      default => 'bi-box-seam',
    };
  };
@endphp
@section('rnd_sections')
  {{-- Pengantar --}}
  <section class="sp-rnd-hero" style="--sp-rnd-bg: url('{{ $heroBgUrl }}');">
    <div class="container">
      <div class="row align-items-center gy-5 gx-xl-5">
        <div class="col-lg-7">
          <span class="sp-eyebrow sp-eyebrow--light">{{ __('website.nav.menu.technology') }}</span>
          <h2 class="sp-title sp-title--light">{{ __('website.technology.hero.title') }}</h2>
          <p class="sp-rnd-hero__desc">{{ __('website.technology.hero.desc') }}</p>
          <div class="sp-hero__actions">
            <a href="#domains" class="sp-btn sp-btn--primary">{{ __('website.technology.hero.cta_explore') }} <i class="bi bi-arrow-down" aria-hidden="true"></i></a>
            <a href="{{ route('contact') }}" class="sp-btn sp-btn--glass">{{ __('website.technology.hero.cta_talk') }}</a>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="sp-kpis">
            @foreach ($rndKpis as $kpi)
              <div class="sp-kpi">
                <span class="sp-kpi__icon"><i class="bi {{ $kpi['icon'] }}" aria-hidden="true"></i></span>
                <span class="sp-kpi__value">{{ $kpi['value'] }}</span>
                <span class="sp-kpi__label">{{ $kpi['label'] }}</span>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Kapabilitas --}}
  <section id="domains" class="sp-section">
    <div class="container">
      <div class="sp-section__head">
        <div class="sp-section__copy">
          <span class="sp-eyebrow">{{ __('website.pro.rnd_capabilities_label') }}</span>
          <h2 class="sp-title">{{ __('website.technology.capabilities.title') }}</h2>
          <p class="sp-lead mb-0">{{ __('website.technology.capabilities.desc') }}</p>
        </div>
        <a href="#portfolio" class="sp-link">{{ __('website.technology.capabilities.cta_portfolio') }} <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
      </div>

      <div class="row g-4">
        @foreach ($rndCapabilities as $i => $capability)
          <div class="col-xl-3 col-md-6">
            <div class="sp-pillar wow fadeInUp" data-wow-delay="{{ 0.05 + $i * 0.08 }}s">
              <span class="sp-pillar__icon"><i class="bi {{ $capability['icon'] }}" aria-hidden="true"></i></span>
              <h3 class="sp-pillar__title">{{ __('website.technology.capabilities.cards.' . $capability['key'] . '.title') }}</h3>
              <p class="sp-pillar__desc">{{ __('website.technology.capabilities.cards.' . $capability['key'] . '.desc') }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- Alur kerja --}}
  <section class="sp-workflow" style="--sp-workflow-bg: url('{{ $workflowBgUrl }}');">
    <div class="container">
      <div class="sp-section__head sp-section__head--center">
        <div>
          <span class="sp-eyebrow sp-eyebrow--light">{{ __('website.technology.workflow.subtitle') }}</span>
          <h2 class="sp-title sp-title--light">{{ __('website.technology.workflow.title') }}</h2>
          <p class="sp-workflow__desc">{{ __('website.technology.workflow.desc') }}</p>
        </div>
      </div>

      <ol class="sp-steps">
        @foreach (['1', '2', '3', '4'] as $i => $step)
          <li class="sp-step wow fadeInUp" data-wow-delay="{{ 0.05 + $i * 0.1 }}s">
            <span class="sp-step__num">{{ str_pad($step, 2, '0', STR_PAD_LEFT) }}</span>
            <h3 class="sp-step__title">{{ __('website.technology.workflow.steps.' . $step . '.title') }}</h3>
            <p class="sp-step__desc">{{ __('website.technology.workflow.steps.' . $step . '.desc') }}</p>
          </li>
        @endforeach
      </ol>
    </div>
  </section>

  {{-- Portofolio --}}
  <section id="portfolio" class="sp-section sp-section--alt">
    <div class="container">
      <div class="sp-section__head">
        <div class="sp-section__copy">
          <span class="sp-eyebrow">{{ __('website.nav.menu.products') }}</span>
          <h2 class="sp-title">{{ __('website.technology.portfolio.title') }}</h2>
          <p class="sp-lead mb-0">{{ __('website.technology.portfolio.desc') }}</p>
        </div>
        <a href="{{ route('contact') }}" class="sp-btn sp-btn--outline">{{ __('website.technology.portfolio.cta_request_datasheet') }} <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
      </div>

      <div class="row g-4">
        @foreach ($portfolio as $i => $cat)
          <div class="col-lg-6">
            <article class="sp-family wow fadeInUp" data-wow-delay="{{ 0.05 + ($i % 2) * 0.08 }}s">
              <header class="sp-family__head">
                <span class="sp-pillar__icon"><i class="bi {{ $portfolioIcon((string) ($cat['title'] ?? '')) }}" aria-hidden="true"></i></span>
                <div>
                  <h3 class="sp-family__title">{{ $cat['title'] }}</h3>
                  @if (!empty($cat['subtitle']))
                    <p class="sp-family__sub">{{ $cat['subtitle'] }}</p>
                  @endif
                </div>
              </header>
              <ul class="sp-family__lines">
                @foreach ($cat['lines'] as $line)
                  <li>
                    <span class="sp-family__line">{{ $line['title'] }}</span>
                    @if (!empty($line['codes']))
                      <span class="sp-family__codes">
                        @foreach ($line['codes'] as $code)
                          <span class="sp-code">{{ $code }}</span>
                        @endforeach
                      </span>
                    @endif
                  </li>
                @endforeach
              </ul>
              <a href="{{ $cat['route'] }}" class="sp-family__more">{{ __('website.common.open_details') }} <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </article>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- Dukungan teknis --}}
  <section class="sp-cta">
    <div class="container">
      <div class="sp-cta__inner">
        <div>
          <h2 class="sp-title sp-title--light">{{ __('website.technology.support.title') }}</h2>
          <p class="sp-cta__text">{{ __('website.technology.support.desc') }}</p>
        </div>
        <a href="{{ route('contact') }}" class="sp-btn sp-btn--light">{{ __('website.common.contact_us') }} <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
      </div>
    </div>
  </section>
@endsection
