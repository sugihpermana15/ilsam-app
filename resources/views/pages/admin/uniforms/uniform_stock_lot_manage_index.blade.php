@extends('layouts.master')

@section('title', 'Ilsam - Stok Seragam per LOT')

@section('title-sub', 'Administrasi')
@section('pagetitle', 'Stok Seragam per LOT')

@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @php
        $canUpdateStock = \App\Support\MenuAccess::can(auth()->user(), 'uniforms_stock', 'update');
        $variantId = (int) ($variantId ?? 0);
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
                    <h5 class="card-title mb-0">Stok per LOT</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.uniforms.stock.index') }}">Rekap Stok</a>
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
                                    @if ($canUpdateStock)
                                        <th style="width: 120px">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($canUpdateStock)
        <!-- Modal: Adjust Stock -->
        <div class="modal fade" id="uniformLotStockAdjustModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <form method="POST" id="uniformLotStockAdjustForm" action="{{ route('admin.uniforms.stock.lot-stocks.adjust', ['lotStock' => old('lot_stock_id', 0)]) }}">
                        @csrf
                        @method('PATCH')

                        <input type="hidden" name="lot_stock_id" id="adjust_lot_stock_id" value="{{ old('lot_stock_id', 0) }}">

                        <div class="modal-header">
                            <h5 class="modal-title">Edit Stok (Penyesuaian)</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="alert alert-warning">
                                Fitur ini membuat <span class="fw-semibold">movement penyesuaian</span> (IN/OUT) agar histori tetap tercatat.
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Item</label>
                                    <input type="text" class="form-control" id="adjust_uniform_name" readonly>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Ukuran</label>
                                    <input type="text" class="form-control" id="adjust_size" readonly>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">LOT Code</label>
                                    <input type="text" class="form-control" id="adjust_lot_code" readonly>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Received At</label>
                                    <input type="text" class="form-control" id="adjust_received_at" readonly>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Stok Saat Ini</label>
                                    <input type="number" class="form-control" id="adjust_current_stock" readonly>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Stok Baru</label>
                                    <input type="number" name="stock_on_hand" class="form-control" min="0" value="{{ old('stock_on_hand') }}" required>
                                    @error('stock_on_hand')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Tanggal Penyesuaian (opsional)</label>
                                    <input type="datetime-local" name="occurred_at" class="form-control" value="{{ old('occurred_at') }}">
                                    @error('occurred_at')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Alasan / Catatan Penyesuaian</label>
                                    <textarea name="notes" class="form-control" rows="3" required>{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
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
            const dtUrl = @json(route('admin.uniforms.stock.lots.datatable'));
            const canUpdateStock = @json((bool) $canUpdateStock);
            const adjustUrlTemplate = @json(route('admin.uniforms.stock.lot-stocks.adjust', ['lotStock' => '__ID__']));
            const filterVariantId = @json($variantId);

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
                ajax: {
                    url: dtUrl,
                    data: function(d) {
                        if (filterVariantId && Number(filterVariantId) > 0) {
                            d.uniform_variant_id = Number(filterVariantId);
                        }
                    }
                },
                order: [[3, 'asc']],
                columns: (function() {
                    const cols = [
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
                    ];

                    if (canUpdateStock) {
                        cols.push({
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(_data, _type, row) {
                                const id = Number(row.lot_stock_id || 0);
                                if (!id) return '';
                                return `
                                    <button type="button" class="btn btn-outline-primary btn-sm js-adjust-stock"
                                        data-id="${id}"
                                        data-uniform-name="${$('<div/>').text(row.uniform_name || '').html()}"
                                        data-size="${$('<div/>').text(row.size || '').html()}"
                                        data-lot-code="${$('<div/>').text(row.lot_code || '').html()}"
                                        data-received-at="${$('<div/>').text(row.received_at || '').html()}"
                                        data-stock-on-hand="${Number(row.stock_on_hand || 0)}">
                                        Edit
                                    </button>
                                `;
                            }
                        });
                    }

                    return cols;
                })(),
            });

            if (canUpdateStock) {
                $(document).on('click', '.js-adjust-stock', function() {
                    const id = String($(this).data('id') || '');
                    if (!id) return;

                    const uniformName = $(this).data('uniform-name') || '';
                    const size = $(this).data('size') || '';
                    const lotCode = $(this).data('lot-code') || '';
                    const receivedAtRaw = $(this).data('received-at') || '';
                    const stockOnHand = Number($(this).data('stock-on-hand') || 0);

                    $('#adjust_lot_stock_id').val(id);
                    $('#adjust_uniform_name').val(uniformName);
                    $('#adjust_size').val(size);
                    $('#adjust_lot_code').val(lotCode);
                    $('#adjust_current_stock').val(stockOnHand);

                    const receivedAtPretty = receivedAtRaw ? String(receivedAtRaw).slice(0, 16).replace('T', ' ') : '-';
                    $('#adjust_received_at').val(receivedAtPretty);

                    const url = String(adjustUrlTemplate).replace('__ID__', encodeURIComponent(id));
                    $('#uniformLotStockAdjustForm').attr('action', url);

                    const m = document.getElementById('uniformLotStockAdjustModal');
                    if (m) new bootstrap.Modal(m).show();
                });

                @if ($errors->any() && (int) old('lot_stock_id', 0) > 0)
                    const m = document.getElementById('uniformLotStockAdjustModal');
                    if (m) new bootstrap.Modal(m).show();
                @endif
            }
        });
    </script>
@endsection
