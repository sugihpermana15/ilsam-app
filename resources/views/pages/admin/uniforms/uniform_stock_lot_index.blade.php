@extends('layouts.master')

@section('title', 'Ilsam - Stok Seragam per LOT')

@section('title-sub', 'Administrasi')
@section('pagetitle', 'Stok Seragam per LOT')

@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                    <h5 class="card-title mb-0">Stok per LOT</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.uniforms.stock.index') }}">Stok</a>
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.uniforms.reports.pivot.index') }}">Pivot</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-muted small mb-3">
                        Menampilkan stok fisik per LOT (source of truth).
                    </div>

                    <div class="table-responsive">
                        <table id="uniform-lot-stock-table" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Ukuran</th>
                                    <th>LOT Code</th>
                                    <th>Received At</th>
                                    <th class="text-end">Stock On Hand</th>
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
            const dtUrl = @json(route('admin.uniforms.reports.lots.datatable'));

            if ($.fn.dataTable && $.fn.dataTable.isDataTable('#uniform-lot-stock-table')) {
                $('#uniform-lot-stock-table').DataTable().destroy();
                $('#uniform-lot-stock-table').find('tbody').empty();
            }

            $('#uniform-lot-stock-table').DataTable({
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
                order: [[3, 'asc']],
                columns: [
                    { data: 'uniform_name', defaultContent: '-' },
                    { data: 'size', defaultContent: '-' },
                    { data: 'lot_code', defaultContent: '-' },
                    {
                        data: 'received_at',
                        render: function(data) {
                            return data ? String(data).slice(0, 16).replace('T', ' ') : '-';
                        }
                    },
                    {
                        data: 'stock_on_hand',
                        className: 'text-end',
                        render: function(data) {
                            return Number(data || 0).toLocaleString('id-ID');
                        }
                    },
                ],
            });
        });
    </script>
@endsection
