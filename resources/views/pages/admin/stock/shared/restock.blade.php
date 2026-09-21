@section('title', 'Ilsam - Stok Masuk ' . strtoupper($category))
@section('title-sub', 'Administrasi')
@section('pagetitle', 'Stok Masuk ' . strtoupper($category))
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <style>
        .stock-page-content {
            padding-bottom: 1.5rem;
        }

        .stock-balance-card {
            overflow: hidden;
        }

        .stock-balance-search {
            position: relative;
        }

        .stock-balance-search .form-control {
            padding-left: 2.25rem;
        }

        .stock-balance-search-icon {
            position: absolute;
            top: 50%;
            left: .75rem;
            z-index: 2;
            color: #98a2b3;
            pointer-events: none;
            transform: translateY(-50%);
        }

        .stock-balance-list {
            max-height: 31.6rem;
            overflow-y: auto;
            scrollbar-gutter: stable;
        }

        .stock-balance-table {
            margin-bottom: 0;
            table-layout: fixed;
        }

        .stock-balance-table thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            padding: .7rem .875rem;
            background-color: var(--bs-tertiary-bg, #f8f9fa);
            box-shadow: inset 0 -1px 0 var(--bs-border-color);
            color: #667085;
            font-size: .6875rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .stock-balance-table tbody td {
            padding: .8rem .875rem;
            vertical-align: middle;
        }

        .stock-balance-number {
            width: 2.75rem;
            color: #98a2b3;
            font-size: .75rem;
        }

        .stock-balance-table th:last-child,
        .stock-balance-table td:last-child {
            width: 7.25rem;
        }

        .stock-balance-name {
            min-width: 0;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .stock-balance-code {
            display: block;
            margin-top: .2rem;
            color: #98a2b3;
            font-size: .75rem;
        }

        .stock-balance-qty {
            color: #344054;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .stock-balance-empty {
            display: none;
        }

        .select2-container--bootstrap-5 .select2-selection {
            border-color: var(--bs-border-color);
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 .25rem rgba(var(--bs-primary-rgb), .15);
        }

        .stock-item-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .stock-item-option-code {
            display: block;
            margin-top: .125rem;
            color: #98a2b3;
            font-size: .75rem;
        }

        .stock-item-option-balance {
            flex: 0 0 auto;
            color: #475467;
            font-size: .75rem;
            font-weight: 600;
            white-space: nowrap;
        }

    </style>
@endsection
@section('content')
    <div class="stock-page-content row g-4">
        <div class="col-xl-8 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tambah Stok {{ strtoupper($category) }}</h5>
                    <a class="btn btn-outline-secondary btn-sm"
                        href="{{ route('admin.stock.master.index', ['category' => $category]) }}"><i
                            class="fas fa-database"></i> Master</a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="GET" action="{{ route('admin.stock.restock.index', ['category' => $category]) }}"
                        class="mb-4">
                        <label class="form-label">Site Tujuan</label>
                        <select name="site" class="form-select" onchange="this.form.submit()">
                            @foreach ($sites as $code => $label)
                                <option value="{{ $code }}" @selected($currentSite === $code)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <form method="POST" action="{{ route('admin.stock.restock.store', ['category' => $category]) }}">
                        @csrf
                        <input type="hidden" name="site_code" value="{{ $currentSite }}">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Pilih Barang</label>
                                <select id="stockItemSelect" name="stock_item_id" class="form-select" required>
                                    <option value="">-- pilih barang --</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->id }}" data-code="{{ $item->code }}"
                                            data-balance="{{ $item->selected_stock }} {{ $item->unit }}"
                                            @selected(old('stock_item_id') == $item->id)>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4"><label class="form-label">Jumlah Stok (Qty IN)</label><input
                                    name="qty" type="number" min="1" class="form-control"
                                    value="{{ old('qty') }}" required></div>
                            <div class="col-md-4"><label class="form-label">Tanggal</label><input name="trx_date"
                                    type="date" class="form-control"
                                    value="{{ old('trx_date', now()->toDateString()) }}" required></div>
                            <div class="col-md-4"><label class="form-label">Keterangan / Catatan</label><input
                                    name="notes" class="form-control" value="{{ old('notes') }}"></div>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mt-4">
                            <button class="btn btn-success"><i class="fas fa-save me-1"></i>Simpan Stok Masuk</button>
                            <a class="btn btn-light"
                                href="{{ route('admin.stock.ledger.index', ['category' => $category, 'site' => $currentSite]) }}">
                                <i class="fas fa-book me-1"></i>Lihat Ledger
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-12">
            <div class="card stock-balance-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Saldo Aktif - {{ $siteLabel }}</h6>
                    <span class="badge bg-light text-dark border">{{ $items->count() }} Barang</span>
                </div>
                <div class="card-body border-bottom py-2">
                    <div class="stock-balance-search">
                        <i class="fas fa-search stock-balance-search-icon"></i>
                        <input id="stockBalanceSearch" type="search" class="form-control form-control-sm"
                            placeholder="Cari saldo barang" autocomplete="off">
                    </div>
                </div>
                <div class="stock-balance-list">
                    <table class="table table-hover stock-balance-table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="stock-balance-number">No.</th>
                                <th scope="col">Barang</th>
                                <th scope="col" class="text-end">Saldo</th>
                            </tr>
                        </thead>
                        <tbody id="stockBalanceList">
                            @foreach ($items as $item)
                                <tr data-search="{{ strtolower($item->code . ' ' . $item->name . ' ' . $item->unit) }}">
                                    <td class="stock-balance-number">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="stock-balance-name" title="{{ $item->name }}">{{ $item->name }}</div>
                                        @if ($item->code)
                                            <span class="stock-balance-code">{{ $item->code }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end"><strong class="stock-balance-qty">{{ $item->selected_stock }} {{ $item->unit }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div id="stockBalanceEmpty" class="stock-balance-empty p-3 text-center text-muted small">
                        Barang tidak ditemukan.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const stockItemSelect = window.jQuery ? window.jQuery('#stockItemSelect') : null;

            if (stockItemSelect?.length && window.jQuery.fn.select2) {
                const formatStockItem = option => {
                    if (!option.id) {
                        return option.text;
                    }

                    const element = option.element;
                    const content = window.jQuery('<div>', { class: 'stock-item-option' });
                    const details = window.jQuery('<div>');

                    details.append(window.jQuery('<div>').text(option.text));
                    details.append(window.jQuery('<span>', { class: 'stock-item-option-code' }).text(element.dataset.code));
                    content.append(details);
                    content.append(window.jQuery('<span>', { class: 'stock-item-option-balance' }).text(element.dataset.balance));

                    return content;
                };

                stockItemSelect.select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: '-- pilih barang --',
                    templateResult: formatStockItem,
                });
            }

            const searchInput = document.getElementById('stockBalanceSearch');
            const emptyState = document.getElementById('stockBalanceEmpty');

            searchInput?.addEventListener('input', () => {
                const keyword = searchInput.value.trim().toLowerCase();
                let visibleItems = 0;

                document.querySelectorAll('#stockBalanceList tr[data-search]').forEach(item => {
                    const visible = item.dataset.search.includes(keyword);
                    item.classList.toggle('d-none', !visible);
                    visibleItems += visible ? 1 : 0;
                });

                emptyState.style.display = visibleItems || !keyword ? 'none' : 'block';
            });
        });
    </script>
@endsection
