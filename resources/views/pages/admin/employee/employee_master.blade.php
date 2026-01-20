@extends('layouts.master')

@section('title', 'Master Karyawan | IGI')

@section('title-sub', 'Data Master Karyawan')
@section('pagetitle', 'Master Karyawan')

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
          <h5 class="card-title mb-0">Data Master Karyawan</h5>
          <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
            <i class="fas fa-plus"></i> Add Employee
          </button>
        </div>

        <div class="card-body">
          <div class="alert alert-info mb-3">
            <div><strong>No ID</strong> otomatis dibuat sistem (tidak bisa diinput manual).</div>
            <div>Format: <code>IGI-LEVEL-YY-XXXX</code> (LEVEL dari Jabatan, YY dari tanggal masuk, XXXX dari sequence global).</div>
            {{-- <hr class="my-2" />
            <div><strong>Wajib diisi:</strong> Name, Gender, Join Date, Department, Position.</div>
            <div><strong>Opsional:</strong> Birth Date, Address, Phone, Email, Photo.</div> --}}
          </div>

            <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
            <thead>
              <tr>
                <th>No</th>
                <th>No ID</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Department</th>
                <th>Position</th>
                <th>Status</th>
                <th>Join Date</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Photo</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($employees as $employee)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td><span class="badge bg-primary-subtle text-primary">{{ $employee->no_id }}</span></td>
                  <td>{{ $employee->name }}</td>
                  <td>{{ $employee->gender }}</td>
                  <td>{{ $employee->department?->name ?? '-' }}</td>
                  <td>
                    {{ $employee->position?->name ?? '-' }}
                    @if($employee->position?->level_code)
                      <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $employee->position->level_code }}</span>
                    @endif
                  </td>
                  <td>{{ $employee->employment_status ?? '-' }}</td>
                  <td>{{ $employee->join_date?->format('d-m-Y') ?? '-' }}</td>
                  <td>{{ $employee->phone ?? '-' }}</td>
                  <td>{{ $employee->email ?? '-' }}</td>
                  <td>
                    @if($employee->photo)
                      @php
                        $isPublicAsset = \Illuminate\Support\Str::startsWith($employee->photo, 'assets/');
                        $photoUrl = $isPublicAsset ? asset($employee->photo) : asset('storage/' . $employee->photo);
                      @endphp
                      <a href="{{ $photoUrl }}" target="_blank" class="d-inline-flex align-items-center gap-2">
                        <img src="{{ $photoUrl }}" alt="photo" style="width: 34px; height: 34px; object-fit: cover; border-radius: 6px;" />
                        <span class="text-decoration-underline">View</span>
                      </a>
                    @else
                      -
                    @endif
                  </td>
                  <td>
                    <div class="d-flex gap-2">
                      <button
                        type="button"
                        class="btn btn-sm btn-outline-primary js-edit-employee"
                        data-bs-toggle="modal"
                        data-bs-target="#editEmployeeModal"
                        data-update-url="{{ route('admin.employees.update', $employee->id) }}"
                        data-employee-id="{{ $employee->id }}"
                        data-no-id="{{ $employee->no_id }}"
                        data-sequence-number="{{ $employee->sequence_number }}"
                        data-name="{{ $employee->name }}"
                        data-gender="{{ $employee->gender }}"
                        data-birth-date="{{ optional($employee->birth_date)->format('Y-m-d') }}"
                        data-join-date="{{ optional($employee->join_date)->format('Y-m-d') }}"
                        data-department-id="{{ $employee->department_id }}"
                        data-position-id="{{ $employee->position_id }}"
                        data-employment-status="{{ $employee->employment_status }}"
                        data-address="{{ $employee->address }}"
                        data-phone="{{ $employee->phone }}"
                        data-email="{{ $employee->email }}"
                      >
                        <i class="fas fa-pen"></i>
                      </button>

                      <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" class="js-delete-employee">
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

  <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form method="POST" action="{{ route('admin.employees.store') }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="modal_context" value="create">
          <div class="modal-header">
            <h5 class="modal-title">Add Employee</h5>
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

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select" required>
                  <option value="">- Select -</option>
                  <option value="Laki-laki" @selected(old('gender') === 'Laki-laki')>Laki-laki</option>
                  <option value="Perempuan" @selected(old('gender') === 'Perempuan')>Perempuan</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Birth Date</label>
                <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Join Date</label>
                <input type="date" name="join_date" class="form-control" value="{{ old('join_date') }}" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select" required>
                  <option value="">- Select -</option>
                  @foreach($departments as $department)
                    <option value="{{ $department->id }}" @selected((string) old('department_id') === (string) $department->id)>
                      {{ $department->name }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Position</label>
                <select name="position_id" class="form-select" required>
                  <option value="">- Select -</option>
                  @foreach($positions as $position)
                    <option value="{{ $position->id }}" @selected((string) old('position_id') === (string) $position->id)>
                      {{ $position->name }} ({{ $position->level_code }})
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-12">
                <label class="form-label">Status (PKWT/PKWTT)</label>
                <select name="employment_status" class="form-select">
                  <option value="">- Optional -</option>
                  <option value="PKWT" @selected(old('employment_status') === 'PKWT')>PKWT</option>
                  <option value="PKWTT" @selected(old('employment_status') === 'PKWTT')>PKWTT</option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
              </div>

              <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
              </div>

              <div class="col-12">
                <label class="form-label">Photo (JPG/PNG max 2MB)</label>
                <input type="file" name="photo" class="form-control" accept="image/png,image/jpeg">
              </div>
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
    $editEmployee = old('modal_context') === 'edit'
      ? $employees->firstWhere('id', (int) old('employee_id'))
      : null;
  @endphp

  <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form
          method="POST"
          action="{{ $editEmployee ? route('admin.employees.update', $editEmployee->id) : '#' }}"
          enctype="multipart/form-data"
          id="editEmployeeForm"
        >
          @csrf
          @method('PUT')
          <input type="hidden" name="modal_context" value="edit">
          <input type="hidden" name="employee_id" id="edit_employee_id" value="{{ old('employee_id', $editEmployee?->id) }}">

          <div class="modal-header">
            <h5 class="modal-title">Edit Employee</h5>
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

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">No ID (read-only)</label>
                <input type="text" class="form-control" id="edit_no_id" value="{{ old('no_id', $editEmployee?->no_id) }}" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label">Sequence Number (read-only)</label>
                <input type="text" class="form-control" id="edit_sequence_number" value="{{ old('sequence_number', $editEmployee?->sequence_number) }}" readonly>
              </div>

              <div class="col-md-6">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" id="edit_name" value="{{ old('name', $editEmployee?->name) }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select" id="edit_gender" required>
                  <option value="">- Select -</option>
                  <option value="Laki-laki" @selected(old('gender', $editEmployee?->gender) === 'Laki-laki')>Laki-laki</option>
                  <option value="Perempuan" @selected(old('gender', $editEmployee?->gender) === 'Perempuan')>Perempuan</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Birth Date</label>
                <input type="date" name="birth_date" class="form-control" id="edit_birth_date" value="{{ old('birth_date', optional($editEmployee?->birth_date)->format('Y-m-d')) }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Join Date</label>
                <input type="date" name="join_date" class="form-control" id="edit_join_date" value="{{ old('join_date', optional($editEmployee?->join_date)->format('Y-m-d')) }}" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select" id="edit_department_id" required>
                  <option value="">- Select -</option>
                  @foreach($departments as $department)
                    <option value="{{ $department->id }}" @selected((string) old('department_id', $editEmployee?->department_id) === (string) $department->id)>
                      {{ $department->name }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Position</label>
                <select name="position_id" class="form-select" id="edit_position_id" required>
                  <option value="">- Select -</option>
                  @foreach($positions as $position)
                    <option value="{{ $position->id }}" @selected((string) old('position_id', $editEmployee?->position_id) === (string) $position->id)>
                      {{ $position->name }} ({{ $position->level_code }})
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-12">
                <label class="form-label">Status (PKWT/PKWTT)</label>
                <select name="employment_status" class="form-select" id="edit_employment_status">
                  <option value="">- Optional -</option>
                  <option value="PKWT" @selected(old('employment_status', $editEmployee?->employment_status) === 'PKWT')>PKWT</option>
                  <option value="PKWTT" @selected(old('employment_status', $editEmployee?->employment_status) === 'PKWTT')>PKWTT</option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2" id="edit_address">{{ old('address', $editEmployee?->address) }}</textarea>
              </div>

              <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" id="edit_phone" value="{{ old('phone', $editEmployee?->phone) }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" id="edit_email" value="{{ old('email', $editEmployee?->email) }}">
              </div>

              <div class="col-12">
                <label class="form-label">Photo (JPG/PNG max 2MB)</label>
                <input type="file" name="photo" class="form-control" accept="image/png,image/jpeg">
                <div class="form-check mt-2">
                  <input class="form-check-input" type="checkbox" name="remove_photo" value="1" id="edit_remove_photo">
                  <label class="form-check-label" for="edit_remove_photo">Remove current photo</label>
                </div>
              </div>
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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

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
      // Prevent browser warning: "Blocked aria-hidden on an element because its descendant retained focus"
      // by moving focus out of the modal BEFORE Bootstrap toggles aria-hidden on close.
      ['addEmployeeModal', 'editEmployeeModal'].forEach((id) => {
        const modalEl = document.getElementById(id);
        if (!modalEl) return;

        // Ensure we control the instance config (disables focus-trap which often causes the warning).
        if (window.bootstrap?.Modal) {
          bootstrap.Modal.getOrCreateInstance(modalEl, { focus: false });
        }

        modalEl.addEventListener('show.bs.modal', (event) => {
          // If opened via data-bs-target, Bootstrap provides relatedTarget (the trigger button)
          modalEl._returnFocusEl = event.relatedTarget || document.activeElement || null;
        });

        modalEl.addEventListener('hide.bs.modal', () => {
          // If something inside the modal is focused (often the close button), blur it first
          // so focus is not retained when Bootstrap toggles aria-hidden.
          const active = document.activeElement;
          if (active && modalEl.contains(active) && typeof active.blur === 'function') {
            active.blur();
          }

          const returnFocusEl = modalEl._returnFocusEl;
          // Only restore focus if the target is outside the modal.
          if (returnFocusEl && !modalEl.contains(returnFocusEl) && typeof returnFocusEl.focus === 'function') {
            // Defer to the next tick to avoid the browser re-focusing the clicked dismiss button.
            setTimeout(() => returnFocusEl.focus(), 0);
            return;
          }

          const fallback = document.querySelector(`[data-bs-target="#${id}"]`);
          if (fallback && typeof fallback.focus === 'function') {
            setTimeout(() => fallback.focus(), 0);
            return;
          }

          // Final fallback: move focus to body to avoid leaving it in a hidden subtree.
          if (document.body && typeof document.body.focus === 'function') {
            setTimeout(() => document.body.focus(), 0);
          }
        });

        // Extra safety: restore focus again after it is fully hidden.
        modalEl.addEventListener('hidden.bs.modal', () => {
          const returnFocusEl = modalEl._returnFocusEl;
          if (returnFocusEl && !modalEl.contains(returnFocusEl) && typeof returnFocusEl.focus === 'function') {
            setTimeout(() => returnFocusEl.focus(), 0);
            return;
          }

          const fallback = document.querySelector(`[data-bs-target="#${id}"]`);
          if (fallback && typeof fallback.focus === 'function') {
            setTimeout(() => fallback.focus(), 0);
          }
        });
      });

      $(document).on('submit', '.js-delete-employee', function (e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
          title: 'Delete employee?',
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

      $(document).on('click', '.js-edit-employee', function () {
        const btn = this;
        const form = document.getElementById('editEmployeeForm');
        if (!form) return;

        form.setAttribute('action', btn.dataset.updateUrl);

        document.getElementById('edit_employee_id').value = btn.dataset.employeeId || '';
        document.getElementById('edit_no_id').value = btn.dataset.noId || '';
        document.getElementById('edit_sequence_number').value = btn.dataset.sequenceNumber || '';
        document.getElementById('edit_name').value = btn.dataset.name || '';
        document.getElementById('edit_gender').value = btn.dataset.gender || '';
        document.getElementById('edit_birth_date').value = btn.dataset.birthDate || '';
        document.getElementById('edit_join_date').value = btn.dataset.joinDate || '';
        document.getElementById('edit_department_id').value = btn.dataset.departmentId || '';
        document.getElementById('edit_position_id').value = btn.dataset.positionId || '';
        const employmentStatus = document.getElementById('edit_employment_status');
        if (employmentStatus) employmentStatus.value = btn.dataset.employmentStatus || '';
        document.getElementById('edit_address').value = btn.dataset.address || '';
        document.getElementById('edit_phone').value = btn.dataset.phone || '';
        document.getElementById('edit_email').value = btn.dataset.email || '';
        const removePhoto = document.getElementById('edit_remove_photo');
        if (removePhoto) removePhoto.checked = false;
      });

      @if($errors->any() && old('modal_context') === 'create')
        const addModal = document.getElementById('addEmployeeModal');
        if (addModal) new bootstrap.Modal(addModal).show();
      @endif

      @if($errors->any() && old('modal_context') === 'edit')
        const editModal = document.getElementById('editEmployeeModal');
        if (editModal) new bootstrap.Modal(editModal).show();
      @endif
    });
  </script>
@endsection