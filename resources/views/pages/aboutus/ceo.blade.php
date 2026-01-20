@extends('about')
@section('title', 'Ilsam - CEO\'s Message')
@section('breadcrumb_title', "CEO's Message")
@section('aboutus')
  <div class="row g-40 align-items-start">
    <div class="col-12 col-lg-7 order-2 order-lg-1">
      <h2 class="heading text-50 mb-4 wow fadeInUp" data-wow-delay=".1s">CEO's Message</h2>

      <p class="text text-18 mb-3 wow fadeInUp" data-wow-delay=".15s"><strong>Greetings,</strong></p>
      <p class="text text-18 mb-3 wow fadeInUp" data-wow-delay=".2s">Thank you for visiting ILSAM.</p>

      <p class="text text-18 mb-3 wow fadeInUp" data-wow-delay=".25s">
        <span class="ceo-highlight-date">September 1, 1972</span> marks the day I began this business on my own.
      </p>

      <p class="text text-18 mb-3 wow fadeInUp" data-wow-delay=".3s">
        From the very beginning, I built this company on integrity and sincerity, dedicating myself wholeheartedly to
        every step of the journey. Through these efforts, we have grown into a company with over 400 employees.
      </p>

      <p class="text text-18 mb-3 wow fadeInUp" data-wow-delay=".35s">
        As our team grew, our sales steadily increased, and the company reached the scale it is today.
      </p>

      <p class="text text-18 mb-3 wow fadeInUp" data-wow-delay=".4s">
        Just as sharing joy multiplies happiness, good things have continued to come our way. And just as sharing sorrow
        eases the burden, difficult moments have become more bearable.
      </p>

      <p class="text text-18 mb-3 wow fadeInUp" data-wow-delay=".45s">
        Living each day with gratitude has been possible thanks to the continued trust and support of our valued
        customers.
      </p>

      <p class="text text-18 mb-4 wow fadeInUp" data-wow-delay=".5s">
        I promise to return your support with <span class="ceo-highlight">perfect products, excellent service, and
          unwavering trust</span>.
      </p>

      <p class="text text-18 mb-3 wow fadeInUp" data-wow-delay=".55s"><strong>To our beloved customers,</strong></p>
      <p class="text text-18 mb-3 wow fadeInUp" data-wow-delay=".6s">May your day be filled with happiness and joy.</p>
      <p class="text text-18 mb-4 wow fadeInUp" data-wow-delay=".65s">Thank you.</p>

      <div class="mt-5 wow fadeInUp" data-wow-delay=".7s">
        <p class="fw-bold mb-0">Chairman &nbsp; Chung Woochul</p>
      </div>
    </div>
    <div class="col-12 col-lg-5 order-1 order-lg-2">
      <div class="ceo-media wow fadeInUp" data-wow-delay=".1s">
        <div class="ceo-media__watermark" aria-hidden="true">
          <span>CEO</span>
          <span>GREETING</span>
        </div>

        <div class="service-details__content-media">
          <div class="ceo-portrait">
            <img src="{{ asset('assets/img/aboutus/ceo.jpg') }}" width="300" height="400" loading="lazy" decoding="async"
              alt="CEO Chung Woochul">
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection