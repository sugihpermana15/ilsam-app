@extends('layouts.master')

@section('title', 'Ilsam - Distribusi Seragam')

@section('title-sub', 'Administrasi')
@section('pagetitle', 'Distribusi Seragam')

@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @php
        $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'uniforms_distribution', 'create');
    @endphp

    <div class="row">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: @json(__('common.success')),
                        text: @json(session('success')),
                        timer: 2000,
                        showConfirmButton: false
                    });
                @endif
                @if (session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: @json(__('common.error')),
                        text: @json(session('error')),
                        timer: 2500,
                        showConfirmButton: false
                    });
                @endif
            });
        </script>

        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                    <h5 class="card-title mb-0">Distribusi Seragam</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uniformDistributionModal" {{ $canCreate ? '' : 'disabled' }}>
                            + Distribusi
                        </button>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.uniforms.distributions.dashboard') }}">Dashboard</a>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.uniforms.stock.index') }}">Stok</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="text-muted small mb-3">
                        Universal: langsung kurangi stok fisik per LOT (FIFO jika LOT tidak dipilih).
                        Assigned: hanya cek & kurangi kuota seragam (tanpa mengurangi stok fisik / LOT).
                    </div>

                    <div class="table-responsive">
                        <table id="uniform-distribution-table" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No</th>
                                    <th>Metode</th>
                                    <th>Karyawan</th>
                                    <th>Seragam / Ukuran / Qty</th>
                                    <th class="text-end">Qty</th>
                                    <th>Catatan</th>
                                    <th>Creator</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Distribution -->
    <div class="modal fade" id="uniformDistributionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.uniforms.distributions.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Distribusi Seragam</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-3">
                                <label class="form-label">Metode</label>
                                <select name="allocation_method" class="form-select" required>
                                    <option value="UNIVERSAL" @selected(old('allocation_method') === 'UNIVERSAL')>UNIVERSAL</option>
                                    <option value="ASSIGNED" @selected(old('allocation_method') === 'ASSIGNED')>ASSIGNED</option>
                                </select>
                                @error('allocation_method')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Karyawan</label>
                                <select name="employee_id" class="form-select" required>
                                    <option value="">Pilih...</option>
                                    @foreach ($employees as $e)
                                        <option value="{{ $e->id }}" @selected(old('employee_id') == $e->id)>{{ $e->name }}</option>
                                    @endforeach
                                </select>
                                <div class="text-muted small mt-2 d-none" id="uniform-assigned-employee-empty-hint">
                                    Tidak ada karyawan yang memiliki kuota seragam aktif / sisa kuota untuk tanggal ini.
                                </div>
                                @error('employee_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label">Tanggal (opsional)</label>
                                <input type="datetime-local" name="allocated_at" class="form-control" value="{{ old('allocated_at') }}">
                                @error('allocated_at')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Catatan (opsional)</label>
                                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <hr class="my-2" />
                                <div class="fw-semibold mb-2">Items</div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered" id="uniform-items-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 35%" class="js-col-uniform d-none">Uniform</th>
                                                <th style="width: 45%" class="js-col-size">Ukuran</th>
                                                <th style="width: 15%">Qty</th>
                                                <th style="width: 25%" class="js-col-lot">LOT (opsional)</th>
                                                <th style="width: 10%"></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="uniform-add-item">+ Tambah Item</button>

                                <div class="text-muted small mt-2 d-none" id="uniform-assigned-empty-hint">
                                    Karyawan ini tidak memiliki kuota seragam yang aktif / sisa kuota habis.
                                    Silakan set kuota di menu Kuota Seragam.
                                </div>

                                @error('items')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" {{ $canCreate ? '' : 'disabled' }}>Simpan</button>
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
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            const dtUrl = @json(route('admin.uniforms.distributions.datatable'));
            const assignedEmployeesUrl = @json(route('admin.uniforms.distributions.assigned-employees'));
            const assignedUniformsUrl = @json(route('admin.uniforms.distributions.assigned-uniforms'));

            if ($.fn.dataTable && $.fn.dataTable.isDataTable('#uniform-distribution-table')) {
                $('#uniform-distribution-table').DataTable().destroy();
                $('#uniform-distribution-table').find('tbody').empty();
            }

            $('#uniform-distribution-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pagingType: "full_numbers",
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                language: {
                    processing: "Memproses...",
                    search: "Cari : ",
                    searchPlaceholder: "Ketik untuk memfilter...",
                    lengthMenu: "Tampilkan _MENU_",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    zeroRecords: "Data tidak ditemukan",
                    emptyTable: "Tidak ada data",
                    paginate: {
                        first: '<i class="ri-arrow-left-double-line"></i>',
                        previous: '<i class="ri-arrow-left-s-line"></i>',
                        next: '<i class="ri-arrow-right-s-line"></i>',
                        last: '<i class="ri-arrow-right-double-line"></i>',
                    },
                },
                dom:
                    "<'row'<'col-sm-12 col-md-6 mb-3'l><'col-sm-12 col-md-6 mt-5'f>>" +
                    "<'table-responsive'tr>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                ajax: { url: dtUrl },
                order: [[0, 'desc']],
                columns: [
                    {
                        data: 'allocated_at',
                        render: function(data) {
                            return data ? String(data).slice(0, 16).replace('T', ' ') : '-';
                        }
                    },
                    { data: 'allocation_no', defaultContent: '-' },
                    {
                        data: 'allocation_method',
                        render: function(data) {
                            const t = (data || '').toString();
                            const cls = t === 'ASSIGNED' ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success';
                            return `<span class="badge ${cls}">${$('<div/>').text(t).html()}</span>`;
                        }
                    },
                    { data: 'employee_name', defaultContent: '-' },
                    {
                        data: 'items_summary',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            const t = String(data || '').trim();
                            if (!t) return '<span class="text-muted">-</span>';
                            const safe = $('<div/>').text(t).html();
                            return safe.replace(/;\s*/g, '<br>');
                        }
                    },
                    {
                        data: 'total_qty',
                        className: 'text-end',
                        render: function(data) {
                            return Number(data || 0).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'notes',
                        render: function(data) {
                            return $('<div/>').text(data || '-').html();
                        }
                    },
                    { data: 'creator_name', defaultContent: '-' },
                ],
            });

            const variantsAll = @json(collect($variants)->map(fn($v) => [
                'id' => (int) $v->id,
                'label' => (($v->uniform?->name ?? '-') . ' - ' . ($v->size ?? '-')),
            ])->values());

            const $method = $('[name="allocation_method"]');
            const $employee = $('[name="employee_id"]');
            const $allocatedAt = $('[name="allocated_at"]');

            const employeeOptionsAllHtml = $employee.html();

            let assignedUniformsCurrent = [];

            const isAssigned = () => String($method.val() || 'UNIVERSAL').toUpperCase() === 'ASSIGNED';

            const lots = @json(collect($lots)->map(fn($l) => [
                'id' => (int) $l->id,
                'label' => (($l->lot_code ?? '-') . ' • ' . optional($l->received_at)->format('Y-m-d H:i')),
            ])->values());

            const renderVariantOptions = (list) => {
                const arr = Array.isArray(list) ? list : [];
                return arr.map(v => `<option value="${v.id}">${$('<div/>').text(v.label).html()}</option>`).join('');
            };

            const renderUniformOptions = () => {
                const base = `<option value="">Pilih...</option>`;
                const opts = assignedUniformsCurrent.map(u => {
                    const label = `${u.label} (sisa ${Number(u.remaining_qty || 0).toLocaleString('id-ID')})`;
                    return `<option value="${u.id}">${$('<div/>').text(label).html()}</option>`;
                }).join('');
                return base + opts;
            };

            const renderLotOptions = () => {
                const base = `<option value="">(AUTO FIFO)</option>`;
                return base + lots.map(l => `<option value="${l.id}">${$('<div/>').text(l.label).html()}</option>`).join('');
            };

            const addRow = () => {
                const idx = $('#uniform-items-table tbody tr').length;
                const uniformCell = isAssigned()
                    ? `
                        <td class="js-col-uniform">
                            <select class="form-select form-select-sm js-uniform" name="items[${idx}][uniform_id]" required>
                                ${renderUniformOptions()}
                            </select>
                        </td>
                    `
                    : `
                        <td class="js-col-uniform d-none"></td>
                    `;

                const sizeCell = isAssigned()
                    ? `
                        <td class="js-col-size d-none"></td>
                    `
                    : `
                        <td class="js-col-size">
                            <select class="form-select form-select-sm js-variant" name="items[${idx}][uniform_variant_id]" required>
                                <option value="">Pilih ukuran...</option>
                                ${renderVariantOptions(variantsAll)}
                            </select>
                        </td>
                    `;

                const lotCell = isAssigned()
                    ? `
                        <td class="js-col-lot d-none"></td>
                    `
                    : `
                        <td class="js-col-lot">
                            <select class="form-select form-select-sm" name="items[${idx}][uniform_lot_id]">
                                ${renderLotOptions()}
                            </select>
                        </td>
                    `;

                const row = `
                    <tr>
                        ${uniformCell}
                        ${sizeCell}
                        <td>
                            <input type="number" class="form-control form-control-sm" name="items[${idx}][qty]" min="1" value="1" required />
                        </td>
                        ${lotCell}
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm js-remove">Hapus</button>
                        </td>
                    </tr>
                `;
                $('#uniform-items-table tbody').append(row);
            };

            const setAssignedModeUi = () => {
                const assigned = isAssigned();
                $('.js-col-uniform').toggleClass('d-none', !assigned);
                $('.js-col-size').toggleClass('d-none', assigned);
                $('.js-col-lot').toggleClass('d-none', assigned);
                $('#uniform-assigned-empty-hint').addClass('d-none');

                // Reset items to match mode.
                $('#uniform-items-table tbody').empty();
                addRow();

                // Apply visibility for the freshly added row.
                $('.js-col-uniform').toggleClass('d-none', !assigned);
                $('.js-col-size').toggleClass('d-none', assigned);
                $('.js-col-lot').toggleClass('d-none', assigned);
            };

            const setEmployeeOptions = (items) => {
                const base = `<option value="">Pilih...</option>`;
                const opts = (Array.isArray(items) ? items : []).map(e => {
                    return `<option value="${e.id}">${$('<div/>').text(e.label || '-').html()}</option>`;
                }).join('');
                $employee.html(base + opts);

                const showEmpty = isAssigned() && (Array.isArray(items) ? items.length === 0 : true);
                $('#uniform-assigned-employee-empty-hint').toggleClass('d-none', !showEmpty);
            };

            const loadAssignedEmployees = () => {
                const allocatedAt = String($allocatedAt.val() || '').trim();
                const params = allocatedAt ? { allocated_at: allocatedAt } : {};

                return $.getJSON(assignedEmployeesUrl, params)
                    .done(function(resp) {
                        const data = (resp && Array.isArray(resp.data)) ? resp.data : [];
                        setEmployeeOptions(data);
                    })
                    .fail(function() {
                        setEmployeeOptions([]);
                    });
            };

            const loadAssignedUniforms = (employeeId) => {
                assignedUniformsCurrent = [];

                if (!employeeId) {
                    return $.Deferred().resolve().promise();
                }

                const allocatedAt = String($allocatedAt.val() || '').trim();
                const params = allocatedAt ? { employee_id: employeeId, allocated_at: allocatedAt } : { employee_id: employeeId };

                return $.getJSON(assignedUniformsUrl, params)
                    .done(function(resp) {
                        assignedUniformsCurrent = (resp && Array.isArray(resp.data)) ? resp.data : [];
                    })
                    .fail(function() {
                        assignedUniformsCurrent = [];
                    });
            };

            const syncUniformSelects = () => {
                $('#uniform-items-table tbody select.js-uniform').each(function() {
                    const prev = String($(this).val() || '');
                    $(this).html(renderUniformOptions());
                    $(this).val(prev);
                });

                const showHint = isAssigned() && String($employee.val() || '') !== '' && assignedUniformsCurrent.length === 0;
                $('#uniform-assigned-empty-hint').toggleClass('d-none', !showHint);
            };

            const syncMode = () => {
                if (!isAssigned()) {
                    // Restore full employee list.
                    $employee.html(employeeOptionsAllHtml);
                    $('#uniform-assigned-employee-empty-hint').addClass('d-none');
                    assignedUniformsCurrent = [];
                    setAssignedModeUi();
                    return;
                }

                // ASSIGNED: only entitled employees.
                loadAssignedEmployees().always(function() {
                    // Reset current selection and dependent state.
                    $employee.val('');
                    assignedUniformsCurrent = [];
                    setAssignedModeUi();
                });
            };

            const renumber = () => {
                $('#uniform-items-table tbody tr').each(function(i) {
                    $(this).find('select, input').each(function() {
                        const name = $(this).attr('name');
                        if (!name) return;
                        $(this).attr('name', name.replace(/items\[\d+\]/, `items[${i}]`));
                    });
                });
            };

            $('#uniform-add-item').on('click', function() {
                addRow();
            });

            $('#uniform-items-table').on('click', '.js-remove', function() {
                $(this).closest('tr').remove();
                renumber();
            });

            // ASSIGNED: when employee changes, reload uniform options.
            $(document).on('change', '[name="employee_id"]', function() {
                if (!isAssigned()) return;

                const employeeId = String($employee.val() || '').trim();
                loadAssignedUniforms(employeeId).always(function() {
                    syncUniformSelects();

                    // reset uniforms in all rows
                    $('#uniform-items-table tbody tr').each(function() {
                        $(this).find('select.js-uniform').val('');
                    });
                });
            });

            // Method change: switch UX.
            $(document).on('change', '[name="allocation_method"]', function() {
                syncMode();
            });

            // Date change: for ASSIGNED, eligibility depends on the selected date.
            $(document).on('change', '[name="allocated_at"]', function() {
                if (!isAssigned()) return;
                syncMode();
            });

            // Add 1 default row.
            syncMode();

            @if ($errors->any() || session('error'))
                const m = document.getElementById('uniformDistributionModal');
                if (m) new bootstrap.Modal(m).show();
            @endif
        });
    </script>
@endsection
