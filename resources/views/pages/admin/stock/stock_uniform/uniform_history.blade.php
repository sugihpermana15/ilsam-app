@extends('layouts.master')

@section('title', __('uniforms.history.page_title'))
@section('title-sub', __('uniforms.history.title_sub'))
@section('pagetitle', __('uniforms.history.pagetitle'))

@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
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
  </style>
@endsection

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div
          class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
          <h5 class="card-title mb-0">{{ __('uniforms.history.card_title') }}</h5>
          <div class="igi-actions">
            <a href="{{ route('admin.uniforms.master') }}" class="btn btn-secondary btn-sm"><i class="fas fa-list"></i>
              {{ __('uniforms.nav.master') }}</a>
            <a href="{{ route('admin.uniforms.stock') }}" class="btn btn-outline-secondary btn-sm"><i
                class="fas fa-warehouse"></i> {{ __('uniforms.nav.stock') }}</a>
            <a href="{{ route('admin.uniforms.distribution') }}" class="btn btn-outline-primary btn-sm"><i
                class="fas fa-people-carry-box"></i> {{ __('uniforms.nav.distribution') }}</a>
            <a href="{{ route('admin.uniforms.lots') }}" class="btn btn-outline-primary btn-sm"><i
              class="fas fa-layer-group"></i> {{ __('uniforms.nav.lots') }}</a>
            <a href="{{ route('admin.uniforms.reconcile') }}" class="btn btn-outline-primary btn-sm"><i
              class="fas fa-scale-balanced"></i> {{ __('uniforms.nav.reconcile') }}</a>
            <a href="{{ route('admin.uniforms.adjustments') }}" class="btn btn-outline-primary btn-sm"><i
                class="fas fa-sliders"></i> {{ __('uniforms.nav.adjustments') }}</a>
            <a href="{{ route('admin.uniforms.writeoffs') }}" class="btn btn-outline-danger btn-sm"><i
                class="fas fa-trash"></i> {{ __('uniforms.nav.writeoffs') }}</a>
          </div>
        </div>
        <div class="card-body">
          @php
            $uniformMovementTypeLabels = [
              'IN' => __('uniforms.movement_types.IN'),
              'OUT' => __('uniforms.movement_types.OUT'),
              'RETURN' => __('uniforms.movement_types.RETURN'),
              'ADJUSTMENT_IN' => __('uniforms.movement_types.ADJUSTMENT_IN'),
              'ADJUSTMENT_OUT' => __('uniforms.movement_types.ADJUSTMENT_OUT'),
              'WRITE_OFF' => __('uniforms.movement_types.WRITE_OFF'),
              'REPLACEMENT' => __('uniforms.movement_types.REPLACEMENT'),
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
                <th>{{ __('common.no') }}</th>
                <th>{{ __('common.date') }}</th>
                <th>{{ __('common.type') }}</th>
                <th>{{ __('common.item') }}</th>
                <th>{{ __('common.size') }}</th>
                <th>{{ __('uniforms.history.table.qty_change') }}</th>
                <th>{{ __('common.employee') }}</th>
                <th>{{ __('uniforms.history.table.issue_id') }}</th>
                <th>{{ __('common.by') }}</th>
                <th>{{ __('common.lot') }}</th>
                <th>{{ __('common.expired_at') }}</th>
                <th>{{ __('common.notes') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($movements as $m)
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
                  <td>{{ $m->issue?->issuedToEmployee?->name ?? $m->issue?->issuedTo?->name ?? '-' }}</td>
                  <td>{{ $m->issue_id ?? '-' }}</td>
                  <td>{{ $m->performedBy?->name ?? '-' }}</td>
                  <td>{{ $m->lot_number ?? '-' }}</td>
                  <td>{{ $m->expired_at ? \Carbon\Carbon::parse($m->expired_at)->format('d-m-Y') : '-' }}</td>
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
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
  <script src="{{ asset('assets/js/table/datatable.init.js') }}"></script>
  <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection