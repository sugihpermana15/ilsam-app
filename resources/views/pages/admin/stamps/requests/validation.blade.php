@extends('layouts.master')

@section('title', 'Ilsam - Validasi Permintaan Materai')

@section('title-sub', 'Administrasi')
@section('pagetitle', 'Validasi Permintaan Materai')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
@endsection

@section('content')
    @php
        $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'stamps_validation', 'update');
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
                    <h5 class="card-title mb-0">Validasi Permintaan Materai</h5>
                    <form method="GET" class="d-flex gap-2">
                        <select name="status" class="form-select form-select-sm">
                            <option value="PENDING" @selected(($status ?? 'PENDING') === 'PENDING')>Pending (Submitted/Approved)</option>
                            <option value="SUBMITTED" @selected(($status ?? '') === 'SUBMITTED')>SUBMITTED</option>
                            <option value="REJECTED" @selected(($status ?? '') === 'REJECTED')>REJECTED</option>
                            <option value="HANDED_OVER" @selected(($status ?? '') === 'HANDED_OVER')>HANDED_OVER</option>
                        </select>
                        <button class="btn btn-outline-secondary btn-sm" type="submit">Filter</button>
                    </form>
                </div>

                <div class="card-body">
                    <div class="text-muted small mb-3">
                        Validasi = approve/reject. Jika approve, sistem otomatis posting OUT ke ledger dan stok berkurang.
                    </div>

                    <div class="table-responsive">
                        <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100 align-middle">
                            <thead>
                                <tr>
                                    <th>Request No</th>
                                    <th>Requester</th>
                                    <th>Materai</th>
                                    <th>Qty</th>
                                    <th>PIC</th>
                                    <th>Status</th>
                                    <th>Catatan</th>
                                    <th style="width: 260px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($requests as $r)
                                    <tr>
                                        <td class="fw-semibold">{{ $r->request_no }}</td>
                                        <td>{{ $r->requester?->display_name ?? '-' }}</td>
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
                                        <td class="text-muted small">{{ $r->notes ?: '-' }}</td>
                                        <td>
                                            @if (!$canUpdate)
                                                <span class="text-muted small">No access</span>
                                            @else
                                                <div class="d-flex gap-2 flex-wrap">
                                                    @if (in_array($r->status, ['SUBMITTED', 'APPROVED'], true))
                                                        <form method="POST" action="{{ route('admin.stamps.validation.approve', $r) }}" class="d-flex gap-1">
                                                            @csrf
                                                            <input type="text" name="validation_notes" class="form-control form-control-sm" placeholder="Catatan" style="max-width: 140px;">
                                                            <input type="hidden" name="handover_date" value="{{ now()->toDateString() }}">
                                                            <button class="btn btn-success btn-sm" type="submit">Approve</button>
                                                        </form>
                                                        <form method="POST" action="{{ route('admin.stamps.validation.reject', $r) }}" class="d-flex gap-1">
                                                            @csrf
                                                            <input type="text" name="validation_notes" class="form-control form-control-sm" placeholder="Alasan" style="max-width: 140px;">
                                                            <button class="btn btn-danger btn-sm" type="submit">Reject</button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-muted">Tidak ada data.</td>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

    <script src="{{ asset('assets/js/table/datatable.init.js') }}"></script>
@endsection
