@extends('layouts.master')

@section('title', __('master_data.asset_vendors.page_title') . ' | IGI')

@section('title-sub', __('master_data.asset_vendors.subtitle'))
@section('pagetitle', __('master_data.asset_vendors.pagetitle'))

@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
@endsection

@section('content')
  @php
    $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'asset_vendors', 'create');
    $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'asset_vendors', 'update');
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
          <h5 class="card-title mb-0">{{ __('master_data.asset_vendors.card_title') }}</h5>
          <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal" {{ $canCreate ? '' : 'disabled' }} title="{{ $canCreate ? '' : __('common.no_access_create') }}">
            <i class="fas fa-plus"></i> {{ __('common.add') }}
          </button>
        </div>

        <div class="card-body">
          <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
            <thead>
              <tr>
                <th>{{ __('common.no') }}</th>
                <th>{{ __('common.name') }}</th>
                <th>{{ __('common.status') }}</th>
                <th>{{ __('common.action') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($vendors as $r)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $r->name }}</td>
                  <td>
                    <span class="badge {{ $r->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                      {{ $r->is_active ? __('common.active') : __('common.inactive') }}
                    </span>
                  </td>
                  <td>
                    <div class="d-flex gap-2">
                      <button type="button" class="btn btn-sm btn-outline-primary js-edit" data-bs-toggle="modal" {{ $canUpdate ? '' : 'disabled' }}
                        data-bs-target="#editModal"
                        data-update-url="{{ route('admin.asset_vendors.update', $r->id) }}"
                        data-id="{{ $r->id }}" data-name="{{ $r->name }}" data-active="{{ $r->is_active ? '1' : '0' }}">
                        <i class="fas fa-pen"></i>
                      </button>

                      <form action="{{ route('admin.asset_vendors.toggle', $r->id) }}" method="POST">
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
        <form method="POST" action="{{ route('admin.asset_vendors.store') }}">
          @csrf
          <input type="hidden" name="modal_context" value="create">
          <div class="modal-header">
            <h5 class="modal-title">{{ __('master_data.asset_vendors.add_modal_title') }}</h5>
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

            <label class="form-label">{{ __('common.name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="{{ __('master_data.asset_vendors.placeholders.name') }}" required>
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
      ? $vendors->firstWhere('id', (int) old('id'))
      : null;
  @endphp

  <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="{{ $editRow ? route('admin.asset_vendors.update', $editRow->id) : '#' }}" id="editForm">
          @csrf
          @method('PUT')
          <input type="hidden" name="modal_context" value="edit">
          <input type="hidden" name="id" id="edit_id" value="{{ old('id', $editRow?->id) }}">

          <div class="modal-header">
            <h5 class="modal-title">{{ __('master_data.asset_vendors.edit_modal_title') }}</h5>
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

            <label class="form-label">{{ __('common.name') }}</label>
            <input type="text" name="name" class="form-control" id="edit_name" value="{{ old('name', $editRow?->name) }}" required>

            <div class="form-check mt-3">
              <input class="form-check-input" type="checkbox" value="1" id="edit_active" name="is_active"
                {{ old('is_active', $editRow?->is_active) ? 'checked' : '' }}>
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
          document.getElementById('edit_name').value = btn.dataset.name || '';
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
