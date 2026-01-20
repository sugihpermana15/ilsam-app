@extends('layouts.master')

@section('title', 'Employee Audit Log | IGI')

@section('title-sub', 'Audit Log Perubahan Karyawan')
@section('pagetitle', 'Employee Audit Log')

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
          <h5 class="card-title mb-0">Audit Log Perubahan Karyawan</h5>
          <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
          </a>
        </div>

        <div class="card-body">
            <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
            <thead>
              <tr>
                <th>No</th>
                <th>Date</th>
                <th>Action</th>
                <th>Employee</th>
                <th>Performed By</th>
                <th>IP</th>
                <th>Changes</th>
                <th>Detail</th>
              </tr>
            </thead>
            <tbody>
              @foreach($logs as $log)
                @php
                  $employeeLabel = $log->employee
                    ? ($log->employee->no_id . ' - ' . $log->employee->name)
                    : '-';

                  $actorLabel = $log->performed_by_name
                    ?? ($log->performedBy?->name ?? '-');

                  $changes = [];
                  if ($log->action === 'update' && is_array($log->old_values) && is_array($log->new_values)) {
                    foreach ($log->new_values as $key => $value) {
                      $old = $log->old_values[$key] ?? null;
                      if ($old !== $value) {
                        $changes[] = $key;
                      }
                    }
                  }
                @endphp
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $log->created_at?->format('d-m-Y H:i') ?? '-' }}</td>
                  <td>
                    <span class="badge
                          @if($log->action === 'create') bg-success-subtle text-success
                          @elseif($log->action === 'update') bg-primary-subtle text-primary
                          @elseif($log->action === 'delete') bg-danger-subtle text-danger
                          @elseif($log->action === 'restore') bg-warning-subtle text-warning
                          @else bg-secondary-subtle text-secondary
                          @endif
                        ">
                      {{ strtoupper($log->action) }}
                    </span>
                  </td>
                  <td>{{ $employeeLabel }}</td>
                  <td>{{ $actorLabel }}</td>
                  <td>{{ $log->ip_address ?? '-' }}</td>
                  <td>
                    @if($log->action === 'update')
                      {{ !empty($changes) ? implode(', ', array_slice($changes, 0, 8)) : '-' }}
                      @if(count($changes) > 8)
                        <span class="text-muted">(+{{ count($changes) - 8 }} more)</span>
                      @endif
                    @else
                      -
                    @endif
                  </td>
                  <td>
                    <button
                      type="button"
                      class="btn btn-sm btn-outline-secondary js-audit-detail"
                      data-bs-toggle="modal"
                      data-bs-target="#auditDetailModal"
                      data-action="{{ $log->action }}"
                      data-created-at="{{ $log->created_at?->format('d-m-Y H:i') ?? '-' }}"
                      data-employee="{{ e($employeeLabel) }}"
                      data-actor="{{ e($actorLabel) }}"
                      data-ip="{{ e($log->ip_address ?? '-') }}"
                      data-old="{{ e(json_encode($log->old_values)) }}"
                      data-new="{{ e(json_encode($log->new_values)) }}"
                    >
                      <i class="fas fa-eye"></i>
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="auditDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Audit Detail</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <div class="text-muted small">Action</div>
              <div id="auditDetailAction" class="fw-semibold">-</div>
            </div>
            <div class="col-md-4">
              <div class="text-muted small">Date</div>
              <div id="auditDetailDate" class="fw-semibold">-</div>
            </div>
            <div class="col-md-4">
              <div class="text-muted small">IP</div>
              <div id="auditDetailIp" class="fw-semibold">-</div>
            </div>

            <div class="col-md-6">
              <div class="text-muted small">Employee</div>
              <div id="auditDetailEmployee" class="fw-semibold">-</div>
            </div>
            <div class="col-md-6">
              <div class="text-muted small">Performed By</div>
              <div id="auditDetailActor" class="fw-semibold">-</div>
            </div>

            <div class="col-12">
              <hr class="my-2" />
            </div>

            <div class="col-md-6">
              <div class="text-muted small mb-1">Old Values</div>
              <pre id="auditDetailOld" class="bg-light p-2 rounded" style="max-height: 340px; overflow:auto;">-</pre>
            </div>
            <div class="col-md-6">
              <div class="text-muted small mb-1">New Values</div>
              <pre id="auditDetailNew" class="bg-light p-2 rounded" style="max-height: 340px; overflow:auto;">-</pre>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
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
      function safeParseJson(text) {
        if (!text || text === 'null' || text === 'undefined') return null;
        try { return JSON.parse(text); } catch (e) { return null; }
      }

      $(document).on('click', '.js-audit-detail', function () {
          const btn = this;
          const oldValues = safeParseJson(btn.dataset.old);
          const newValues = safeParseJson(btn.dataset.new);

          document.getElementById('auditDetailAction').textContent = (btn.dataset.action || '-').toUpperCase();
          document.getElementById('auditDetailDate').textContent = btn.dataset.createdAt || '-';
          document.getElementById('auditDetailIp').textContent = btn.dataset.ip || '-';
          document.getElementById('auditDetailEmployee').textContent = btn.dataset.employee || '-';
          document.getElementById('auditDetailActor').textContent = btn.dataset.actor || '-';

          document.getElementById('auditDetailOld').textContent = oldValues ? JSON.stringify(oldValues, null, 2) : '-';
          document.getElementById('auditDetailNew').textContent = newValues ? JSON.stringify(newValues, null, 2) : '-';
      });
    });
  </script>
@endsection