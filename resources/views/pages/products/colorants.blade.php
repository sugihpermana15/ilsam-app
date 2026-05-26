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

@push('head')
    <style>
        /* ── Page Header ─────────────────────────────── */
        .catalog-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .catalog-header h2 {
            font-size: 2.25rem;
            font-weight: 800;
            color: #1e3a8a;
            margin-bottom: 10px;
        }

        .catalog-header p {
            color: #6b7280;
            max-width: 600px;
            margin: 0 auto;
            font-size: 1rem;
        }

        /* ── Filter Bar ──────────────────────────────── */
        .filter-bar {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 40px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .filter-select {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 12px 16px;
            font-size: 0.95rem;
            width: 100%;
            outline: none;
            transition: all 0.2s;
        }

        .filter-select:focus {
            border-color: #1e3a8a;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        }

        .custom-search-wrapper {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 0 16px;
            transition: all 0.2s;
        }

        .custom-search-wrapper:focus-within {
            border-color: #1e3a8a;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        }

        .custom-search-wrapper .search-icon {
            width: 18px;
            height: 18px;
            color: #94a3b8;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .custom-search-input {
            flex-grow: 1;
            border: none;
            padding: 12px 0;
            font-size: 0.95rem;
            outline: none;
            background: transparent;
            color: #334155;
        }

        .custom-search-input::placeholder {
            color: #94a3b8;
        }

        .custom-search-input:focus {
            outline: none;
            box-shadow: none;
        }

        .filter-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
            display: block;
            font-size: 0.9rem;
        }

        /* ── Product Card ────────────────────────────── */
        .product-card {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            background: #ffffff;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            border-color: #1e3a8a;
            box-shadow: 0 12px 20px -5px rgba(30, 58, 138, 0.15);
            transform: translateY(-4px);
        }

        .product-images {
            display: flex;
            background: #f1f5f9;
        }

        .product-image-container {
            width: 50%;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image-container:first-child {
            border-right: 1px solid #e2e8f0;
        }

        .product-image-container img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
            mix-blend-mode: multiply;
        }

        .product-code {
            background: #1e3a8a;
            color: #ffffff;
            text-align: center;
            font-weight: 700;
            padding: 15px;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        .product-details {
            padding: 20px;
            text-align: center;
            background: #ffffff;
        }

        /* ── Pagination ──────────────────────────────── */
        .pagination .page-item .page-link {
            border-radius: 8px;
            margin: 0 4px;
            border: none;
            color: #475569;
            background-color: #f8fafc;
            font-weight: 600;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .pagination .page-item.active .page-link {
            background-color: #1e3a8a;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(30, 58, 138, 0.2);
        }

        .pagination .page-item .page-link:hover {
            background-color: #e2e8f0;
            color: #1e3a8a;
        }

        .pagination .page-item.disabled .page-link {
            background-color: #f1f5f9;
            color: #cbd5e1;
            cursor: not-allowed;
        }

        /* ── Custom Dark Dropdown (Navbar Style) ────────────── */
        .filter-btn {
            background-color: #fff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 0.95rem;
            color: #334155;
            transition: all 0.2s;
        }
        .filter-btn:focus, .filter-btn[aria-expanded="true"] {
            border-color: #1e3a8a;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        }
        .custom-dark-dropdown {
            background-color: #111111;
            border: none;
            border-radius: 4px;
            padding: 10px 0;
            margin-top: 5px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.15);
        }
        .custom-dark-dropdown .dropdown-item {
            color: #ffffff;
            font-weight: 500;
            font-size: 15px;
            padding: 10px 20px;
            transition: all 0.3s;
            position: relative;
            background: transparent !important;
        }
        .custom-dark-dropdown .dropdown-item:hover,
        .custom-dark-dropdown .dropdown-item.active {
            color: #007aff;
            padding-left: 28px;
        }
        .custom-dark-dropdown .dropdown-item:hover::before,
        .custom-dark-dropdown .dropdown-item.active::before {
            content: "—";
            position: absolute;
            left: 10px;
            color: #007aff;
        }

        .product-type {
            font-style: italic;
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #cbd5e1;
        }

        .product-color {
            font-weight: 800;
            color: #0f172a;
            font-size: 1.05rem;
            text-transform: uppercase;
        }

        /* ── Category Badge ──────────────────────────── */
        .category-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #1e3a8a;
            color: white;
            font-size: 0.75rem;
            font-weight: bold;
            padding: 4px 10px;
            border-radius: 20px;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* ── Utilities ───────────────────────────────── */
        .hidden {
            display: none !important;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: #f8fafc;
            border-radius: 12px;
            border: 2px dashed #cbd5e1;
        }

        .empty-state h4 {
            color: #1e3a8a;
            font-weight: bold;
            margin-top: 16px;
        }

        .empty-state svg {
            color: #94a3b8;
        }

        /* ── Premium Category Cards ────────────────────── */
        .category-cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            width: 100%;
            margin: 0 auto 60px auto;
        }

        @media (max-width: 1200px) {
            .category-cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .category-cards-grid {
                grid-template-columns: 1fr;
            }
        }

        .category-info-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            border-top: none; /* Make the gradient pseudo-element flush with the top */
            box-shadow: 0 10px 30px -5px rgba(30, 58, 138, 0.08);
            /* overflow: hidden removed to allow pseudo-element to cover borders */
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            position: relative;
            padding-top: 4px; /* Space for the pseudo-element border */
        }

        .category-info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -1px; /* Extend over the left border */
            right: -1px; /* Extend over the right border */
            height: 4px;
            background: linear-gradient(90deg, #1e3a8a 0%, #3b82f6 50%, #06b6d4 100%);
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .category-info-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -10px rgba(30, 58, 138, 0.15);
        }

        .category-info-card:hover::before {
            background: linear-gradient(90deg, #2563eb 0%, #0ea5e9 50%, #10b981 100%);
        }

        .category-info-card .card-header {
            background: #ffffff;
            color: #1e3a8a;
            padding: 24px 24px 16px 24px;
            font-weight: 800;
            font-size: 1.25rem;
            text-align: left;
            letter-spacing: 0.02em;
            border-bottom: 1px solid #f1f5f9;
        }

        .category-info-card .card-body {
            padding: 24px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .info-block {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .info-block-title {
            color: #475569;
            font-size: 0.95rem;
            font-weight: 600;
            line-height: 1.4;
        }

        .info-block-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .badge-code {
            background: #f1f5f9;
            color: #1e3a8a;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
            cursor: default;
        }

        .badge-code:hover {
            background: #1e3a8a;
            color: #ffffff;
            border-color: #1e3a8a;
        }
    </style>
@endpush

@section('products_content')

    <div class="catalog-header">
        <h2>{{ __('website.catalog.title') }}</h2>
        <p class="mb-4">{{ __('website.catalog.desc') }}</p>

        <div class="d-inline-flex align-items-center justify-content-center bg-white px-3 py-2 rounded-pill shadow-sm border mb-5" style="transition: all 0.3s ease;">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;">
                    <i class="fa-solid fa-leaf text-success" style="font-size: 0.9rem;"></i>
                </div>
                <span class="text-dark" style="font-size: 0.95rem;">
                    {!! __('website.catalog.search.zdhc_badge') !!}
                </span>
            </div>
            <a href="https://www.my-aip.com/ZDHCLogin/Login" target="_blank" rel="noopener noreferrer" class="ms-4 btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" style="font-size: 0.8rem; letter-spacing: 0.5px; border-width: 1.5px;">
                {{ __('website.catalog.search.verify_now') }} <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size: 0.8em;"></i>
            </a>
        </div>
    </div>

    {{-- Premium Category Info Cards --}}
    <div class="category-cards-grid">
        <!-- Card 1: COLORANTS -->
        <div class="category-info-card">
            <div class="card-header">{{ __('website.catalog.colorants.title') }}</div>
            <div class="card-body">
                <div class="info-block">
                    <div class="info-block-title">{{ __('website.catalog.colorants.leather_pu') }}</div>
                    <div class="info-block-badges">
                        <span class="badge-code">SW</span>
                        <span class="badge-code">SU</span>
                        <span class="badge-code">SF</span>
                    </div>
                </div>
                <div class="info-block">
                    <div class="info-block-title">{{ __('website.catalog.colorants.leather_pvc') }}</div>
                    <div class="info-block-badges">
                        <span class="badge-code">SV</span>
                        <span class="badge-code">SFV</span>
                    </div>
                </div>
                <div class="info-block">
                    <div class="info-block-title">{{ __('website.catalog.colorants.printing') }}</div>
                    <div class="info-block-badges">
                        <span class="badge-code">SP</span>
                        <span class="badge-code">SG</span>
                    </div>
                </div>
                <div class="info-block">
                    <div class="info-block-title">{{ __('website.catalog.colorants.water_based') }}</div>
                    <div class="info-block-badges">
                        <span class="badge-code">SUW</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: SURFACE COATING AGENTS -->
        <div class="category-info-card">
            <div class="card-header">{{ __('website.catalog.surface_coating.title') }}</div>
            <div class="card-body">
                <div class="info-block">
                    <div class="info-block-title">{{ __('website.catalog.surface_coating.solution_type') }}</div>
                    <div class="info-block-badges">
                        <span class="badge-code">SUS</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: ADDITIVE COATING -->
        <div class="category-info-card">
            <div class="card-header">{{ __('website.catalog.additive_coating.title') }}</div>
            <div class="card-body">
                <div class="info-block">
                    <div class="info-block-title">{{ __('website.catalog.additive_coating.supplementary') }}</div>
                    <div class="info-block-badges">
                        <span class="badge-code">SC</span>
                        <span class="badge-code">SS</span>
                        <span class="badge-code">SI</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: PU RESIN -->
        <div class="category-info-card">
            <div class="card-header">{{ __('website.catalog.pu_resin.title') }}</div>
            <div class="card-body">
                <div class="info-block">
                    <div class="info-block-title">{{ __('website.catalog.pu_resin.skin_adhesive') }}</div>
                    <div class="info-block-badges">
                        <span class="badge-code">ISU</span>
                        <span class="badge-code">ISA</span>
                        <span class="badge-code">ISW</span>
                        <span class="badge-code">IWD</span>
                        <span class="badge-code">IWA</span>
                        <span class="badge-code">IWS</span>
                        <span class="badge-code">IEU</span>
                        <span class="badge-code">IEA</span>
                        <span class="badge-code">IEW</span>
                    </div>
                </div>
                <div class="info-block">
                    <div class="info-block-title">{{ __('website.catalog.pu_resin.polyester') }}</div>
                    <div class="info-block-badges">
                        <span class="badge-code">EB</span>
                        <span class="badge-code">B</span>
                        <span class="badge-code">DEB</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="filter-bar">
        <div class="row g-3">
            <div class="col-md-8">
                <label for="searchInput" class="filter-label">{{ __('website.catalog.search.product_search') }}</label>
                <div class="custom-search-wrapper">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="searchInput" class="custom-search-input"
                        placeholder="{{ __('website.catalog.search.placeholder') }}">
                </div>
            </div>
            <div class="col-md-4">
                <label class="filter-label">{{ __('website.catalog.search.category') }}</label>
                <div class="dropdown">
                    <button class="btn w-100 text-start d-flex justify-content-between align-items-center filter-btn" type="button" id="categoryDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                        <span id="selectedCategoryText">{{ __('website.catalog.search.all_categories') }}</span>
                        <i class="fa-solid fa-chevron-down ms-2" style="font-size: 12px; color: #94a3b8;"></i>
                    </button>
                    <ul class="dropdown-menu w-100 custom-dark-dropdown" aria-labelledby="categoryDropdownBtn">
                        <li><a class="dropdown-item category-dropdown-item active" href="javascript:void(0)" data-value="all">{{ __('website.catalog.search.all_categories') }}</a></li>
                        <li><a class="dropdown-item category-dropdown-item" href="javascript:void(0)" data-value="COLORANTS">COLORANTS</a></li>
                        <li><a class="dropdown-item category-dropdown-item" href="javascript:void(0)" data-value="SURFACE COATING AGENTS">SURFACE COATING AGENTS</a></li>
                        <li><a class="dropdown-item category-dropdown-item" href="javascript:void(0)" data-value="ADDITIVE COATING">ADDITIVE COATING</a></li>
                        <li><a class="dropdown-item category-dropdown-item" href="javascript:void(0)" data-value="PU RESIN">PU RESIN</a></li>
                    </ul>
                </div>
                <input type="hidden" id="categoryFilter" value="all">
            </div>
        </div>
        <div class="row mt-4 mb-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div class="product-counter text-muted fw-medium" id="productCounter"
                    style="background: #f1f5f9; padding: 8px 16px; border-radius: 20px; font-weight: 600; color: #475569; font-size: 0.95rem; border: 1px solid #e2e8f0; display: inline-block;">
                    <i class="bi bi-box-seam me-2 text-primary"></i> {{ __('website.catalog.search.showing_total') }} <span
                        class="text-primary fw-bold mx-1">{{ count($colorants ?? []) }}</span> {{ __('website.catalog.search.products') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Product Grid --}}
    <div class="row g-4" id="productGrid">
        @forelse ($colorants ?? collect() as $item)
            @php
                $bgColor = $item->bg_color ?? '#1e3a8a';
                $hex = str_replace('#', '', $bgColor);
                if (strlen($hex) === 3) {
                    $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
                    $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
                    $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
                } else {
                    $r = hexdec(substr($hex, 0, 2));
                    $g = hexdec(substr($hex, 2, 2));
                    $b = hexdec(substr($hex, 4, 2));
                }
                $lum = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
                $textColor = $lum > 0.5 ? '#000000' : '#ffffff';
            @endphp
            <div class="col-12 col-md-6 col-lg-3 product-item" data-code="{{ strtolower($item->name) }}"
                data-color="{{ strtolower($item->color) }}" data-category="{{ $item->category }}">

                <div class="product-card position-relative">
                    <div class="category-badge"
                        style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                        {{ $item->category }}
                    </div>

                    <div class="product-images">
                        <div class="product-image-container">
                            @if ($item->image1)
                                <img src="{{ asset('storage/' . $item->image1) }}" alt="{{ $item->name }} bottle"
                                    loading="lazy" style="cursor: zoom-in;"
                                    onclick="showImageModal(this.src, '{{ addslashes($item->name) }}')">
                            @else
                                <div class="text-muted small">No Image</div>
                            @endif
                        </div>
                        <div class="product-image-container">
                            @if ($item->image2)
                                <img src="{{ asset('storage/' . $item->image2) }}" alt="{{ $item->name }} liquid"
                                    loading="lazy" style="cursor: zoom-in;"
                                    onclick="showImageModal(this.src, '{{ addslashes($item->name) }}')">
                            @else
                                <div class="text-muted small">No Image</div>
                            @endif
                        </div>
                    </div>

                    <div class="product-code"
                        style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                        {{ $item->name }}
                    </div>

                    <div class="product-details">
                        <div class="product-type">{{ $item->type }}</div>
                        <div class="product-color">{{ $item->color }}</div>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <h4 class="mb-2">{{ __('website.catalog.search.empty_db') }}</h4>
                    <p class="text-muted">{{ __('website.catalog.search.empty_db_desc') }}</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination Controls --}}
    <nav aria-label="Product Pagination" class="mt-5 mb-4">
        <ul class="pagination justify-content-center" id="paginationControls">
            {{-- Injected via JS --}}
        </ul>
    </nav>

    {{-- No Results State (JS toggled) --}}
    <div id="noResults" class="empty-state hidden mt-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor"
            class="bi bi-search mb-3" viewBox="0 0 16 16">
            <path
                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
        </svg>
        <h4>{{ __('website.catalog.search.no_results') }}</h4>
        <p class="text-muted mb-0">{!! __('website.catalog.search.no_results_desc') !!}</p>
    </div>

    {{-- Image Modal --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header bg-light border-0 px-4 py-3 align-items-center">
                    <h5 class="modal-title fw-bold text-dark fs-5" id="modalTitle">Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="box-shadow: none;"></button>
                </div>
                <div class="modal-body text-center p-4 bg-white">
                    <img src="" id="modalImage" class="img-fluid" alt="Preview"
                        style="max-height: 70vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');
            const productItems = document.querySelectorAll('.product-item');
            const noResults = document.getElementById('noResults');
            const productCounter = document.getElementById('productCounter');
            const paginationControls = document.getElementById('paginationControls');

            let visibleItems = Array.from(productItems);
            let currentPage = 1;
            const itemsPerPage = 12;

            window.showImageModal = function(src, title) {
                document.getElementById('modalImage').src = src;
                document.getElementById('modalTitle').textContent = title || 'Preview';
                var myModal = new bootstrap.Modal(document.getElementById('imageModal'));
                myModal.show();
            };

            function renderPagination() {
                paginationControls.innerHTML = '';
                const totalPages = Math.ceil(visibleItems.length / itemsPerPage);

                if (totalPages <= 1) return;

                // Prev Button
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML =
                    `<a class="page-link" href="javascript:void(0)" aria-label="Previous">&laquo;</a>`;
                prevLi.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        showPage();
                    }
                });
                paginationControls.appendChild(prevLi);

                // Page Numbers
                for (let i = 1; i <= totalPages; i++) {
                    const li = document.createElement('li');
                    li.className = `page-item ${i === currentPage ? 'active' : ''}`;
                    li.innerHTML = `<a class="page-link" href="javascript:void(0)">${i}</a>`;
                    li.addEventListener('click', () => {
                        currentPage = i;
                        showPage();
                    });
                    paginationControls.appendChild(li);
                }

                // Next Button
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
                nextLi.innerHTML = `<a class="page-link" href="javascript:void(0)" aria-label="Next">&raquo;</a>`;
                nextLi.addEventListener('click', () => {
                    if (currentPage < totalPages) {
                        currentPage++;
                        showPage();
                    }
                });
                paginationControls.appendChild(nextLi);
            }

            function showPage() {
                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;

                // Hide all initially
                productItems.forEach(item => item.classList.add('hidden'));

                // Show only items for current page
                visibleItems.forEach((item, index) => {
                    if (index >= startIndex && index < endIndex) {
                        item.classList.remove('hidden');
                    }
                });

                renderPagination();

                // Update counter text
                if (productCounter) {
                    productCounter.innerHTML =
                        `<i class="bi bi-box-seam me-2 text-primary"></i> {{ __('website.catalog.search.showing_total') }} <span class="text-primary fw-bold mx-1">${visibleItems.length}</span> {{ __('website.catalog.search.products') }}`;
                }
            }

            function filterProducts() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const category = categoryFilter.value;
                visibleItems = [];

                productItems.forEach(item => {
                    const code = (item.getAttribute('data-code') || '').toLowerCase();
                    const color = (item.getAttribute('data-color') || '').toLowerCase();
                    const itemCategory = item.getAttribute('data-category');

                    const matchesSearch = code.includes(searchTerm) || color.includes(searchTerm);
                    const matchesCategory = category === 'all' || itemCategory === category;

                    if (matchesSearch && matchesCategory) {
                        visibleItems.push(item);
                    } else {
                        item.classList.add('hidden');
                    }
                });

                if (visibleItems.length === 0) {
                    if (noResults) noResults.classList.remove('hidden');
                    if (paginationControls) paginationControls.innerHTML = '';
                    if (productCounter) productCounter.innerHTML =
                        `<i class="bi bi-box-seam me-2 text-primary"></i> {{ __('website.catalog.search.showing_total') }} <span class="text-primary fw-bold mx-1">0</span> {{ __('website.catalog.search.products') }}`;
                } else {
                    if (noResults) noResults.classList.add('hidden');
                    currentPage = 1;
                    showPage();
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterProducts);
            }

            // Handle custom category dropdown
            const categoryItems = document.querySelectorAll('.category-dropdown-item');
            const selectedCategoryText = document.getElementById('selectedCategoryText');

            categoryItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Update Active state
                    categoryItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');

                    // Update button text and hidden input value
                    selectedCategoryText.textContent = this.textContent;
                    categoryFilter.value = this.getAttribute('data-value');

                    // Trigger filter
                    filterProducts();
                });
            });

            // Initialize on load
            showPage();
        });
    </script>
@endpush
