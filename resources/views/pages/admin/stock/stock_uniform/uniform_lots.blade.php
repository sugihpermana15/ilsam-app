@extends('layouts.master')

@section('title', __('uniforms.lots.page_title'))
@section('title-sub', __('uniforms.lots.title_sub'))
@section('pagetitle', __('uniforms.lots.pagetitle'))

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
          <h5 class="card-title mb-0">{{ __('uniforms.lots.card_title') }}</h5>
          <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.uniforms.history') }}" class="btn btn-outline-secondary btn-sm"><i
                class="fas fa-clock"></i> {{ __('uniforms.nav.history') }}</a>
            <a href="{{ route('admin.uniforms.reconcile') }}" class="btn btn-outline-primary btn-sm"><i
                class="fas fa-scale-balanced"></i> {{ __('uniforms.nav.reconcile') }}</a>
          </div>
        </div>
        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
          @endif

          <form method="GET" action="{{ route('admin.uniforms.lots') }}" class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">{{ __('uniforms.lots.filters.item') }}</label>
              <select name="uniform_item_id" class="form-select">
                <option value="">{{ __('uniforms.lots.filters.all_items') }}</option>
                @foreach($items as $item)
                  <option value="{{ $item->id }}" @selected(request('uniform_item_id') == $item->id)>
                    {{ $item->item_code }} - {{ $item->item_name }} - {{ $item->sizeMaster?->code ?? $item->size ?? '-' }} - {{ $item->location }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">{{ __('uniforms.lots.filters.location') }}</label>
              <select name="location" class="form-select">
                <option value="">{{ __('uniforms.lots.filters.all') }}</option>
                <option value="Jababeka" @selected(request('location') === 'Jababeka')>Jababeka</option>
                <option value="Karawang" @selected(request('location') === 'Karawang')>Karawang</option>
              </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <button class="btn btn-primary w-100" type="submit"><i class="fas fa-filter"></i> {{ __('uniforms.lots.filters.submit') }}</button>
            </div>
          </form>

          <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
            <thead>
              <tr>
                <th>{{ __('common.no') }}</th>
                <th>{{ __('common.item') }}</th>
                <th>{{ __('common.size') }}</th>
                <th>{{ __('uniforms.lots.table.lot_number') }}</th>
                <th>{{ __('uniforms.lots.table.qty_in') }}</th>
                <th>{{ __('uniforms.lots.table.remaining') }}</th>
                <th>{{ __('uniforms.lots.table.received_at') }}</th>
                <th>{{ __('common.expired_at') }}</th>
                <th>{{ __('uniforms.lots.table.received_by') }}</th>
                <th>{{ __('common.notes') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($lots as $lot)
                @php
                  $expired = $lot->expired_at && \Carbon\Carbon::parse($lot->expired_at)->isPast();
                  $near = $lot->expired_at && !$expired && \Carbon\Carbon::parse($lot->expired_at)->diffInDays(now()) <= 30;
                @endphp
                <tr class="{{ $expired ? 'table-danger' : ($near ? 'table-warning' : '') }}">
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $lot->item?->item_code }} - {{ $lot->item?->item_name }} ({{ $lot->item?->location }})</td>
                  <td>{{ $lot->item?->sizeMaster?->code ?? $lot->item?->size ?? '-' }}</td>
                  <td>{{ $lot->lot_number }}</td>
                  <td>{{ $lot->qty_in }}</td>
                  <td>{{ $lot->remaining_qty }}</td>
                  <td>{{ $lot->received_at ? \Carbon\Carbon::parse($lot->received_at)->format('d-m-Y H:i') : '-' }}</td>
                  <td>{{ $lot->expired_at ? \Carbon\Carbon::parse($lot->expired_at)->format('d-m-Y') : '-' }}</td>
                  <td>{{ $lot->receivedBy?->name ?? '-' }}</td>
                  <td>{{ $lot->notes ?? '-' }}</td>
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