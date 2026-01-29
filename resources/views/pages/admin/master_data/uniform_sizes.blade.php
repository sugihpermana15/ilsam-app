@extends('layouts.master')

@section('title', __('master_data.uniform_sizes.page_title') . ' | IGI')

@section('title-sub', __('master_data.uniform_sizes.subtitle'))
@section('pagetitle', __('master_data.uniform_sizes.pagetitle'))

@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
@endsection

@section('content')
  @php
    $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'uniform_sizes', 'create');
    $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'uniform_sizes', 'update');
    $canDelete = \App\Support\MenuAccess::can(auth()->user(), 'uniform_sizes', 'delete');
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
          <h5 class="card-title mb-0">{{ __('master_data.uniform_sizes.card_title') }}</h5>
          @if($canCreate)
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSizeModal">
              <i class="fas fa-plus"></i> {{ __('master_data.uniform_sizes.add_button') }}
            </button>
          @else
            <button type="button" class="btn btn-success" disabled title="{{ __('common.read_only') }}">
              <i class="fas fa-plus"></i> {{ __('master_data.uniform_sizes.add_button') }}
            </button>
          @endif
        </div>

        <div class="card-body">
          <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
            <thead>
              <tr>
                <th>{{ __('common.no') }}</th>
                <th>{{ __('common.code') }}</th>
                <th>{{ __('common.name') }}</th>
                <th>{{ __('common.status') }}</th>
                <th>{{ __('common.action') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($sizes as $s)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $s->code }}</td>
                  <td>{{ $s->name }}</td>
                  <td>
                    <span class="badge {{ $s->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                      {{ $s->is_active ? __('common.active') : __('common.inactive') }}
                    </span>
                  </td>
                  <td>
                    <div class="d-flex gap-2">
                      @if($canUpdate)
                        <button type="button" class="btn btn-sm btn-outline-primary js-edit-size" data-bs-toggle="modal"
                          data-bs-target="#editSizeModal"
                          data-update-url="{{ route('admin.uniform_sizes.update', $s->id) }}"
                          data-size-id="{{ $s->id }}" data-code="{{ $s->code }}" data-name="{{ $s->name }}"
                          data-active="{{ $s->is_active ? '1' : '0' }}">
                          <i class="fas fa-pen"></i>
                        </button>
                      @else
                        <button type="button" class="btn btn-sm btn-outline-primary" disabled title="{{ __('common.read_only') }}">
                          <i class="fas fa-pen"></i>
                        </button>
                      @endif

                      @if($canUpdate)
                        <form action="{{ route('admin.uniform_sizes.toggle', $s->id) }}" method="POST">
                          @csrf
                          <button type="submit" class="btn btn-sm {{ $s->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                            title="{{ __('common.change_status') }}">
                            <i class="fas {{ $s->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                          </button>
                        </form>
                      @else
                        <button type="button" class="btn btn-sm {{ $s->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}" disabled title="{{ __('common.read_only') }}">
                          <i class="fas {{ $s->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                        </button>
                      @endif

                      @if($canDelete)
                        <form action="{{ route('admin.uniform_sizes.destroy', $s->id) }}" method="POST" class="js-delete-size">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('master_data.uniform_sizes.delete_tooltip') }}">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      @else
                        <button type="button" class="btn btn-sm btn-outline-danger" disabled title="{{ __('common.read_only') }}">
                          <i class="fas fa-trash"></i>
                        </button>
                      @endif
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

  <div class="modal fade" id="addSizeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="{{ route('admin.uniform_sizes.store') }}">
          @csrf
          <input type="hidden" name="modal_context" value="create_size">
          <div class="modal-header">
            <h5 class="modal-title">{{ __('master_data.uniform_sizes.add_modal_title') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            @if($errors->any() && old('modal_context') === 'create_size')
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <label class="form-label">{{ __('common.code') }}</label>
            <input type="text" name="code" class="form-control" value="{{ old('code') }}" placeholder="{{ __('master_data.uniform_sizes.placeholders.code') }}" required>

            <label class="form-label mt-2">{{ __('common.name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="{{ __('master_data.uniform_sizes.placeholders.name') }}">
            <small class="text-muted">{{ __('master_data.uniform_sizes.hint_same_as_code') }}</small>
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
    $editSize = old('modal_context') === 'edit_size'
      ? $sizes->firstWhere('id', (int) old('size_id'))
      : null;
  @endphp

  <div class="modal fade" id="editSizeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="{{ $editSize ? route('admin.uniform_sizes.update', $editSize->id) : '#' }}" id="editSizeForm">
          @csrf
          @method('PUT')
          <input type="hidden" name="modal_context" value="edit_size">
          <input type="hidden" name="size_id" id="edit_size_id" value="{{ old('size_id', $editSize?->id) }}">

          <div class="modal-header">
            <h5 class="modal-title">{{ __('master_data.uniform_sizes.edit_modal_title') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            @if($errors->any() && old('modal_context') === 'edit_size')
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <label class="form-label">{{ __('common.code') }}</label>
            <input type="text" name="code" class="form-control" id="edit_size_code" value="{{ old('code', $editSize?->code) }}" required>

            <label class="form-label mt-2">{{ __('common.name') }}</label>
            <input type="text" name="name" class="form-control" id="edit_size_name" value="{{ old('name', $editSize?->name) }}">

            <div class="form-check mt-3">
              <input class="form-check-input" type="checkbox" value="1" id="edit_size_active" name="is_active"
                {{ old('is_active', $editSize?->is_active) ? 'checked' : '' }}>
              <label class="form-check-label" for="edit_size_active">{{ __('common.active') }}</label>
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
      document.querySelectorAll('.js-delete-size').forEach((form) => {
        form.addEventListener('submit', function (e) {
          e.preventDefault();
          Swal.fire({
            title: @json(__('master_data.uniform_sizes.delete.title')),
            text: @json(__('master_data.uniform_sizes.delete.text')),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: @json(__('master_data.uniform_sizes.delete.confirm')),
            cancelButtonText: @json(__('common.cancel')),
          }).then((result) => {
            if (result.isConfirmed) {
              form.submit();
            }
          });
        });
      });

      document.querySelectorAll('.js-edit-size').forEach((btn) => {
        btn.addEventListener('click', function () {
          const form = document.getElementById('editSizeForm');
          if (!form) return;

          form.setAttribute('action', btn.dataset.updateUrl);
          document.getElementById('edit_size_id').value = btn.dataset.sizeId || '';
          document.getElementById('edit_size_code').value = btn.dataset.code || '';
          document.getElementById('edit_size_name').value = btn.dataset.name || '';
          document.getElementById('edit_size_active').checked = (btn.dataset.active === '1');
        });
      });

      @if($errors->any() && old('modal_context') === 'create_size')
        const addModal = document.getElementById('addSizeModal');
        if (addModal) new bootstrap.Modal(addModal).show();
      @endif

      @if($errors->any() && old('modal_context') === 'edit_size')
        const editModal = document.getElementById('editSizeModal');
        if (editModal) new bootstrap.Modal(editModal).show();
      @endif
    });
  </script>
@endsection
