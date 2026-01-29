@extends('layouts.master')

@section('title', __('uniforms.reconcile.page_title'))
@section('title-sub', __('uniforms.reconcile.title_sub'))
@section('pagetitle', __('uniforms.reconcile.pagetitle'))

@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
@endsection

@section('content')
  @php
    $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'uniforms_stock', 'create');
  @endphp
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div
          class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
            <h5 class="card-title mb-0">{{ __('uniforms.reconcile.card_title') }}</h5>
          <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.uniforms.lots') }}" class="btn btn-outline-primary btn-sm"><i
              class="fas fa-layer-group"></i> {{ __('uniforms.nav.lots') }}</a>
            <a href="{{ route('admin.uniforms.adjustments') }}" class="btn btn-outline-primary btn-sm"><i
              class="fas fa-sliders"></i> {{ __('uniforms.nav.adjustments') }}</a>
            <a href="{{ route('admin.uniforms.history') }}" class="btn btn-outline-secondary btn-sm"><i
              class="fas fa-clock"></i> {{ __('uniforms.nav.history') }}</a>
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
            <strong>{{ __('uniforms.reconcile.audit_note_title') }}</strong> {{ __('uniforms.reconcile.audit_note_text') }}
          </div>

          <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
            <thead>
              <tr>
                <th>{{ __('common.no') }}</th>
                <th>{{ __('common.item') }}</th>
                <th>{{ __('common.size') }}</th>
                <th>{{ __('common.location') }}</th>
                <th>{{ __('uniforms.reconcile.table.stock_cache') }}</th>
                <th>{{ __('uniforms.reconcile.table.total_lots_remaining') }}</th>
                <th>{{ __('uniforms.reconcile.table.diff') }}</th>
                <th>{{ __('common.action') }}</th>
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
                        <input type="hidden" name="reason" value="{{ __('uniforms.reconcile.auto_reason', ['item_code' => $item->item_code]) }}">
                        <button type="submit" class="btn btn-sm btn-primary"
                          onclick="return confirm(@json(__('uniforms.reconcile.confirm_create_request', ['diff' => $diff])))" {{ $canCreate ? '' : 'disabled' }} title="{{ $canCreate ? '' : __('common.no_access_create') }}">{{ __('uniforms.reconcile.action_create_request') }}</button>
                      </form>
                    @else
                      <span class="text-muted">{{ __('common.ok') }}</span>
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