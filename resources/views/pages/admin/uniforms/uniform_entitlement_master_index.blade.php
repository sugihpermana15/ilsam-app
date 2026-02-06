@extends('layouts.master')

@section('title', 'Ilsam - Kuota Seragam Karyawan')

@section('title-sub', 'Application')
@section('pagetitle', 'Kuota Seragam Karyawan')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <style>
        .igi-actions {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .igi-actions .btn {
            white-space: nowrap;
        }

        @media (max-width: 575.98px) {
            .igi-actions {
                flex-direction: column;
                width: 100%;
            }

            .igi-actions .btn {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'uniforms_entitlements', 'create');
        $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'uniforms_entitlements', 'update');
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
                    <h6 class="mb-0">Kuota Seragam Karyawan</h6>
                    <div class="igi-actions">
                        @if ($canCreate)
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#entCreateModal">
                                <i class="fas fa-plus"></i> Tambah / Set Kuota
                            </button>
                        @else
                            <button type="button" class="btn btn-success btn-sm" disabled title="{{ __('common.no_access_create') }}">
                                <i class="fas fa-plus"></i> Tambah / Set Kuota
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-muted small mb-3">Kuota digunakan untuk distribusi metode Assigned. Saat distribusi, used akan bertambah otomatis.</div>
                    <div class="table-responsive">
                        <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100" data-dt-server="1">
                            <thead class="table-light">
                                <tr>
                                    <th data-orderable="true">NIK/ID</th>
                                    <th data-orderable="true">Nama</th>
                                    <th data-orderable="true">Kode</th>
                                    <th data-orderable="true">Uniform</th>
                                    <th data-orderable="true" class="text-end">Total</th>
                                    <th data-orderable="true" class="text-end">Used</th>
                                    <th data-orderable="false" class="text-end">Sisa</th>
                                    <th data-orderable="true">Mulai</th>
                                    <th data-orderable="true">Sampai</th>
                                    <th data-orderable="false">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="entCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.uniforms.entitlements.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="modal_context" value="create_ent">

                    <div class="modal-header">
                        <h5 class="modal-title">Set Kuota Seragam</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any() && old('modal_context') === 'create_ent')
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Karyawan</label>
                                <select name="employee_id" class="form-select" required>
                                    <option value="">- pilih -</option>
                                    @foreach ($employees as $e)
                                        <option value="{{ $e->id }}" {{ (int) old('employee_id') === (int) $e->id ? 'selected' : '' }}>
                                            {{ $e->no_id }} - {{ $e->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Uniform</label>
                                <select name="uniform_id" class="form-select" required>
                                    <option value="">- pilih -</option>
                                    @foreach ($uniforms as $u)
                                        <option value="{{ $u->id }}" {{ (int) old('uniform_id') === (int) $u->id ? 'selected' : '' }}>
                                            {{ $u->code }} - {{ $u->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Total Kuota</label>
                                <input type="number" name="total_qty" class="form-control" value="{{ old('total_qty', 0) }}" min="0" step="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Berlaku Mulai</label>
                                <input type="date" name="effective_from" class="form-control" value="{{ old('effective_from') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Berlaku Sampai</label>
                                <input type="date" name="effective_to" class="form-control" value="{{ old('effective_to') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                        <button type="submit" class="btn btn-success" {{ $canCreate ? '' : 'disabled' }} title="{{ $canCreate ? '' : __('common.no_access_create') }}">{{ __('common.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="entEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="entEditForm" method="POST" action="{{ old('ent_id') ? url('/admin/uniforms/entitlements/' . old('ent_id')) : '' }}">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="modal_context" value="edit_ent">
                    <input type="hidden" name="ent_id" id="edit_ent_id" value="{{ old('ent_id') }}">
                    <input type="hidden" name="employee_id" id="edit_employee_id" value="{{ old('employee_id') }}">
                    <input type="hidden" name="uniform_id" id="edit_uniform_id" value="{{ old('uniform_id') }}">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Kuota</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any() && old('modal_context') === 'edit_ent')
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Karyawan</label>
                                <input type="text" id="edit_employee_label" class="form-control" value="" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Uniform</label>
                                <input type="text" id="edit_uniform_label" class="form-control" value="" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Total Kuota</label>
                                <input type="number" name="total_qty" id="edit_total_qty" class="form-control" value="{{ old('total_qty', 0) }}" min="0" step="1" required>
                                <div class="text-muted small mt-1">Used tidak diubah di sini.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Berlaku Mulai</label>
                                <input type="date" name="effective_from" id="edit_effective_from" class="form-control" value="{{ old('effective_from') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Berlaku Sampai</label>
                                <input type="date" name="effective_to" id="edit_effective_to" class="form-control" value="{{ old('effective_to') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                        <button type="submit" class="btn btn-primary" {{ $canUpdate ? '' : 'disabled' }} title="{{ $canUpdate ? '' : __('common.no_access_update') }}">{{ __('common.save') }}</button>
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
    <script src="{{ asset('assets/js/table/datatable.init.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>

    <script>
        $(document).ready(function() {
            const dtUrl = @json(route('admin.uniforms.entitlements.datatable'));
            const canUpdate = @json($canUpdate);
            const baseUrl = @json(url('/admin/uniforms/entitlements'));

            if ($.fn.dataTable && $.fn.dataTable.isDataTable('#alternative-pagination')) {
                $('#alternative-pagination').DataTable().destroy();
                $('#alternative-pagination').find('tbody').empty();
            }

            $('#alternative-pagination').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pagingType: "full_numbers",
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                dom: "<'row align-items-center'<'col-sm-12 col-md-6 mb-3'l><'col-sm-12 col-md-6 mb-3'f>>" +
                    "<'table-responsive'tr>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
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
                },
                ajax: {
                    url: dtUrl,
                },
                order: [
                    [1, 'asc'],
                    [3, 'asc']
                ],
                columns: [{
                        data: 'employee_no_id'
                    },
                    {
                        data: 'employee_name'
                    },
                    {
                        data: 'uniform_code'
                    },
                    {
                        data: 'uniform_name'
                    },
                    {
                        data: 'total_qty',
                        className: 'text-end'
                    },
                    {
                        data: 'used_qty',
                        className: 'text-end'
                    },
                    {
                        data: 'remaining_qty',
                        className: 'text-end'
                    },
                    {
                        data: 'effective_from',
                        render: function(data) {
                            const t = String(data || '').trim();
                            return t ? t : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'effective_to',
                        render: function(data) {
                            const t = String(data || '').trim();
                            return t ? t : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            if (!canUpdate) {
                                return '<span class="text-muted">-</span>';
                            }
                            const id = String(data);
                            return `<button type="button" class="btn btn-sm btn-outline-primary js-ent-edit" data-id="${id}">Edit</button>`;
                        }
                    },
                ],
            });

            $(document).on('click', '.js-ent-edit', async function() {
                if (!canUpdate) return;

                const id = String($(this).data('id') || '');
                if (!id) return;

                const form = document.getElementById('entEditForm');
                if (form) form.action = `${baseUrl}/${id}`;

                $('#edit_ent_id').val(id);
                $('#edit_employee_id').val('');
                $('#edit_uniform_id').val('');
                $('#edit_employee_label').val('');
                $('#edit_uniform_label').val('');
                $('#edit_total_qty').val('0');
                $('#edit_effective_from').val('');
                $('#edit_effective_to').val('');

                try {
                    const res = await fetch(`${baseUrl}/${id}/json`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!res.ok) throw new Error(`HTTP ${res.status}`);

                    const payload = await res.json();
                    $('#edit_employee_id').val(String(payload.employee_id || ''));
                    $('#edit_uniform_id').val(String(payload.uniform_id || ''));
                    $('#edit_employee_label').val(payload.employee_label || '');
                    $('#edit_uniform_label').val(payload.uniform_label || '');
                    $('#edit_total_qty').val(payload.total_qty ?? 0);
                    $('#edit_effective_from').val(payload.effective_from || '');
                    $('#edit_effective_to').val(payload.effective_to || '');

                    const editModal = document.getElementById('entEditModal');
                    if (editModal) new bootstrap.Modal(editModal).show();
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: @json(__('common.error')),
                        text: 'Gagal memuat data kuota untuk diedit.'
                    });
                }
            });

            @if ($errors->any() && old('modal_context') === 'create_ent')
                const addModal = document.getElementById('entCreateModal');
                if (addModal) new bootstrap.Modal(addModal).show();
            @endif

            @if ($errors->any() && old('modal_context') === 'edit_ent')
                const oldEditId = @json(old('ent_id'));
                if (oldEditId) {
                    const form = document.getElementById('entEditForm');
                    if (form) form.action = `${baseUrl}/${oldEditId}`;
                }
                const editModal = document.getElementById('entEditModal');
                if (editModal) new bootstrap.Modal(editModal).show();
            @endif

        });
    </script>
@endsection
