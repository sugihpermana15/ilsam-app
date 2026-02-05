@extends('layouts.master')

@section('title', 'Ilsam - Ledger Materai')

@section('title-sub', 'Administrasi')
@section('pagetitle', 'Ledger Materai')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
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
        $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'stamps_transactions', 'create');
        $canReadMaster = \App\Support\MenuAccess::can(auth()->user(), 'stamps_master', 'read');

        $currentUser = auth()->user();
        $currentPicId = $currentUser?->employee_id ? (int) $currentUser->employee_id : null;
        $currentPicName = $currentPicId
            ? (optional(($employees ?? collect())->firstWhere('id', $currentPicId))->name ?? ($currentUser?->name ?? '-'))
            : ($currentUser?->name ?? '-');
    @endphp
    <div class="row">
        {{-- SweetAlert2 notification --}}
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
                    <h5 class="card-title mb-0">Ledger Materai</h5>
                    <div class="igi-actions">
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#stampInModal" {{ $canCreate ? '' : 'disabled' }} title="{{ $canCreate ? '' : __('common.no_access_create') }}">
                            <i class="fas fa-arrow-down"></i> Pembelian (IN)
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#stampOutModal" {{ $canCreate ? '' : 'disabled' }} title="{{ $canCreate ? '' : __('common.no_access_create') }}">
                            <i class="fas fa-arrow-up"></i> Permintaan (OUT)
                        </button>
                        <a class="btn btn-primary btn-sm" href="{{ route('admin.stamps.report.pdf', request()->only(['stamp_id','trx_type','date_from','date_to'])) }}" target="_blank">
                            <i class="fas fa-file-pdf"></i> Unduh PDF
                        </a>
                        @if ($canReadMaster)
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.stamps.master.index') }}">
                                <i class="fas fa-database"></i> Master
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-muted small mb-3">
                        IN = materai baru dibeli/masuk stok. OUT = materai keluar saat user meminta.
                        Sumber data utama adalah ledger transaksi; saldo di snapshot balance diupdate atomik saat posting.
                    </div>

                    <form method="GET" action="{{ route('admin.stamps.transactions.index') }}" class="row g-3">
                        <div class="col-12 col-md-3">
                            <label class="form-label">Materai</label>
                            <select name="stamp_id" class="form-select">
                                <option value="">Semua</option>
                                @foreach ($stamps as $stamp)
                                    <option value="{{ $stamp->id }}" @selected(request('stamp_id') == $stamp->id)>
                                        {{ $stamp->name }} ({{ $stamp->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label">Tipe</label>
                            <select name="trx_type" class="form-select">
                                <option value="">Semua</option>
                                <option value="IN" @selected(request('trx_type') === 'IN')>IN</option>
                                <option value="OUT" @selected(request('trx_type') === 'OUT')>OUT</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label">Dari</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label">Sampai</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> Filter</button>
                            <a class="btn btn-light" href="{{ route('admin.stamps.transactions.index') }}"><i class="fas fa-rotate-left"></i> Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Riwayat Transaksi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100" data-dt-server="1">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No. Transaksi</th>
                                    <th>Materai</th>
                                    <th>Tipe</th>
                                    <th class="text-end">Jumlah</th>
                                    <th class="text-end">Saldo</th>
                                    <th>PIC</th>
                                    <th>Catatan</th>
                                    <th>Dibuat oleh</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- IN Modal -->
    <div class="modal fade" id="stampInModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.stamps.transactions.store_in') }}" method="POST">
                    @csrf
                    <input type="hidden" name="modal_context" value="trx_in">
                    <div class="modal-header">
                        <h5 class="modal-title">Pembelian Materai (IN / Masuk Stok)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any() && old('modal_context') === 'trx_in')
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Materai</label>
                                <select name="stamp_id" class="form-select" required>
                                    <option value="">-- pilih materai --</option>
                                    @foreach ($stampsActive as $stamp)
                                        <option value="{{ $stamp->id }}" @selected(old('stamp_id') == $stamp->id)>
                                            {{ $stamp->name }} ({{ $stamp->code }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Klik dropdown, lalu ketik untuk mencari.</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jumlah</label>
                                <input type="number" name="qty" class="form-control" value="{{ old('qty') }}" min="1" step="1" inputmode="numeric" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="trx_date" class="form-control" value="{{ old('trx_date', now()->toDateString()) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">PIC</label>
                                <input type="text" class="form-control" value="{{ $currentPicName }}" readonly>
                                <input type="hidden" name="pic_id" value="{{ $currentPicId }}">
                                <small class="text-muted">Otomatis mengikuti user yang login.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Catatan</label>
                                <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="contoh: vendor / pembelian / keterangan">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                        <button type="submit" class="btn btn-success" {{ $canCreate ? '' : 'disabled' }} title="{{ $canCreate ? '' : __('common.no_access_create') }}">
                            <i class="fas fa-save"></i> Simpan Stok Masuk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- OUT Modal -->
    <div class="modal fade" id="stampOutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.stamps.transactions.store_out') }}" method="POST">
                    @csrf
                    <input type="hidden" name="modal_context" value="trx_out">
                    <div class="modal-header">
                        <h5 class="modal-title">Permintaan Materai (OUT / Keluar Stok)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any() && old('modal_context') === 'trx_out')
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (session('error') && old('modal_context') === 'trx_out')
                            <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Materai</label>
                                <select name="stamp_id" class="form-select" required>
                                    <option value="">-- pilih materai --</option>
                                    @foreach ($stampsActive as $stamp)
                                        <option value="{{ $stamp->id }}" @selected(old('stamp_id') == $stamp->id)>
                                            {{ $stamp->name }} ({{ $stamp->code }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Klik dropdown, lalu ketik untuk mencari.</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jumlah</label>
                                <input type="number" name="qty" class="form-control" value="{{ old('qty') }}" min="1" step="1" inputmode="numeric" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="trx_date" class="form-control" value="{{ old('trx_date', now()->toDateString()) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">PIC (wajib)</label>
                                <select name="pic_id" class="form-select js-select2-modal" required>
                                    <option value="">Pilih peminta</option>
                                    @foreach ($employees as $e)
                                        <option value="{{ $e->id }}" @selected(old('pic_id') == $e->id)>{{ $e->name }}</option>
                                    @endforeach
                                </select>
                                <div class="text-muted small mt-1">Dipakai sebagai jejak audit: siapa yang meminta/mengambil materai.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Catatan</label>
                                <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="contoh: no. dokumen / tujuan penggunaan">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                        <button type="submit" class="btn btn-danger" {{ $canCreate ? '' : 'disabled' }} title="{{ $canCreate ? '' : __('common.no_access_create') }}">
                            <i class="fas fa-save"></i> Simpan Stok Keluar
                        </button>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/js/table/datatable.init.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>

    <script>
        $(document).ready(function() {
            const dtUrl = @json(route('admin.stamps.transactions.datatable'));

            const initSelect2InModal = ($modal) => {
                const $modalSelects = $modal.find('.js-select2-modal');
                if (!$modalSelects.length || !$.fn.select2) {
                    return;
                }

                $modalSelects.each(function() {
                    const $el = $(this);
                    if ($el.hasClass('select2-hidden-accessible')) {
                        $el.select2('destroy');
                    }

                    $el.select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        dropdownParent: $modal,
                        placeholder: 'Ketik untuk mencari...'
                    });
                });
            };

            // Ensure searchable PIC on OUT modal.
            const $outModal = $('#stampOutModal');
            if ($outModal.length) {
                initSelect2InModal($outModal);
                $outModal.on('shown.bs.modal', function() {
                    initSelect2InModal($outModal);
                });
            }

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
                },
                dom:
                    "<'row'<'col-sm-12 col-md-6 mb-3'l><'col-sm-12 col-md-6 mt-5'f>>" +
                    "<'table-responsive'tr>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                ajax: {
                    url: dtUrl,
                    data: function(d) {
                        const stampId = ($('select[name="stamp_id"]').val() || '').toString().trim();
                        const trxType = ($('select[name="trx_type"]').val() || '').toString().trim();
                        const dateFrom = ($('input[name="date_from"]').val() || '').toString().trim();
                        const dateTo = ($('input[name="date_to"]').val() || '').toString().trim();
                        if (stampId) d.stamp_id = stampId;
                        if (trxType) d.trx_type = trxType;
                        if (dateFrom) d.date_from = dateFrom;
                        if (dateTo) d.date_to = dateTo;
                    }
                },
                order: [[0, 'desc']],
                columns: [
                    {
                        data: 'trx_date',
                        render: function(data) {
                            return data ? String(data).slice(0, 10) : '-';
                        }
                    },
                    { data: 'trx_no', defaultContent: '-' },
                    {
                        data: 'stamp_name',
                        render: function(data, _type, row) {
                            const name = $('<div/>').text(data || '-').html();
                            const code = $('<div/>').text(row.stamp_code || '').html();
                            const fv = Number(row.stamp_face_value || 0).toLocaleString('id-ID');
                            return `<div class="fw-semibold">${name}</div><div class="text-muted small">${code} • Rp ${fv}</div>`;
                        }
                    },
                    {
                        data: 'trx_type',
                        render: function(data) {
                            const t = (data || '').toString();
                            const cls = t === 'IN' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger';
                            return `<span class="badge ${cls}">${$('<div/>').text(t).html()}</span>`;
                        }
                    },
                    {
                        data: 'qty',
                        className: 'text-end',
                        render: function(data) {
                            return Number(data || 0).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'on_hand_after',
                        className: 'text-end',
                        render: function(data) {
                            return Number(data || 0).toLocaleString('id-ID');
                        }
                    },
                    { data: 'pic_name', defaultContent: '-' },
                    {
                        data: 'notes',
                        render: function(data) {
                            return $('<div/>').text(data || '-').html();
                        }
                    },
                    { data: 'creator_name', defaultContent: '-' },
                ],
            });

            @if ($errors->any() && old('modal_context') === 'trx_in')
                const inModal = document.getElementById('stampInModal');
                if (inModal) new bootstrap.Modal(inModal).show();
            @endif
            @if (($errors->any() && old('modal_context') === 'trx_out') || (session('error') && old('modal_context') === 'trx_out'))
                const outModal = document.getElementById('stampOutModal');
                if (outModal) new bootstrap.Modal(outModal).show();
            @endif
        });
    </script>
@endsection
