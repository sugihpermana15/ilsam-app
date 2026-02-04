<section class="pb-0">
  @php
    $heroImageRaw = (string) data_get($product ?? [], 'heroImage', '');
    $heroImageRaw = trim($heroImageRaw) !== '' ? trim($heroImageRaw) : 'assets/img/img9.jpg';
    $heroImageUrl = preg_match('~^https?://~i', $heroImageRaw) ? $heroImageRaw : asset(ltrim($heroImageRaw, '/'));
  @endphp
  <div class="row g-30 align-items-center">
    <div class="col-lg-7">
      <div class="section__title-wrapper text-center text-lg-start mb-20">
        <span class="section__subtitle justify-content-start mb-13">
          <span data-width="40px" class="left-separetor"></span>
          {{ $product['title'] ?? __('website.products_detail.product') }}
        </span>

        @if (!empty($product['tagline']))
          <h2 class="section__title title-animation mb-15 rr-br-hidden-md" data-cursor="-opaque">
            {{ $product['tagline'] }}
          </h2>
        @endif

        @if (!empty($product['intro']))
          <p class="des mb-0">{{ $product['intro'] }}</p>
        @endif

        @if (!empty($product['cta']))
          <div class="mt-25 d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
            @if (!empty($product['cta']['primaryUrl']) && !empty($product['cta']['primaryText']))
              <a href="{{ $product['cta']['primaryUrl'] }}" class="rr-btn w-auto">
                <span class="btn-wrap">
                  <span class="text-one">{{ $product['cta']['primaryText'] }}</span>
                  <span class="text-two">{{ $product['cta']['primaryText'] }}</span>
                </span>
              </a>
            @endif

            @if (!empty($product['cta']['secondaryUrl']) && !empty($product['cta']['secondaryText']))
              <a href="{{ $product['cta']['secondaryUrl'] }}" class="rr-btn rr-btn__transparent w-auto">
                <span class="btn-wrap">
                  <span class="text-one">{{ $product['cta']['secondaryText'] }}</span>
                  <span class="text-two">{{ $product['cta']['secondaryText'] }}</span>
                </span>
              </a>
            @endif
          </div>
        @endif
      </div>
    </div>

    <div class="col-lg-5">
      <div class="ilsam-hero-media">
        <div class="ilsam-hero-media__bg" data-background="{{ $heroImageUrl }}"></div>
      </div>
    </div>
  </div>
</section>

@if (!empty($product['capabilities']))
  <section class="working-process pt-55 overflow-hidden">
    <div class="row mb-minus-30">
      @foreach ($product['capabilities'] as $cap)
        @php
          $accentPalette = ['#ef4444', '#06b6d4', '#f59e0b', '#8b5cf6', '#22c55e', '#3b82f6'];
          $capAccent = $accentPalette[$loop->index % count($accentPalette)];
        @endphp
        <div class="col-xl-3 col-sm-6">
          <div class="working-process__item text-center mb-30" style="--ilsam-accent: {{ $capAccent }};">
            <div class="working-process__item-icon mb-40">
              <div class="working-process__item-icon-img" aria-hidden="true">
                <i class="bi {{ $cap['icon'] ?? 'bi-check2-circle' }}"></i>
              </div>
            </div>
            <h4 class="title mb-10">{{ $cap['title'] ?? '' }}</h4>
            <p class="des mb-0">{{ $cap['desc'] ?? '' }}</p>
          </div>
        </div>
      @endforeach
    </div>
  </section>
@endif

@if (!empty($product['lines']))
  <section class="pt-60">
    <div class="ilsam-panel p-4 p-lg-5">
      <div class="row align-items-end g-20">
        <div class="col-lg-7">
          <h3 class="mb-10">{{ __('website.products_detail.product_lines.title') }}</h3>
          <p class="mb-0" style="color: rgba(21, 24, 27, 0.72);">
            {{ __('website.products_detail.product_lines.desc') }}
          </p>
        </div>
        <div class="col-lg-5 d-flex justify-content-lg-end">
          <a href="{{ route('contact') }}" class="rr-btn rr-btn__transparent w-auto">
            <span class="btn-wrap">
              <span class="text-one">{{ __('website.products_detail.product_lines.cta_request') }}</span>
              <span class="text-two">{{ __('website.products_detail.product_lines.cta_request') }}</span>
            </span>
          </a>
        </div>
      </div>

      <div class="row g-0 mt-20">
        @foreach ($product['lines'] as $line)
          @php
            $accentPalette = ['#ef4444', '#06b6d4', '#f59e0b', '#8b5cf6', '#22c55e', '#3b82f6'];
            $lineAccent = $accentPalette[$loop->index % count($accentPalette)];
          @endphp
          <div class="col-md-6 p-2 p-md-3">
            <div class="ilsam-line h-100" style="--ilsam-accent: {{ $lineAccent }};">
              <h5 class="mb-8">{{ $line['title'] ?? '' }}</h5>
              @if (!empty($line['subtitle']))
                <p class="mb-12 ilsam-muted-sm">{{ $line['subtitle'] }}</p>
              @endif
              @if (!empty($line['codes']))
                <div class="ilsam-codes">
                  @foreach ($line['codes'] as $code)
                    <span class="ilsam-code">{{ $code }}</span>
                  @endforeach
                </div>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>
@endif

@if (!empty($product['applications']) || !empty($product['specs']))
  <section class="pt-60">
    <div class="row g-0 align-items-stretch">
      <div class="col-lg-6 p-2 p-md-3 mb-3 mb-lg-0">
        <div class="ilsam-panel p-4 p-lg-5 h-100">
          <h3 class="mb-12">{{ __('website.products_detail.applications.title') }}</h3>
          @if (!empty($product['applicationsIntro']))
            <p class="mb-18">{{ $product['applicationsIntro'] }}</p>
          @else
            <p class="mb-18">{{ __('website.products_detail.applications.desc_fallback') }}</p>
          @endif

          @if (!empty($product['applications']))
            <ul class="mb-0 ilsam-list">
              @foreach ($product['applications'] as $app)
                <li class="mb-8">{{ $app }}</li>
              @endforeach
            </ul>
          @endif
        </div>
      </div>

      <div class="col-lg-6 p-2 p-md-3">
        <div class="ilsam-panel ilsam-panel--soft p-4 p-lg-5 h-100">
          <h3 class="mb-12">{{ __('website.products_detail.overview.title') }}</h3>
          @if (!empty($product['overviewIntro']))
            <p class="mb-18">{{ $product['overviewIntro'] }}</p>
          @else
            <p class="mb-18">{{ __('website.products_detail.overview.desc_fallback') }}</p>
          @endif

          @if (!empty($product['specs']))
            <div class="table-responsive">
              <table class="table mb-0 ilsam-table">
                <tbody>
                  @foreach ($product['specs'] as $label => $value)
                    <tr>
                      <td class="ilsam-table__key">{{ $label }}</td>
                      <td class="ilsam-table__val">{{ $value }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    </div>
  </section>
@endif

<section class="pt-60">
  <div class="ilsam-panel ilsam-panel--dark p-4 p-lg-5">
    <div class="row align-items-center g-20">
      <div class="col-lg-8">
        <h3 class="mb-10">{{ $product['ctaHeading'] ?? __('website.products_detail.cta.heading_fallback') }}</h3>
        <p class="mb-0">
          {{ $product['ctaText'] ?? __('website.products_detail.cta.text_fallback') }}
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