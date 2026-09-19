@section('title', 'Ilsam - Pengambilan ' . strtoupper($category))
@section('title-sub', 'Administrasi')
@section('pagetitle', 'Pengambilan ' . strtoupper($category))
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .stock-request-toolbar {
            gap: .75rem;
            border: 1px solid #e9ecef;
            border-radius: .375rem;
            background: #fff;
            padding: .875rem 1rem;
        }

        .stock-page-content {
            padding-bottom: 1.5rem;
        }

        .stock-request-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem
        }

        .stock-site-filter {
            border: 1px solid #e9ecef;
            border-radius: .375rem;
            background: #fff;
            padding: .875rem 1rem;
        }

        .stock-search {
            position: relative;
        }

        .stock-search .form-control {
            padding-left: 2.5rem;
        }

        .stock-search-icon {
            position: absolute;
            top: 50%;
            left: .875rem;
            z-index: 2;
            color: #98a2b3;
            pointer-events: none;
            transform: translateY(-50%);
        }

        .stock-search-empty {
            display: none;
        }

        .product-card {
            height: 100%;
            overflow: hidden;
            border: 1px solid #e9ecef;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 .5rem 1.25rem rgba(27, 43, 65, .10);
        }

        .product-media {
            position: relative;
            height: 132px;
            width: 100%;
            overflow: hidden;
            border-radius: .375rem;
            background: #f4f6f8;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9aa6b2;
            font-size: 2rem
        }

        .product-title {
            display: -webkit-box;
            overflow: hidden;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            min-height: 2.5rem;
        }

        .stock-meta {
            color: #667085;
        }

        .qty-control {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .75rem
        }

        .qty-control button {
            width: 32px;
            height: 32px;
            padding: 0
        }

        .summary-sticky {
            position: sticky;
            top: 1rem;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
            scrollbar-gutter: stable;
        }

        .mobile-request-summary {
            display: none;
        }

        @media (max-width: 575.98px) {
            .stock-request-toolbar {
                align-items: flex-start !important;
                flex-direction: column;
            }
        }

        @media (max-width: 1199.98px) {
            .summary-sticky {
                max-height: none;
                position: static;
            }
        }

        @media (max-width: 991.98px) {
            .stock-page-content {
                padding-bottom: 5.75rem;
            }

            .summary-sticky {
                scroll-margin-top: 5rem;
            }

            .mobile-request-summary {
                position: fixed;
                right: 1rem;
                bottom: 1rem;
                left: 1rem;
                z-index: 1020;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: .75rem;
                padding: .625rem .75rem;
                border: 1px solid #e4e7ec;
                border-radius: .5rem;
                background: #fff;
                box-shadow: 0 .75rem 1.5rem rgba(16, 24, 40, .16);
            }

            .mobile-request-summary .btn {
                flex: 0 0 auto;
            }
        }
    </style>
@endsection
@section('content')
    <div class="stock-page-content row g-4">
        <div class="col-xl-8 col-12">
            <div class="stock-request-toolbar d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="mb-1">Etalase {{ strtoupper($category) }}</h5>
                    <div class="small text-muted">Pengambilan dari {{ $siteLabel }}</div>
                </div>
                <div class="stock-request-actions">
                    <a class="btn btn-outline-primary btn-sm"
                        href="{{ route('admin.stock.ledger.index', ['category' => $category, 'site' => $currentSite]) }}"><i
                            class="fas fa-book me-1"></i>Ledger</a>
                    <a class="btn btn-outline-warning btn-sm"
                        href="{{ route('admin.stock.transfer.index', ['category' => $category]) }}"><i
                            class="fas fa-truck me-1"></i>Minta Transfer</a>
                </div>
            </div>
            <form method="GET" action="{{ route('admin.stock.request.index', ['category' => $category]) }}"
                class="stock-site-filter mb-3">
                <label class="form-label mb-1">Site Pengambilan</label><select name="site" class="form-select"
                    onchange="this.form.submit()">
                    @foreach ($sites as $code => $label)
                        <option value="{{ $code }}" @selected($currentSite === $code)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
            <div class="stock-search mb-3">
                <i class="fas fa-search stock-search-icon"></i>
                <input id="stockItemSearch" type="search" class="form-control"
                    placeholder="Cari nama barang atau satuan" autocomplete="off">
            </div>
            <div id="stockItemGrid" class="row g-3">
                @forelse($items as $item)
                    <div class="col-sm-6 col-lg-4 js-stock-item"
                        data-search="{{ strtolower($item->code . ' ' . $item->name . ' ' . $item->unit) }}">
                        <div class="card product-card">
                            <div class="card-body p-3">
                                <div class="product-media mb-3">
                                    @if ($item->image_url)
                                        <img class="product-image" src="{{ $item->image_url }}" alt="{{ $item->name }}"
                                            onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');">
                                    @endif
                                    <div class="product-placeholder {{ $item->image_url ? 'd-none' : '' }}"><i
                                            class="fas fa-image"></i></div>
                                </div>
                                <div>
                                    <h6 class="product-title mb-1">{{ $item->name }}</h6>
                                    <div class="stock-meta small">Sisa stok: <span class="js-stock"
                                            data-id="{{ $item->id }}"
                                            data-stock="{{ $item->selected_stock }}">{{ $item->selected_stock }}</span>
                                        {{ $item->unit }}
                                        @if ($item->selected_stock <= $item->low_stock_threshold)
                                            <span class="badge bg-warning text-dark ms-1">Menipis</span>
                                        @endif
                                    </div>
                                    <div class="qty-control mt-3"><button type="button"
                                            class="btn btn-outline-secondary js-minus"
                                            data-id="{{ $item->id }}">-</button><strong class="js-qty"
                                            data-id="{{ $item->id }}">0</strong><button type="button"
                                            class="btn btn-outline-primary js-plus"
                                            data-id="{{ $item->id }}">+</button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">Belum ada barang aktif.</div>
                    </div>
                @endforelse
            </div>
            <div id="stockSearchEmpty" class="stock-search-empty alert alert-light border mt-3 mb-0">
                Barang tidak ditemukan.
            </div>
        </div>
        <div class="col-xl-4 col-12">
            <div class="card summary-sticky">
                <div class="card-header">
                    <h5 class="mb-0">Ringkasan Pengambilan</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.stock.request.store', ['category' => $category]) }}"
                        id="stockRequestForm">@csrf<input type="hidden" name="site_code" value="{{ $currentSite }}">
                        <div id="cartLines">
                            <div class="text-muted small">Belum ada barang dipilih.</div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between"><span>Total item</span><strong id="totalQty">0</strong>
                        </div>
                        <div class="mt-3"><label class="form-label">Tanggal</label><input name="trx_date" type="date"
                                class="form-control" value="{{ old('trx_date', now()->toDateString()) }}" required></div>
                        <div class="mt-3"><label class="form-label">Nama Pengambil (PIC)</label><select name="pic_id"
                                class="form-select" required>
                                <option value="">-- pilih karyawan --</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select></div>
                        <div class="mt-3"><label class="form-label">Catatan</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div><button id="submitRequest" class="btn btn-danger w-100 mt-4" disabled><i
                                class="fas fa-cart-arrow-down"></i> Submit Pengambilan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="mobile-request-summary">
        <div>
            <div class="small text-muted">Barang dipilih</div>
            <strong><span id="mobileRequestTotal">0</span> item</strong>
        </div>
        <button id="mobileRequestAction" type="button" class="btn btn-danger btn-sm">
            <i id="mobileRequestActionIcon" class="fas fa-cart-arrow-down me-1"></i><span
                id="mobileRequestActionLabel">Ringkasan</span>
        </button>
    </div>
