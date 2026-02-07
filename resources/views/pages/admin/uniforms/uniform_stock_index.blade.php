@extends('layouts.master')

@section('title', 'Ilsam - Stok Seragam')

@section('title-sub', 'Administrasi')
@section('pagetitle', 'Stok Seragam')

@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @php
        $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'uniforms_stock', 'create');
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
                    <h5 class="card-title mb-0">Stok Seragam</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#uniformStockInModal" {{ $canCreate ? '' : 'disabled' }}>
                            + Stok Masuk (IN)
                        </button>
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.uniforms.reports.pivot.index') }}">
                            Laporan Pivot
                        </a>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.uniforms.stock.lots.index') }}">
                            Stok per LOT
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="text-muted small mb-3">
                        Rekap stok adalah SUM stock_on_hand dari seluruh LOT per ukuran.
                        Stok fisik (source of truth) disimpan di level LOT.
                    </div>

                    <div class="table-responsive">
                        <table id="uniform-stock-table" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Item</th>
                                    <th>Ukuran</th>
                                    <th class="text-end">Total Stok</th>
                                    <th style="width: 90px">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Stock In -->
    <div class="modal fade" id="uniformStockInModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.uniforms.stock.in') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Stok Masuk (IN) ke LOT</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Variant (Item - Ukuran)</label>
                                <select name="uniform_variant_id" class="form-select" required>
                                    <option value="">Pilih...</option>
                                    @foreach ($variants as $v)
                                        <option value="{{ $v->id }}" @selected(old('uniform_variant_id') == $v->id)>
                                            {{ $v->uniform?->name ?? '-' }} - {{ $v->size }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('uniform_variant_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label">Qty</label>
                                <input type="number" name="qty" class="form-control" min="1" value="{{ old('qty', 1) }}" required>
                                @error('qty')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-8">
                                <label class="form-label">Tanggal Transaksi (opsional)</label>
                                <input type="datetime-local" name="occurred_at" class="form-control" value="{{ old('occurred_at') }}">
                                @error('occurred_at')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <hr class="my-2" />
                                <div class="fw-semibold mb-1">LOT / Batch</div>
                                <div class="text-muted small">Pilih LOT yang sudah ada, atau biarkan kosong untuk membuat LOT baru (berdasarkan received_at). Kode LOT otomatis: <span class="fw-semibold">LOT-UF-YYYYMMDD</span>.</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Pilih LOT (opsional)</label>
                                <select name="uniform_lot_id" class="form-select">
                                    <option value="">(Buat LOT baru)</option>
                                    @foreach ($lots as $l)
                                        <option value="{{ $l->id }}" @selected(old('uniform_lot_id') == $l->id)>
                                            {{ $l->lot_code }} • {{ optional($l->received_at)->format('Y-m-d H:i') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('uniform_lot_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div id="stockin-new-lot-fields" class="col-12">
                                <div class="border rounded p-3">
                                    <div class="fw-semibold mb-2">Buat LOT baru</div>
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">LOT Code</label>
                                            <input type="text" name="lot_code" id="stockin_lot_code" class="form-control" value="{{ old('lot_code') }}" placeholder="Otomatis" readonly disabled>
                                            @error('lot_code')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label class="form-label">Received At</label>
                                            <input type="datetime-local" name="received_at" id="stockin_received_at" class="form-control" value="{{ old('received_at') }}">
                                            @error('received_at')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Catatan LOT (opsional)</label>
                                            <textarea name="lot_notes" id="stockin_lot_notes" class="form-control" rows="2">{{ old('lot_notes') }}</textarea>
                                            @error('lot_notes')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Catatan Transaksi (opsional)</label>
                                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" {{ $canCreate ? '' : 'disabled' }}>Simpan</button>
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

    <script>
        $(document).ready(function() {
            const dtUrl = @json(route('admin.uniforms.stock.datatable'));
            const lotsIndexUrlTemplate = @json(route('admin.uniforms.stock.lots.index', ['uniform_variant_id' => '__VARIANT__']));

            function lotCodeFromReceivedAt(datetimeLocalVal) {
                if (!datetimeLocalVal) return '';
                const datePart = String(datetimeLocalVal).split('T')[0] || '';
                if (!datePart) return '';
                const ymd = datePart.replaceAll('-', '');
                if (!ymd) return '';
                return 'LOT-UF-' + ymd;
            }

            function syncLotFields() {
                const selectedLotId = String($('select[name="uniform_lot_id"]').val() || '');
                const isExisting = selectedLotId !== '';

                const $newLotWrap = $('#stockin-new-lot-fields');
                if ($newLotWrap.length) {
                    $newLotWrap.toggle(!isExisting);
                }

                $('#stockin_received_at').prop('disabled', isExisting);
                $('#stockin_lot_notes').prop('disabled', isExisting);
                $('#stockin_lot_code').prop('disabled', true);

                if (isExisting) {
                    $('#stockin_received_at').val('');
                    $('#stockin_lot_notes').val('');
                    $('#stockin_lot_code').val('');
                } else {
                    const code = lotCodeFromReceivedAt($('#stockin_received_at').val());
                    $('#stockin_lot_code').val(code);
                }
            }

            $('select[name="uniform_lot_id"]').on('change', syncLotFields);
            $('#stockin_received_at').on('change keyup', function() {
                const selectedLotId = String($('select[name="uniform_lot_id"]').val() || '');
                if (selectedLotId !== '') return;
                const code = lotCodeFromReceivedAt($(this).val());
                $('#stockin_lot_code').val(code);
            });

            syncLotFields();

            if ($.fn.dataTable && $.fn.dataTable.isDataTable('#uniform-stock-table')) {
                $('#uniform-stock-table').DataTable().destroy();
                $('#uniform-stock-table').find('tbody').empty();
            }

            $('#uniform-stock-table').DataTable({
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
                },
                order: [[1, 'asc']],
                columns: [
                    { data: 'uniform_code', defaultContent: '-' },
                    { data: 'uniform_name', defaultContent: '-' },
                    { data: 'size', defaultContent: '-' },
                    {
                        data: 'stock_total',
                        className: 'text-end',
                        render: function(data) {
                            return Number(data || 0).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'variant_id',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            const vid = Number(data || 0);
                            if (!vid) return '';
                            const url = String(lotsIndexUrlTemplate).replace('__VARIANT__', encodeURIComponent(String(vid)));
                            return `<a class="btn btn-outline-secondary btn-sm" href="${url}">LOT</a>`;
                        }
                    },
                ],
            });

            @if ($errors->any())
                const m = document.getElementById('uniformStockInModal');
                if (m) new bootstrap.Modal(m).show();
            @endif
        });
    </script>
@endsection
