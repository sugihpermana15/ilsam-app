<footer>
  @php
    $ws = \App\Support\WebsiteSettings::all();
    $phoneDisplay = data_get($ws, 'contact.phone_display', '+62 (021) 89830313 / 0314');
    $phoneTel = data_get($ws, 'contact.phone_tel', '02189830313');
    $email = data_get($ws, 'contact.email', 'market.ilsamindonesia@yahoo.com');
    $mapUrl = data_get($ws, 'contact.map_url', 'https://maps.app.goo.gl/reUj3juAoQ8NrGLE6');
    $addressText = data_get($ws, 'contact.address_text', '');

    $toAssetUrl = function ($raw, $fallback) {
      $value = is_string($raw) ? trim($raw) : '';
      if ($value === '') {
        $value = $fallback;
      }

      if (preg_match('~^https?://~i', $value)) {
        $path = (string) parse_url($value, PHP_URL_PATH);
        $query = (string) parse_url($value, PHP_URL_QUERY);
        if ($path !== '' && (str_starts_with($path, '/assets/') || str_starts_with($path, '/storage/'))) {
          $local = asset(ltrim($path, '/'));
          return $query !== '' ? ($local . '?' . $query) : $local;
        }
        return $value;
      }

      return asset(ltrim($value, '/'));
    };

    $toBgUrl = function ($raw, $fallback) {
      $value = is_string($raw) ? trim($raw) : '';
      if ($value === '') {
        $value = $fallback;
      }

      if (preg_match('~^https?://~i', $value)) {
        $path = (string) parse_url($value, PHP_URL_PATH);
        $query = (string) parse_url($value, PHP_URL_QUERY);
        if ($path !== '' && (str_starts_with($path, '/assets/') || str_starts_with($path, '/storage/'))) {
          $local = route('img', ['path' => ltrim($path, '/'), 'w' => 1920, 'q' => 65]);
          return $query !== '' ? ($local . '&' . $query) : $local;
        }
        return $value;
      }

      return route('img', ['path' => ltrim($value, '/'), 'w' => 1920, 'q' => 65]);
    };

    $footerBgUrl = $toBgUrl(data_get($ws, 'footer.bg_image'), 'assets/img/footer/main_footer_bg.jpg');
    $footerLogoUrl = $toAssetUrl(data_get($ws, 'footer.logo'), 'assets/img/logo_wh.svg');
  @endphp

  <section class="footer__area-common theme-bg-heading-primary overflow-hidden">
    <div class="footer__bg" data-background="{{ $footerBgUrl }}"></div>
    <div class="footer__main-wrapper footer__bottom-border">
      <div class="container">
        <div class="row mb-minus-50">
          <div class="col-lg-4 col-6">
            <div class="footer__widget footer__widget-item-1">
              <div class="footer__logo mb-35 mb-xs-30">
                <a href="{{ route('home') }}">
                  <img class="img-fluid" src="{{ $footerLogoUrl }}" width="200px" height="200px"
                    alt="{{ __('website.footer.logo_alt') }}">
                </a>
              </div>

              <div class="footer__content mb-30 mb-xs-35">
                <p class="mb-0">{{ __('website.footer.about') }}</p>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="footer__widget footer__widget-item-2">
              <div class="footer__widget-title">
                <h4>{{ __('website.footer.sections.pages') }}</h4>
              </div>
              <div class="footer__link">
                <ul>
                  <li><a href="{{ route('aboutus') }}">{{ __('website.nav.menu.about_us') }}</a></li>
                  <li><a href="{{ route('products') }}">{{ __('website.nav.menu.products') }}</a></li>
                  <li><a href="{{ route('technology') }}">{{ __('website.nav.menu.technology') }}</a></li>
                  <li><a href="{{ route('career') }}">{{ __('website.nav.menu.careers') }}</a></li>
                  <li><a href="{{ route('contact') }}">{{ __('website.nav.menu.contact') }}</a></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="footer__widget footer__widget-item-3">
              <div class="footer__widget-title">
                <h4>{{ __('website.footer.sections.products') }}</h4>
              </div>

              <div class="footer__link">
                <ul>
                  <li><a href="{{ route('products.colorants') }}">{{ __('website.nav.menu.products_items.chemical_colorants') }}</a></li>
                  <li><a href="{{ route('products.surface-coating-agents') }}">{{ __('website.nav.menu.products_items.surface_coating_agents') }}</a></li>
                  <li><a href="{{ route('products.additive-coating') }}">{{ __('website.nav.menu.products_items.additive_coating') }}</a></li>
                  <li><a href="{{ route('products.pu-resin') }}">{{ __('website.nav.menu.products_items.pu_resin') }}</a></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-6">
            <div class="footer__widget footer__widget-item-4">
              <div class="footer__widget-title">
                <h4>{{ __('website.footer.sections.contact_us') }}</h4>
              </div>

              <div class="footer__link footer__link-contact">
                <ul>
                  <li>
                    <span class="icon">
                      <img src="{{ asset('assets/img/style2/icon/call.svg') }}" alt="">
                    </span>
                    <span class="text">
                      <span>{{ __('website.footer.contact.call_support') }}</span>
                      <a href="tel:{{ $phoneTel }}">{{ $phoneDisplay }}</a>
                    </span>
                  </li>
                  <li>
                    <span class="icon">
                      <img src="{{ asset('assets/img/style2/icon/mail.svg') }}" alt="">
                    </span>
                    <span class="text">
                      <span>{{ __('website.footer.contact.email_query') }}</span>
                      <a href="mailto:{{ $email }}">{{ $email }}</a>
                    </span>
                  </li>
                  <li class="address">
                    <span class="icon">
                      <img src="{{ asset('assets/img/style2/icon/map.svg') }}" alt="">
                    </span>
                    <span class="text">
                      <a target="_blank" href="{{ $mapUrl }}">{!! nl2br(e($addressText)) !!}</a>
                    </span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="footer__bottom">
      <div class="container">
        <div class="row">
          <div class="col-lg-6">
            <div class="footer__copyright text-lg-start text-center">
              <p class="mb-0">{{ __('website.footer.copyright_prefix') }}<span class="current-year">{{ now()->year }}</span>
                {{ __('website.footer.company_name') }}. {{ __('website.footer.rights_reserved') }}</p>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="footer__copyright--who-create text-lg-end text-center">
              <p><a href="{{ route('privacy-policy') }}">{{ __('website.footer.privacy_policy') }}</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</footer>
<!-- Footer area end -->