@extends('layouts.master')

@section('title', 'Penyesuaian Stok Seragam | IGI')
@section('title-sub', ' Dashboard Penyesuaian Stok Seragam ')
@section('pagetitle', 'Penyesuaian Stok Seragam')

@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
@endsection

@section('content')
  <div class="row">
    <div class="col-12">
      @php
        $uniformApprovalStatusLabels = [
          'PENDING' => 'Menunggu',
          'APPROVED' => 'Disetujui',
          'REJECTED' => 'Ditolak',
        ];

        $uniformApprovalStatusBadge = function (?string $status): string {
          $status = (string) ($status ?? '');

          return match ($status) {
            'PENDING' => 'bg-warning-subtle text-warning',
            'APPROVED' => 'bg-success-subtle text-success',
            'REJECTED' => 'bg-danger-subtle text-danger',
            default => 'bg-secondary-subtle text-secondary',
          };
        };
      @endphp

      <div class="card">
        <div
          class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
          <h5 class="card-title mb-0"> Permintaan Penyesuaian Stok </h5>
          <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.uniforms.history') }}" class="btn btn-outline-secondary btn-sm"><i
                class="fas fa-clock"></i> Riwayat</a>
            <a href="{{ route('admin.uniforms.writeoffs') }}" class="btn btn-outline-danger btn-sm"><i
                class="fas fa-trash"></i> Penghapusan</a>
          </div>
        </div>
        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
          @endif

          <form method="POST" action="{{ route('admin.uniforms.adjustments.store') }}" class="row g-3">
            @csrf
            <div class="col-md-5">
              <label class="form-label">Item</label>
              <select name="uniform_item_id" class="form-select" required>
                <option value="">-- pilih item --</option>
                @foreach($items as $item)
                  <option value="{{ $item->id }}">{{ $item->item_code }} - {{ $item->item_name }} - {{ $item->sizeMaster?->code ?? $item->size ?? '-' }}
                    - {{ $item->location }}</option>
                @endforeach
              </select>
              <small class="text-muted">Penyesuaian Masuk akan membuat lot ADJ otomatis.</small>
            </div>
            <div class="col-md-3">
              <label class="form-label">Perubahan Qty</label>
              <input type="number" name="qty_change" class="form-control" required placeholder="ex: -2 atau 5">
              <small class="text-muted">Gunakan minus (-) untuk pengurangan.</small>
            </div>
            <div class="col-md-4">
              <label class="form-label">Lot (opsional)</label>
              <select name="lot_id" class="form-select">
                <option value="">FIFO (otomatis) / kosong untuk penyesuaian masuk</option>
                @foreach($lots as $lot)
                  <option value="{{ $lot->id }}">{{ $lot->item?->item_code }} | {{ $lot->lot_number }} | Rem:
                    {{ $lot->remaining_qty }}</option>
                @endforeach
              </select>
              <small class="text-muted">Untuk Penyesuaian Keluar bisa pilih lot spesifik atau FIFO.</small>
            </div>
            <div class="col-12">
              <label class="form-label">Alasan</label>
              <textarea name="reason" class="form-control" rows="2" required
                placeholder="Alasan adjustment (audit)"></textarea>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Ajukan Permintaan</button>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0"> Permintaan Menunggu Persetujuan </h5>
        </div>
        <div class="card-body">
          <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
            <thead>
              <tr>
                <th>No</th>
                <th>Diajukan Pada</th>
                <th>Item</th>
                <th>Ukuran</th>
                <th>Lot</th>
                <th>Qty</th>
                <th>Alasan</th>
                <th>Diajukan Oleh</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($pending as $r)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $r->requested_at ? \Carbon\Carbon::parse($r->requested_at)->format('d-m-Y H:i') : '-' }}</td>
                  <td>{{ $r->item?->item_code }} - {{ $r->item?->item_name }}</td>
                  <td>{{ $r->item?->sizeMaster?->code ?? $r->item?->size ?? '-' }}</td>
                  <td>{{ $r->lot?->lot_number ?? 'FIFO/ADJ' }}</td>
                  <td>{{ $r->qty_change }}</td>
                  <td>{{ $r->reason }}</td>
                  <td>{{ $r->requestedBy?->name ?? '-' }}</td>
                  <td>
                    <form method="POST" action="{{ route('admin.uniforms.adjustments.approve', $r->id) }}" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-success"
                        onclick="return confirm('Setujui permintaan ini?')">Setujui</button>
                    </form>

                    <form method="POST" action="{{ route('admin.uniforms.adjustments.reject', $r->id) }}" class="d-inline">
                      @csrf
                      <input type="hidden" name="rejection_reason" value="Ditolak oleh admin">
                      <button type="submit" class="btn btn-sm btn-outline-danger"
                        onclick="return confirm('Tolak permintaan ini?')">Tolak</button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0"> Riwayat Disetujui / Ditolak </h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-nowrap table-striped table-bordered w-100">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Status</th>
                  <th>Item</th>
                  <th>Ukuran</th>
                  <th>Lot</th>
                  <th>Qty</th>
                  <th>Diajukan Pada</th>
                  <th>Diproses Pada</th>
                  <th>Diproses Oleh</th>
                </tr>
              </thead>
              <tbody>
                @foreach($recent as $r)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                      <span class="badge {{ $uniformApprovalStatusBadge((string) $r->approval_status) }}">
                        {{ $uniformApprovalStatusLabels[$r->approval_status] ?? $r->approval_status }}
                      </span>
                    </td>
                    <td>{{ $r->item?->item_code }} - {{ $r->item?->item_name }}</td>
                    <td>{{ $r->item?->sizeMaster?->code ?? $r->item?->size ?? '-' }}</td>
                    <td>{{ $r->lot?->lot_number ?? '-' }}</td>
                    <td>{{ $r->qty_change }}</td>
                    <td>{{ $r->requested_at ? \Carbon\Carbon::parse($r->requested_at)->format('d-m-Y H:i') : '-' }}</td>
                    <td>{{ $r->approved_at ? \Carbon\Carbon::parse($r->approved_at)->format('d-m-Y H:i') : '-' }}</td>
                    <td>{{ $r->approvedBy?->name ?? '-' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('js')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
  <script src="{{ asset('assets/js/table/datatable.init.js') }}"></script>
  <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection