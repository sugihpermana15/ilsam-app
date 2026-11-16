{{-- Ilustrasi banner dashboard: murni dekoratif, dianimasikan lewat CSS (admin-pro.css). --}}
<svg class="dash-hero__art" viewBox="0 0 440 320" aria-hidden="true" focusable="false">
  <defs>
    <linearGradient id="dashHeroArea" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" class="art-area-stop art-area-stop--top" />
      <stop offset="1" class="art-area-stop art-area-stop--bottom" />
    </linearGradient>
    <radialGradient id="dashHeroGlow" cx="50%" cy="50%" r="50%">
      <stop offset="0" class="art-glow-stop art-glow-stop--in" />
      <stop offset="1" class="art-glow-stop art-glow-stop--out" />
    </radialGradient>
  </defs>

  {{-- Latar: cahaya lembut, orbit berputar, partikel --}}
  <circle cx="225" cy="165" r="150" fill="url(#dashHeroGlow)" />
  <g class="art-orbit">
    <circle cx="225" cy="165" r="138" class="art-orbit__ring" />
    <circle cx="225" cy="27" r="5" class="art-orbit__dot" />
    <circle cx="87" cy="165" r="3.5" class="art-orbit__dot art-orbit__dot--soft" />
  </g>
  <circle cx="58" cy="78" r="4" class="art-spark art-spark--1" />
  <circle cx="392" cy="232" r="5" class="art-spark art-spark--2" />
  <circle cx="352" cy="42" r="3" class="art-spark art-spark--3" />

  {{-- Panel utama: grafik tren --}}
  <g class="art-float art-float--main">
    <rect x="88" y="70" width="264" height="182" rx="18" class="art-surface" />
    <circle cx="110" cy="92" r="4" class="art-dot" />
    <circle cx="124" cy="92" r="4" class="art-dot art-dot--2" />
    <circle cx="138" cy="92" r="4" class="art-dot art-dot--3" />
    <rect x="270" y="86" width="62" height="12" rx="6" class="art-pill" />

    <rect x="110" y="116" width="74" height="8" rx="4" class="art-line art-line--strong" />
    <rect x="110" y="131" width="46" height="6" rx="3" class="art-line" />

    <path d="M110 162H330M110 190H330M110 218H330" class="art-grid" />
    <path d="M110 214C132 210 142 188 164 190S196 206 218 186S250 150 272 158S308 146 330 132V232H110Z"
      fill="url(#dashHeroArea)" class="art-area" />
    <path d="M110 214C132 210 142 188 164 190S196 206 218 186S250 150 272 158S308 146 330 132"
      pathLength="100" class="art-trend" />
    <circle cx="330" cy="132" r="10" class="art-pulse" />
    <circle cx="330" cy="132" r="5" class="art-point" />
  </g>

  {{-- Kartu melayang: progres --}}
  <g class="art-float art-float--a">
    <rect x="286" y="34" width="134" height="64" rx="16" class="art-surface" />
    <circle cx="318" cy="66" r="17" class="art-ring" />
    <circle cx="318" cy="66" r="17" pathLength="100" transform="rotate(-90 318 66)" class="art-ring art-ring--fill" />
    <rect x="346" y="54" width="54" height="8" rx="4" class="art-line art-line--strong" />
    <rect x="346" y="70" width="36" height="6" rx="3" class="art-line" />
  </g>

  {{-- Kartu melayang: tugas selesai --}}
  <g class="art-float art-float--b">
    <rect x="30" y="196" width="132" height="60" rx="16" class="art-surface" />
    <circle cx="60" cy="226" r="15" class="art-check" />
    <path d="M53 226l5 5 9-10" pathLength="100" class="art-tick" />
    <rect x="86" y="214" width="56" height="8" rx="4" class="art-line art-line--strong" />
    <rect x="86" y="230" width="38" height="6" rx="3" class="art-line" />
  </g>

  {{-- Kartu melayang: batang --}}
  <g class="art-float art-float--c">
    <rect x="296" y="222" width="104" height="76" rx="16" class="art-surface" />
    <rect x="314" y="262" width="12" height="22" rx="4" class="art-bar art-bar--1" />
    <rect x="334" y="248" width="12" height="36" rx="4" class="art-bar art-bar--2" />
    <rect x="354" y="238" width="12" height="46" rx="4" class="art-bar art-bar--3" />
    <rect x="374" y="254" width="12" height="30" rx="4" class="art-bar art-bar--4" />
  </g>
</svg>
