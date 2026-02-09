@extends('layouts.master')

@section('title', 'Rekrutmen - Forms')

@section('title-sub', 'Recruitment')
@section('pagetitle', 'Form Kandidat & Tes Pengetahuan')

@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @php
        $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'recruitment', 'create');
        $canDelete = \App\Support\MenuAccess::can(auth()->user(), 'recruitment', 'delete');
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
                    <h5 class="card-title mb-0">Forms Rekrutmen</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createRecruitmentFormModal" {{ $canCreate ? '' : 'disabled' }}>
                            + Buat Form
                        </button>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.recruitment.candidates.index') }}">List Kandidat</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="recruitment-forms-table" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Dibuat</th>
                                    <th>Judul</th>
                                    <th>Posisi</th>
                                    <th>Inisial</th>
                                    <th>Security</th>
                                    <th>Status</th>
                                    <th>Link</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="deleteRecruitmentFormForm" method="POST" style="display:none">
        @csrf
        @method('DELETE')
    </form>

    <div class="modal fade" id="createRecruitmentFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.recruitment.forms.store') }}">
                    @csrf
                    <input type="hidden" name="modal_context" value="create_recruitment_form">
                    <div class="modal-header">
                        <h5 class="modal-title">Buat Form Rekrutmen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any() && old('modal_context') === 'create_recruitment_form')
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Judul Form</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                            </div>

                            <div class="col-12 col-md-8">
                                <label class="form-label">Nama Posisi</label>
                                <input type="text" name="position_name" class="form-control" value="{{ old('position_name') }}" required>
                                <div class="form-text">Contoh: SATUAN PENGAMANAN</div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label">Inisial Kode Posisi</label>
                                <input type="text" name="position_code_initial" class="form-control" value="{{ old('position_code_initial') }}" maxlength="20" required>
                                <div class="form-text">Contoh: SEC</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="create_is_security" name="is_security_position" value="1" @checked(old('is_security_position'))>
                                    <label class="form-check-label" for="create_is_security">Posisi Security (wajib upload Garda Pratama + KTA)</label>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="create_is_active" name="is_active" value="1" @checked(old('is_active', true))>
                                    <label class="form-check-label" for="create_is_active">Aktif</label>
                                </div>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            const dtUrl = @json(route('admin.recruitment.forms.datatable'));

            $('#recruitment-forms-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pagingType: 'full_numbers',
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                language: {
                    processing: 'Memproses...',
                    search: 'Cari : ',
                    searchPlaceholder: 'Ketik untuk memfilter...',
                    lengthMenu: 'Tampilkan _MENU_',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    infoFiltered: '(difilter dari _MAX_ total data)',
                    zeroRecords: 'Data tidak ditemukan',
                    emptyTable: 'Tidak ada data',
                    paginate: {
                        first: '<i class="ri-arrow-left-double-line"></i>',
                        previous: '<i class="ri-arrow-left-s-line"></i>',
                        next: '<i class="ri-arrow-right-s-line"></i>',
                        last: '<i class="ri-arrow-right-double-line"></i>',
                    },
                },
                dom: "<'row'<'col-sm-12 col-md-6 mb-3'l><'col-sm-12 col-md-6 mt-5'f>>" +
                    "<'table-responsive'tr>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                ajax: {
                    url: dtUrl
                },
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'created_at',
                        defaultContent: '-'
                    },
                    {
                        data: 'title',
                        defaultContent: '-'
                    },
                    {
                        data: 'position_name',
                        defaultContent: '-'
                    },
                    {
                        data: 'position_code_initial',
                        defaultContent: '-'
                    },
                    {
                        data: 'is_security_position',
                        render: function(data) {
                            const v = !!data;
                            const cls = v ? 'bg-warning-subtle text-warning' : 'bg-secondary-subtle text-secondary';
                            return `<span class="badge ${cls}">${v ? 'SECURITY' : 'NON-SECURITY'}</span>`;
                        }
                    },
                    {
                        data: 'is_active',
                        render: function(data) {
                            const v = !!data;
                            const cls = v ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger';
                            return `<span class="badge ${cls}">${v ? 'AKTIF' : 'NONAKTIF'}</span>`;
                        }
                    },
                    {
                        data: 'public_url',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            const url = String(data || '');
                            const safe = $('<div/>').text(url).html();
                            return `
                                <div class="d-flex gap-2 align-items-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary js-copy-link" data-url="${safe}">
                                        Copy Link
                                    </button>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            const showUrl = data && data.show_url ? String(data.show_url) : '#';
                            const deleteUrl = data && data.delete_url ? String(data.delete_url) : '#';
                            const safeShow = $('<div/>').text(showUrl).html();
                            const safeDelete = $('<div/>').text(deleteUrl).html();
                            const canDelete = @json($canDelete);
                            return `
                                <div class="d-flex gap-2">
                                    <a class="btn btn-sm btn-outline-primary" href="${safeShow}">Detail</a>
                                    <button type="button" class="btn btn-sm btn-outline-danger js-del-form" ${canDelete ? '' : 'disabled'} data-delete-url="${safeDelete}">
                                        Hapus
                                    </button>
                                </div>
                            `;
                        }
                    },
                ]
            });

            document.addEventListener('click', async function(e) {
                const btn = e.target.closest('.js-copy-link');
                if (!btn) return;
                const url = btn.getAttribute('data-url') || '';

                try {
                    await navigator.clipboard.writeText(url);
                    Swal.fire({ icon: 'success', title: @json(__('common.success')), text: 'Link berhasil disalin.', timer: 1200, showConfirmButton: false });
                } catch (err) {
                    Swal.fire({ icon: 'error', title: @json(__('common.error')), text: 'Gagal menyalin link.', timer: 1600, showConfirmButton: false });
                }
            });

            document.addEventListener('click', function(e) {
                const delBtn = e.target.closest('.js-del-form');
                if (!delBtn) return;
                const url = delBtn.getAttribute('data-delete-url') || '#';

                Swal.fire({
                    title: 'Hapus form rekrutmen?',
                    text: 'Semua pertanyaan, jawaban, dan data kandidat pada form ini akan ikut terhapus.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (!result.isConfirmed) return;
                    const f = document.getElementById('deleteRecruitmentFormForm');
                    f.setAttribute('action', url);
                    f.submit();
                });
            });

            @if ($errors->any() && old('modal_context') === 'create_recruitment_form')
                const modal = document.getElementById('createRecruitmentFormModal');
                if (modal) new bootstrap.Modal(modal).show();
            @endif
        });
    </script>
@endsection
