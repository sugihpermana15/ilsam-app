@section('title', 'Ilsam - Transfer Stok ' . strtoupper($category))
@section('title-sub', 'Administrasi')
@section('pagetitle', 'Transfer Stok ' . strtoupper($category))
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .transfer-status {
            font-size: .72rem;
        }

        .transfer-pagination .pagination {
            justify-content: flex-end;
            gap: .35rem;
            margin: 0;
        }

        .transfer-pagination .page-item .page-link {
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

        .transfer-pagination .page-item.active .page-link {
            border-color: #f26b21;
            background-color: #f26b21;
            color: #fff;
        }

        @media (max-width: 575.98px) {
            .transfer-pagination .pagination {
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
                    <div class="card-header">
                        <h5 class="mb-0">Transfer Jababeka ke Karawang - {{ strtoupper($category) }}</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.stock.transfer.store', ['category' => $category]) }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="row g-3">
                                <div class="col-md-5"><label class="form-label">Barang</label><select name="stock_item_id"
                                        class="form-select" required>
                                        <option value="">-- pilih barang --</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->id }}" @disabled($item->source_stock < 1)>
                                                {{ $item->name }} (stok Jababeka: {{ $item->source_stock }} {{ $item->unit }}){{ $item->source_stock < 1 ? ' - Kosong' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2"><label class="form-label">Qty</label><input name="qty"
                                        type="number" min="1" class="form-control" required></div>
                                <div class="col-md-3"><label class="form-label">PIC Karawang</label><select name="pic_id"
                                        class="form-select">
                                        <option value="">-- pilih PIC --</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100"><i
                                            class="fas fa-paper-plane"></i> Minta</button></div>
                                <div class="col-12"><label class="form-label">Catatan</label>
                                    <textarea name="notes" class="form-control" rows="2"
                                        placeholder="Contoh: Pulpen 1 box untuk kebutuhan Karawang"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Monitoring Pengiriman</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>No. Request</th>
                                        <th>Barang</th>
                                        <th>Qty</th>
                                        <th>Rute</th>
                                        <th>PIC</th>
                                        <th>Status</th>
                                        <th>Driver</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transfers as $transfer)
                                        <tr>
                                            <td>{{ $transfer->request_no }}<br><small
                                                    class="text-muted">{{ $transfer->created_at?->format('d/m/Y H:i') }}</small>
                                            </td>
                                            <td>{{ $transfer->item->name }}</td>
                                            <td>{{ $transfer->qty }} {{ $transfer->item->unit }}</td>
                                            <td>Jababeka <i class="fas fa-arrow-right"></i> Karawang</td>
                                            <td>{{ $transfer->requestedPic?->name ?? '-' }}</td>
                                            <td><span
                                                    class="badge bg-{{ $transfer->status === 'RECEIVED' ? 'success' : ($transfer->status === 'REJECTED' ? 'danger' : 'warning') }} transfer-status">{{ $transfer->status }}</span>
                                            </td>
                                            <td>{{ $transfer->driver_name ?? '-' }}{{ $transfer->driver_phone ? ' (' . $transfer->driver_phone . ')' : '' }}
                                            </td>
                                            <td class="text-nowrap">
                                                @if ($transfer->status === 'REQUESTED')
                                                    <form method="POST"
                                                        action="{{ route('admin.stock.transfer.prepare', ['category' => $category, 'transfer' => $transfer->id]) }}"
                                                        class="d-inline">@csrf @method('PATCH')<button
                                                            class="btn btn-sm btn-success">Siapkan</button></form>
                                                @elseif($transfer->status === 'PREPARED')
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#ship{{ $transfer->id }}">Kirim</button>
                                                @elseif($transfer->status === 'SHIPPED')
                                                    <form method="POST"
                                                        action="{{ route('admin.stock.transfer.receive', ['category' => $category, 'transfer' => $transfer->id]) }}"
                                                        class="d-inline">@csrf @method('PATCH')<button
                                                        class="btn btn-sm btn-success">Terima</button></form>@else<span
                                                        class="text-muted">Selesai</span>
                                                @endif
                                            </td>
                                    </tr>@empty<tr>
                                            <td colspan="8" class="text-center text-muted">Belum ada permintaan transfer.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($transfers->hasPages())
                            <div class="transfer-pagination mt-3">
                                {{ $transfers->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>
                </div>
                @foreach ($transfers as $transfer)
                    @if ($transfer->status === 'PREPARED')
                        <div class="modal fade" id="ship{{ $transfer->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST"
                                        action="{{ route('admin.stock.transfer.ship', ['category' => $category, 'transfer' => $transfer->id]) }}">
                                        @csrf @method('PATCH')<div class="modal-header">
                                            <h5 class="modal-title">Data Driver - {{ $transfer->request_no }}</h5><button
                                                type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body"><label class="form-label">Nama Driver</label><input
                                                name="driver_name" class="form-control mb-3" required><label
                                                class="form-label">No. HP Driver</label><input name="driver_phone"
                                                class="form-control mb-3"><label class="form-label">Catatan
                                                Pengiriman</label>
                                            <textarea name="shipping_notes" class="form-control"></textarea>
                                        </div>
                                        <div class="modal-footer"><button class="btn btn-primary">Konfirmasi Kirim</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
        </div>
    </div>
@endsection
