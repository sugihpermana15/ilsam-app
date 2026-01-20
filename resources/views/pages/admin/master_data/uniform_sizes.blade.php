@extends('layouts.master')

@section('title', 'Master Ukuran Seragam | IGI')

@section('title-sub', 'Master Data Ukuran Seragam')
@section('pagetitle', 'Master Ukuran Seragam')

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
          Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('success')), timer: 2000, showConfirmButton: false });
        @endif
        @if(session('error'))
          Swal.fire({ icon: 'error', title: 'Gagal', text: @json(session('error')), timer: 2500, showConfirmButton: false });
        @endif
      });
    </script>

    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Master Ukuran Seragam</h5>
          <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSizeModal">
            <i class="fas fa-plus"></i> Tambah Ukuran
          </button>
        </div>

        <div class="card-body">
          <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
            <thead>
              <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Status</th>
                <th>Aksi</th>
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
                      {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  </td>
                  <td>
                    <div class="d-flex gap-2">
                      <button type="button" class="btn btn-sm btn-outline-primary js-edit-size" data-bs-toggle="modal"
                        data-bs-target="#editSizeModal"
                        data-update-url="{{ route('admin.uniform_sizes.update', $s->id) }}"
                        data-size-id="{{ $s->id }}" data-code="{{ $s->code }}" data-name="{{ $s->name }}"
                        data-active="{{ $s->is_active ? '1' : '0' }}">
                        <i class="fas fa-pen"></i>
                      </button>

                      <form action="{{ route('admin.uniform_sizes.toggle', $s->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $s->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                          title="Ubah Status">
                          <i class="fas {{ $s->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                        </button>
                      </form>

                      <form action="{{ route('admin.uniform_sizes.destroy', $s->id) }}" method="POST" class="js-delete-size">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
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

  <div class="modal fade" id="addSizeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="{{ route('admin.uniform_sizes.store') }}">
          @csrf
          <input type="hidden" name="modal_context" value="create_size">
          <div class="modal-header">
            <h5 class="modal-title">Tambah Ukuran</h5>
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

            <label class="form-label">Kode</label>
            <input type="text" name="code" class="form-control" value="{{ old('code') }}" placeholder="mis: XL" required>

            <label class="form-label mt-2">Nama</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="mis: Extra Large">
            <small class="text-muted">Jika kosong, akan disamakan dengan kode.</small>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-success">Simpan</button>
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
            <h5 class="modal-title">Edit Ukuran</h5>
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

            <label class="form-label">Kode</label>
            <input type="text" name="code" class="form-control" id="edit_size_code" value="{{ old('code', $editSize?->code) }}" required>

            <label class="form-label mt-2">Nama</label>
            <input type="text" name="name" class="form-control" id="edit_size_name" value="{{ old('name', $editSize?->name) }}">

            <div class="form-check mt-3">
              <input class="form-check-input" type="checkbox" value="1" id="edit_size_active" name="is_active"
                {{ old('is_active', $editSize?->is_active) ? 'checked' : '' }}>
              <label class="form-check-label" for="edit_size_active">Aktif</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
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
      document.querySelectorAll('.js-delete-size').forEach((form) => {
        form.addEventListener('submit', function (e) {
          e.preventDefault();
          Swal.fire({
            title: 'Hapus ukuran?',
            text: 'Ukuran hanya bisa dihapus jika belum dipakai oleh item.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
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
