<footer>
  <section class="footer__area-common theme-bg-heading-primary overflow-hidden">
    <div class="footer__bg" data-background="{{ asset('assets/img/footer/main_footer_bg.jpg') }}"></div>
    <div class="footer__main-wrapper footer__bottom-border">
      <div class="container">
        <div class="row mb-minus-50">
          <div class="col-lg-4 col-6">
            <div class="footer__widget footer__widget-item-1">
              <div class="footer__logo mb-35 mb-xs-30">
                <a href="{{ route('home') }}">
                  <img class="img-fluid" src="{{ asset('assets/img/logo_wh.svg') }}" width="200px" height="200px"
                    alt="logo ilsam">
                </a>
              </div>

              <div class="footer__content mb-30 mb-xs-35">
                <p class="mb-0">Our company is an entity established in Indonesia on August 11, 1999. Headquartered in
                  South Korea with branches spread across various countries, we have proven our continued success
                  through hard work and commitment.</p>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="footer__widget footer__widget-item-2">
              <div class="footer__widget-title">
                <h4>Pages</h4>
              </div>
              <div class="footer__link">
                <ul>
                  <li><a href="{{ route('aboutus') }}">About Us</a></li>
                  <li><a href="{{ route('products') }}">Products</a></li>
                  <li><a href="{{ route('technology') }}">Technology (R&D)</a></li>
                  <li><a href="{{ route('career') }}">Careers</a></li>
                  <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="footer__widget footer__widget-item-3">
              <div class="footer__widget-title">
                <h4>Products</h4>
              </div>

              <div class="footer__link">
                <ul>
                  <li><a href="{{ route('products.colorants') }}">Colorants</a></li>
                  <li><a href="{{ route('products.surface-coating-agents') }}">Surface Coating Agents</a></li>
                  <li><a href="{{ route('products.additive-coating') }}">Additive Coating</a></li>
                  <li><a href="{{ route('products.pu-resin') }}">PU Resin</a></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-6">
            <div class="footer__widget footer__widget-item-4">
              <div class="footer__widget-title">
                <h4>Contact Us</h4>
              </div>

              <div class="footer__link footer__link-contact">
                <ul>
                  <li>
                    <span class="icon">
                      <img src="{{ asset('assets/img/style2/icon/call.svg') }}" alt="">
                    </span>
                    <span class="text">
                      <span>Call us for support</span>
                      <a href="tel:02189830313">+62 (021) 89830313 / 0314</a>
                    </span>
                  </li>
                  <li>
                    <span class="icon">
                      <img src="{{ asset('assets/img/style2/icon/mail.svg') }}" alt="">
                    </span>
                    <span class="text">
                      <span>Email us for query</span>
                      <a href="mailto:market.ilsamindonesia@yahoo.com">market.ilsamindonesia@yahoo.com</a>
                    </span>
                  </li>
                  <li class="address">
                    <span class="icon">
                      <img src="{{ asset('assets/img/style2/icon/map.svg') }}" alt="">
                    </span>
                    <span class="text">
                      <a target="_blank" href="https://maps.app.goo.gl/reUj3juAoQ8NrGLE6">Jl. Trans Heksa Artha
                        Industrial Hill Area Block E No.13 Wanajaya Village,<br> District Telukjambe Barat, Karawang
                        Regency, West Java, 41361</a>
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
              <p class="mb-0">Copyright ©<span class="current-year">{{ now()->year }}</span> ILSAM INDONESIA.
                All rights reserved.</p>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="footer__copyright--who-create text-lg-end text-center">
              <p><a href="{{ route('privacy-policy') }}">Privacy Policy</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</footer>
<!-- Footer area end -->