@endsection
@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cart = {};
            const stock = @json($items->mapWithKeys(fn($item) => [$item->id => $item->selected_stock]));
            const names = @json($items->mapWithKeys(fn($item) => [$item->id => $item->name]));
            const units = @json($items->mapWithKeys(fn($item) => [$item->id => $item->unit]));
            const searchInput = document.getElementById('stockItemSearch');
            const searchEmpty = document.getElementById('stockSearchEmpty');
            const summary = document.querySelector('.summary-sticky');
            const mobileAction = document.getElementById('mobileRequestAction');
            const mobileActionLabel = document.getElementById('mobileRequestActionLabel');
            const mobileActionIcon = document.getElementById('mobileRequestActionIcon');
            let summaryIsVisible = false;

            searchInput?.addEventListener('input', () => {
                const keyword = searchInput.value.trim().toLowerCase();
                let visibleItems = 0;

                document.querySelectorAll('.js-stock-item').forEach(item => {
                    const visible = item.dataset.search.includes(keyword);
                    item.classList.toggle('d-none', !visible);
                    visibleItems += visible ? 1 : 0;
                });

                searchEmpty.style.display = visibleItems || !keyword ? 'none' : 'block';
            });
            const render = () => {
                const lines = document.getElementById('cartLines');
                let total = 0;
                lines.innerHTML = '';
                Object.entries(cart).forEach(([id, qty]) => {
                    if (!qty) return;
                    total += qty;
                    const line = document.createElement('div');
                    line.className = 'd-flex justify-content-between small mb-2';
                    line.innerHTML =
                        `<span>${names[id]} x ${qty} ${units[id]}<br><span class="text-muted">Sisa setelah submit: ${stock[id] - qty} ${units[id]}</span></span><strong>${qty} ${units[id]}</strong>`;
                    lines.appendChild(line);
                    const item = document.createElement('input');
                    item.type = 'hidden';
                    item.name = `items[${id}][stock_item_id]`;
                    item.value = id;
                    lines.appendChild(item);
                    const qtyInput = document.createElement('input');
                    qtyInput.type = 'hidden';
                    qtyInput.name = `items[${id}][qty]`;
                    qtyInput.value = qty;
                    lines.appendChild(qtyInput);
                });
                if (!total) lines.innerHTML = '<div class="text-muted small">Belum ada barang dipilih.</div>';
                document.getElementById('totalQty').textContent = total;
                document.getElementById('submitRequest').disabled = total === 0;
                document.getElementById('mobileRequestTotal').textContent = total;
                document.querySelectorAll('.js-qty').forEach(el => el.textContent = cart[el.dataset.id] || 0);
            };
            mobileAction?.addEventListener('click', () => {
                const target = summaryIsVisible ? searchInput : summary;
                target?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
            if (summary && mobileAction && 'IntersectionObserver' in window) {
                new IntersectionObserver(entries => {
                    summaryIsVisible = entries[0].isIntersecting;
                    mobileActionLabel.textContent = summaryIsVisible ? 'Cari Barang' : 'Ringkasan';
                    mobileActionIcon.className = summaryIsVisible ?
                        'fas fa-search me-1' :
                        'fas fa-cart-arrow-down me-1';
                }, {
                    threshold: .35
                }).observe(summary);
            }
            document.querySelectorAll('.js-plus').forEach(btn => btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                cart[id] = (cart[id] || 0) < stock[id] ? (cart[id] || 0) + 1 : (cart[id] || 0);
                render();
            }));
            document.querySelectorAll('.js-minus').forEach(btn => btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                cart[id] = Math.max(0, (cart[id] || 0) - 1);
                if (!cart[id]) delete cart[id];
                render();
            }));
        });
    </script>
@endsection
