@section('title', 'Ilsam - Ledger ' . strtoupper($category))
@section('title-sub', 'Administrasi')
@section('pagetitle', 'Ledger ' . strtoupper($category))
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <style>
        .ledger-actions {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        #stock-ledger-table_wrapper > .row:last-child {
            align-items: center;
            row-gap: .75rem;
            margin-top: .25rem;
        }

        #stock-ledger-table_wrapper .dataTables_info,
        #stock-ledger-table_wrapper .dataTables_paginate {
            padding-top: 0;
        }

        #stock-ledger-table_wrapper .pagination {
            justify-content: flex-end;
            gap: .35rem;
            margin: 0;
        }

        #stock-ledger-table_wrapper .page-item .page-link {
            min-width: 2.25rem;
            height: 2.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .35rem .6rem;
            border: 1px solid #dee2e6;
            border-radius: .375rem !important;
            color: #495057;
        }

        #stock-ledger-table_wrapper .page-item.active .page-link {
            border-color: #f26b21;
            background-color: #f26b21;
            color: #fff;
        }

        @media (max-width: 575.98px) {
            #stock-ledger-table_wrapper > .row:last-child > div {
                text-align: center !important;
            }

            #stock-ledger-table_wrapper .pagination {
                justify-content: center;
            }
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <div class="card">
                    <div
                        class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                        <h5 class="mb-0">Ledger {{ strtoupper($category) }} - {{ $siteLabel }}</h5>
                        <div class="ledger-actions">
                            <a class="btn btn-success btn-sm"
                                href="{{ route('admin.stock.restock.index', ['category' => $category, 'site' => $currentSite]) }}"><i
                                    class="fas fa-arrow-down me-1"></i>Stok Masuk</a>
                            <a class="btn btn-danger btn-sm"
                                href="{{ route('admin.stock.request.index', ['category' => $category, 'site' => $currentSite]) }}"><i
                                    class="fas fa-arrow-up me-1"></i>Pengambilan</a>
                            <a class="btn btn-outline-secondary btn-sm"
                                href="{{ route('admin.stock.master.index', ['category' => $category]) }}"><i
                                    class="fas fa-database me-1"></i>Master</a>
                            <a class="btn btn-outline-dark btn-sm"
                                href="{{ route('admin.stock.ledger.export', ['category' => $category, 'site' => $currentSite]) }}"><i
                                    class="fas fa-file-excel me-1"></i>Export Excel</a>
                            <a class="btn btn-outline-danger btn-sm" target="_blank"
                                href="{{ route('admin.stock.report.pdf', ['category' => $category, 'site' => $currentSite]) }}"><i
                                    class="fas fa-file-pdf me-1"></i>PDF Laporan</a>
                            <a class="btn btn-outline-secondary btn-sm" target="_blank"
                                href="{{ route('admin.stock.report.print', ['category' => $category, 'site' => $currentSite]) }}"><i
                                    class="fas fa-print me-1"></i>Print</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="ledgerFilter" class="row g-3">
                            <div class="col-md-3"><label class="form-label">Site</label><select name="site_code"
                                    class="form-select">
                                    @foreach ($sites as $code => $label)
                                        <option value="{{ $code }}" @selected($currentSite === $code)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select></div>
                            <div class="col-md-3"><label class="form-label">Tanggal Dari</label><input name="date_from"
                                    type="date" class="form-control"></div>
                            <div class="col-md-3"><label class="form-label">Tanggal Sampai</label><input name="date_to"
                                    type="date" class="form-control"></div>
                            <div class="col-md-2"><label class="form-label">Tipe</label><select name="trx_type"
                                    class="form-select">
                                    <option value="">Semua</option>
                                    <option value="IN">IN</option>
                                    <option value="OUT">OUT</option>
                                </select></div>
                            <div class="col-md-1 d-flex align-items-end"><button class="btn btn-primary" type="submit"><i
                                        class="fas fa-filter"></i></button></div>
                        </form>
                    </div>
                </div>
        </div>
        <div class="col-12">
            <div class="card">
                    <div class="card-body">
                        <table id="stock-ledger-table" class="table table-nowrap table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No. Transaksi</th>
                                    <th>Nama Barang</th>
                                    <th>Satuan</th>
                                    <th>Site</th>
                                    <th>Tipe</th>
                                    <th class="text-end">Jumlah</th>
                                    <th class="text-end">Saldo (Stok Akhir)</th>
                                    <th>PIC (Nama Pengambil)</th>
                                    <th>Catatan</th>
                                    <th>Dibuat Oleh</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>
    <script>
        $(function() {
            const form = $('#ledgerFilter');
            const table = $('#stock-ledger-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pagingType: 'full_numbers',
                pageLength: 10,
                dom: "<'row align-items-center'<'col-sm-12 col-md-6 mb-3'l><'col-sm-12 col-md-6 mb-3'f>>" +
                    "tr" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                ajax: {
                    url: @json(route('admin.stock.ledger.datatable', ['category' => $category])),
                    data: d => {
                        form.serializeArray().forEach(v => d[v.name] = v.value);
                    }
                },
                language: {
                    search: 'Pencarian : ',
                    lengthMenu: 'Tampilkan _MENU_',
                    zeroRecords: 'Data tidak ditemukan',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    paginate: {
                        first: '<i class="fas fa-angles-left" aria-hidden="true"></i>',
                        previous: '<i class="fas fa-angle-left" aria-hidden="true"></i>',
                        next: '<i class="fas fa-angle-right" aria-hidden="true"></i>',
                        last: '<i class="fas fa-angles-right" aria-hidden="true"></i>'
                    }
                },
                columns: [{
                    data: 'trx_date'
                }, {
                    data: 'trx_no'
                }, {
                    data: 'item_name'
                }, {
                    data: 'item_unit'
                }, {
                    data: 'site_code',
                    render: d => d === 'jababeka' ? 'Jababeka' : 'Karawang'
                }, {
                    data: 'trx_type',
                    render: d => d === 'IN' ? '<span class="badge bg-success">IN</span>' :
                        '<span class="badge bg-danger">OUT</span>'
                }, {
                    data: 'qty',
                    className: 'text-end'
                }, {
                    data: 'stock_after',
                    className: 'text-end'
                }, {
                    data: 'pic_name'
                }, {
                    data: 'notes'
                }, {
                    data: 'creator_name'
                }]
            });
            form.on('submit', e => {
                e.preventDefault();
                table.ajax.reload();
            });
        });
    </script>
@endsection
