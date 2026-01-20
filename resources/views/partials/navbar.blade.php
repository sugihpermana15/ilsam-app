<!-- Offcanvas area start -->
<div class="fix">
  <div class="offcanvas__area">
    <div class="offcanvas__wrapper">
      <div class="offcanvas__content">
        <div class="offcanvas__top d-flex justify-content-between align-items-center">
          <div class="offcanvas__logo">
            <a href="{{ route('home') }}">
              <img src="{{ asset('assets/img/logo_wh.svg') }}" alt="logo ilsam">
            </a>
          </div>
          <div class="offcanvas__close">
            <button class="offcanvas-close-icon animation--flip">
              <span class="offcanvas-m-lines">
                <span class="offcanvas-m-line line--1"></span><span class="offcanvas-m-line line--2"></span><span
                  class="offcanvas-m-line line--3"></span>
              </span>
            </button>
          </div>
        </div>
        <div class="mobile-menu fix"></div>
        <div class="offcanvas__social">
          <h4 class="offcanvas__title mb-20">Subscribe & Follow</h4>
          <p class="mb-30">PT ILSAM GLOBAL INDONESIA provides reliable chemical solutions for industrial partners, with
            a focus on quality consistency and long-term supply reliability.</p>
          <ul class="header-top-socail-menu d-flex">
            <li><a href="https://www.facebook.com/"><i class="fab fa-facebook-f"></i></a></li>
            <li><a href="https://www.linkedin.com/"><i class="fa-brands fa-linkedin-in"></i></a></li>
            <li><a href="https://www.pinterest.com/"><i class="fa-brands fa-pinterest-p"></i></a></li>
            <li><a href="https://vimeo.com/"><i class="fa-brands fa-vimeo-v"></i></a></li>
          </ul>
        </div>
        <div class="offcanvas__btn">
          <div class="header__btn-wrap">
            <a href="{{ route('contact') }}" class="rr-btn__header d-sm-none mb-10 w-100">
              <span class="btn-wrap">
                <span class="text-one">Get Started</span>
                <span class="text-two">Get Started</span>
              </span>
            </a>
            <a href="{{ route('contact') }}" class="rr-btn__header w-100">
              <span class="btn-wrap">
                <span class="text-one">Contact Sales</span>
                <span class="text-two">Contact Sales</span>
              </span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="offcanvas__overlay"></div>
<div class="offcanvas__overlay-white"></div>
<!-- Offcanvas area start -->

