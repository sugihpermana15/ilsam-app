@extends('layouts.master')

@section('title', 'Daily Tasks | IGI')

@section('title-sub', 'Daily Tasks')
@section('pagetitle', 'Daily Tasks')

@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
@endsection

@section('content')
  @php
    $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'daily_tasks', 'create');
    $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'daily_tasks', 'update');
    $canDelete = \App\Support\MenuAccess::can(auth()->user(), 'daily_tasks', 'delete');
    $canToggleChecklist = \App\Support\MenuAccess::can(auth()->user(), 'daily_tasks', 'read');
  @endphp

  <div id="layout-wrapper">
    <div class="row">
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Success', text: @json(session('success')), timer: 1800, showConfirmButton: false });
          @endif
          @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Error', text: @json(session('error')), timer: 2500, showConfirmButton: false });
          @endif
        });
      </script>

      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daily Tasks</h5>
            <div class="d-flex gap-2 align-items-center">
              <button type="button" class="btn btn-outline-primary" id="btn-open-export" data-bs-toggle="modal" data-bs-target="#exportPdfModal">
                <i class="fas fa-file-pdf"></i> Unduh PDF
              </button>
              <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#taskCreateModal" {{ $canCreate ? '' : 'disabled' }}>
                <i class="fas fa-plus"></i> New Task
              </button>
            </div>
          </div>

          <div class="card-body">
            <ul class="nav nav-pills mb-3" id="taskTabs" role="tablist">
              <li class="nav-item" role="presentation"><button class="nav-link active" data-tab="all" type="button">All</button></li>
              <li class="nav-item" role="presentation"><button class="nav-link" data-tab="my" type="button">My Tasks</button></li>
              <li class="nav-item" role="presentation"><button class="nav-link" data-tab="created" type="button">Created By Me</button></li>
              <li class="nav-item" role="presentation"><button class="nav-link" data-tab="overdue" type="button">Overdue</button></li>
              <li class="nav-item" role="presentation"><button class="nav-link" data-tab="completed" type="button">Completed</button></li>
            </ul>

            <div class="row g-2 align-items-end mb-3">
              <div class="col-12 col-lg-3">
                <label class="form-label mb-1">Ditugaskan Ke</label>
                <select class="form-select" id="filter-assigned-to">
                  <option value="">Semua</option>
                  @foreach(($assignees ?? collect()) as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}{{ $u->no_id ? ' (' . $u->no_id . ')' : '' }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 col-lg-2">
                <label class="form-label mb-1">Status</label>
                <select class="form-select" id="filter-status">
                  <option value="">Semua</option>
                  @foreach(($taskStatuses ?? []) as $opt)
                    <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 col-lg-2">
                <label class="form-label mb-1">Prioritas</label>
                <select class="form-select" id="filter-priority">
                  <option value="">Semua</option>
                  @foreach(($taskPriorities ?? []) as $opt)
                    <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 col-lg-2">
                <label class="form-label mb-1">Jatuh Tempo Dari</label>
                <input type="date" class="form-control" id="filter-due-start" />
              </div>
              <div class="col-12 col-lg-2">
                <label class="form-label mb-1">Jatuh Tempo Ke</label>
                <input type="date" class="form-control" id="filter-due-end" />
              </div>
              <div class="col-12 col-lg-1 d-flex justify-content-lg-end">
                <button type="button" class="btn btn-outline-secondary w-100" id="btn-clear-filters" title="Reset">
                  <i class="fas fa-rotate-left"></i>
                </button>
              </div>
            </div>

            <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100" data-dt-server="1">
              <thead>
                <tr>
                  <th>No</th>
                  <th>ID</th>
                  <th>Type</th>
                  <th>Title</th>
                  <th>Due Start</th>
                  <th>Due End</th>
                  <th>SLA</th>
                  <th>Status</th>
                  <th>Priority</th>
                  <th>Assigned To</th>
                  <th>Created By</th>
                  <th>Attachments</th>
                  <th>Checklist</th>
                  <th>Last Updated</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>

            <!-- Export PDF Modal -->
            <div class="modal fade" id="exportPdfModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Unduh PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row g-3">
                      <div class="col-12">
                        <label class="form-label">Periode</label>
                        <select class="form-select" id="export-period">
                          <option value="daily">Harian</option>
                          <option value="weekly">Mingguan</option>
                          <option value="monthly">Bulanan</option>
                          <option value="yearly">Tahunan</option>
                        </select>
                      </div>

                      <div class="col-12" data-export-period="daily">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="export-date" />
                      </div>
                      <div class="col-12 d-none" data-export-period="weekly">
                        <label class="form-label">Tanggal (Minggu)</label>
                        <input type="date" class="form-control" id="export-week-date" />
                        <div class="form-text">Minggu dihitung Senin–Minggu.</div>
                      </div>
                      <div class="col-12 d-none" data-export-period="monthly">
                        <label class="form-label">Bulan</label>
                        <input type="month" class="form-control" id="export-month" />
                      </div>
                      <div class="col-12 d-none" data-export-period="yearly">
                        <label class="form-label">Tahun</label>
                        <input type="number" class="form-control" id="export-year" min="2000" max="2100" step="1" placeholder="{{ now()->year }}" />
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn-export-pdf">Unduh</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Create Modal -->
      <div class="modal fade" id="taskCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Create Daily Task</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form method="POST" action="{{ route('admin.daily_tasks.store') }}">
                @csrf
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Task Type</label>
                    <select class="form-select" name="task_type" required>
                      @foreach(($taskTypes ?? []) as $opt)
                        <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Assigned To</label>
                    @if(!$isAdmin)
                      @if(!empty($currentEmployee))
                        <input type="hidden" name="assigned_employee_id" value="{{ $currentEmployee->id }}" />
                        <input type="text" class="form-control" value="{{ $currentEmployee->name }}{{ $currentEmployee->no_id ? ' (' . $currentEmployee->no_id . ')' : '' }}" disabled />
                      @else
                        <div class="alert alert-warning mb-2">
                          Akun user belum terhubung ke master karyawan. Task akan fallback ke user.
                        </div>
                        <input type="hidden" name="assigned_to" value="{{ auth()->id() }}" />
                        <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled />
                      @endif
                    @else
                      <select class="form-select" name="assigned_employee_id">
                        <option value="">(Unassigned)</option>
                        @foreach(($assignees ?? collect()) as $u)
                          <option value="{{ $u->id }}">{{ $u->name }}{{ $u->no_id ? ' (' . $u->no_id . ')' : '' }}</option>
                        @endforeach
                      </select>
                    @endif
                  </div>

                  <div class="col-12">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" name="title" maxlength="200" required />
                  </div>

                  <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3"></textarea>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Due Start</label>
                    <input type="date" class="form-control" name="due_start" />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Due End</label>
                    <input type="date" class="form-control" name="due_end" />
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" required>
                      @foreach(($taskStatuses ?? []) as $opt)
                        <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Priority</label>
                    <select class="form-select" name="priority" required>
                      @foreach(($taskPriorities ?? []) as $opt)
                        <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-12">
                    <label class="form-label">Checklist (1 item per line)</label>
                    <textarea class="form-control" name="checklist_lines" rows="4" placeholder="- Item 1\n- Item 2"></textarea>
                  </div>

                  <div class="col-12 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Create</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Detail Modal -->
      <div class="modal fade" id="taskDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Task Detail</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-12">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <div class="fw-semibold" id="detail-title">-</div>
                      <div class="text-muted small" id="detail-meta">-</div>
                    </div>
                    <div class="d-flex gap-2">
                      <select class="form-select form-select-sm" id="detail-status-select" style="min-width: 160px" {{ $canUpdate ? '' : 'disabled' }}></select>
                      <button type="button" class="btn btn-primary btn-sm" id="btn-update-status" {{ $canUpdate ? '' : 'disabled' }}>
                        <i class="fas fa-pen"></i>
                      </button>
                      <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-refresh-detail"><i class="fas fa-rotate"></i></button>
                      <button type="button" class="btn btn-danger btn-sm" id="btn-delete-task" {{ $canDelete ? '' : 'disabled' }}><i class="fas fa-trash"></i></button>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-lg-6">
                  <div class="card">
                    <div class="card-header"><strong>Checklist</strong></div>
                    <div class="card-body">
                      <div class="input-group mb-2">
                        <input type="text" class="form-control" id="new-checklist-text" placeholder="Add checklist item..." {{ $canUpdate ? '' : 'disabled' }}>
                        <button class="btn btn-primary" type="button" id="btn-add-checklist" {{ $canUpdate ? '' : 'disabled' }}>Add</button>
                      </div>
                      <div id="checklist-items" class="d-grid gap-2"></div>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-lg-6">
                  <div class="card">
                    <div class="card-header"><strong>Attachments</strong></div>
                    <div class="card-body">
                      <div class="mb-2">
                        <input type="file" id="upload-file" class="form-control" {{ $canUpdate ? '' : 'disabled' }} />
                        <button class="btn btn-primary mt-2" type="button" id="btn-upload-attachment" {{ $canUpdate ? '' : 'disabled' }}>Upload</button>
                      </div>
                      <div id="attachment-items" class="list-group"></div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
@endsection

@section('js')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap.min.js"></script>

  <script src="{{ asset('assets/js/table/datatable.init.js') }}"></script>
  <script type="module" src="{{ asset('assets/js/app.js') }}"></script>

  <script>
    (function () {
      const csrfToken = @json(csrf_token());
      const dtUrl = @json(route('admin.daily_tasks.datatable'));
      const exportPdfUrl = @json(route('admin.daily_tasks.export.pdf'));
      const canUpdate = @json($canUpdate);
      const canDelete = @json($canDelete);
      const canToggleChecklist = @json($canToggleChecklist);
      const doneStatusValue = @json(\App\Enums\DailyTaskStatus::Done->value);

      let currentTab = 'all';
      let currentTaskId = null;

      const $filterAssigned = $('#filter-assigned-to');
      const $filterStatus = $('#filter-status');
      const $filterPriority = $('#filter-priority');
      const $filterDueStart = $('#filter-due-start');
      const $filterDueEnd = $('#filter-due-end');

      const $exportPeriod = $('#export-period');
      const $exportDate = $('#export-date');
      const $exportWeekDate = $('#export-week-date');
      const $exportMonth = $('#export-month');
      const $exportYear = $('#export-year');

      const toYmd = (d) => {
        const pad = (n) => String(n).padStart(2, '0');
        return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
      };

      const setDefaultExportDates = () => {
        const now = new Date();
        if (!$exportDate.val()) $exportDate.val(toYmd(now));
        if (!$exportWeekDate.val()) $exportWeekDate.val(toYmd(now));
        const m = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        if (!$exportMonth.val()) $exportMonth.val(m);
        if (!$exportYear.val()) $exportYear.val(String(now.getFullYear()));
      };

      const toggleExportInputs = () => {
        const p = String($exportPeriod.val() || 'daily');
        $('[data-export-period]').addClass('d-none');
        $(`[data-export-period="${p}"]`).removeClass('d-none');
      };

      const statusBadge = (s) => {
        const map = {
          'To Do': 'bg-secondary-subtle text-secondary',
          'In Progress': 'bg-info-subtle text-info',
          'Done': 'bg-success-subtle text-success',
          'Blocked': 'bg-warning-subtle text-warning',
          'Canceled': 'bg-danger-subtle text-danger',
        };
        const cls = map[s] || 'bg-light-subtle text-body';
        return `<span class="badge ${cls}">${$('<div/>').text(s || '-').html()}</span>`;
      };

      const priorityBadge = (p) => {
        const map = {
          'Low': 'bg-light-subtle text-body',
          'Medium': 'bg-primary-subtle text-primary',
          'High': 'bg-warning-subtle text-warning',
          'Urgent': 'bg-danger-subtle text-danger',
        };
        const cls = map[p] || 'bg-light-subtle text-body';
        return `<span class="badge ${cls}">${$('<div/>').text(p || '-').html()}</span>`;
      };

      const slaBadge = (s) => {
        const val = (s || '').toString();
        let cls = 'bg-light-subtle text-body';
        if (/^Overdue/i.test(val) || val === 'Late') cls = 'bg-danger-subtle text-danger';
        else if (val === 'Due Today' || val === 'Due Soon') cls = 'bg-warning-subtle text-warning';
        else if (val === 'On Track' || val === 'On Time' || val === 'Done') cls = 'bg-success-subtle text-success';
        else if (val === 'Canceled') cls = 'bg-secondary-subtle text-secondary';
        return `<span class="badge ${cls}">${$('<div/>').text(val || '-').html()}</span>`;
      };

      const dt = $('#alternative-pagination').DataTable({
        processing: true,
        serverSide: true,
        pagingType: "full_numbers",
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        dom:
          "<'row'<'col-sm-12 col-md-6 mb-3'l><'col-sm-12 col-md-6 mt-5'f>>" +
          "<'table-responsive'tr>" +
          "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        language: {
          search: 'Cari : ',
          searchPlaceholder: 'Ketik untuk filter...'
        },
        ajax: {
          url: dtUrl,
          data: function (d) {
            d.f_tab = currentTab;
            const a = ($filterAssigned.val() || '').toString().trim();
            const s = ($filterStatus.val() || '').toString().trim();
            const p = ($filterPriority.val() || '').toString().trim();
            const ds = ($filterDueStart.val() || '').toString().trim();
            const de = ($filterDueEnd.val() || '').toString().trim();
            if (a) d.f_assigned_to = a;
            if (s) d.f_status = s;
            if (p) d.f_priority = p;
            if (ds) d.f_due_start = ds;
            if (de) d.f_due_end = de;
          }
        },
        order: [[1, 'desc']],
        columns: [
          {
            data: 'id',
            orderable: false,
            searchable: false,
            render: function (_data, _type, _row, meta) {
              return meta.row + meta.settings._iDisplayStart + 1;
            }
          },
          { data: 'id' },
          { data: 'task_type', defaultContent: '-' },
          {
            data: 'title',
            defaultContent: '-',
            render: function (data, _type, row) {
              const title = (data || '-');
              const desc = row.description_preview ? `<div class="text-muted small">${$('<div/>').text(row.description_preview).html()}</div>` : '';
              return `<div class="fw-semibold">${$('<div/>').text(title).html()}</div>${desc}`;
            }
          },
          { data: 'due_start', defaultContent: '-' },
          { data: 'due_end', defaultContent: '-' },
          {
            data: 'sla',
            defaultContent: '-',
            searchable: false,
            orderable: false,
            render: function (data) { return slaBadge(data); }
          },
          {
            data: 'status',
            defaultContent: '-',
            render: function (data) { return statusBadge(data); }
          },
          {
            data: 'priority',
            defaultContent: '-',
            render: function (data) { return priorityBadge(data); }
          },
          { data: 'assigned_to_name', defaultContent: '-' },
          { data: 'created_by_name', defaultContent: '-' },
          { data: 'attachments_count', defaultContent: '0', searchable: false },
          {
            data: 'checklists_count',
            searchable: false,
            render: function (_data, _type, row) {
              const done = row.checklists_done_count || 0;
              const total = row.checklists_count || 0;
              return `${done}/${total}`;
            }
          },
          { data: 'updated_at', defaultContent: '-' },
          {
            data: 'id',
            orderable: false,
            searchable: false,
            render: function (data) {
              const id = String(data);
              const btnView = `<button type="button" class="btn btn-sm btn-info btn-view-task" data-id="${id}"><i class="fas fa-eye"></i></button>`;
              return `${btnView}`;
            }
          },
        ]
      });

      const redraw = () => dt.ajax.reload(null, true);

      $('#taskTabs button[data-tab]').on('click', function () {
        $('#taskTabs button').removeClass('active');
        $(this).addClass('active');
        currentTab = String($(this).data('tab'));
        redraw();
      });

      $filterAssigned.on('change', redraw);
      $filterStatus.on('change', redraw);
      $filterPriority.on('change', redraw);
      $filterDueStart.on('change', redraw);
      $filterDueEnd.on('change', redraw);

      $('#btn-clear-filters').on('click', function () {
        $filterAssigned.val('');
        $filterStatus.val('');
        $filterPriority.val('');
        $filterDueStart.val('');
        $filterDueEnd.val('');
        redraw();
      });

      // PDF Export
      setDefaultExportDates();
      toggleExportInputs();

      $exportPeriod.on('change', toggleExportInputs);

      $('#exportPdfModal').on('shown.bs.modal', function () {
        setDefaultExportDates();
        toggleExportInputs();
      });

      $('#btn-export-pdf').on('click', function () {
        const period = String($exportPeriod.val() || 'daily');
        const params = new URLSearchParams();
        params.set('period', period);
        params.set('tab', currentTab);

        const a = ($filterAssigned.val() || '').toString().trim();
        const s = ($filterStatus.val() || '').toString().trim();
        const p = ($filterPriority.val() || '').toString().trim();
        const ds = ($filterDueStart.val() || '').toString().trim();
        const de = ($filterDueEnd.val() || '').toString().trim();

        if (a) params.set('assigned_to', a);
        if (s) params.set('status', s);
        if (p) params.set('priority', p);
        if (ds) params.set('due_start', ds);
        if (de) params.set('due_end', de);

        if (period === 'daily') {
          const v = ($exportDate.val() || '').toString().trim();
          if (v) params.set('date', v);
        } else if (period === 'weekly') {
          const v = ($exportWeekDate.val() || '').toString().trim();
          if (v) params.set('week_date', v);
        } else if (period === 'monthly') {
          const v = ($exportMonth.val() || '').toString().trim();
          if (v) params.set('month', v);
        } else if (period === 'yearly') {
          const v = ($exportYear.val() || '').toString().trim();
          if (v) params.set('year', v);
        }

        const url = `${exportPdfUrl}?${params.toString()}`;
        window.location.href = url;

        const modalEl = document.getElementById('exportPdfModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
      });

      const openDetail = async (id) => {
        currentTaskId = id;
        await refreshDetail();
        const modal = new bootstrap.Modal(document.getElementById('taskDetailModal'));
        modal.show();
      };

      const refreshDetail = async () => {
        if (!currentTaskId) return;
        const url = `{{ url('/admin/daily-tasks') }}/${currentTaskId}/json`;
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) {
          Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load task detail.' });
          return;
        }
        const data = await res.json();

        const hasOpenChecklist = (data.checklists || []).some((i) => !i.is_done);
        $('#detail-title').text(`#${data.id} - ${data.title}`);
        $('#detail-meta').text(`${data.task_type?.label || '-'} | ${data.status?.label || '-'} | SLA: ${data.sla || '-'} | ${data.priority?.label || '-'} | Due: ${data.due_start || '-'} → ${data.due_end || '-'}`);

        // Status change UI (role + flow restricted by server)
        const $statusSel = $('#detail-status-select');
        const $btnUpdateStatus = $('#btn-update-status');
        $statusSel.empty();
        const allowed = Array.isArray(data.allowed_statuses) ? data.allowed_statuses : [];
        if (allowed.length > 0) {
          allowed.forEach((opt) => {
            const v = String(opt.value);
            const label = String(opt.label || v);
            $statusSel.append(`<option value="${$('<div/>').text(v).html()}">${$('<div/>').text(label).html()}</option>`);
          });
        } else {
          $statusSel.append('<option value="">-</option>');
        }

        if (hasOpenChecklist) {
          $statusSel.find(`option[value="${String(doneStatusValue)}"]`).prop('disabled', true);
        }

        const currentStatusValue = (data.status && data.status.value != null) ? String(data.status.value) : '';
        $statusSel.val(currentStatusValue);

        const canStatusChange = !!canUpdate && allowed.some(o => String(o.value) === currentStatusValue);
        $statusSel.prop('disabled', !canStatusChange);
        $btnUpdateStatus.prop('disabled', !canStatusChange);

        const $check = $('#checklist-items');
        $check.empty();
        (data.checklists || []).forEach((i) => {
          const inputId = `chk-item-${i.id}`;
          const checked = i.is_done ? 'checked' : '';
          const disabledToggle = canToggleChecklist ? '' : 'disabled';
          const disabledManage = canUpdate ? '' : 'disabled';
          $check.append(`
            <div class="d-flex align-items-center justify-content-between border rounded px-2 py-1">
              <div class="form-check mb-0">
                <input class="form-check-input chk-item" id="${inputId}" type="checkbox" data-id="${i.id}" ${checked} ${disabledToggle}>
                <label class="form-check-label" for="${inputId}">${$('<div/>').text(i.item_text).html()}</label>
              </div>
              <button class="btn btn-sm btn-outline-danger btn-del-item" data-id="${i.id}" ${disabledManage}><i class="fas fa-trash"></i></button>
            </div>
          `);
        });

        const $att = $('#attachment-items');
        $att.empty();
        (data.attachments || []).forEach((a) => {
          const disabled = canUpdate ? '' : 'disabled';
          $att.append(`
            <div class="list-group-item d-flex justify-content-between align-items-center">
              <a href="${a.url}" target="_blank" rel="noopener">${$('<div/>').text(a.file_name).html()}</a>
              <button class="btn btn-sm btn-outline-danger btn-del-attachment" data-id="${a.id}" ${disabled}><i class="fas fa-trash"></i></button>
            </div>
          `);
        });
      };

      $('#btn-update-status').on('click', async function () {
        if (!currentTaskId) return;
        const statusVal = ($('#detail-status-select').val() || '').toString().trim();
        if (!statusVal) return;
        const url = `{{ url('/admin/daily-tasks') }}/${currentTaskId}`;
        const res = await fetch(url, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
          body: JSON.stringify({ status: Number(statusVal) })
        });
        if (!res.ok) {
          let msg = 'Failed to update status.';
          try {
            const body = await res.json();
            if (body && body.message) msg = body.message;
          } catch (_e) {}
          Swal.fire({ icon: 'error', title: 'Error', text: msg });
          return;
        }
        await refreshDetail();
        redraw();
      });

      $(document).on('click', '.btn-view-task', function () {
        const id = String($(this).data('id'));
        openDetail(id);
      });

      $('#btn-refresh-detail').on('click', refreshDetail);

      $('#btn-add-checklist').on('click', async function () {
        const text = ($('#new-checklist-text').val() || '').toString().trim();
        if (!text) return;
        const url = `{{ url('/admin/daily-tasks') }}/${currentTaskId}/checklists`;
        const res = await fetch(url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
          body: JSON.stringify({ item_text: text })
        });
        if (!res.ok) {
          Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to add checklist.' });
          return;
        }
        $('#new-checklist-text').val('');
        await refreshDetail();
        redraw();
      });

      $(document).on('change', '.chk-item', async function () {
        const id = String($(this).data('id'));
        const isDone = !!this.checked;
        const url = `{{ url('/admin/daily-tasks/checklists') }}/${id}`;
        const res = await fetch(url, {
          method: 'PATCH',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
          body: JSON.stringify({ is_done: isDone })
        });
        if (!res.ok) {
          let msg = 'Gagal memperbarui checklist.';
          try {
            const body = await res.json();
            if (body && body.message) msg = body.message;
          } catch (_e) {}
          Swal.fire({ icon: 'error', title: 'Error', text: msg });
          this.checked = !isDone;
          return;
        }
        redraw();
      });

      $(document).on('click', '.btn-del-item', async function () {
        const id = String($(this).data('id'));
        const url = `{{ url('/admin/daily-tasks/checklists') }}/${id}`;
        const res = await fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } });
        if (!res.ok) {
          Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete item.' });
          return;
        }
        await refreshDetail();
        redraw();
      });

      $('#btn-upload-attachment').on('click', async function () {
        const input = document.getElementById('upload-file');
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const url = `{{ url('/admin/daily-tasks') }}/${currentTaskId}/attachments`;
        const form = new FormData();
        form.append('file', file);
        const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: form });
        if (!res.ok) {
          Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to upload attachment.' });
          return;
        }
        input.value = '';
        await refreshDetail();
        redraw();
      });

      $(document).on('click', '.btn-del-attachment', async function () {
        const id = String($(this).data('id'));
        const url = `{{ url('/admin/daily-tasks/attachments') }}/${id}`;
        const res = await fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } });
        if (!res.ok) {
          Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete attachment.' });
          return;
        }
        await refreshDetail();
        redraw();
      });

      $('#btn-delete-task').on('click', async function () {
        if (!currentTaskId) return;
        const confirm = await Swal.fire({
          icon: 'warning',
          title: 'Delete task?',
          text: 'This will soft-delete the task.',
          showCancelButton: true,
          confirmButtonText: 'Delete'
        });
        if (!confirm.isConfirmed) return;
        const url = `{{ url('/admin/daily-tasks') }}/${currentTaskId}`;
        const res = await fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } });
        if (!res.ok) {
          Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete task.' });
          return;
        }
        Swal.fire({ icon: 'success', title: 'Deleted', timer: 1200, showConfirmButton: false });
        currentTaskId = null;
        redraw();
        const modalEl = document.getElementById('taskDetailModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
      });

    })();
  </script>
@endsection
