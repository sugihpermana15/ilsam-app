@extends('layouts.master')

@section('title', 'Deleted Employees | IGI')

@section('title-sub', 'Karyawan Terhapus')
@section('pagetitle', 'Deleted Employees')

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
          <h5 class="card-title mb-0">Karyawan Terhapus (Soft Deleted)</h5>
          <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
          </a>
        </div>

        <div class="card-body">
            <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
              <thead>
                <tr>
                  <th>No</th>
                  <th>No ID</th>
                  <th>Name</th>
                  <th>Department</th>
                  <th>Position</th>
                  <th>Deleted At</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($employees as $employee)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><span class="badge bg-primary-subtle text-primary">{{ $employee->no_id }}</span></td>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->department?->name ?? '-' }}</td>
                    <td>{{ $employee->position?->name ?? '-' }}</td>
                    <td>{{ $employee->deleted_at?->format('d-m-Y H:i') ?? '-' }}</td>
                    <td>
                      <form action="{{ route('admin.employees.restore', $employee->id) }}" method="POST"
                        class="js-restore-employee" style="display:inline-block">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-success">
                          <i class="fas fa-rotate-left"></i> Restore
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
    $(document).ready(function () {
      $(document).on('submit', '.js-restore-employee', function (e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
          title: 'Restore employee?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Yes, restore',
          cancelButtonText: 'Cancel',
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      });
    });
  </script>
@endsection