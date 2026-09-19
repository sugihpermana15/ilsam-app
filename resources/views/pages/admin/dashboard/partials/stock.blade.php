@php
  $stockKpi = $stock['kpi'] ?? [];
  $lowStockItems = $stock['lowStockItems'] ?? collect();
  $recentTransactions = $stock['recentTransactions'] ?? collect();
@endphp

<div class="d-flex flex-wrap gap-2 justify-content-end mb-3">
  <a href="{{ route('admin.stock.master.index', ['category' => 'atk']) }}" class="btn btn-outline-secondary btn-sm">
    <i class="fas fa-list"></i> Master Stok
  </a>
  <a href="{{ route('admin.stock.ledger.index', ['category' => 'atk']) }}" class="btn btn-outline-primary btn-sm">
    <i class="fas fa-book"></i> Ledger ATK
  </a>
</div>

<div class="row g-3 row-cols-1 row-cols-md-2 row-cols-xl-3 row-cols-xxl-5">
  @foreach([
    ['Total Item', $stockKpi['total_items'] ?? 0, 'text-primary', 'fa-boxes-stacked'],
    ['Item Aktif', $stockKpi['active_items'] ?? 0, 'text-success', 'fa-circle-check'],
    ['Stok On Hand', $stockKpi['total_on_hand'] ?? 0, 'text-info', 'fa-cubes'],
    ['Nilai Persediaan', 'Rp ' . number_format((int) ($stockKpi['total_value'] ?? 0), 0, ',', '.'), 'text-warning', 'fa-sack-dollar'],
    ['Stok Rendah', $stockKpi['low_stock_items'] ?? 0, 'text-danger', 'fa-triangle-exclamation'],
  ] as [$label, $value, $color, $icon])
    <div class="col">
      <div class="card overflow-hidden h-100 kpi-card">
        <div class="card-body position-relative z-1">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="text-muted">{{ $label }}</div>
              <div class="fs-4 fw-semibold">{{ is_numeric($value) ? number_format((int) $value, 0, ',', '.') : $value }}</div>
            </div>
            <div class="{{ $color }} fs-3"><i class="fas {{ $icon }}"></i></div>
          </div>
        </div>
        <img src="{{ asset('assets/img/dashboard/academy-bg1.png') }}" alt="" class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
      </div>
    </div>
  @endforeach
</div>

<div class="row g-3 mt-1">
  <div class="col-12 col-xl-7">
    <div class="card h-100">
      <div class="card-header"><h6 class="mb-0">Transaksi Stok 30 Hari Terakhir</h6></div>
      <div class="card-body"><div id="stockTransactionsDaily30d" style="min-height: 300px;"></div></div>
    </div>
  </div>
  <div class="col-12 col-xl-5">
    <div class="card h-100">
      <div class="card-header"><h6 class="mb-0">Saldo per Lokasi</h6></div>
      <div class="card-body"><div id="stockBySite" style="min-height: 300px;"></div></div>
    </div>
  </div>
  <div class="col-12 col-xl-5">
    <div class="card h-100">
      <div class="card-header"><h6 class="mb-0">Barang Stok Rendah</h6></div>
      <div class="card-body table-responsive">
        <table class="table table-sm table-striped align-middle mb-0">
          <thead><tr><th>Barang</th><th>Kategori</th><th class="text-end">Stok</th><th class="text-end">Minimum</th></tr></thead>
          <tbody>
            @forelse($lowStockItems as $item)
              <tr><td><div class="fw-semibold">{{ $item->name }}</div><div class="text-muted small">{{ $item->code }}</div></td><td>{{ strtoupper($item->category) }}</td><td class="text-end text-danger">{{ number_format($item->current_stock) }}</td><td class="text-end">{{ number_format($item->low_stock_threshold) }}</td></tr>
            @empty
              <tr><td colspan="4" class="text-center text-muted">Tidak ada stok rendah</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-12 col-xl-7">
    <div class="card h-100">
      <div class="card-header"><h6 class="mb-0">Transaksi Terbaru</h6></div>
      <div class="card-body table-responsive">
        <table class="table table-sm table-striped align-middle mb-0">
          <thead><tr><th>Tanggal</th><th>Barang</th><th>Lokasi</th><th>Tipe</th><th class="text-end">Qty</th><th class="text-end">Saldo</th></tr></thead>
          <tbody>
            @forelse($recentTransactions as $transaction)
              <tr><td>{{ optional($transaction->trx_date)->format('d-m-Y') }}</td><td><div class="fw-semibold">{{ $transaction->item?->name ?? '-' }}</div><div class="text-muted small">{{ $transaction->item?->code }}</div></td><td>{{ ucfirst($transaction->site_code) }}</td><td><span class="badge {{ $transaction->trx_type === 'IN' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ $transaction->trx_type }}</span></td><td class="text-end">{{ number_format($transaction->qty) }}</td><td class="text-end">{{ number_format($transaction->stock_after) }}</td></tr>
            @empty
              <tr><td colspan="6" class="text-center text-muted">Belum ada transaksi</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>