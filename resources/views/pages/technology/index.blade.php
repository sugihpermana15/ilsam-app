@extends('technology')

@section('page_title', 'Ilsam - Technology (R&D)')
@section('breadcrumb_title', 'Technology (R&D)')

@section('rnd_content')
  <section class="ilsam-rnd-hero">
    <div class="ilsam-rnd-hero__bg" data-background="{{ asset('assets/img/img15.jpg') }}"></div>
    <div class="ilsam-rnd-hero__overlay" aria-hidden="true"></div>

    <div class="row align-items-center g-30">
      <div class="col-lg-7">
        <span class="section__subtitle justify-content-start mb-13 ilsam-rnd-hero__subtitle">
          <span data-width="40px" class="left-separetor"></span>
          Technology (R&amp;D)
        </span>

        <h2 class="section__title title-animation mb-15 rr-br-hidden-md ilsam-rnd-hero__title" data-cursor="-opaque">
          Production-focused R&amp;D for stable processes and repeatable output.
        </h2>

        <p class="des mb-0 ilsam-rnd-hero__desc">
          Our work is oriented to manufacturing reality: dispersion stability, viscosity control, curing behavior, and
          batch-to-batch consistency. We support pilot trials and scale-up with clear technical guidance and
          documentation.
        </p>

        <div class="mt-25 d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
          <a href="#domains" class="rr-btn rr-btn__white w-auto">
            <span class="btn-wrap">
              <span class="text-one">Explore Capabilities</span>
              <span class="text-two">Explore Capabilities</span>
            </span>
          </a>
          <a href="{{ route('contact') }}" class="rr-btn w-auto ilsam-rnd-btn-ghost">
            <span class="btn-wrap">
              <span class="text-one">Talk to Technical Team</span>
              <span class="text-two">Talk to Technical Team</span>
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
              <div class="ilsam-rnd-kpi__label">Product families</div>
            </div>
          </div>
          <div class="col-6 p-2">
            <div class="ilsam-rnd-kpi__item">
              <div class="ilsam-rnd-kpi__icon"><i class="bi bi-tag" aria-hidden="true"></i></div>
              <div class="ilsam-rnd-kpi__value">15+</div>
              <div class="ilsam-rnd-kpi__label">Line codes</div>
            </div>
          </div>
          <div class="col-6 p-2">
            <div class="ilsam-rnd-kpi__item">
              <div class="ilsam-rnd-kpi__icon"><i class="bi bi-file-earmark-text" aria-hidden="true"></i></div>
              <div class="ilsam-rnd-kpi__value">COA</div>
              <div class="ilsam-rnd-kpi__label">SDS / COA support</div>
            </div>
          </div>
          <div class="col-6 p-2">
            <div class="ilsam-rnd-kpi__item">
              <div class="ilsam-rnd-kpi__icon"><i class="bi bi-gear" aria-hidden="true"></i></div>
              <div class="ilsam-rnd-kpi__value">Pilot</div>
              <div class="ilsam-rnd-kpi__label">Scale-up support</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="domains" class="pt-60">
    <div class="row align-items-end g-20">
      <div class="col-lg-8">
        <h3 class="mb-10">Manufacturing Capabilities</h3>
        <p class="mb-0 ilsam-muted-sm">
          R&amp;D activities are aligned with production needs across PU/PVC synthetic leather, coatings, additives, and
          resin systems.
        </p>
      </div>
      <div class="col-lg-4 d-flex justify-content-lg-end">
        <a href="#portfolio" class="rr-btn rr-btn__transparent w-auto">
          <span class="btn-wrap">
            <span class="text-one">View Portfolio</span>
            <span class="text-two">View Portfolio</span>
          </span>
        </a>
      </div>
    </div>

    <div class="row g-0 mt-20">
      <div class="col-lg-3 col-sm-6 p-2 p-md-3">
        <div class="ilsam-rnd-card ilsam-rnd-card--process h-100">
          <div class="ilsam-rnd-card__icon"><i class="bi bi-sliders" aria-hidden="true"></i></div>
          <h4 class="mb-10">Process optimization</h4>
          <p class="mb-0 ilsam-muted-sm">Parameter guidance for stable runs and consistent results.</p>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6 p-2 p-md-3">
        <div class="ilsam-rnd-card ilsam-rnd-card--dispersion h-100">
          <div class="ilsam-rnd-card__icon"><i class="bi bi-droplet-half" aria-hidden="true"></i></div>
          <h4 class="mb-10">Dispersion stability</h4>
          <p class="mb-0 ilsam-muted-sm">Designed for storage stability and robust shop-floor handling.</p>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6 p-2 p-md-3">
        <div class="ilsam-rnd-card ilsam-rnd-card--curing h-100">
          <div class="ilsam-rnd-card__icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></div>
          <h4 class="mb-10">Curing &amp; throughput</h4>
          <p class="mb-0 ilsam-muted-sm">Support for curing, adhesion, and line-speed considerations.</p>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6 p-2 p-md-3">
        <div class="ilsam-rnd-card ilsam-rnd-card--quality h-100">
          <div class="ilsam-rnd-card__icon"><i class="bi bi-shield-check" aria-hidden="true"></i></div>
          <h4 class="mb-10">Quality &amp; traceability</h4>
          <p class="mb-0 ilsam-muted-sm">COA/SDS support and structured validation guidance when needed.</p>
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
              R&amp;D Workflow
            </span>
            <h3 class="mb-12 ilsam-rnd-stage__title">From requirement to production-ready recommendation</h3>
            <p class="mb-0 ilsam-rnd-stage__desc">
              We translate your process requirements into a recommended product line code and documentation set.
              The workflow below reflects a practical manufacturing-first approach.
            </p>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="working-process-2__list d-flex flex-column">
            <div class="working-process-2__list-item d-flex active">
              <div class="number">01</div>
              <div>
                <h4 class="title ilsam-rnd-stage__item-title">Process intake</h4>
                <p class="des mb-0">Substrate, equipment, target properties, and compliance needs.</p>
              </div>
            </div>
            <div class="working-process-2__list-item d-flex">
              <div class="number">02</div>
              <div>
                <h4 class="title ilsam-rnd-stage__item-title">Bench trials</h4>
                <p class="des mb-0">Select candidates, verify stability, and confirm key performance.</p>
              </div>
            </div>
            <div class="working-process-2__list-item d-flex">
              <div class="number">03</div>
              <div>
                <h4 class="title ilsam-rnd-stage__item-title">Pilot &amp; scale-up</h4>
                <p class="des mb-0">Confirm behavior in real process conditions and align parameters.</p>
              </div>
            </div>
            <div class="working-process-2__list-item d-flex">
              <div class="number">04</div>
              <div>
                <h4 class="title ilsam-rnd-stage__item-title">Release &amp; documentation</h4>
                <p class="des mb-0">Provide COA/SDS and guidance for stable repeatable production runs.</p>
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
        <h3 class="mb-10">Portfolio Reference (Line Codes)</h3>
        <p class="mb-0 ilsam-muted-sm">
          These line codes match the product detail pages. Use them when requesting samples, documentation, or
          process support.
        </p>
      </div>
      <div class="col-lg-4 d-flex justify-content-lg-end">
        <a href="{{ route('contact') }}" class="rr-btn rr-btn__transparent w-auto">
          <span class="btn-wrap">
            <span class="text-one">Request Datasheet / COA</span>
            <span class="text-two">Request Datasheet / COA</span>
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
                  <span class="text-one">Open Details</span>
                  <span class="text-two">Open Details</span>
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
          <h3 class="mb-10">Need production support?</h3>
          <p class="mb-0">
            Share your process conditions and target performance. We can recommend the relevant product line code and
            next steps for stable production runs.
          </p>
        </div>
        <div class="col-lg-4 d-flex justify-content-lg-end">
          <a href="{{ route('contact') }}" class="rr-btn rr-btn__white w-auto">
            <span class="btn-wrap">
              <span class="text-one">Contact Us</span>
              <span class="text-two">Contact Us</span>
            </span>
          </a>
        </div>
      </div>
    </div>
  </section>
@endsection