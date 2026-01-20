@extends('layouts.master')

@section('title', 'Master Position | IGI')

@section('title-sub', 'Data Master Jabatan')
@section('pagetitle', 'Master Jabatan')

@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
@endsection

@section('content')
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
          <h5 class="card-title mb-0">Data Master Jabatan</h5>
          <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPositionModal">
            <i class="fas fa-plus"></i> Add Position
          </button>
        </div>

        <div class="card-body">
          <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
            <thead>
              <tr>
                <th>No</th>
                <th>Name</th>
                <th>Level Code</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($positions as $position)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $position->name }}</td>
                  <td><span class="badge bg-secondary-subtle text-secondary">{{ $position->level_code }}</span></td>
                  <td>
                    <div class="d-flex gap-2">
                      <button type="button" class="btn btn-sm btn-outline-primary js-edit-position" data-bs-toggle="modal"
                        data-bs-target="#editPositionModal"
                        data-update-url="{{ route('admin.positions.update', $position->id) }}"
                        data-position-id="{{ $position->id }}" data-name="{{ $position->name }}"
                        data-level-code="{{ $position->level_code }}">
                        <i class="fas fa-pen"></i>
                      </button>

                      <form action="{{ route('admin.positions.destroy', $position->id) }}" method="POST"
                        class="js-delete-position">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                          <i class="fas fa-trash"></i>
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

  <div class="modal fade" id="addPositionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="{{ route('admin.positions.store') }}">
          @csrf
          <input type="hidden" name="modal_context" value="create_position">
          <div class="modal-header">
            <h5 class="modal-title">Add Position</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            @if($errors->any() && old('modal_context') === 'create_position')
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <div class="mb-3">
              <label class="form-label">Name</label>
              <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div>
              <label class="form-label">Level Code</label>
              <input type="text" name="level_code" class="form-control" value="{{ old('level_code') }}" maxlength="10"
                required>
              <div class="form-text">Contoh: DIR, GM, MGR, SUP, STF, OPR, LAB, QC, RD</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @php
    $editPosition = old('modal_context') === 'edit_position'
      ? $positions->firstWhere('id', (int) old('position_id'))
      : null;
  @endphp

  <div class="modal fade" id="editPositionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="{{ $editPosition ? route('admin.positions.update', $editPosition->id) : '#' }}"
          id="editPositionForm">
          @csrf
          @method('PUT')
          <input type="hidden" name="modal_context" value="edit_position">
          <input type="hidden" name="position_id" id="edit_position_id"
            value="{{ old('position_id', $editPosition?->id) }}">

          <div class="modal-header">
            <h5 class="modal-title">Edit Position</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            @if($errors->any() && old('modal_context') === 'edit_position')
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <div class="mb-3">
              <label class="form-label">Name</label>
              <input type="text" name="name" class="form-control" id="edit_position_name"
                value="{{ old('name', $editPosition?->name) }}" required>
            </div>

            <div>
              <label class="form-label">Level Code</label>
              <input type="text" name="level_code" class="form-control" id="edit_level_code"
                value="{{ old('level_code', $editPosition?->level_code) }}" maxlength="10" required>
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
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.js-delete-position').forEach((form) => {
        form.addEventListener('submit', function (e) {
          e.preventDefault();
          Swal.fire({
            title: 'Delete position?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
          }).then((result) => {
            if (result.isConfirmed) {
              form.submit();
            }
          });
        });
      });

      document.querySelectorAll('.js-edit-position').forEach((btn) => {
        btn.addEventListener('click', function () {
          const form = document.getElementById('editPositionForm');
          if (!form) return;

          form.setAttribute('action', btn.dataset.updateUrl);
          document.getElementById('edit_position_id').value = btn.dataset.positionId || '';
          document.getElementById('edit_position_name').value = btn.dataset.name || '';
          document.getElementById('edit_level_code').value = btn.dataset.levelCode || '';
        });
      });

      @if($errors->any() && old('modal_context') === 'create_position')
        const addModal = document.getElementById('addPositionModal');
        if (addModal) new bootstrap.Modal(addModal).show();
      @endif

        @if($errors->any() && old('modal_context') === 'edit_position')
          const editModal = document.getElementById('editPositionModal');
          if (editModal) new bootstrap.Modal(editModal).show();
        @endif
      });
  </script>
@endsection