<!-- Header area start -->
<header>
  <div id="header-sticky" class="header__area header-3">
    <div class="header__top d-none d-xl-block">
      <div class="container">
        <div class="row g-24">
          <div class="col-xl-5">
            <ul class="header__top-menu d-flex ">
              <li>
                <a href="https://maps.app.goo.gl/reUj3juAoQ8NrGLE6">
                  <span>
                    <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path
                        d="M14.7143 7.54545C14.7143 12.6364 7.85714 17 7.85714 17C7.85714 17 1 12.6364 1 7.54545C1 5.80949 1.72245 4.14463 3.00841 2.91712C4.29437 1.68961 6.03852 1 7.85714 1C9.67577 1 11.4199 1.68961 12.7059 2.91712C13.9918 4.14463 14.7143 5.80949 14.7143 7.54545Z"
                        stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                      <path
                        d="M7.857 9.72741C9.11937 9.72741 10.1427 8.75057 10.1427 7.54559C10.1427 6.3406 9.11937 5.36377 7.857 5.36377C6.59464 5.36377 5.57129 6.3406 5.57129 7.54559C5.57129 8.75057 6.59464 9.72741 7.857 9.72741Z"
                        stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                  </span>
                  Jl. Trans Heksa Artha Industrial Hill Area Block E No.13.
                </a>
              </li>
            </ul>
          </div>
          <div class="col-xl-7">
            <div class="header__top-wrapper d-flex justify-content-end">

              <div class="header__top-email">
                <a href="mailto:market.ilsamindonesia@yahoo.com">
                  <svg width="19" height="16" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M2.7 1H16.3C17.235 1 18 1.7875 18 2.75V13.25C18 14.2125 17.235 15 16.3 15H2.7C1.765 15 1 14.2125 1 13.25V2.75C1 1.7875 1.765 1 2.7 1Z"
                      stroke="#007aff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M18 2.75L9.5 8.875L1 2.75" stroke="#007aff" stroke-width="1.5" stroke-linecap="round"
                      stroke-linejoin="round" />
                  </svg>
                  market.ilsamindonesia@yahoo.com
                </a>
              </div>
              <ul class="header__top-socail d-flex ">
                <li class="title">Visit Us Ilsam Center :</li>
                <li><a href="https://www.ilsam.com/">www.ilsam.com</a></li>
                {{-- <li><a href="https://twitter.com/"><i class="fab fa-twitter"></i></a></li>
                <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                <li><a href="#"><i class="fa-brands fa-linkedin"></i></a></li> --}}
              </ul>
            </div>
          </div>

        </div>
      </div>
    </div>
    <div class="container">
      <div class="mega__menu-wrapper p-relative">
        <div class="header__main">
          <div class="header__logo">
            <a href="{{ route('home') }}">
              <div class="logo">
                <img class="img-fluid" src="{{ asset('assets/img/logo.png') }}" alt="logo ilsam">
              </div>
            </a>
          </div>

          <div class="mean__menu-wrapper d-none d-lg-block">
            <div class="main-menu main-menu-3">
              <nav id="mobile-menu">
                <ul>
                  <li class="has-dropdown ">
                    <a href="javascript:void(0)">About Us</a>
                    <ul class="submenu">
                      <li><a href="{{ route('aboutus') }}">Company Overview</a></li>
                      <li><a href="{{ route('ceo') }}">CEO's Message</a></li>
                      <li><a href="{{ route('philosophy') }}">Management Philosophy</a></li>
                    </ul>
                  </li>
                  <li class="has-dropdown">
                    <a href="{{ route('products') }}">Products</a>
                    <ul class="submenu">
                      <li><a href="{{ route('products.colorants') }}">Chemical Colorants</a></li>
                      <li><a href="{{ route('products.surface-coating-agents') }}">Surface Coating Agents</a></li>
                      <li><a href="{{ route('products.additive-coating') }}">Additive Coating</a></li>
                      <li><a href="{{ route('products.pu-resin') }}">PU Resin</a></li>
                    </ul>
                  </li>
                  <li class="has-dropdown">
                    <a href="{{ route('technology') }}">Technology (R&D)</a>
                    <ul class="submenu">
                      <li><a href="{{ route('technology') }}">Technology (R&D)</a></li>
                      <li><a href="{{ route('technology.certification-status') }}">Certification Status</a></li>
                    </ul>
                  </li>
                  <li><a href="{{ route('career') }}">Careers</a></li>
                  <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
              </nav>
            </div>
          </div>

          <div class="header__right">
            <div class="header__action d-flex align-items-center">
              <div class="header__btn-wrap align-items-center d-inline-flex">
                <div class="rr-header-contact-btn d-flex align-items-center d-none d-md-flex">
                  <a href="https://drive.google.com/uc?export=download&id=1G4sEtK56mxtXtg71gsx7zyvDG2CSVqVX"
                    class="rr-btn__header" target="_blank" rel="noopener" download>
                    <span class="btn-wrap">
                      <span class="text-one">Company Profile</span>
                      <span class="text-two">Download</span>
                    </span>
                  </a>
                </div>
              </div>

              <div class="header__hamburger ml-30 d-xl-none">
                <div class="sidebar__toggle">
                  <a class="bar-icon" href="javascript:void(0)">
                    <span></span>
                    <span></span>
                    <span></span>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>
<!-- Header area end -->