@extends('layouts.master')

@section('title', 'Ilsam - Permintaan Materai')

@section('title-sub', 'Administrasi')
@section('pagetitle', 'Permintaan Materai')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
@endsection

@section('content')
    @php
        $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'stamps_requests', 'create');
        $currentUser = auth()->user();
        $currentPicName = $currentUser?->employee?->name ?? ($currentUser?->name ?? '-');
        $needsManualPic = ((int) ($currentUser?->role_id ?? 0) === 1) && ((int) ($currentUser?->employee_id ?? 0) <= 0);
    @endphp

    <div class="row">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if (session('success'))
                    Swal.fire({ icon: 'success', title: @json(__('common.success')), text: @json(session('success')), timer: 2000, showConfirmButton: false });
                @endif
                @if (session('error'))
                    Swal.fire({ icon: 'error', title: @json(__('common.error')), text: @json(session('error')), timer: 2500, showConfirmButton: false });
                @endif
            });
        </script>

        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                    <h5 class="card-title mb-0">Permintaan Materai</h5>
                </div>
                <div class="card-body">
                    <div class="text-muted small mb-3">
                        Permintaan akan masuk ke antrean validasi. Stok berkurang setelah permintaan di-approve.
                    </div>

                    @if ($canCreate)
                        <form method="POST" action="{{ route('admin.stamps.requests.store') }}" class="row g-3 mb-4">
                            @csrf
                            <div class="col-12 col-md-4">
                                <label class="form-label">Materai</label>
                                <select name="stamp_id" class="form-select" required>
                                    <option value="">Pilih</option>
                                    @foreach (($stampsActive ?? collect()) as $stamp)
                                        <option value="{{ $stamp->id }}" @selected(old('stamp_id') == $stamp->id)>
                                            {{ $stamp->name }} ({{ $stamp->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('stamp_id')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-2">
                                <label class="form-label">Qty</label>
                                <input type="number" min="1" name="qty" class="form-control" value="{{ old('qty', 1) }}" required>
                                @error('qty')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label">Tanggal Dibutuhkan (opsional)</label>
                                <input type="date" name="trx_date" class="form-control" value="{{ old('trx_date') }}">
                                @error('trx_date')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-3">
                                @if ($needsManualPic)
                                    <label class="form-label">PIC</label>
                                    <select name="pic_id" class="form-select" required>
                                        <option value="">Pilih Karyawan</option>
                                        @foreach (($employees ?? collect()) as $emp)
                                            <option value="{{ $emp->id }}" @selected(old('pic_id') == $emp->id)>{{ $emp->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('pic_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                @else
                                    <label class="form-label">PIC (otomatis dari user login)</label>
                                    <input type="text" class="form-control" value="{{ $currentPicName }}" disabled>
                                @endif
                            </div>

                            <div class="col-12 col-md-8">
                                <label class="form-label">Catatan (opsional)</label>
                                <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" maxlength="1000">
                                @error('notes')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-paper-plane"></i> Kirim Permintaan
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-warning">Anda tidak punya akses untuk membuat permintaan.</div>
                    @endif

                    <div class="table-responsive">
                        <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100 align-middle">
                            <thead>
                                <tr>
                                    <th>Request No</th>
                                    <th>Tanggal</th>
                                    <th>Materai</th>
                                    <th>Qty</th>
                                    <th>PIC</th>
                                    <th>Status</th>
                                    <th>Validator</th>
                                    <th>Ledger OUT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($requests as $r)
                                    <tr>
                                        <td class="fw-semibold">{{ $r->request_no }}</td>
                                        <td>{{ $r->requested_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                        <td>{{ $r->stamp?->name ?? '-' }}</td>
                                        <td>{{ (int) $r->qty }}</td>
                                        <td>{{ $r->pic?->name ?? '-' }}</td>
                                        <td>
                                            @php
                                                $badge = match($r->status) {
                                                    'SUBMITTED' => 'bg-warning',
                                                    'APPROVED' => 'bg-success',
                                                    'REJECTED' => 'bg-danger',
                                                    'HANDED_OVER' => 'bg-primary',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $badge }}">{{ $r->status }}</span>
                                        </td>
                                        <td>{{ $r->validator?->name ?? '-' }}</td>
                                        <td>{{ $r->handoverTransaction?->trx_no ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-muted">Belum ada permintaan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
    <script src="{{ asset('assets/js/table/datatable.init.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {
            // Apply Select2 only to form selects (avoid DataTables length/menu selects).
            $('select[name="stamp_id"], select[name="pic_id"]').select2({ theme: 'bootstrap-5', width: '100%' });
        });
    </script>
@endsection
