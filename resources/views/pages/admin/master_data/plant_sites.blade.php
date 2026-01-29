@extends('layouts.master')

@section('title', __('master_data.plant_sites.page_title') . ' | IGI')

@section('title-sub', __('master_data.plant_sites.subtitle'))
@section('pagetitle', __('master_data.plant_sites.pagetitle'))

@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
@endsection

@section('content')
  @php
    $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'plant_sites', 'create');
    $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'plant_sites', 'update');
  @endphp

  <div class="row">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
          Swal.fire({ icon: 'success', title: @json(__('common.success')), text: @json(session('success')), timer: 2000, showConfirmButton: false });
        @endif
        @if(session('error'))
          Swal.fire({ icon: 'error', title: @json(__('common.error')), text: @json(session('error')), timer: 2500, showConfirmButton: false });
        @endif
      });
    </script>

    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0">{{ __('master_data.plant_sites.card_title') }}</h5>
            <div class="text-muted small">{{ __('master_data.plant_sites.description') }}</div>
          </div>
          <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal" {{ $canCreate ? '' : 'disabled' }} title="{{ $canCreate ? '' : __('common.no_access_create') }}">
            <i class="fas fa-plus"></i> {{ __('common.add') }}
          </button>
        </div>

        <div class="card-body">
          <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
            <thead>
              <tr>
                <th>{{ __('common.no') }}</th>
                <th>{{ __('master_data.plant_sites.table.plant_site') }}</th>
                <th>{{ __('master_data.plant_sites.table.name') }}</th>
                <th>{{ __('master_data.plant_sites.table.building') }}</th>
                <th>{{ __('master_data.plant_sites.table.floor') }}</th>
                <th>{{ __('master_data.plant_sites.table.room_rack') }}</th>
                <th>{{ __('common.status') }}</th>
                <th>{{ __('common.action') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($locations as $r)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $r->plant_site }}</td>
                  <td>{{ $r->name ?? '-' }}</td>
                  <td>{{ $r->building ?? '-' }}</td>
                  <td>{{ $r->floor ?? '-' }}</td>
                  <td>{{ $r->room_rack ?? '-' }}</td>
                  <td>
                    <span class="badge {{ $r->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                      {{ $r->is_active ? __('common.active') : __('common.inactive') }}
                    </span>
                  </td>
                  <td>
                    <div class="d-flex gap-2">
                      <button type="button" class="btn btn-sm btn-outline-primary js-edit" data-bs-toggle="modal" {{ $canUpdate ? '' : 'disabled' }}
                        data-bs-target="#editModal"
                        data-update-url="{{ route('admin.plant_sites.update', $r->id) }}"
                        data-id="{{ $r->id }}"
                        data-plant-site="{{ $r->plant_site }}"
                        data-name="{{ $r->name }}"
                        data-building="{{ $r->building }}"
                        data-floor="{{ $r->floor }}"
                        data-room-rack="{{ $r->room_rack }}"
                        data-active="{{ $r->is_active ? '1' : '0' }}">
                        <i class="fas fa-pen"></i>
                      </button>

                      <form action="{{ route('admin.plant_sites.toggle', $r->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $r->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}" {{ $canUpdate ? '' : 'disabled' }} title="{{ $canUpdate ? __('common.change_status') : __('common.no_access_update') }}">
                          <i class="fas {{ $r->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="{{ route('admin.plant_sites.store') }}">
          @csrf
          <input type="hidden" name="modal_context" value="create">
          <div class="modal-header">
            <h5 class="modal-title">{{ __('master_data.plant_sites.add_modal_title') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            @if($errors->any() && old('modal_context') === 'create')
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <label class="form-label">{{ __('master_data.plant_sites.fields.plant_site') }} <span class="text-danger">*</span></label>
            <input type="text" name="plant_site" class="form-control" value="{{ old('plant_site') }}" placeholder="{{ __('master_data.plant_sites.placeholders.plant_site') }}" required>

            <label class="form-label mt-2">{{ __('master_data.plant_sites.fields.name_optional') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="{{ __('master_data.plant_sites.placeholders.name') }}">

            <div class="row g-2 mt-1">
              <div class="col-12 col-md-4">
                <label class="form-label mt-2">{{ __('master_data.plant_sites.fields.building') }}</label>
                <input type="text" name="building" class="form-control" value="{{ old('building') }}">
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label mt-2">{{ __('master_data.plant_sites.fields.floor') }}</label>
                <input type="text" name="floor" class="form-control" value="{{ old('floor') }}">
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label mt-2">{{ __('master_data.plant_sites.fields.room_rack') }}</label>
                <input type="text" name="room_rack" class="form-control" value="{{ old('room_rack') }}">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
            <button type="submit" class="btn btn-success">{{ __('common.save') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @php
    $editRow = old('modal_context') === 'edit'
      ? $locations->firstWhere('id', (int) old('id'))
      : null;
  @endphp

  <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="{{ $editRow ? route('admin.plant_sites.update', $editRow->id) : '#' }}" id="editForm">
          @csrf
          @method('PUT')
          <input type="hidden" name="modal_context" value="edit">
          <input type="hidden" name="id" id="edit_id" value="{{ old('id', $editRow?->id) }}">

          <div class="modal-header">
            <h5 class="modal-title">{{ __('master_data.plant_sites.edit_modal_title') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            @if($errors->any() && old('modal_context') === 'edit')
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <label class="form-label">{{ __('master_data.plant_sites.fields.plant_site') }} <span class="text-danger">*</span></label>
            <input type="text" name="plant_site" class="form-control" id="edit_plant_site" value="{{ old('plant_site', $editRow?->plant_site) }}" required>

            <label class="form-label mt-2">{{ __('master_data.plant_sites.fields.name_optional') }}</label>
            <input type="text" name="name" class="form-control" id="edit_name" value="{{ old('name', $editRow?->name) }}">

            <div class="row g-2 mt-1">
              <div class="col-12 col-md-4">
                <label class="form-label mt-2">{{ __('master_data.plant_sites.fields.building') }}</label>
                <input type="text" name="building" class="form-control" id="edit_building" value="{{ old('building', $editRow?->building) }}">
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label mt-2">{{ __('master_data.plant_sites.fields.floor') }}</label>
                <input type="text" name="floor" class="form-control" id="edit_floor" value="{{ old('floor', $editRow?->floor) }}">
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label mt-2">{{ __('master_data.plant_sites.fields.room_rack') }}</label>
                <input type="text" name="room_rack" class="form-control" id="edit_room_rack" value="{{ old('room_rack', $editRow?->room_rack) }}">
              </div>
            </div>

            <div class="form-check mt-3">
              <input class="form-check-input" type="checkbox" value="1" id="edit_active" name="is_active" {{ old('is_active', $editRow?->is_active) ? 'checked' : '' }}>
              <label class="form-check-label" for="edit_active">{{ __('common.active') }}</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
            <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
          </div>
        </form>
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

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.js-edit').forEach((btn) => {
        btn.addEventListener('click', function () {
          const form = document.getElementById('editForm');
          if (!form) return;

          form.setAttribute('action', btn.dataset.updateUrl);
          document.getElementById('edit_id').value = btn.dataset.id || '';
          document.getElementById('edit_plant_site').value = btn.dataset.plantSite || '';
          document.getElementById('edit_name').value = btn.dataset.name || '';
          document.getElementById('edit_building').value = btn.dataset.building || '';
          document.getElementById('edit_floor').value = btn.dataset.floor || '';
          document.getElementById('edit_room_rack').value = btn.dataset.roomRack || '';
          document.getElementById('edit_active').checked = (btn.dataset.active === '1');
        });
      });

      @if($errors->any() && old('modal_context') === 'create')
        const addModal = document.getElementById('addModal');
        if (addModal) new bootstrap.Modal(addModal).show();
      @endif

      @if($errors->any() && old('modal_context') === 'edit')
        const editModal = document.getElementById('editModal');
        if (editModal) new bootstrap.Modal(editModal).show();
      @endif
    });
  </script>
@endsection
