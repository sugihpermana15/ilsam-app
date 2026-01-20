@extends('layouts.master')

@section('title', 'Rekonsiliasi Stok Seragam | IGI')
@section('title-sub', ' Dashboard Rekonsiliasi Stok Seragam ')
@section('pagetitle', 'Rekonsiliasi Stok Seragam')

@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
@endsection

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div
          class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
            <h5 class="card-title mb-0"> Rekonsiliasi current_stock vs Sisa Lot </h5>
          <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.uniforms.lots') }}" class="btn btn-outline-primary btn-sm"><i
              class="fas fa-layer-group"></i> Lot</a>
            <a href="{{ route('admin.uniforms.adjustments') }}" class="btn btn-outline-primary btn-sm"><i
              class="fas fa-sliders"></i> Penyesuaian</a>
            <a href="{{ route('admin.uniforms.history') }}" class="btn btn-outline-secondary btn-sm"><i
              class="fas fa-clock"></i> Riwayat</a>
          </div>
        </div>
        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
          @endif

          <div class="alert alert-info">
            <strong>Catatan audit:</strong> halaman ini tidak mengubah stok otomatis. Jika ada selisih, sistem membuat
            <em>Permintaan Penyesuaian (Menunggu)</em> agar ada jejak persetujuan.
          </div>

          <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
            <thead>
              <tr>
                <th>No</th>
                <th>Item</th>
                <th>Ukuran</th>
                <th>Lokasi</th>
                <th>Stok (cache)</th>
                <th>Total Sisa Lot</th>
                <th>Selisih</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($items as $item)
                @php
                  $lotSum = (int) ($item->lots_remaining_sum ?? 0);
                  $current = (int) ($item->current_stock ?? 0);
                  $diff = $lotSum - $current;
                @endphp
                <tr class="{{ $diff === 0 ? '' : ($diff > 0 ? 'table-warning' : 'table-danger') }}">
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $item->item_code }} - {{ $item->item_name }}</td>
                  <td>{{ $item->sizeMaster?->code ?? $item->size ?? '-' }}</td>
                  <td>{{ $item->location }}</td>
                  <td>{{ $current }}</td>
                  <td>{{ $lotSum }}</td>
                  <td><strong>{{ $diff }}</strong></td>
                  <td>
                    @if($diff !== 0)
                      <form method="POST" action="{{ route('admin.uniforms.reconcile.adjustment') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="uniform_item_id" value="{{ $item->id }}">
                        <input type="hidden" name="diff" value="{{ $diff }}">
                        <input type="hidden" name="reason" value="Rekonsiliasi (otomatis) untuk item {{ $item->item_code }}">
                        <button type="submit" class="btn btn-sm btn-primary"
                          onclick="return confirm('Buat permintaan penyesuaian untuk selisih {{ $diff }}?')">Buat Permintaan</button>
                      </form>
                    @else
                      <span class="text-muted">OK</span>
                    @endif
                  </td>
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
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
  <script src="{{ asset('assets/js/table/datatable.init.js') }}"></script>
  <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection