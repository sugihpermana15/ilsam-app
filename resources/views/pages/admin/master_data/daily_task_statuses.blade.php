@extends('layouts.master')

@section('title', 'Master Status Daily Task | IGI')

@section('title-sub', 'Master Data Daily Task - Status')
@section('pagetitle', 'Master Status Daily Task')

@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
@endsection

@section('content')
  @php
    $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'daily_task_statuses', 'update');
  @endphp
  <div class="row">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
          Swal.fire({ icon: 'success', title: 'Success', text: @json(session('success')), timer: 2000, showConfirmButton: false });
        @endif
        @if(session('error'))
          Swal.fire({ icon: 'error', title: 'Error', text: @json(session('error')), timer: 2500, showConfirmButton: false });
        @endif
      });
    </script>

    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Master Status Daily Task</h5>
          <div class="text-muted small">Catatan: Status mengikuti aturan flow sistem (ID fixed), hanya nama/aktif/sort yang bisa diubah.</div>
        </div>

        <div class="card-body">
          <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
            <thead>
              <tr>
                <th>No</th>
                <th>ID</th>
                <th>Name</th>
                <th>Sort</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($statuses as $r)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td><span class="badge bg-primary-subtle text-primary">{{ $r->id }}</span></td>
                  <td>{{ $r->name }}</td>
                  <td>{{ $r->sort_order }}</td>
                  <td>
                    <span class="badge {{ $r->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                      {{ $r->is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td>
                    <div class="d-flex gap-2">
                      <button type="button" class="btn btn-sm btn-outline-primary js-edit" data-bs-toggle="modal" data-bs-target="#editModal" {{ $canUpdate ? '' : 'disabled' }}
                        data-update-url="{{ route('admin.daily_task_statuses.update', $r->id) }}"
                        data-id="{{ $r->id }}" data-name="{{ $r->name }}" data-sort="{{ $r->sort_order }}" data-active="{{ $r->is_active ? '1' : '0' }}">
                        <i class="fas fa-pen"></i>
                      </button>

                      <form action="{{ route('admin.daily_task_statuses.toggle', $r->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $r->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}" {{ $canUpdate ? '' : 'disabled' }}>
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

  @php
    $editRow = old('modal_context') === 'edit'
      ? $statuses->firstWhere('id', (int) old('id'))
      : null;
  @endphp

  <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="{{ $editRow ? route('admin.daily_task_statuses.update', $editRow->id) : '#' }}" id="editForm">
          @csrf
          @method('PUT')
          <input type="hidden" name="modal_context" value="edit">
          <input type="hidden" name="id" id="edit_id" value="{{ old('id', $editRow?->id) }}">

          <div class="modal-header">
            <h5 class="modal-title">Edit Status</h5>
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

            <div class="mb-2">
              <label class="form-label">ID</label>
              <input type="text" class="form-control" id="edit_id_display" disabled>
            </div>

            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" id="edit_name" value="{{ old('name', $editRow?->name) }}" required>

            <label class="form-label mt-3">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" id="edit_sort" value="{{ old('sort_order', $editRow?->sort_order) }}" min="0" max="65535">

            <div class="form-check mt-3">
              <input class="form-check-input" type="checkbox" value="1" id="edit_active" name="is_active" {{ old('is_active', $editRow?->is_active) ? 'checked' : '' }}>
              <label class="form-check-label" for="edit_active">Active</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update</button>
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
          document.getElementById('edit_id_display').value = btn.dataset.id || '';
          document.getElementById('edit_name').value = btn.dataset.name || '';
          document.getElementById('edit_sort').value = btn.dataset.sort || '0';
          document.getElementById('edit_active').checked = (btn.dataset.active === '1');
        });
      });

      @if($errors->any() && old('modal_context') === 'edit')
        const editModal = document.getElementById('editModal');
        if (editModal) new bootstrap.Modal(editModal).show();
      @endif
    });
  </script>
@endsection
