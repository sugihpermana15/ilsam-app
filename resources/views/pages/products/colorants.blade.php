@extends('products')

@section('page_title', __('website.titles.product_colorants'))
@section('breadcrumb_title', __('website.nav.menu.products_items.chemical_colorants'))

@php
    $metaDescription =
        data_get($product ?? [], 'seoDescription') ?:
        data_get($product ?? [], 'tagline') ?:
        'Chemical Colorants by PT ILSAM GLOBAL INDONESIA: colorants for PU synthetic leather (SW, SU, SF), PVC synthetic leather (SV, SFV), printing (SP, SG), and water-based systems (SUW).';
    $metaImage =
        data_get($product ?? [], 'seoImage') ?: data_get($product ?? [], 'heroImage') ?: asset('assets/img/logo.png');
@endphp
@section('meta_description', $metaDescription)
@section('meta_image', str_starts_with($metaImage, 'http') ? $metaImage : asset($metaImage))

@php
    $catalogItems = collect($colorants ?? []);
    $catalogCategories = $catalogItems->pluck('category')->filter()->unique()->values();

    // Keluarga produk beserta kode lini. `filter` = nilai kategori di katalog.
    $families = [
        [
            'title' => __('website.catalog.colorants.title'), 'icon' => 'bi-palette', 'filter' => 'COLORANTS',
            'lines' => [
                [__('website.catalog.colorants.leather_pu'), ['SW', 'SU', 'SF']],
                [__('website.catalog.colorants.leather_pvc'), ['SV', 'SFV']],
                [__('website.catalog.colorants.printing'), ['SP', 'SG']],
                [__('website.catalog.colorants.water_based'), ['SUW']],
            ],
        ],
        [
            'title' => __('website.catalog.surface_coating.title'), 'icon' => 'bi-brush', 'filter' => 'SURFACE COATING AGENTS',
            'lines' => [
                [__('website.catalog.surface_coating.solution_type'), ['SUS']],
            ],
        ],
        [
            'title' => __('website.catalog.additive_coating.title'), 'icon' => 'bi-layers', 'filter' => 'ADDITIVE COATING',
            'lines' => [
                [__('website.catalog.additive_coating.supplementary'), ['SC', 'SS', 'SI']],
            ],
        ],
        [
            'title' => __('website.catalog.pu_resin.title'), 'icon' => 'bi-droplet', 'filter' => 'PU RESIN',
            'lines' => [
                [__('website.catalog.pu_resin.skin_adhesive'), ['ISU', 'ISA', 'ISW', 'IWD', 'IWA', 'IWS', 'IEU', 'IEA', 'IEW']],
                [__('website.catalog.pu_resin.polyester'), ['EB', 'B', 'DEB']],
            ],
        ],
    ];

    // Warna teks yang terbaca di atas warna latar produk.
    $readableOn = function (?string $hexColor): string {
        $hex = ltrim((string) $hexColor, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (!preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            return '#ffffff';
        }
        $lum = (0.299 * hexdec(substr($hex, 0, 2)) + 0.587 * hexdec(substr($hex, 2, 2)) + 0.114 * hexdec(substr($hex, 4, 2))) / 255;

        return $lum > 0.6 ? '#15181b' : '#ffffff';
    };
@endphp

@section('products_content')
    {{-- Pengantar --}}
    <section class="sp-section sp-catalog-intro">
        <div class="container">
            <div class="sp-section__head sp-section__head--center">
                <div>
                    <span class="sp-eyebrow">{{ __('website.pro.catalog_label') }}</span>
                    <h2 class="sp-title">{{ __('website.catalog.title') }}</h2>
                    <p class="sp-lead mb-0">{{ __('website.catalog.desc') }}</p>
                </div>
            </div>

            <div class="sp-zdhc">
                <span class="sp-zdhc__icon"><i class="bi bi-patch-check" aria-hidden="true"></i></span>
                <span class="sp-zdhc__text">{!! __('website.catalog.search.zdhc_badge') !!}</span>
                <a href="https://www.my-aip.com/ZDHCLogin/Login" target="_blank" rel="noopener noreferrer" class="sp-link">
                    {{ __('website.catalog.search.verify_now') }} <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                </a>
            </div>

            <div class="row g-4 sp-families">
                @foreach ($families as $i => $family)
                    <div class="col-xl-3 col-md-6">
                        <article class="sp-family wow fadeInUp" data-wow-delay="{{ 0.05 + $i * 0.08 }}s">
                            <header class="sp-family__head">
                                <span class="sp-pillar__icon"><i class="bi {{ $family['icon'] }}" aria-hidden="true"></i></span>
                                <h3 class="sp-family__title">{{ $family['title'] }}</h3>
                            </header>
                            <ul class="sp-family__lines">
                                @foreach ($family['lines'] as [$lineTitle, $codes])
                                    <li>
                                        <span class="sp-family__line">{{ $lineTitle }}</span>
                                        <span class="sp-family__codes">
                                            @foreach ($codes as $code)
                                                <span class="sp-code">{{ $code }}</span>
                                            @endforeach
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                            @if ($catalogCategories->contains($family['filter']))
                                <a href="#catalog" class="sp-family__more" data-catalog-category="{{ $family['filter'] }}">
                                    {{ __('website.pro.catalog_view') }} <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                </a>
                            @endif
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Katalog --}}
    <section class="sp-section sp-section--alt" id="catalog">
        <div class="container">
            <div class="sp-toolbar">
                <div class="sp-toolbar__search">
                    <label for="searchInput" class="visually-hidden">{{ __('website.catalog.search.product_search') }}</label>
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input type="search" id="searchInput" autocomplete="off"
                        placeholder="{{ __('website.catalog.search.placeholder') }}">
                </div>
                <div class="sp-toolbar__count" id="productCounter" aria-live="polite">
                    {{ __('website.catalog.search.showing_total') }}
                    <strong data-catalog-count>{{ $catalogItems->count() }}</strong>
                    {{ __('website.catalog.search.products') }}
                </div>
                @if ($catalogCategories->isNotEmpty())
                    <div class="sp-toolbar__filters" role="group" aria-label="{{ __('website.catalog.search.category') }}">
                        <button type="button" class="sp-chip is-active" data-catalog-category="all" aria-pressed="true">
                            {{ __('website.catalog.search.all_categories') }}
                        </button>
                        @foreach ($catalogCategories as $category)
                            <button type="button" class="sp-chip" data-catalog-category="{{ $category }}" aria-pressed="false">
                                {{ $category }}
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="row g-4" id="productGrid">
                @forelse ($catalogItems as $item)
                    @php
                        $bgColor = $item->bg_color ?: '#1e3a8a';
                        $textColor = $readableOn($bgColor);
                    @endphp
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3 product-item" data-code="{{ strtolower($item->name) }}"
                        data-color="{{ strtolower($item->color) }}" data-category="{{ $item->category }}">
                        <article class="sp-item" style="--sp-item-color: {{ $bgColor }}; --sp-item-ink: {{ $textColor }};">
                            <div class="sp-item__media">
                                @foreach ([$item->image1, $item->image2] as $imageIndex => $image)
                                    @if ($image)
                                        <button type="button" class="sp-item__shot" data-catalog-image="{{ asset('storage/' . $image) }}"
                                            data-catalog-title="{{ $item->name }}" aria-label="{{ $item->name }} — preview {{ $imageIndex + 1 }}">
                                            <img src="{{ asset('storage/' . $image) }}" alt="{{ $item->name }} {{ $item->color }}" loading="lazy" decoding="async">
                                        </button>
                                    @else
                                        <span class="sp-item__shot sp-item__shot--empty"><i class="bi bi-image" aria-hidden="true"></i></span>
                                    @endif
                                @endforeach
                            </div>
                            <div class="sp-item__code">{{ $item->name }}</div>
                            <div class="sp-item__body">
                                <span class="sp-item__category">{{ $item->category }}</span>
                                <span class="sp-item__type">{{ $item->type }}</span>
                                <span class="sp-item__color"><span class="sp-item__swatch" aria-hidden="true"></span>{{ $item->color }}</span>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="sp-empty">
                            <i class="bi bi-box-seam" aria-hidden="true"></i>
                            <h3>{{ __('website.catalog.search.empty_db') }}</h3>
                            <p>{{ __('website.catalog.search.empty_db_desc') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div id="noResults" class="sp-empty" hidden>
                <i class="bi bi-search" aria-hidden="true"></i>
                <h3>{{ __('website.catalog.search.no_results') }}</h3>
                <p>{!! __('website.catalog.search.no_results_desc') !!}</p>
            </div>

            <nav aria-label="Product pagination">
                <ul class="sp-pagination" id="paginationControls"></ul>
            </nav>
        </div>
    </section>

    {{-- Ajakan --}}
    <section class="sp-cta">
        <div class="container">
            <div class="sp-cta__inner">
                <div>
                    <h2 class="sp-title sp-title--light">{{ __('website.pro.catalog_cta_title') }}</h2>
                    <p class="sp-cta__text">{{ __('website.pro.catalog_cta_text') }}</p>
                </div>
                <a href="{{ route('contact') }}" class="sp-btn sp-btn--light">{{ __('website.pro.catalog_cta_button') }} <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
        </div>
    </section>

    {{-- Pratinjau gambar --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content sp-modal">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="" id="modalImage" class="img-fluid" alt="">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ITEMS_PER_PAGE = 12;
            const searchInput = document.getElementById('searchInput');
            const items = Array.from(document.querySelectorAll('#productGrid .product-item'));
            const noResults = document.getElementById('noResults');
            const counter = document.querySelector('[data-catalog-count]');
            const pagination = document.getElementById('paginationControls');
            const chips = Array.from(document.querySelectorAll('.sp-chip[data-catalog-category]'));
            const catalog = document.getElementById('catalog');

            let category = 'all';
            let visible = items.slice();
            let page = 1;

            function pageButton(label, target, options) {
                const li = document.createElement('li');
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = label;
                button.className = 'sp-pagination__btn' + (options.active ? ' is-active' : '');
                button.disabled = !!options.disabled;
                if (options.ariaLabel) button.setAttribute('aria-label', options.ariaLabel);
                if (options.active) button.setAttribute('aria-current', 'page');
                button.addEventListener('click', function () {
                    page = target;
                    render();
                    catalog.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
                li.appendChild(button);
                return li;
            }

            function render() {
                const totalPages = Math.max(1, Math.ceil(visible.length / ITEMS_PER_PAGE));
                page = Math.min(Math.max(1, page), totalPages);
                const start = (page - 1) * ITEMS_PER_PAGE;

                items.forEach(function (item) { item.hidden = true; });
                visible.slice(start, start + ITEMS_PER_PAGE).forEach(function (item) { item.hidden = false; });

                if (counter) counter.textContent = visible.length;
                if (noResults) noResults.hidden = visible.length > 0 || items.length === 0;

                pagination.innerHTML = '';
                if (totalPages <= 1) return;
                pagination.appendChild(pageButton('‹', page - 1, { disabled: page === 1, ariaLabel: 'Previous page' }));
                for (let i = 1; i <= totalPages; i++) {
                    pagination.appendChild(pageButton(String(i), i, { active: i === page }));
                }
                pagination.appendChild(pageButton('›', page + 1, { disabled: page === totalPages, ariaLabel: 'Next page' }));
            }

            function applyFilters() {
                const term = (searchInput ? searchInput.value : '').toLowerCase().trim();
                visible = items.filter(function (item) {
                    const matchesTerm = (item.dataset.code || '').includes(term) || (item.dataset.color || '').includes(term);
                    const matchesCategory = category === 'all' || item.dataset.category === category;
                    return matchesTerm && matchesCategory;
                });
                page = 1;
                render();
            }

            function setCategory(value) {
                category = value;
                chips.forEach(function (chip) {
                    const active = chip.dataset.catalogCategory === value;
                    chip.classList.toggle('is-active', active);
                    chip.setAttribute('aria-pressed', active ? 'true' : 'false');
                });
                applyFilters();
            }

            if (searchInput) searchInput.addEventListener('input', applyFilters);

            document.addEventListener('click', function (event) {
                const categoryTrigger = event.target.closest('[data-catalog-category]');
                if (categoryTrigger) {
                    setCategory(categoryTrigger.dataset.catalogCategory);
                    return;
                }

                const shot = event.target.closest('[data-catalog-image]');
                if (shot && window.bootstrap) {
                    document.getElementById('modalImage').src = shot.dataset.catalogImage;
                    document.getElementById('modalImage').alt = shot.dataset.catalogTitle || '';
                    document.getElementById('modalTitle').textContent = shot.dataset.catalogTitle || 'Preview';
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('imageModal')).show();
                }
            });

            render();
        });
    </script>
@endpush
