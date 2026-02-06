@extends('layouts.master')

@section('title', 'Ilsam - Master Seragam')

@section('title-sub', 'Application')
@section('pagetitle', 'Master Seragam')

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
        $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'uniforms_master', 'create');
        $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'uniforms_master', 'update');
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
                    <h6 class="mb-0">Master Seragam</h6>
                    <div class="igi-actions">
                        @if ($canCreate)
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#uniformCreateModal">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                        @else
                            <button type="button" class="btn btn-success btn-sm" disabled title="{{ __('common.no_access_create') }}">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-muted small mb-3">Kelola jenis seragam. Nonaktifkan jika sudah tidak digunakan.</div>
                    <div class="table-responsive">
                        <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100" data-dt-server="1">
                            <thead class="table-light">
                                <tr>
                                    <th data-orderable="true">Kode</th>
                                    <th data-orderable="true">Nama</th>
                                    <th data-orderable="true">Status</th>
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

    <div class="modal fade" id="uniformCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.uniforms.master.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="modal_context" value="create_uniform">

                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Uniform</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any() && old('modal_context') === 'create_uniform')
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Kode</label>
                                <input type="text" name="code" class="form-control" value="{{ old('code', $nextCode ?? '') }}" maxlength="50" readonly required>
                                <div class="text-muted small mt-1">Kode dibuat otomatis: IGI-UF-001 (+1).</div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Nama</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" maxlength="150" required>
                            </div>
                            <div class="col-md-12 d-flex align-items-center">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Aktif</label>
                                </div>
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

    <div class="modal fade" id="uniformEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="uniformEditForm" method="POST" action="{{ old('uniform_id') ? url('/admin/uniforms/master/' . old('uniform_id')) : '' }}">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="modal_context" value="edit_uniform">
                    <input type="hidden" name="uniform_id" id="edit_uniform_id" value="{{ old('uniform_id') }}">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Uniform</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any() && old('modal_context') === 'edit_uniform')
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Kode</label>
                                <input type="text" name="code" id="edit_code" class="form-control" value="{{ old('code') }}" maxlength="50" readonly required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Nama</label>
                                <input type="text" name="name" id="edit_name" class="form-control" value="{{ old('name') }}" maxlength="150" required>
                            </div>
                            <div class="col-md-12 d-flex align-items-center">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" value="1" id="edit_is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="edit_is_active">Aktif</label>
                                </div>
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
            const csrfToken = @json(csrf_token());
            const dtUrl = @json(route('admin.uniforms.master.datatable'));
            const canUpdate = @json($canUpdate);
            const baseUrl = @json(url('/admin/uniforms/master'));

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
                    [2, 'desc'],
                    [1, 'asc']
                ],
                columns: [{
                        data: 'code'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'is_active',
                        render: function(data) {
                            const active = !!data;
                            const cls = active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary';
                            const label = active ? 'Aktif' : 'Nonaktif';
                            return `<span class="badge ${cls}">${label}</span>`;
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, _type, row) {
                            if (!canUpdate) {
                                return '<span class="text-muted">-</span>';
                            }

                            const id = String(data);
                            const toggleUrl = `${baseUrl}/${id}/toggle`;
                            const active = !!row.is_active;

                            return `
                                <button type="button" class="btn btn-sm btn-outline-primary js-uniform-edit" data-id="${id}">Edit</button>
                                <form class="d-inline" method="POST" action="${toggleUrl}">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="_method" value="PATCH">
                                    <button class="btn btn-sm ${active ? 'btn-outline-danger' : 'btn-outline-success'}" type="submit">
                                        ${active ? 'Nonaktifkan' : 'Aktifkan'}
                                    </button>
                                </form>
                            `.trim();
                        }
                    },
                ],
            });

            $(document).on('click', '.js-uniform-edit', async function() {
                if (!canUpdate) return;

                const id = String($(this).data('id') || '');
                if (!id) return;

                const form = document.getElementById('uniformEditForm');
                if (form) form.action = `${baseUrl}/${id}`;

                $('#edit_uniform_id').val(id);
                $('#edit_code').val('');
                $('#edit_name').val('');
                $('#edit_is_active').prop('checked', false);

                try {
                    const res = await fetch(`${baseUrl}/${id}/json`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!res.ok) throw new Error(`HTTP ${res.status}`);

                    const payload = await res.json();
                    $('#edit_code').val(payload.code || '');
                    $('#edit_name').val(payload.name || '');
                    $('#edit_is_active').prop('checked', !!payload.is_active);

                    const editModal = document.getElementById('uniformEditModal');
                    if (editModal) new bootstrap.Modal(editModal).show();
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: @json(__('common.error')),
                        text: 'Gagal memuat data uniform untuk diedit.'
                    });
                }
            });

            @if ($errors->any() && old('modal_context') === 'create_uniform')
                const addModal = document.getElementById('uniformCreateModal');
                if (addModal) new bootstrap.Modal(addModal).show();
            @endif

            @if ($errors->any() && old('modal_context') === 'edit_uniform')
                const oldEditId = @json(old('uniform_id'));
                if (oldEditId) {
                    const form = document.getElementById('uniformEditForm');
                    if (form) form.action = `${baseUrl}/${oldEditId}`;
                }
                const editModal = document.getElementById('uniformEditModal');
                if (editModal) new bootstrap.Modal(editModal).show();
            @endif
        });
    </script>
@endsection
