@extends('layouts.master')

@section('title', 'Ilsam - Master LOT Seragam')

@section('title-sub', 'Application')
@section('pagetitle', 'Master LOT Seragam')

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
        $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'uniforms_lots', 'create');
        $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'uniforms_lots', 'update');
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
                    <h6 class="mb-0">Master LOT Seragam</h6>
                    <div class="igi-actions">
                        @if ($canCreate)
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#lotCreateModal">
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
                    <div class="text-muted small mb-3">Kelola LOT penerimaan seragam.</div>
                    <div class="table-responsive">
                        <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100" data-dt-server="1">
                            <thead class="table-light">
                                <tr>
                                    <th data-orderable="true">LOT</th>
                                    <th data-orderable="true">Diterima</th>
                                    <th data-orderable="false">Catatan</th>
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

    <div class="modal fade" id="lotCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.uniforms.lots.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="modal_context" value="create_lot">

                    <div class="modal-header">
                        <h5 class="modal-title">Tambah LOT</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any() && old('modal_context') === 'create_lot')
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
                                <label class="form-label">Kode LOT</label>
                                <input type="text" name="lot_code" id="create_lot_code" class="form-control" value="{{ old('lot_code') }}" maxlength="60" readonly>
                                <div class="text-muted small">Otomatis: <span class="fw-semibold">LOT-UF-YYYYMMDD</span> (akan ditambah suffix jika bentrok).</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Diterima</label>
                                <input type="datetime-local" name="received_at" id="create_received_at" class="form-control" value="{{ old('received_at') }}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Catatan</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
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

    <div class="modal fade" id="lotEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="lotEditForm" method="POST" action="{{ old('lot_id') ? url('/admin/uniforms/lots/' . old('lot_id')) : '' }}">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="modal_context" value="edit_lot">
                    <input type="hidden" name="lot_id" id="edit_lot_id" value="{{ old('lot_id') }}">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit LOT</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any() && old('modal_context') === 'edit_lot')
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
                                <label class="form-label">Kode LOT</label>
                                <input type="text" name="lot_code" id="edit_lot_code" class="form-control" value="{{ old('lot_code') }}" maxlength="60" readonly>
                                <div class="text-muted small">Otomatis: <span class="fw-semibold">LOT-UF-YYYYMMDD</span> (akan ditambah suffix jika bentrok).</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Diterima</label>
                                <input type="datetime-local" name="received_at" id="edit_received_at" class="form-control" value="{{ old('received_at') }}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Catatan</label>
                                <textarea name="notes" id="edit_notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
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
            const dtUrl = @json(route('admin.uniforms.lots.datatable'));
            const canUpdate = @json($canUpdate);
            const baseUrl = @json(url('/admin/uniforms/lots'));

            function lotCodeFromReceivedAt(datetimeLocalVal) {
                if (!datetimeLocalVal) return '';
                const datePart = String(datetimeLocalVal).split('T')[0] || '';
                if (!datePart) return '';
                const ymd = datePart.replaceAll('-', '');
                if (!ymd) return '';
                return 'LOT-UF-' + ymd;
            }

            $('#create_received_at').on('change keyup', function() {
                const code = lotCodeFromReceivedAt($(this).val());
                $('#create_lot_code').val(code);
            });

            $('#edit_received_at').on('change keyup', function() {
                const code = lotCodeFromReceivedAt($(this).val());
                $('#edit_lot_code').val(code);
            });

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
                    [1, 'desc']
                ],
                columns: [{
                        data: 'lot_code'
                    },
                    {
                        data: 'received_at'
                    },
                    {
                        data: 'notes',
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
                            return `<button type="button" class="btn btn-sm btn-outline-primary js-lot-edit" data-id="${id}">Edit</button>`;
                        }
                    },
                ],
            });

            $(document).on('click', '.js-lot-edit', async function() {
                if (!canUpdate) return;

                const id = String($(this).data('id') || '');
                if (!id) return;

                const form = document.getElementById('lotEditForm');
                if (form) form.action = `${baseUrl}/${id}`;

                $('#edit_lot_id').val(id);
                $('#edit_lot_code').val('');
                $('#edit_received_at').val('');
                $('#edit_notes').val('');

                try {
                    const res = await fetch(`${baseUrl}/${id}/json`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!res.ok) throw new Error(`HTTP ${res.status}`);

                    const payload = await res.json();
                    $('#edit_lot_code').val(payload.lot_code || '');
                    $('#edit_received_at').val(payload.received_at || '');
                    $('#edit_notes').val(payload.notes || '');

                    const editModal = document.getElementById('lotEditModal');
                    if (editModal) new bootstrap.Modal(editModal).show();
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: @json(__('common.error')),
                        text: 'Gagal memuat data LOT untuk diedit.'
                    });
                }
            });

            @if ($errors->any() && old('modal_context') === 'create_lot')
                const addModal = document.getElementById('lotCreateModal');
                if (addModal) new bootstrap.Modal(addModal).show();
            @endif

            @if ($errors->any() && old('modal_context') === 'edit_lot')
                const oldEditId = @json(old('lot_id'));
                if (oldEditId) {
                    const form = document.getElementById('lotEditForm');
                    if (form) form.action = `${baseUrl}/${oldEditId}`;
                }
                const editModal = document.getElementById('lotEditModal');
                if (editModal) new bootstrap.Modal(editModal).show();
            @endif
        });
    </script>
@endsection
