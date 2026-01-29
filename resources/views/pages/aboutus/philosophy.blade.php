@extends('about')
@section('title', __('website.titles.business_philosophy'))
@section('breadcrumb_title', __('website.about.business_philosophy.title'))
@section('aboutus')

    <!-- Working Process area start -->
    <section class="working-process section-space overflow-hidden">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section__title-wrapper text-center mb-60 mb-sm-40 mb-xs-35">
                        <h2 class="section__title title-animation text-capitalize rr-br-hidden-md" data-cursor="-opaque">{{ __('website.about.business_philosophy.motto_title') }}</h2>
                    </div>
                </div>
            </div>

            <div class="row mb-minus-30 rr-shape-p-c_1">
                <div class="working-process__shape-1 rr-shape-p-s_1 leftRight">
                    <img src="{{ asset('assets/img/style2/working-process/shape.png') }}" alt="">
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="working-process__item text-center mb-30 mt-30">
                        <div class="working-process__item-icon mb-40">
                            <div class="working-process__item-icon-img" aria-hidden="true">
                                <i class="bi bi-activity"></i>
                            </div>
                        </div>
                        <h4 class="title mb-10">{{ __('website.about.business_philosophy.motto.good_health.title') }}</h4>
                        <p class="des mb-0">
                            {{ __('website.about.business_philosophy.motto.good_health.desc') }}
                        </p>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="working-process__item text-center mb-30">
                        <div class="working-process__item-icon mb-40">
                            <div class="working-process__item-icon-img" aria-hidden="true">
                                <i class="bi bi-hand-thumbs-up-fill"></i>
                            </div>
                        </div>
                        <h4 class="title mb-10">{{ __('website.about.business_philosophy.motto.loyalty.title') }}</h4>
                        <p class="des mb-0">
                            {{ __('website.about.business_philosophy.motto.loyalty.desc') }}
                        </p>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="working-process__item text-center mb-30 mt-30">
                        <div class="working-process__item-icon mb-40">
                            <div class="working-process__item-icon-img" aria-hidden="true">
                                <i class="bi bi-bank2"></i>
                            </div>
                        </div>
                        <h4 class="title mb-10">{{ __('website.about.business_philosophy.motto.justice.title') }}</h4>
                        <p class="des mb-0">{{ __('website.about.business_philosophy.motto.justice.desc') }}</p>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="working-process__item text-center mb-30">
                        <div class="working-process__item-icon mb-40">
                            <div class="working-process__item-icon-img" aria-hidden="true">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                        </div>
                        <h4 class="title mb-10">{{ __('website.about.business_philosophy.motto.morality.title') }}</h4>
                        <p class="des mb-0">
                            {{ __('website.about.business_philosophy.motto.morality.desc') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- working-process area end -->

    <section
    class="about-hero about-hero--bleed wow fadeInUp"
    data-wow-delay=".1s"
    style="--about-hero-bg: url('{{ asset('assets/img/aboutus/img12.jpg') }}');"
        aria-label="{{ __('website.about.business_philosophy.aria') }}">
    <div class="about-hero__inner">
            <h2 class="about-hero__title">{{ __('website.about.business_philosophy.title') }}</h2>
      <p class="about-hero__lead mb-0">
                <span class="about-hero__quote">{{ __('website.about.business_philosophy.quote') }}</span>.
      </p>
    </div>
    </section>

        <!-- Company Slogan area start -->
        <section class="company-slogan section-space">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="section__title-wrapper text-center mb-60 mb-sm-40 mb-xs-35">
                            <h2 class="section__title title-animation text-capitalize rr-br-hidden-md" data-cursor="-opaque">{{ __('website.about.business_philosophy.slogans_title') }}</h2>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-12 col-lg-8">
                        <ul class="company-slogan__list company-slogan__list--center mb-0 wow fadeInUp" data-wow-delay=".15s">
                            <li class="company-slogan__item">{{ __('website.about.business_philosophy.slogans.1') }}</li>
                            <li class="company-slogan__item">{{ __('website.about.business_philosophy.slogans.2') }}</li>
                            <li class="company-slogan__item">{{ __('website.about.business_philosophy.slogans.3') }}</li>
                            <li class="company-slogan__item">{{ __('website.about.business_philosophy.slogans.4') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!-- Company Slogan area end -->

@endsection