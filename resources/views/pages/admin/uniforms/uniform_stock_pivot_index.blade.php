@extends('layouts.master')

@section('title', 'Ilsam - Laporan Pivot Stok Seragam')

@section('title-sub', 'Administrasi')
@section('pagetitle', 'Laporan Pivot Stok Seragam')

@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                    <h5 class="card-title mb-0">Pivot Stok (Item x Ukuran)</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.uniforms.stock.index') }}">Stok</a>
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.uniforms.reports.lots.index') }}">Stok per LOT</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-muted small mb-3">
                        Baris = Item (Uniform). Kolom = Ukuran. Value = SUM stok dari seluruh LOT. Total per item ditampilkan.
                    </div>

                    <div class="table-responsive">
                        <table id="uniform-pivot-table" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Item</th>
                                    @foreach ($sizes as $s)
                                        <th class="text-end">{{ $s }}</th>
                                    @endforeach
                                    <th class="text-end">TOTAL</th>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            const dtUrl = @json(route('admin.uniforms.reports.pivot.datatable'));
            const sizes = @json($sizes);

            const cols = [
                { data: 'uniform_code', defaultContent: '-' },
                { data: 'uniform_name', defaultContent: '-' },
            ];

            sizes.forEach(function(s) {
                const key = 'size_' + String(s).toLowerCase().trim().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
                cols.push({
                    data: key || 'size_unknown',
                    className: 'text-end',
                    render: function(data) {
                        return Number(data || 0).toLocaleString('id-ID');
                    }
                });
            });

            cols.push({
                data: 'total',
                className: 'text-end fw-semibold',
                render: function(data) {
                    return Number(data || 0).toLocaleString('id-ID');
                }
            });

            if ($.fn.dataTable && $.fn.dataTable.isDataTable('#uniform-pivot-table')) {
                $('#uniform-pivot-table').DataTable().destroy();
                $('#uniform-pivot-table').find('tbody').empty();
            }

            $('#uniform-pivot-table').DataTable({
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
                order: [[1, 'asc']],
                columns: cols,
            });
        });
    </script>
@endsection
