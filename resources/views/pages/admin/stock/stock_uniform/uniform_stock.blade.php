@extends('layouts.master')

@section('title', 'Stok Masuk Seragam | IGI')
@section('title-sub', ' Dashboard Stok Masuk Seragam ')
@section('pagetitle', 'Stok Masuk Seragam')

@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
  <style>
    .igi-actions {
      display: flex;
      gap: .5rem;
      flex-wrap: wrap;
    }

    .igi-actions .btn {
      white-space: nowrap;
    }

    @media (max-width: 575.98px) {
      .igi-actions {
        flex-direction: column;
        width: 100%;
      }

      .igi-actions .btn {
        width: 100%;
      }
    }

    /* Select2 -> follow Bootstrap primary theme */
    .select2-container--bootstrap-5 .select2-selection {
      border-color: var(--bs-border-color);
    }

    .select2-container--bootstrap-5.select2-container--open .select2-selection,
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5 .select2-selection:focus {
      border-color: var(--bs-primary);
      box-shadow: 0 0 0 .25rem rgba(var(--bs-primary-rgb), .25);
    }

    .select2-container--bootstrap-5 .select2-dropdown .select2-results__option--highlighted {
      background-color: var(--bs-primary);
      color: #fff;
    }

    .select2-container--bootstrap-5 .select2-dropdown .select2-results__option[aria-selected="true"] {
      background-color: rgba(var(--bs-primary-rgb), .12);
      color: inherit;
    }
  </style>
@endsection

@section('content')
  <div class="row">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
          Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('success')), timer: 2000, showConfirmButton: false });
        @endif
        @if(session('error'))
          Swal.fire({ icon: 'error', title: 'Gagal', text: @json(session('error')), timer: 2500, showConfirmButton: false });
        @endif
                            });
    </script>

    <div class="col-12">
      <div class="card">
        <div
          class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
          <h5 class="card-title mb-0"> Stok Masuk Seragam </h5>
          <div class="igi-actions">
            <a href="{{ route('admin.uniforms.master') }}" class="btn btn-secondary btn-sm"><i class="fas fa-list"></i>
              Master</a>
            <a href="{{ route('admin.uniforms.distribution') }}" class="btn btn-primary btn-sm"><i
                class="fas fa-people-carry-box"></i> Distribusi</a>
          </div>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.uniforms.stock.in') }}" class="row g-3">
            @csrf
            <div class="col-md-6">
              <label class="form-label">Item</label>
              <select name="uniform_item_id" class="form-select js-item-select" required>
                <option value="">-- pilih item --</option>
                @foreach($items as $item)
                  <option value="{{ $item->id }}">{{ $item->item_name }} - {{ $item->sizeMaster?->code ?? $item->size ?? '-' }} - {{ $item->location }}
                  </option>
                @endforeach
              </select>
              <small class="text-muted">Klik dropdown lalu ketik untuk mencari item.</small>
            </div>
            <div class="col-md-2">
              <label class="form-label">Qty</label>
              <input type="number" name="qty" class="form-control" min="1" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Lot / Batch (opsional)</label>
              <input type="text" name="lot_number" class="form-control" placeholder="ex: LOT-2026-01">
            </div>
            <div class="col-md-4">
              <label class="form-label">Tanggal Kedaluwarsa (opsional)</label>
              <input type="date" name="expired_at" class="form-control">
            </div>
            <div class="col-md-8">
              <label class="form-label">Catatan</label>
              <input type="text" name="notes" class="form-control"
                placeholder="ex: pembelian vendor X / penambahan stok tahunan">
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Stok Masuk</button>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0"> Riwayat Transaksi Terbaru </h5>
          <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.uniforms.lots') }}" class="btn btn-outline-primary btn-sm">Lot</a>
            <a href="{{ route('admin.uniforms.reconcile') }}" class="btn btn-outline-primary btn-sm">Rekonsiliasi</a>
            <a href="{{ route('admin.uniforms.adjustments') }}" class="btn btn-outline-primary btn-sm">Penyesuaian</a>
            <a href="{{ route('admin.uniforms.writeoffs') }}" class="btn btn-outline-danger btn-sm">Penghapusan</a>
            <a href="{{ route('admin.uniforms.history') }}" class="btn btn-outline-secondary btn-sm">Buka Riwayat Lengkap</a>
          </div>
        </div>
        <div class="card-body">
          @php
            $uniformMovementTypeLabels = [
              'IN' => 'Stok Masuk',
              'OUT' => 'Distribusi',
              'RETURN' => 'Retur',
              'ADJUSTMENT_IN' => 'Penyesuaian Masuk',
              'ADJUSTMENT_OUT' => 'Penyesuaian Keluar',
              'WRITE_OFF' => 'Penghapusan',
              'REPLACEMENT' => 'Penggantian',
            ];

            $uniformMovementTypeBadge = function (?string $movementType): string {
            $movementType = (string) ($movementType ?? '');

              return match ($movementType) {
                'IN', 'ADJUSTMENT_IN' => 'bg-success-subtle text-success',
                'OUT', 'ADJUSTMENT_OUT', 'WRITE_OFF', 'REPLACEMENT' => 'bg-danger-subtle text-danger',
                'RETURN' => 'bg-info-subtle text-info',
                default => 'bg-secondary-subtle text-secondary',
              };
            };
          @endphp

          <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Item</th>
                <th>Ukuran</th>
                <th>Perubahan Qty</th>
                <th>Lot</th>
                <th>Kedaluwarsa</th>
                <th>Oleh</th>
                <th>Catatan</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentMovements as $m)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $m->performed_at ? \Carbon\Carbon::parse($m->performed_at)->format('d-m-Y H:i') : '-' }}</td>
                  <td>
                    <span
                      class="badge {{ $uniformMovementTypeBadge((string) $m->movement_type) }}">
                      {{ $uniformMovementTypeLabels[$m->movement_type] ?? $m->movement_type }}
                    </span>
                  </td>
                  <td>{{ $m->item?->item_name ?? '-' }}</td>
                  <td>{{ $m->item?->sizeMaster?->code ?? $m->item?->size ?? '-' }}</td>
                  <td>{{ $m->qty_change }}</td>
                  <td>{{ $m->lot_number ?? '-' }}</td>
                  <td>{{ $m->expired_at ? \Carbon\Carbon::parse($m->expired_at)->format('d-m-Y') : '-' }}</td>
                  <td>{{ $m->performedBy?->name ?? '-' }}</td>
                  <td>{{ $m->notes ?? '-' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('js')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
  <script src="{{ asset('assets/js/table/datatable.init.js') }}"></script>
  <script type="module" src="{{ asset('assets/js/app.js') }}"></script>

  <script>
    $(function () {
      const $select = $('.js-item-select');
      if ($select.length && $.fn.select2) {
        $select.select2({
          theme: 'bootstrap-5',
          width: '100%',
          placeholder: '-- pilih item --',
          allowClear: true,
        });
      }
    });
  </script>
@endsection