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
            border-bottom: 4px solid #1e3a8a;
            background: #f1f5f9;
        }

        .product-image-container {
            width: 50%;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image-container:first-child {
            border-right: 1px solid #e2e8f0;
        }

        .product-image-container img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            mix-blend-mode: multiply;
        }

        .product-code {
            background: #1e3a8a;
            color: #ffffff;
            text-align: center;
            font-weight: 700;
            font-size: 1.15rem;
            padding: 12px;
            letter-spacing: 0.05em;
        }

        .product-details {
            padding: 16px;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
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
            border-top: 4px solid #1e3a8a;
            box-shadow: 0 10px 30px -5px rgba(30, 58, 138, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .category-info-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -10px rgba(30, 58, 138, 0.15);
            border-top-color: #3b82f6;
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
        <h2>ILSAM PRODUCT CATALOG</h2>
        <p class="mb-5">Professional chemical colorants engineered for PU, PVC, and Printing applications. Find the perfect color for
            your industrial needs.</p>
    </div>

    {{-- Premium Category Info Cards --}}
    <div class="category-cards-grid">
        <!-- Card 1: COLORANTS -->
        <div class="category-info-card">
            <div class="card-header">COLORANTS</div>
            <div class="card-body">
                <div class="info-block">
                    <div class="info-block-title">For Leather & Synthetic Leather PU</div>
                    <div class="info-block-badges">
                        <span class="badge-code">SW</span>
                        <span class="badge-code">SU</span>
                        <span class="badge-code">SF</span>
                    </div>
                </div>
                <div class="info-block">
                    <div class="info-block-title">For Synthetic Leather PVC</div>
                    <div class="info-block-badges">
                        <span class="badge-code">SV</span>
                        <span class="badge-code">SFV</span>
                    </div>
                </div>
                <div class="info-block">
                    <div class="info-block-title">For Printing</div>
                    <div class="info-block-badges">
                        <span class="badge-code">SP</span>
                        <span class="badge-code">SG</span>
                    </div>
                </div>
                <div class="info-block">
                    <div class="info-block-title">For Water-based</div>
                    <div class="info-block-badges">
                        <span class="badge-code">SUW</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: SURFACE COATING AGENTS -->
        <div class="category-info-card">
            <div class="card-header">SURFACE COATING AGENTS</div>
            <div class="card-body">
                <div class="info-block">
                    <div class="info-block-title">Solution-type Surface Coating Agent for Leather and Synthetic Leather PU and PVC</div>
                    <div class="info-block-badges">
                        <span class="badge-code">SUS</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: ADDITIVE COATING -->
        <div class="category-info-card">
            <div class="card-header">ADDITIVE COATING</div>
            <div class="card-body">
                <div class="info-block">
                    <div class="info-block-title">Supplementary agent for promoting quality and curing PU and PVC</div>
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
            <div class="card-header">PU RESIN</div>
            <div class="card-body">
                <div class="info-block">
                    <div class="info-block-title">Skin and Adhesive For Leather and Synthetic Leather PU</div>
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
                    <div class="info-block-title">Polyester for production Resin PU</div>
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
                <label for="searchInput" class="filter-label">Pencarian Produk</label>
                <div class="custom-search-wrapper">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="searchInput" class="custom-search-input"
                        placeholder="Cari berdasarkan kode (misal: 736DF) atau warna (misal: BLACK)...">
                </div>
            </div>
            <div class="col-md-4">
                <label for="categoryFilter" class="filter-label">Kategori</label>
                <select id="categoryFilter" class="filter-select">
                    <option value="all">Semua Kategori</option>
                    <option value="COLORANTS">COLORANTS</option>
                    <option value="SURFACE COATING AGENTS">SURFACE COATING AGENTS</option>
                    <option value="ADDITIVE COATING">ADDITIVE COATING</option>
                    <option value="PU RESIN">PU RESIN</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Product Grid --}}
    <div class="row g-4" id="productGrid">
        @forelse ($colorants ?? collect() as $item)
            <div class="col-12 col-md-6 col-lg-4 product-item" data-code="{{ strtolower($item->name) }}"
                data-color="{{ strtolower($item->color) }}" data-category="{{ $item->category }}">

                <div class="product-card position-relative">
                    <div class="category-badge" style="background-color: {{ $item->bg_color ?? '#1e3a8a' }};">
                        {{ $item->category }}
                    </div>

                    <div class="product-images">
                        <div class="product-image-container">
                            @if ($item->image1)
                                <img src="{{ asset('storage/' . $item->image1) }}" alt="{{ $item->name }} bottle"
                                    loading="lazy">
                            @else
                                <div class="text-muted small">No Image</div>
                            @endif
                        </div>
                        <div class="product-image-container">
                            @if ($item->image2)
                                <img src="{{ asset('storage/' . $item->image2) }}" alt="{{ $item->name }} liquid"
                                    loading="lazy">
                            @else
                                <div class="text-muted small">No Image</div>
                            @endif
                        </div>
                    </div>

                    <div class="product-code" style="background-color: {{ $item->bg_color ?? '#1e3a8a' }};">
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
                    <h4 class="mb-2">No Product</h4>
                    <p class="text-muted">Data katalog belum dimasukkan oleh sistem.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- No Results State (JS toggled) --}}
    <div id="noResults" class="empty-state hidden mt-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-search mb-3"
            viewBox="0 0 16 16">
            <path
                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
        </svg>
        <h4>Produk tidak ditemukan</h4>
        <p class="text-muted mb-0">Tidak ada produk yang cocok dengan kata kunci atau filter pencarian Anda.<br>Silakan coba
            dengan kata kunci yang lain.</p>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');
            const productItems = document.querySelectorAll('.product-item');
            const noResults = document.getElementById('noResults');

            function filterProducts() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const category = categoryFilter.value;
                let visibleCount = 0;

                productItems.forEach(item => {
                    const code = (item.getAttribute('data-code') || '').toLowerCase();
                    const color = (item.getAttribute('data-color') || '').toLowerCase();
                    const itemCategory = item.getAttribute('data-category');

                    const matchesSearch = code.includes(searchTerm) || color.includes(searchTerm);
                    const matchesCategory = category === 'all' || itemCategory === category;

                    if (matchesSearch && matchesCategory) {
                        item.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        item.classList.add('hidden');
                    }
                });

                if (visibleCount === 0) {
                    if (noResults) noResults.classList.remove('hidden');
                } else {
                    if (noResults) noResults.classList.add('hidden');
                }
            }

            if (searchInput && categoryFilter) {
                searchInput.addEventListener('input', filterProducts);
                categoryFilter.addEventListener('change', filterProducts);
            }
        });
    </script>
@endpush
