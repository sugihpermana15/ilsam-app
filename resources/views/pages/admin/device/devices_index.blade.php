@extends('layouts.master')

@section('title', 'Master Device | IGI')
@section('title-sub', 'Master Device')
@section('pagetitle', 'Master Device')

@section('css')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
@endsection

@section('content')
  @php
    $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'devices', 'create');
    $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'devices', 'update');
    $canDelete = \App\Support\MenuAccess::can(auth()->user(), 'devices', 'delete');
  @endphp

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      @if(session('success'))
        Swal.fire({ icon: 'success', title: @json(__('common.success')), text: @json(session('success')), timer: 2000, showConfirmButton: false });
      @endif
      @if(session('error'))
        Swal.fire({ icon: 'error', title: @json(__('common.error')), text: @json(session('error')), timer: 2500, showConfirmButton: false });
      @endif

      document.querySelectorAll('form[data-confirm-submit]')?.forEach(function (form) {
        form.addEventListener('submit', async function (e) {
          e.preventDefault();

          const title = form.getAttribute('data-confirm-title') || 'Konfirmasi';
          const text = form.getAttribute('data-confirm-text') || 'Yakin lanjutkan aksi ini?';
          const confirmText = form.getAttribute('data-confirm-button') || 'Ya, lanjutkan';

          const result = await Swal.fire({
            icon: 'warning',
            title: title,
            text: text,
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
          });

          if (result.isConfirmed) {
            form.submit();
          }
        });
      });
    });
  </script>

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <h5 class="card-title mb-0">Master Device</h5>
        <div class="text-muted small">Kelola data device/komputer terhubung dengan Asset.</div>
      </div>
      <a href="{{ route('admin.devices.create') }}" class="btn btn-success" {{ $canCreate ? '' : 'disabled' }}>
        <i class="fas fa-plus"></i> {{ __('common.add') }}
      </a>
    </div>
    <div class="card-body">
      @if(($devices ?? collect())->isEmpty())
        <div class="alert alert-info mb-3">Belum ada data device.</div>
      @endif

      <div class="table-responsive">
        <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
          <thead>
            <tr>
              <th>#</th>
              <th>Kode Asset</th>
              <th>Asset Name</th>
              <th>Device Name</th>
              <th>User/PIC</th>
              <th>Location</th>
              <th>Last Maintenance</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach(($devices ?? collect()) as $device)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="fw-semibold">{{ $device->asset_code ?? '-' }}</td>
                <td>{{ $device->asset_name ?? '-' }}</td>
                <td>{{ $device->device_name ?? '-' }}</td>
                <td>{{ $device->asset_person_in_charge ?? '-' }}</td>
                <td>{{ $device->asset_location ?? '-' }}</td>
                <td>{{ $device->last_maintenance_at ? $device->last_maintenance_at->format('d-m-Y H:i') : '-' }}</td>
                <td class="text-nowrap">
                  <a href="{{ route('admin.devices.show', $device) }}" class="btn btn-sm btn-info" title="{{ __('common.details') }}">
                    <i class="fas fa-eye"></i>
                  </a>
                  <a href="{{ route('admin.devices.edit', $device) }}" class="btn btn-sm btn-warning" {{ $canUpdate ? '' : 'disabled' }} title="{{ __('common.edit') }}">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form
                    action="{{ route('admin.devices.destroy', $device) }}"
                    method="POST"
                    style="display:inline-block"
                    data-confirm-submit
                    data-confirm-title="Hapus Device"
                    data-confirm-text="Yakin hapus device ini? Data yang sudah dihapus tidak bisa dikembalikan."
                    data-confirm-button="Ya, hapus"
                  >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" {{ $canDelete ? '' : 'disabled' }} title="{{ __('common.delete') }}">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection

@section('js')
  <script src="{{ asset('assets/js/vendor/jquery-3.7.1.min.js') }}"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap.min.js"></script>
  <script src="{{ asset('assets/js/table/datatable.init.js') }}"></script>
@endsection
