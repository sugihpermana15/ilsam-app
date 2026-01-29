@extends('layouts.master')

@section('title', __('assets.history_delete.title') . ' - Ilsam')
@section('title-sub', __('assets.management'))
@section('pagetitle', __('assets.history_delete.title'))
@section('css')
  <!--datatable css-->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <!--datatable responsive css-->
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
@endsection
@section('content')
@php
  $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'assets_data', 'update');
@endphp
<div id="layout-wrapper">
  <div class="row">
    {{-- SweetAlert2 notification --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
          Swal.fire({
            icon: 'success',
            title: @json(__('common.success')),
            text: @json(session('success')),
            timer: 2000,
            showConfirmButton: false
          });
        @endif
        @if(session('error'))
          Swal.fire({
            icon: 'error',
            title: @json(__('common.error')),
            text: @json(session('error')),
            timer: 2500,
            showConfirmButton: false
          });
        @endif
                });
    </script>
    <div class="col-12">
      <div class="card">
        <!--start::card-->
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('assets.history_delete.title') }}</h5>
        </div>
        <div class="card-body">
          <form method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="{{ __('assets.history_delete.search_placeholder') }}"
                value="{{ $search ?? '' }}">
                <button class="btn btn-primary" type="submit">{{ __('common.search') }}</button>
            </div>
          </form>
          <div class="table-responsive" style="overflow-x:auto;">
            <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
              <thead>
                <tr>
                  <th>{{ __('assets.history_delete.table.id') }}</th>
                  <th>{{ __('assets.fields.asset_code') }}</th>
                  <th>{{ __('assets.fields.asset_name') }}</th>
                  <th>{{ __('assets.fields.category') }}</th>
                  <th>{{ __('assets.fields.qty') }}</th>
                  <th>{{ __('assets.fields.uom') }}</th>
                  <th>{{ __('assets.fields.deleted_at') }}</th>
                  <th>{{ __('assets.fields.deleted_by') }}</th>
                  <th>{{ __('assets.history_delete.table.action') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse($deletedAssets as $asset)
                <tr>
                  <td>{{ $asset->id }}</td>
                  <td>{{ $asset->asset_code }}</td>
                  <td>{{ $asset->asset_name }}</td>
                  <td>{{ $asset->asset_category }}</td>
                  <td>{{ $asset->qty ?? '-' }}</td>
                  <td>{{ $asset->satuan ?? '-' }}</td>
                  <td>{{ $asset->deleted_at ? \Carbon\Carbon::parse($asset->deleted_at)->format('d-m-Y H:i') : '-' }}
                  </td>
                  <td>
                    @php
                      $deletedBy = $asset->deleted_by ? DB::table('users')->where('id', $asset->deleted_by)->first() : null;
                    @endphp
                    {{ $deletedBy ? $deletedBy->name : '-' }}
                  </td>
                  <td>
                    <form action="{{ route('admin.assets.restore', $asset->id) }}" method="POST"
                      style="display:inline-block" class="form-restore-asset">
                      @csrf
                      <button type="button" class="btn btn-sm btn-success btn-restore-asset" {{ $canUpdate ? '' : 'disabled' }} title="{{ $canUpdate ? __('assets.history_delete.restore') : __('assets.actions.no_access_update') }}">{{ __('assets.history_delete.restore') }}</button>
                    </form>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="9" class="text-center text-muted">{{ __('assets.history_delete.empty') }}</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="mt-3">
            {{ $deletedAssets->links() }}
          </div>
        </div>
      </div>
      <!--end::card-->
    </div>
  </div><!--End row-->
</div><!--End container-fluid-->
</main><!--End app-wrapper-->
@endsection

@section('js')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="{{ asset('assets/js/table/datatable.init.js') }}"></script>
  <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
  <script>
    $(document).ready(function () {
      if ($.fn.DataTable.isDataTable('#alternative-pagination')) {
        $('#alternative-pagination').DataTable().destroy();
      }
      $('#alternative-pagination').DataTable({
        responsive: true,
        scrollX: true,
        paging: false,
        searching: false,
        info: false
      });

      // SweetAlert2 for restore confirmation
      $(document).on('click', '.btn-restore-asset', function (e) {
        e.preventDefault();
        var form = $(this).closest('form');
        Swal.fire({
          title: @json(__('assets.history_delete.alerts.restore_confirm_title')),
          text: @json(__('assets.history_delete.alerts.restore_confirm_text')),
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: @json(__('common.ok')),
          cancelButtonText: @json(__('common.cancel'))
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      });
    });
  </script>
@endsection