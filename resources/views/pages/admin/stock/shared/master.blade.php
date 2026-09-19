@section('title', 'Ilsam - Master ' . strtoupper($category))
@section('title-sub', 'Application')
@section('pagetitle', 'Master ' . strtoupper($category))

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <style>
        .stock-actions {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap
        }

        .stock-thumb {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 6px
        }

        .stock-placeholder {
            display: inline-flex;
            width: 48px;
            height: 48px;
            align-items: center;
            justify-content: center;
            background: #f1f3f5;
            color: #98a2b3;
            border-radius: 6px
        }

        .stock-actions .btn {
            white-space: nowrap;
        }

        #stock-master-table_wrapper>.row:last-child {
            align-items: center;
            row-gap: .75rem;
            margin-top: .25rem;
        }

        #stock-master-table_wrapper .dataTables_info {
            padding-top: 0;
            color: #6c757d;
            font-size: .875rem;
        }

        #stock-master-table_wrapper .dataTables_paginate {
            padding-top: 0;
        }

        #stock-master-table_wrapper .pagination {
            justify-content: flex-end;
            gap: .35rem;
            margin: 0;
        }

        #stock-master-table_wrapper .page-item .page-link {
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

        #stock-master-table_wrapper .page-item.active .page-link {
            border-color: #f26b21;
            background-color: #f26b21;
            color: #fff;
        }

        #stock-master-table_wrapper .page-item.disabled .page-link {
            color: #adb5bd;
            background-color: #f8f9fa;
        }

        @media (max-width: 575.98px) {
            #stock-master-table_wrapper>.row:last-child>div {
                text-align: center !important;
            }

            #stock-master-table_wrapper .pagination {
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
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card">
                <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                    <h5 class="card-title mb-0">Master {{ strtoupper($category) }}</h5>
                    <div class="stock-actions">
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#stockCreateModal"><i
                                class="fas fa-plus"></i> Tambah Barang</button>
                        <a class="btn btn-primary btn-sm"
                            href="{{ route('admin.stock.restock.index', ['category' => $category]) }}"><i
                                class="fas fa-arrow-down"></i> Stok Masuk</a>
                        <a class="btn btn-outline-primary btn-sm"
                            href="{{ route('admin.stock.ledger.index', ['category' => $category]) }}"><i
                                class="fas fa-book"></i> Ledger</a>
                        <a class="btn btn-outline-success btn-sm"
                            href="{{ route('admin.stock.master.template', ['category' => $category]) }}"><i
                                class="fas fa-file-arrow-down"></i> Template Excel</a>
                        <a class="btn btn-outline-dark btn-sm"
                            href="{{ route('admin.stock.master.export', ['category' => $category]) }}"><i
                                class="fas fa-file-excel"></i> Export</a>
                        <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal"
                            data-bs-target="#stockImportModal"><i class="fas fa-file-import"></i> Import</button>
                    </div>

                    <div class="modal fade" id="stockImportModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.stock.master.import', ['category' => $category]) }}"
                                    method="POST" enctype="multipart/form-data">@csrf<div class="modal-header">
                                        <h5 class="modal-title">Import Master dari Excel</h5><button type="button"
                                            class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="small text-muted">Gunakan file CSV dari template Excel. Kolom: name,
                                            unit, unit_price, low_stock_threshold, is_active. Kode dibuat otomatis.</p><input type="file"
                                            name="import_file" class="form-control" accept=".csv,.txt" required>
                                    </div>
                                    <div class="modal-footer"><button type="submit" class="btn btn-warning"><i
                                                class="fas fa-upload"></i> Import Data</button></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-muted small mb-3">Kelola barang {{ strtoupper($category) }}, harga satuan, gambar, dan
                        status penggunaannya.</div>
                    <table id="stock-master-table" class="table table-striped table-bordered align-middle w-100">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Gambar</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Jababeka</th>
                                <th class="text-end">Karawang</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="stockCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.stock.master.store', ['category' => $category]) }}" method="POST"
                    enctype="multipart/form-data">@csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Barang {{ strtoupper($category) }}</h5><button type="button"
                            class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body"><input type="hidden" name="modal_context" value="create_stock">
                        <div class="row g-3">
                            <div class="col-md-3"><label class="form-label">Kode Sistem</label><input name="code_display"
                                    class="form-control" value="Otomatis" readonly></div>
                            <div class="col-md-4"><label class="form-label">Nama Barang</label><input name="name"
                                    class="form-control" value="{{ old('name') }}" required></div>
                            <div class="col-md-2"><label class="form-label">Satuan</label><input name="unit"
                                    list="stock-units" class="form-control" value="{{ old('unit', 'pcs') }}" required>
                            </div>
                            <div class="col-md-2"><label class="form-label">Harga (Satuan)</label><input
                                    name="unit_price" type="number" min="0" class="form-control"
                                    value="{{ old('unit_price', 0) }}" required></div>
                            <div class="col-md-3"><label class="form-label">Batas Stok Menipis</label><input
                                    name="low_stock_threshold" type="number" min="0" class="form-control"
                                    value="{{ old('low_stock_threshold', 5) }}" required></div>
                            <div class="col-md-8"><label class="form-label">Gambar Barang</label><input name="image"
                                    type="file" accept="image/jpeg,image/png,image/webp" class="form-control"></div>
                            <div class="col-md-4 d-flex align-items-center">
                                <div class="form-check mt-4"><input name="is_active" value="1" type="checkbox"
                                        class="form-check-input" checked><label class="form-check-label">Aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">Batal</button><button class="btn btn-success"
                            type="submit">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="stockEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="stockEditForm" method="POST" enctype="multipart/form-data">@csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Barang {{ strtoupper($category) }}</h5><button type="button"
                            class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body"><input type="hidden" name="modal_context" value="edit_stock">
                        <div class="row g-3">
                            <div class="col-md-3"><label class="form-label">Kode Sistem</label><input id="edit_code"
                                    class="form-control" readonly></div>
                            <div class="col-md-5"><label class="form-label">Nama Barang</label><input id="edit_name"
                                    name="name" class="form-control" required></div>
                            <div class="col-md-2"><label class="form-label">Satuan</label><input id="edit_unit"
                                    name="unit" list="stock-units" class="form-control" required></div>
                            <div class="col-md-2"><label class="form-label">Harga (Satuan)</label><input
                                    id="edit_unit_price" name="unit_price" type="number" min="0"
                                    class="form-control" required></div>
                            <div class="col-md-3"><label class="form-label">Batas Stok Menipis</label><input
                                    id="edit_low_stock_threshold" name="low_stock_threshold" type="number"
                                    min="0" class="form-control" required></div>
                            <div class="col-md-8"><label class="form-label">Gambar Barang</label><input name="image"
                                    type="file" accept="image/jpeg,image/png,image/webp" class="form-control">
                                <div id="edit_image_hint" class="small text-muted mt-1"></div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center">
                                <div class="form-check mt-4"><input id="edit_is_active" name="is_active" value="1"
                                        type="checkbox" class="form-check-input"><label
                                        class="form-check-label">Aktif</label></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit">Simpan
                            Perubahan</button></div>
                </form>
            </div>
        </div>
    </div>
    <datalist id="stock-units">
        <option value="pcs">
        <option value="box">
        <option value="pack">
        <option value="set">
        <option value="unit">
        <option value="roll">
        <option value="rim">
        <option value="liter">
        <option value="kg">
        <option value="meter">
    </datalist>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>
    <script>
        $(function() {
            const base = @json('/admin/stock/' . $category);
            const detailUrl = `${base}/master/__STOCK_ITEM__/json`;
            const table = $('#stock-master-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pagingType: 'full_numbers',
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                dom: "<'row align-items-center'<'col-sm-12 col-md-6 mb-3'l><'col-sm-12 col-md-6 mb-3'f>>" +
                    "tr" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                ajax: `${base}/master/datatable`,
                language: {
                    processing: 'Memproses...',
                    search: 'Cari : ',
                    searchPlaceholder: 'Ketik untuk memfilter...',
                    lengthMenu: 'Tampilkan _MENU_',
                    zeroRecords: 'Data tidak ditemukan',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    infoFiltered: '(difilter dari _MAX_ total data)',
                    emptyTable: 'Tidak ada data',
                    paginate: {
                        first: '<i class="fas fa-angles-left" aria-hidden="true"></i>',
                        previous: '<i class="fas fa-angle-left" aria-hidden="true"></i>',
                        next: '<i class="fas fa-angle-right" aria-hidden="true"></i>',
                        last: '<i class="fas fa-angles-right" aria-hidden="true"></i>'
                    }
                },
                columns: [{
                    data: 'code',
                    render: d => `<span class="fw-semibold text-primary">${d || '-'}</span>`
                }, {
                    data: 'image_url',
                    orderable: false,
                    searchable: false,
                    render: d => d ? `<img class="stock-thumb" src="${d}" alt="">` :
                        '<span class="stock-placeholder"><i class="fas fa-image"></i></span>'
                }, {
                    data: 'name'
                }, {
                    data: 'unit'
                }, {
                    data: 'unit_price',
                    className: 'text-end',
                    render: d => 'Rp ' + Number(d || 0).toLocaleString('id-ID')
                }, {
                    data: 'stock_jababeka',
                    className: 'text-end'
                }, {
                    data: 'stock_karawang',
                    className: 'text-end'
                }, {
                    data: 'is_active',
                    render: d => d ? '<span class="badge bg-success">Aktif</span>' :
                        '<span class="badge bg-secondary">Nonaktif</span>'
                }, {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: (id, t, row) =>
                        `<button type="button" class="btn btn-sm btn-outline-primary js-edit" data-id="${id}">Edit</button> <form class="d-inline" method="POST" action="${base}/master/${id}/toggle"><input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="PATCH"><button type="submit" class="btn btn-sm ${row.is_active?'btn-outline-danger':'btn-outline-success'}">${row.is_active?'Nonaktifkan':'Aktifkan'}</button></form>`
                }]
            });
            $(document).on('click', '.js-edit', async function() {
                const id = $(this).data('id');
                try {
                    const response = await fetch(detailUrl.replace('__STOCK_ITEM__', encodeURIComponent(
                        id)), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    const data = await response.json();
                    $('#stockEditForm').attr('action', `${base}/master/${id}`);
                    $('#edit_code').val(data.code || '');
                    $('#edit_name').val(data.name);
                    $('#edit_unit').val(data.unit);
                    $('#edit_unit_price').val(data.unit_price);
                    $('#edit_low_stock_threshold').val(data.low_stock_threshold);
                    $('#edit_is_active').prop('checked', data.is_active);
                    $('#edit_image_hint').text(data.image_url ? 'Gambar saat ini tersimpan.' :
                        'Belum ada gambar.');
                    new bootstrap.Modal(document.getElementById('stockEditModal')).show();
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: @json(__('common.error')),
                        text: `Data barang tidak dapat dimuat${error.message ? ` (${error.message})` : ''}. Silakan muat ulang halaman.`,
                        timer: 3000,
                        showConfirmButton: false
                    });
                    console.error('Gagal memuat detail stock item:', error);
                }
            });
            @if (old('modal_context') === 'create_stock')
                new bootstrap.Modal('#stockCreateModal').show();
            @endif
            @if (old('modal_context') === 'edit_stock')
                new bootstrap.Modal('#stockEditModal').show();
            @endif
        });
    </script>
@endsection
