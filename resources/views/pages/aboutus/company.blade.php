@extends('about')
@section('title', 'Ilsam - Company Overview')
@section('breadcrumb_title', 'Company Overview')
@section('aboutus')
  <div class="service-details__content-media company-overview__media wow fadeInUp" data-wow-delay=".1s">
    <img
      src="{{ asset('assets/img/img8.jpeg') }}"
      loading="lazy"
      decoding="async"
      alt="ILSAM factory location"
    >
  </div>

  <h2 class="heading text-50 wow fadeInUp" data-wow-delay=".2s">Company Overview</h2>

  <p class="text text-18 wow fadeInUp" data-wow-delay=".25s">
    Our company was established in Indonesia on August 11, 1999. Headquartered in South Korea, with branches across
    multiple countries, we continue to grow through hard work and commitment.
  </p>

  <p class="text text-18 wow fadeInUp" data-wow-delay=".3s">
    Guided by our philosophy of integrity and honesty, we relocated our operations to the Jababeka Industrial Estate on
    December 28, 2001. The site was designated as a bonded zone on September 21, 2005.
  </p>

  <p class="text text-18 wow fadeInUp" data-wow-delay=".35s">
    To meet customer needs and expand market reach, we inaugurated a new factory in the Artha Industrial Hill Karawang
    area, officially registered on January 23, 2024. The facility is designed as a modern bonded-zone factory with an
    annual capacity of thousands of tons.
  </p>

  <p class="text text-18 mb-0 wow fadeInUp" data-wow-delay=".4s">
    This expansion strengthens our position and marks a new chapter in delivering high-quality products sustainably.
  </p>

  <div class="mt-4 wow fadeInUp" data-wow-delay=".45s">
    <div class="about-timeline" role="list" aria-label="Company history milestones">
      <div class="about-timeline__item is-active" role="listitem">
        <div class="about-timeline__node" aria-hidden="true">
          <span class="about-timeline__number">01</span>
        </div>
        <div class="about-timeline__body">
          <div class="about-timeline__meta">1999</div>
          <h3 class="about-timeline__title">Established in Indonesia</h3>
          <p class="about-timeline__text mb-0">Company founded and began operations.</p>
        </div>
      </div>

      <div class="about-timeline__item" role="listitem">
        <div class="about-timeline__node" aria-hidden="true">
          <span class="about-timeline__number">02</span>
        </div>
        <div class="about-timeline__body">
          <div class="about-timeline__meta">2001</div>
          <h3 class="about-timeline__title">Relocated to Jababeka</h3>
          <p class="about-timeline__text mb-0">Operational facilities moved to the industrial estate.</p>
        </div>
      </div>

      <div class="about-timeline__item" role="listitem">
        <div class="about-timeline__node" aria-hidden="true">
          <span class="about-timeline__number">03</span>
        </div>
        <div class="about-timeline__body">
          <div class="about-timeline__meta">2005</div>
          <h3 class="about-timeline__title">Bonded Zone Status</h3>
          <p class="about-timeline__text mb-0">Officially designated as a bonded zone.</p>
        </div>
      </div>

      <div class="about-timeline__item" role="listitem">
        <div class="about-timeline__node" aria-hidden="true">
          <span class="about-timeline__number">04</span>
        </div>
        <div class="about-timeline__body">
          <div class="about-timeline__meta">2024</div>
          <h3 class="about-timeline__title">New Karawang Factory</h3>
          <p class="about-timeline__text mb-0">Modern facility inaugurated to expand capacity.</p>
        </div>
      </div>
    </div>
  </div>
@endsection