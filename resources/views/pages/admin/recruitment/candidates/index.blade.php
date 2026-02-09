@extends('layouts.master')

@section('title', 'Rekrutmen - Kandidat')

@section('title-sub', 'Recruitment')
@section('pagetitle', 'List Kandidat')

@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
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
                    <h5 class="card-title mb-0">Kandidat</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.recruitment.forms.index') }}">Forms</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3 align-items-end mb-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label">Filter Form</label>
                            <select class="form-select" id="filter_form_id">
                                <option value="">Semua</option>
                                @foreach($forms as $f)
                                    <option value="{{ $f->id }}">{{ $f->title }} - {{ $f->position_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="recruitment-candidates-table" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Dibuat</th>
                                    <th>Kode Kandidat</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>No HP</th>
                                    <th>Posisi</th>
                                    <th>Nilai</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
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
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            const dtUrl = @json(route('admin.recruitment.candidates.datatable'));

            const table = $('#recruitment-candidates-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pagingType: 'full_numbers',
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
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
                    url: dtUrl,
                    data: function(d) {
                        d.f_form_id = $('#filter_form_id').val();
                    }
                },
                order: [[0, 'desc']],
                columns: [
                    { data: 'created_at', defaultContent: '-' },
                    { data: 'candidate_code', defaultContent: '-' },
                    { data: 'full_name', defaultContent: '-' },
                    { data: 'email', defaultContent: '-' },
                    { data: 'phone', defaultContent: '-' },
                    { data: 'position_applied', defaultContent: '-' },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const earned = row && row.test_points_earned != null ? Number(row.test_points_earned) : 0;
                            const total = row && row.test_points_total != null ? Number(row.test_points_total) : 0;
                            if (!total) return '<span class="text-muted">-</span>';
                            return `<span class="fw-semibold">${earned}</span> / ${total}`;
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            const key = (row && row.status ? String(row.status) : '');
                            const label = (row && row.status_label ? String(row.status_label) : key);
                            const cls = key === 'test_submitted' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning';
                            return `<span class="badge ${cls}">${$('<div/>').text(label).html()}</span>`;
                        }
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            const showUrl = data && data.show_url ? String(data.show_url) : '#';
                            const safe = $('<div/>').text(showUrl).html();
                            return `<a class="btn btn-sm btn-outline-primary" href="${safe}">Detail</a>`;
                        }
                    }
                ]
            });

            $('#filter_form_id').on('change', function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
