@php
  $variant = $variant ?? 'default'; // default | compact | offcanvas
  $current = app()->getLocale();

  $label = match ($current) {
    'id' => 'ID',
    'ko' => 'KO',
    default => 'EN',
  };

  $name = match ($current) {
    'id' => __('website.nav.languages.id'),
    'ko' => __('website.nav.languages.ko'),
    default => __('website.nav.languages.en'),
  };

  $variants = [
    'default' => [
      'root' => '',
      'button' => '',
      'menu' => '',
    ],
    'compact' => [
      'root' => ' lang-dropdown--compact',
      'button' => '',
      'menu' => '',
    ],
    'offcanvas' => [
      'root' => ' lang-dropdown--offcanvas',
      'button' => ' w-100',
      'menu' => ' w-100',
    ],
  ];

  $v = $variants[$variant] ?? $variants['default'];
@endphp

<div class="lang-dropdown js-lang-dropdown{{ $v['root'] }}" data-open="false">
  <button
    type="button"
    class="lang-dropdown__button js-lang-dropdown-button{{ $v['button'] }}"
    aria-label="{{ __('website.nav.language') }}"
    aria-haspopup="menu"
    aria-expanded="false"
  >
    <span class="lang-dropdown__icon" aria-hidden="true">
      <i class="fas fa-language"></i>
    </span>

    <span class="lang-dropdown__flag" aria-hidden="true">
      @if($current === 'id')
        <svg viewBox="0 0 36 24" width="18" height="12" focusable="false" aria-hidden="true">
          <rect width="36" height="12" fill="#E70011"/>
          <rect y="12" width="36" height="12" fill="#FFFFFF"/>
        </svg>
      @elseif($current === 'ko')
        <svg viewBox="0 0 36 24" width="18" height="12" focusable="false" aria-hidden="true">
          <rect width="36" height="24" fill="#FFFFFF"/>
          <circle cx="18" cy="12" r="6" fill="#CD2E3A"/>
          <path d="M18 6a6 6 0 0 0 0 12a3 3 0 0 1 0-6a3 3 0 0 0 0-6z" fill="#0047A0"/>
        </svg>
      @else
        <svg viewBox="0 0 36 24" width="18" height="12" focusable="false" aria-hidden="true">
          <rect width="36" height="24" fill="#B22234"/>
          <g fill="#FFFFFF">
            <rect y="2" width="36" height="2"/>
            <rect y="6" width="36" height="2"/>
            <rect y="10" width="36" height="2"/>
            <rect y="14" width="36" height="2"/>
            <rect y="18" width="36" height="2"/>
            <rect y="22" width="36" height="2"/>
          </g>
          <rect width="16" height="12" fill="#3C3B6E"/>
        </svg>
      @endif
    </span>

    <span class="lang-dropdown__label" aria-hidden="true">{{ $label }}</span>
    <span class="lang-dropdown__chevron" aria-hidden="true"><i class="fas fa-chevron-down"></i></span>

    <span class="visually-hidden">{{ $name }}</span>
  </button>

  <div class="lang-dropdown__menu js-lang-dropdown-menu{{ $v['menu'] }}" role="menu" hidden>
    @php
      $items = [
        'en' => ['label' => 'EN', 'name' => __('website.nav.languages.en')],
        'id' => ['label' => 'ID', 'name' => __('website.nav.languages.id')],
        'ko' => ['label' => 'KO', 'name' => __('website.nav.languages.ko')],
      ];
    @endphp

    @foreach($items as $code => $it)
      <form action="{{ route('language.update') }}" method="POST" class="lang-dropdown__form">
        @csrf
        <input type="hidden" name="locale" value="{{ $code }}">
        <button
          type="submit"
          class="lang-dropdown__item js-lang-dropdown-item"
          role="menuitemradio"
          aria-checked="{{ $current === $code ? 'true' : 'false' }}"
          tabindex="{{ $current === $code ? '0' : '-1' }}"
        >
          <span class="lang-dropdown__item-flag" aria-hidden="true">
            @if($code === 'id')
              <svg viewBox="0 0 36 24" width="18" height="12" focusable="false" aria-hidden="true">
                <rect width="36" height="12" fill="#E70011"/>
                <rect y="12" width="36" height="12" fill="#FFFFFF"/>
              </svg>
            @elseif($code === 'ko')
              <svg viewBox="0 0 36 24" width="18" height="12" focusable="false" aria-hidden="true">
                <rect width="36" height="24" fill="#FFFFFF"/>
                <circle cx="18" cy="12" r="6" fill="#CD2E3A"/>
                <path d="M18 6a6 6 0 0 0 0 12a3 3 0 0 1 0-6a3 3 0 0 0 0-6z" fill="#0047A0"/>
              </svg>
            @else
              <svg viewBox="0 0 36 24" width="18" height="12" focusable="false" aria-hidden="true">
                <rect width="36" height="24" fill="#B22234"/>
                <g fill="#FFFFFF">
                  <rect y="2" width="36" height="2"/>
                  <rect y="6" width="36" height="2"/>
                  <rect y="10" width="36" height="2"/>
                  <rect y="14" width="36" height="2"/>
                  <rect y="18" width="36" height="2"/>
                  <rect y="22" width="36" height="2"/>
                </g>
                <rect width="16" height="12" fill="#3C3B6E"/>
              </svg>
            @endif
          </span>
          <span class="lang-dropdown__item-text">{{ $it['name'] }}</span>
          <span class="lang-dropdown__item-code" aria-hidden="true">{{ $it['label'] }}</span>
        </button>
      </form>
    @endforeach
  </div>
</div>
