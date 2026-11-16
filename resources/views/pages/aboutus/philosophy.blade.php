@extends('about')
@section('title', __('website.titles.business_philosophy'))
@section('breadcrumb_title', __('website.about.business_philosophy.title'))
@php
    $ws = \App\Support\WebsiteSettings::all();
    $locale = app()->getLocale();

    $metaDescription = data_get($ws, 'seo.about.philosophy.meta_description.' . $locale);
    if (!is_string($metaDescription) || trim($metaDescription) === '') {
        $metaDescription = data_get($ws, 'seo.about.philosophy.meta_description.en');
    }
    if (!is_string($metaDescription)) {
        $metaDescription = '';
    }

    $metaImageRaw = data_get($ws, 'seo.about.philosophy.meta_image', 'assets/img/img3.jpg');
    $metaImageRaw = is_string($metaImageRaw) && trim($metaImageRaw) !== '' ? trim($metaImageRaw) : 'assets/img/img3.jpg';
    $metaImage = preg_match('~^https?://~i', $metaImageRaw) ? $metaImageRaw : asset(ltrim($metaImageRaw, '/'));

    $heroRaw = data_get($ws, 'about.philosophy.hero_bg', 'assets/img/aboutus/img12.jpg');
    $heroRaw = is_string($heroRaw) && trim($heroRaw) !== '' ? trim($heroRaw) : 'assets/img/aboutus/img12.jpg';
    $heroUrl = preg_match('~^https?://~i', $heroRaw)
        ? $heroRaw
        : asset(ltrim($heroRaw, '/'));
@endphp

@section('meta_description', $metaDescription)
@section('meta_image', $metaImage)
@php
    $mottos = [
        ['key' => 'good_health', 'icon' => 'bi-heart-pulse'],
        ['key' => 'loyalty', 'icon' => 'bi-people'],
        ['key' => 'justice', 'icon' => 'bi-shield-check'],
        ['key' => 'morality', 'icon' => 'bi-gem'],
    ];
@endphp
@section('aboutus')
    <section class="sp-section">
        <div class="container">
            <div class="sp-section__head sp-section__head--center">
                <div>
                    <span class="sp-eyebrow">{{ __('website.pro.values_label') }}</span>
                    <h2 class="sp-title">{{ __('website.about.business_philosophy.motto_title') }}</h2>
                    <p class="sp-lead mb-0">{{ __('website.about.business_philosophy.motto_desc') }}</p>
                </div>
            </div>

            <div class="row g-4">
                @foreach ($mottos as $i => $motto)
                    <div class="col-xl-3 col-md-6">
                        <div class="sp-value wow fadeInUp" data-wow-delay="{{ 0.05 + $i * 0.08 }}s">
                            <span class="sp-value__num">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="sp-pillar__icon"><i class="bi {{ $motto['icon'] }}" aria-hidden="true"></i></span>
                            <h3 class="sp-value__title">{{ __('website.about.business_philosophy.motto.' . $motto['key'] . '.title') }}</h3>
                            <p class="sp-value__desc">{{ __('website.about.business_philosophy.motto.' . $motto['key'] . '.desc') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="sp-quote" style="--sp-quote-bg: url('{{ $heroUrl }}');"
        aria-label="{{ __('website.about.business_philosophy.aria') }}">
        <div class="container">
            <span class="sp-eyebrow sp-eyebrow--light">{{ __('website.pro.philosophy_label') }}</span>
            <p class="sp-quote__text">&ldquo;{{ __('website.about.business_philosophy.quote') }}&rdquo;</p>
        </div>
    </section>

    <section class="sp-section sp-section--alt">
        <div class="container">
            <div class="sp-section__head sp-section__head--center">
                <div>
                    <span class="sp-eyebrow">{{ __('website.pro.slogan_label') }}</span>
                    <h2 class="sp-title mb-0">{{ __('website.about.business_philosophy.slogans_title') }}</h2>
                </div>
            </div>

            <ol class="sp-slogans">
                @foreach (['1', '2', '3', '4'] as $i => $sloganKey)
                    <li class="sp-slogan wow fadeInUp" data-wow-delay="{{ 0.05 + $i * 0.08 }}s">
                        <span class="sp-slogan__num">{{ str_pad($sloganKey, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="sp-slogan__text">{{ __('website.about.business_philosophy.slogans.' . $sloganKey) }}</span>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>
@endsection
