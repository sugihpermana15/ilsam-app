@extends('about')
@section('title', 'Ilsam - Business Philosophy')
@section('breadcrumb_title', 'Business Philosophy')
@section('aboutus')

    <!-- Working Process area start -->
    <section class="working-process section-space overflow-hidden">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section__title-wrapper text-center mb-60 mb-sm-40 mb-xs-35">
                        <h2 class="section__title title-animation text-capitalize rr-br-hidden-md" data-cursor="-opaque">Company Motto</h2>
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
                        <h4 class="title mb-10">GOOD HEALTH</h4>
                        <p class="des mb-0">
                            Workers obtain the highest degree of health (physical, spiritual, and social) by preventing and treating
                            diseases or health problems caused by work and the environment.
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
                        <h4 class="title mb-10">LOYALTY</h4>
                        <p class="des mb-0">
                            High responsibility of employees towards their company as a form of appreciation given by the company to
                            its employees.
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
                        <h4 class="title mb-10">JUSTICE</h4>
                        <p class="des mb-0">Employee perceptions about fairness at work.</p>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="working-process__item text-center mb-30">
                        <div class="working-process__item-icon mb-40">
                            <div class="working-process__item-icon-img" aria-hidden="true">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                        </div>
                        <h4 class="title mb-10">MORALITY</h4>
                        <p class="des mb-0">
                            The attitude of individuals in a group towards their work environment, and working together voluntarily
                            to mobilize their abilities to achieve organizational goals.
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
    aria-label="Business Philosophy">
    <div class="about-hero__inner">
      <h2 class="about-hero__title">Business Philosophy</h2>
      <p class="about-hero__lead mb-0">
        <span class="about-hero__quote">Good to the divine eye, perfectly meeting customer's needs, helpful for a happy family life</span>.
      </p>
    </div>
    </section>

        <!-- Company Slogan area start -->
        <section class="company-slogan section-space">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="section__title-wrapper text-center mb-60 mb-sm-40 mb-xs-35">
                            <h2 class="section__title title-animation text-capitalize rr-br-hidden-md" data-cursor="-opaque">Company Slogans</h2>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-12 col-lg-8">
                        <ul class="company-slogan__list company-slogan__list--center mb-0 wow fadeInUp" data-wow-delay=".15s">
                            <li class="company-slogan__item">Say “I know” when sure, “I don't know” when unsure.</li>
                            <li class="company-slogan__item">Always think and innovate with a smile.</li>
                            <li class="company-slogan__item">We act after looking closely, listening carefully and understanding clearly.</li>
                            <li class="company-slogan__item">Be good, smile and enjoy life.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!-- Company Slogan area end -->

@endsection