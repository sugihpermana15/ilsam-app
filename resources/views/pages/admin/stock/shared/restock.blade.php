@section('title', 'Ilsam - Stok Masuk ' . strtoupper($category))
@section('title-sub', 'Administrasi')
@section('pagetitle', 'Stok Masuk ' . strtoupper($category))
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .stock-balance-item {
            gap: 1rem;
            padding: .75rem 1rem;
        }

        .stock-balance-name {
            min-width: 0;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .stock-balance-qty {
            flex: 0 0 auto;
            color: #344054;
        }

        .stock-balance-empty {
            display: none;
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
                                <select name="stock_item_id" class="form-select" required>
                                    <option value="">-- pilih barang --</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->id }}" @selected(old('stock_item_id') == $item->id)>
                                            {{ $item->name }} (saldo {{ $item->selected_stock }} {{ $item->unit }})
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
                    <div id="stockBalanceList" class="list-group list-group-flush">
                        @foreach ($items as $item)
                            <div class="stock-balance-item list-group-item d-flex justify-content-between align-items-center"
                                data-search="{{ strtolower($item->code . ' ' . $item->name . ' ' . $item->unit) }}">
                                <span class="stock-balance-name" title="{{ $item->name }}">{{ $item->name }}</span>
                                <strong class="stock-balance-qty">{{ $item->selected_stock }} {{ $item->unit }}</strong>
                            </div>
                        @endforeach
                    </div>
                    <div id="stockBalanceEmpty" class="stock-balance-empty p-3 text-center text-muted small">
                        Barang tidak ditemukan.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('stockBalanceSearch');
            const emptyState = document.getElementById('stockBalanceEmpty');

            searchInput?.addEventListener('input', () => {
                const keyword = searchInput.value.trim().toLowerCase();
                let visibleItems = 0;

                document.querySelectorAll('#stockBalanceList [data-search]').forEach(item => {
                    const visible = item.dataset.search.includes(keyword);
                    item.classList.toggle('d-none', !visible);
                    visibleItems += visible ? 1 : 0;
                });

                emptyState.style.display = visibleItems || !keyword ? 'none' : 'block';
            });
        });
    </script>
@endsection
