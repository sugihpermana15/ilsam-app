@extends('layouts.master')

@section('title', 'Ilsam - Manajemen Pengguna')

@section('title-sub', 'Settings & UI')
@section('pagetitle', 'Manajemen Pengguna')
@section('css')
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />

    <style>
        .menu-access-toolbar {
            position: sticky;
            top: 0;
            z-index: 2;
            background: rgba(var(--bs-body-bg-rgb, 255, 255, 255), 1);
            padding: .25rem 0;
        }

        .menu-access-surface {
            background: rgba(var(--bs-body-bg-rgb, 255, 255, 255), 1);
        }

        .menu-access-box {
            max-height: 360px;
            overflow: auto;
            padding: .5rem;
            scrollbar-gutter: stable;
        }

        .menu-access-sticky {
            position: sticky;
            top: 0;
            z-index: 3;
            background: rgba(var(--bs-body-bg-rgb, 255, 255, 255), 1);
            border: 1px solid var(--bs-border-color);
            border-radius: .5rem;
            padding: .35rem .5rem;
            margin-bottom: .5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            flex-wrap: wrap;
            box-shadow: 0 6px 12px rgba(0, 0, 0, .05);
        }

        .menu-access-sticky .sticky-left,
        .menu-access-sticky .sticky-right {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            min-width: 0;
        }

        .menu-access-sticky .sticky-left {
            flex: 1 1 auto;
        }

        .menu-access-sticky .sticky-right {
            flex: 0 0 auto;
        }

        .menu-access-sticky .sticky-right {
            justify-content: flex-end;
        }

        .menu-access-sticky .sticky-title {
            color: var(--bs-secondary-color);
            font-size: .75rem;
            font-weight: 800;
            letter-spacing: .02em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .menu-access-sticky .sticky-help {
            color: var(--bs-secondary-color);
            font-size: .75rem;
            font-weight: 600;
            white-space: normal;
        }

        .menu-access-sticky .sticky-icons {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            white-space: nowrap;
        }

        .menu-access-sticky .sticky-icons i {
            font-size: .9rem;
            line-height: 1;
        }

        .menu-access-box .border {
            background: var(--bs-body-bg);
        }

        .menu-access-box .border .form-check {
            margin-bottom: 0;
        }

        .menu-access-box .border .form-check .menu-row,
        .menu-access-box .border .form-check > .form-check-input,
        .menu-access-box .border .form-check > .form-check-label {
            border-radius: .35rem;
        }

        .menu-access-box .border .form-check .menu-row {
            padding: .3rem .25rem;
            border-bottom: 1px solid var(--bs-border-color-translucent);
        }

        .menu-access-box .border .form-check:last-child .menu-row {
            border-bottom: 0;
        }

        .menu-access-box .border .form-check:hover .menu-row {
            background: var(--bs-tertiary-bg);
        }



        .menu-access-box .fw-semibold {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .menu-access-box .form-check label {
            line-height: 1.2;
        }

        .menu-access-box .menu-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .menu-access-box .menu-row-left {
            display: flex;
            align-items: center;
            gap: .5rem;
            min-width: 0;
            flex: 1 1 auto;
        }

        .menu-access-box .menu-row-left .form-check-label {
            margin-bottom: 0;
            min-width: 0;
            flex: 1 1 auto;
            overflow-wrap: break-word;
            word-break: normal;
        }

        .menu-access-box .menu-row-right {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: .4rem;
            flex: 0 0 auto;
            flex-wrap: wrap;
            white-space: normal;
        }

        .rw-actions {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }

        .rw-actions .btn {
            padding: .15rem .45rem;
            line-height: 1;
        }

        .rw-actions .btn i {
            font-size: .9rem;
            line-height: 1;
        }

        .menu-counter {
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .01em;
        }

        @media (max-width: 575.98px) {
            .menu-access-box .menu-row {
                flex-direction: column;
                align-items: stretch;
            }
            .menu-access-box .menu-row-right {
                justify-content: flex-start;
                flex-wrap: wrap;
                white-space: normal;
            }

            .menu-access-sticky {
                flex-direction: column;
                align-items: flex-start;
                gap: .25rem;
            }
        }

        .menu-access-muted {
            color: var(--bs-secondary-color);
            font-size: .825rem;
        }

        @media (min-width: 992px) {
            .menu-access-box {
                max-height: 520px;
            }
        }
    </style>
@endsection
@section('content')

    @php
        $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'settings_users', 'create');
        $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'settings_users', 'update');
        $canDelete = \App\Support\MenuAccess::can(auth()->user(), 'settings_users', 'delete');
    @endphp

    <!--begin::App-->
    <div id="layout-wrapper">
        <div class="row">
            {{-- SweetAlert2 notification --}}
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    @if(session('success'))
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: @json(session('success')),
                            timer: 2000,
                            showConfirmButton: false
                        });
                    @endif
                    @if(session('error'))
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: @json(session('error')),
                            timer: 2500,
                            showConfirmButton: false
                        });
                    @endif
                                                });
            </script>
            <div class="col-12">
                <div class="card">
                    <!--start::card-->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"> Manajemen Pengguna </h5>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal" {{ $canCreate ? '' : 'disabled' }} title="{{ $canCreate ? '' : 'Tidak punya akses tambah' }}">Tambah
                            Pengguna</button>
                    </div>
                    <div class="card-body">
                        <!-- Add User Modal -->
                        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <form action="{{ route('admin.users.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="dash_permissions_present" value="1">
                                        <input type="hidden" name="dash_tabs_present" value="1">
                                        <input type="hidden" name="menu_permissions_present" value="1">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addUserModalLabel">Tambah Pengguna</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-12 col-lg-5">
                                            @if ((int) (auth()->user()->role_id ?? 0) === 1)
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="add_no_employee" name="no_employee" value="1">
                                                    <label class="form-check-label" for="add_no_employee">Akun tanpa karyawan</label>
                                                </div>
                                                <div class="form-text">Hanya untuk Super Admin. Jika dicentang, akun tidak terhubung ke data karyawan.</div>
                                            </div>
                                            @endif
                                            <div class="mb-3">
                                                <label for="employee_id" class="form-label">Karyawan</label>
                                                <select class="form-select" id="employee_id" name="employee_id" required>
                                                    <option value="">Pilih Karyawan</option>
                                                    @foreach (($employees ?? collect()) as $emp)
                                                        <option value="{{ $emp->id }}" data-emp-name="{{ $emp->name }}">{{ $emp->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Nama</label>
                                                <input type="text" class="form-control" id="name" name="name" required readonly>
                                                <div class="form-text">Nama otomatis mengikuti data karyawan.</div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="username" class="form-label">Username</label>
                                                <input type="text" class="form-control" id="username" name="username">
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Password</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="password"
                                                        name="password" required>
                                                    <button class="btn btn-primary" type="button" tabindex="-1"
                                                        onclick="togglePassword()">
                                                        <i class="fas fa-eye-slash" id="togglePasswordIcon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="role" class="form-label">Peran</label>
                                                <select class="form-select" id="role" name="role_id" required>
                                                    <option value="">Pilih Peran</option>
                                                    <option value="1">Super Admin</option>
                                                    <option value="2">Admin</option>
                                                    <option value="3">Pengguna</option>
                                                </select>
                                            </div>

                                            @if ((int) (auth()->user()->role_id ?? 0) === 1)
                                            <div class="mb-3">
                                                <label for="stamp_validator_user_id" class="form-label">Validator Materai (default)</label>
                                                <select class="form-select" id="stamp_validator_user_id" name="stamp_validator_user_id">
                                                    <option value="">(Tidak ditentukan)</option>
                                                    @foreach (($stampValidatorCandidates ?? collect()) as $cand)
                                                        <option value="{{ $cand->id }}">{{ $cand->display_name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="form-text">Jika diisi, permintaan Materai dari user ini otomatis ditugaskan ke validator tersebut.</div>
                                            </div>
                                            @endif

                                            <div class="mb-3">
                                                <label class="form-label">Tab Dashboard Admin</label>
                                                <div class="border rounded p-2">
                                                    <div class="row g-2">
                                                        <div class="col-12 col-md-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="add_dash_tab_asset" name="dash_tab_asset" value="1" checked>
                                                                <label class="form-check-label" for="add_dash_tab_asset">Asset</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="add_dash_tab_stamps" name="dash_tab_stamps" value="1" checked>
                                                                <label class="form-check-label" for="add_dash_tab_stamps">Materai</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="add_dash_tab_uniforms" name="dash_tab_uniforms" value="1" checked>
                                                                <label class="form-check-label" for="add_dash_tab_uniforms">Seragam</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="add_dash_tab_documents" name="dash_tab_documents" value="1" checked>
                                                                <label class="form-check-label" for="add_dash_tab_documents">Arsip Berkas</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="add_dash_tab_employee" name="dash_tab_employee" value="1" checked>
                                                                <label class="form-check-label" for="add_dash_tab_employee">Karyawan</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-text">Jika semua dicentang, dashboard mengikuti akses default (tanpa pembatasan tab).</div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Widget Dashboard</label>
                                                <div class="row g-2">
                                                    <div class="col-12 col-md-6">
                                                        <div class="border rounded p-2 h-100">
                                                            <div class="fw-semibold mb-2">Aset</div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="add_dash_asset_kpi" name="dash_asset_kpi" value="1"
                                                                    checked>
                                                                <label class="form-check-label"
                                                                    for="add_dash_asset_kpi">KPI</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="add_dash_asset_charts" name="dash_asset_charts"
                                                                    value="1" checked>
                                                                <label class="form-check-label"
                                                                    for="add_dash_asset_charts">Grafik</label>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox" id="edit_dash_tab_uniforms" name="dash_tab_uniforms" value="1">
                                                                                <label class="form-check-label" for="edit_dash_tab_uniforms">Seragam</label>
                                                                            </div>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="add_dash_asset_recent" name="dash_asset_recent"
                                                                    value="1" checked>
                                                                <label class="form-check-label"
                                                                    for="add_dash_asset_recent">Terbaru</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-text">Jika semua dicentang, akses dashboard default (full).
                                                </div>
                                            </div>

                                                </div>
                                                <div class="col-12 col-lg-7">

                                            <div class="mb-3">
                                                <label class="form-label">Akses Menu</label>
                                                <div class="menu-access-toolbar d-flex align-items-center gap-2 flex-wrap">
                                                    <div class="grow" style="min-width: 200px;">
                                                        <input type="text" class="form-control form-control-sm" id="add_menu_filter"
                                                            placeholder="Cari menu... (contoh: aset, terhapus, riwayat)">
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="add_menu_select_all">Pilih semua</button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="add_menu_clear_all">Kosongkan</button>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" id="add_menu_reset_default">Reset ke default peran</button>
                                                    </div>
                                                    <div class="menu-access-muted w-100">Tips: gunakan checkbox parent untuk toggle submenu.</div>
                                                </div>

                                                <div class="menu-access-box menu-access-surface" id="add_menu_access_container">
                                                    <div class="menu-access-sticky">
                                                        <div class="sticky-left">
                                                            <span class="sticky-title">Lihat</span>
                                                            <span class="sticky-help">(boleh buka halaman)</span>
                                                        </div>
                                                        <div class="sticky-right">
                                                            <span class="sticky-title">Aksi</span>
                                                            <span class="sticky-icons">
                                                                <i class="fas fa-plus" title="Tambah" aria-hidden="true"></i>
                                                                <i class="fas fa-pencil-alt" title="Ubah" aria-hidden="true"></i>
                                                                <i class="fas fa-trash-alt" title="Hapus" aria-hidden="true"></i>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="row g-2">
                                                    <div class="col-12">
                                                        <div class="border rounded p-2 h-100">
                                                            <div class="fw-semibold mb-2">Area Pengguna</div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="add_menu_user_dashboard" name="menu_user_dashboard"
                                                                    value="1">
                                                                <label class="form-check-label"
                                                                    for="add_menu_user_dashboard">Dashboard Karyawan</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="border rounded p-2 h-100">
                                                            <div class="fw-semibold mb-2">Area Admin</div>
                                                            <div class="row g-2">
                                                                <div class="col-12 col-xl-6">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_admin_dashboard"
                                                                            name="menu_admin_dashboard" value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_admin_dashboard">Dashboard
                                                                            Admin</label>
                                                                    </div>

                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_stamps" name="menu_stamps" value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_stamps">Manajemen Materai <span class="badge bg-light text-dark ms-2 menu-counter" id="add_menu_counter_stamps"></span></label>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_stamps_master" name="menu_stamps_master" value="1">
                                                                            <label class="form-check-label" for="add_menu_stamps_master">Master Materai</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_stamps_transactions" name="menu_stamps_transactions" value="1">
                                                                            <label class="form-check-label" for="add_menu_stamps_transactions">Transaksi Materai</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_stamps_requests" name="menu_stamps_requests" value="1">
                                                                            <label class="form-check-label" for="add_menu_stamps_requests">Permintaan Materai</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_stamps_validation" name="menu_stamps_validation" value="1">
                                                                            <label class="form-check-label" for="add_menu_stamps_validation">Validasi Permintaan</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_employees" name="menu_employees"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_employees">Master Karyawan <span class="badge bg-light text-dark ms-2 menu-counter" id="add_menu_counter_employees"></span></label>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_employees_index"
                                                                                name="menu_employees_index" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_employees_index">Karyawan</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_employees_deleted"
                                                                                name="menu_employees_deleted" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_employees_deleted">Terhapus</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_employees_audit"
                                                                                name="menu_employees_audit" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_employees_audit">Audit Log</label>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_daily_tasks" name="menu_daily_tasks"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_daily_tasks">Tugas Harian</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_devices" name="menu_devices"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_devices">Master Device</label>
                                                                    </div>

                                                                    <div class="fw-semibold mt-3 mb-2">Seragam Karyawan</div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_uniforms_stock" name="menu_uniforms_stock" value="1">
                                                                        <label class="form-check-label" for="add_menu_uniforms_stock">Stok Seragam</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_uniforms_distribution" name="menu_uniforms_distribution" value="1">
                                                                        <label class="form-check-label" for="add_menu_uniforms_distribution">Distribusi Seragam</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_uniforms_reports" name="menu_uniforms_reports" value="1">
                                                                        <label class="form-check-label" for="add_menu_uniforms_reports">Laporan Seragam</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_uniforms_entitlements" name="menu_uniforms_entitlements" value="1">
                                                                        <label class="form-check-label" for="add_menu_uniforms_entitlements">Kuota Seragam</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_uniforms_master" name="menu_uniforms_master" value="1">
                                                                        <label class="form-check-label" for="add_menu_uniforms_master">Master Seragam</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_uniforms_variants" name="menu_uniforms_variants" value="1">
                                                                        <label class="form-check-label" for="add_menu_uniforms_variants">Varian Seragam</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_uniforms_lots" name="menu_uniforms_lots" value="1">
                                                                        <label class="form-check-label" for="add_menu_uniforms_lots">Lots Seragam</label>
                                                                    </div>

                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_master_hr"
                                                                            name="menu_master_hr" value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_master_hr">Master HR <span class="badge bg-light text-dark ms-2 menu-counter" id="add_menu_counter_master_hr"></span></label>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_departments" name="menu_departments"
                                                                                value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_departments">Departemen</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_positions" name="menu_positions"
                                                                                value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_positions">Posisi</label>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_master_assets"
                                                                            name="menu_master_assets" value="1">
                                                                        <label class="form-check-label"
                                                                                for="add_menu_master_assets">Master Aset <span class="badge bg-light text-dark ms-2 menu-counter" id="add_menu_counter_master_assets"></span></label>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_asset_categories"
                                                                                name="menu_asset_categories" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_asset_categories">Kategori Aset</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_asset_locations"
                                                                                name="menu_asset_locations" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_asset_locations">Lokasi Aset</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_plant_sites" name="menu_plant_sites"
                                                                                value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_plant_sites">Plant/Site</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_asset_uoms" name="menu_asset_uoms"
                                                                                value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_asset_uoms">Satuan Aset</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_asset_vendors"
                                                                                name="menu_asset_vendors" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_asset_vendors">Vendor Aset</label>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_master_accounts"
                                                                            name="menu_master_accounts" value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_master_accounts">Master Akun <span class="badge bg-light text-dark ms-2 menu-counter" id="add_menu_counter_master_accounts"></span></label>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_account_types"
                                                                                name="menu_account_types" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_account_types">Kategori Akun</label>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_master_daily_task"
                                                                            name="menu_master_daily_task" value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_master_daily_task">Master Tugas Harian <span class="badge bg-light text-dark ms-2 menu-counter" id="add_menu_counter_master_daily_task"></span></label>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_daily_task_types" name="menu_daily_task_types"
                                                                                value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_daily_task_types">Tipe Tugas</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_daily_task_priorities" name="menu_daily_task_priorities"
                                                                                value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_daily_task_priorities">Prioritas</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_daily_task_statuses" name="menu_daily_task_statuses"
                                                                                value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_daily_task_statuses">Status</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_career" name="menu_career"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_career">Career</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_certificate" name="menu_certificate"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_certificate">Sertifikat</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_website_settings" name="menu_website_settings"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_website_settings">Website Settings</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_website_products" name="menu_website_products"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_website_products">Website Products</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_website_home_sections" name="menu_website_home_sections"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_website_home_sections">Website Home Sections</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_website_contact_page" name="menu_website_contact_page"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_website_contact_page">Website Contact Page</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-xl-6">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_assets" name="menu_assets"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_assets">Perlengkapan Aset <span class="badge bg-light text-dark ms-2 menu-counter" id="add_menu_counter_assets"></span></label>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_assets_data"
                                                                                name="menu_assets_data" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_assets_data">Data
                                                                                Asset</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_accounts_data"
                                                                                name="menu_accounts_data" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_accounts_data">Data Akun</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_accounts_secrets"
                                                                                name="menu_accounts_secrets" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_accounts_secrets">Data Akun (Secrets)</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_documents_archive"
                                                                                name="menu_documents_archive" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_documents_archive">Arsip Berkas</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_documents_restricted"
                                                                                name="menu_documents_restricted" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_documents_restricted">Arsip Berkas (Terbatas)</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_assets_jababeka"
                                                                                name="menu_assets_jababeka" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_assets_jababeka">Aset
                                                                                Jababeka</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_assets_karawang"
                                                                                name="menu_assets_karawang" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_assets_karawang">Aset
                                                                                Karawang</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_assets_in"
                                                                                name="menu_assets_in" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_assets_in">Aset Masuk</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_assets_transfer"
                                                                                name="menu_assets_transfer" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_assets_transfer">Aset
                                                                                Keluar</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_settings" name="menu_settings"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_settings">Pengaturan & Log <span class="badge bg-light text-dark ms-2 menu-counter" id="add_menu_counter_settings"></span></label>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_settings_users"
                                                                                name="menu_settings_users" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_settings_users">Pengguna</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_settings_history_user"
                                                                                name="menu_settings_history_user" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_settings_history_user">Riwayat Hapus Pengguna</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_settings_history_asset"
                                                                                name="menu_settings_history_asset" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_settings_history_asset">Riwayat Hapus Aset</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-text">Akses menu akan divalidasi juga di route
                                                                (bukan hanya hide sidebar).</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Tutup</button>
                                            <button type="submit" class="btn btn-primary" {{ $canCreate ? '' : 'disabled' }} title="{{ $canCreate ? '' : 'Tidak punya akses tambah' }}">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- start:: Alternative Pagination Datatable -->
                        <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Peran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->employee?->name ?? $user->name }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->role->role_name ?? '-' }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary btn-edit-user" {{ $canUpdate ? '' : 'disabled' }} title="{{ $canUpdate ? '' : 'Tidak punya akses edit' }}"
                                                data-id="{{ $user->id }}" data-employee-id="{{ $user->employee_id }}" data-name="{{ $user->employee?->name ?? $user->name }}"
                                                data-username="{{ $user->username }}" data-email="{{ $user->email }}"
                                                data-role="{{ $user->role_id }}"
                                                data-stamp-validator-user-id="{{ $user->stamp_validator_user_id }}"
                                                data-dashboard='@json($user->dashboard_permissions)'
                                                data-dashboard-tabs='@json($user->dashboard_tabs)'
                                                data-menu='@json($user->menu_permissions)'>
                                                Ubah
                                            </button>
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                style="display:inline-block" class="form-delete-user">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        class="btn btn-sm btn-danger btn-delete-user" {{ $canDelete ? '' : 'disabled' }} title="{{ $canDelete ? '' : 'Tidak punya akses hapus' }}">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $users->links() }}
                        </div>

                        <!-- Edit User Modal -->
                        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <form id="editUserForm" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="dash_permissions_present" value="1">
                                        <input type="hidden" name="dash_tabs_present" value="1">
                                        <input type="hidden" name="menu_permissions_present" value="1">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editUserModalLabel">Ubah Pengguna</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" id="edit_user_id" name="user_id">
                                            <div class="row g-3">
                                                <div class="col-12 col-lg-5">
                                            @if ((int) (auth()->user()->role_id ?? 0) === 1)
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="edit_no_employee" name="no_employee" value="1">
                                                    <label class="form-check-label" for="edit_no_employee">Akun tanpa karyawan</label>
                                                </div>
                                                <div class="form-text">Jika dicentang, akun tidak terhubung ke data karyawan.</div>
                                            </div>
                                            @endif
                                            <div class="mb-3">
                                                <label for="edit_employee_id" class="form-label">Karyawan</label>
                                                <select class="form-select" id="edit_employee_id" name="employee_id" required>
                                                    <option value="">Pilih Karyawan</option>
                                                    @foreach (($employees ?? collect()) as $emp)
                                                        <option value="{{ $emp->id }}" data-emp-name="{{ $emp->name }}">{{ $emp->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_name" class="form-label">Nama</label>
                                                <input type="text" class="form-control" id="edit_name" name="name" required readonly>
                                                <div class="form-text">Nama otomatis mengikuti data karyawan.</div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_username" class="form-label">Username</label>
                                                <input type="text" class="form-control" id="edit_username" name="username">
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="edit_email" name="email"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_password" class="form-label">Password</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="edit_password"
                                                        name="password" placeholder="Kosongkan untuk mempertahankan password saat ini">
                                                    <button class="btn btn-primary" type="button" tabindex="-1"
                                                        onclick="toggleEditPassword()">
                                                        <i class="fas fa-eye-slash" id="toggleEditPasswordIcon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_role" class="form-label">Peran</label>
                                                <select class="form-select" id="edit_role" name="role_id" required>
                                                    <option value="">Pilih Peran</option>
                                                    <option value="1">Super Admin</option>
                                                    <option value="2">Admin</option>
                                                    <option value="3">Pengguna</option>
                                                </select>
                                            </div>

                                            @if ((int) (auth()->user()->role_id ?? 0) === 1)
                                            <div class="mb-3">
                                                <label for="edit_stamp_validator_user_id" class="form-label">Validator Materai (default)</label>
                                                <select class="form-select" id="edit_stamp_validator_user_id" name="stamp_validator_user_id">
                                                    <option value="">(Tidak ditentukan)</option>
                                                    @foreach (($stampValidatorCandidates ?? collect()) as $cand)
                                                        <option value="{{ $cand->id }}">{{ $cand->display_name }}</option>
                                                    @endforeach
                                                </select>
                                                <div id="edit_stamp_validator_warning" class="alert alert-warning py-2 px-3 mt-2 mb-0 d-none" role="alert">
                                                    Validator Materai yang tersimpan sudah tidak valid (mis. akses "Validasi Permintaan" dicabut), jadi tidak efektif.
                                                    Silakan pilih ulang validator.
                                                </div>
                                                <div class="form-text">Jika diisi, permintaan Materai dari user ini otomatis ditugaskan ke validator tersebut.</div>
                                            </div>
                                            @endif

                                            <div class="mb-3">
                                                <label class="form-label">Tab Dashboard Admin</label>
                                                <div class="border rounded p-2">
                                                    <div class="row g-2">
                                                        <div class="col-12 col-md-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="edit_dash_tab_asset" name="dash_tab_asset" value="1">
                                                                <label class="form-check-label" for="edit_dash_tab_asset">Asset</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="edit_dash_tab_stamps" name="dash_tab_stamps" value="1">
                                                                <label class="form-check-label" for="edit_dash_tab_stamps">Materai</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="edit_dash_tab_documents" name="dash_tab_documents" value="1">
                                                                <label class="form-check-label" for="edit_dash_tab_documents">Arsip Berkas</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="edit_dash_tab_employee" name="dash_tab_employee" value="1">
                                                                <label class="form-check-label" for="edit_dash_tab_employee">Karyawan</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-text">Matikan tab tertentu untuk menyembunyikannya dari pengguna. Jika semua aktif, dashboard default (tanpa pembatasan tab).</div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Widget Dashboard</label>
                                                <div class="row g-2">
                                                    <div class="col-12 col-md-6">
                                                        <div class="border rounded p-2 h-100">
                                                            <div class="fw-semibold mb-2">Aset</div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="edit_dash_asset_kpi" name="dash_asset_kpi"
                                                                    value="1">
                                                                <label class="form-check-label"
                                                                    for="edit_dash_asset_kpi">KPI</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="edit_dash_asset_charts" name="dash_asset_charts"
                                                                    value="1">
                                                                <label class="form-check-label"
                                                                    for="edit_dash_asset_charts">Grafik</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="edit_dash_asset_recent" name="dash_asset_recent"
                                                                    value="1">
                                                                <label class="form-check-label"
                                                                    for="edit_dash_asset_recent">Terbaru</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-text">Matikan bagian tertentu untuk menyembunyikannya dari
                                                    pengguna.</div>
                                            </div>

                                                </div>
                                                <div class="col-12 col-lg-7">

                                            <div class="mb-3">
                                                <label class="form-label">Akses Menu</label>
                                                <div class="menu-access-toolbar d-flex align-items-center gap-2 flex-wrap">
                                                    <div class="grow" style="min-width: 200px;">
                                                        <input type="text" class="form-control form-control-sm" id="edit_menu_filter"
                                                            placeholder="Cari menu... (contoh: aset, terhapus, riwayat)">
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="edit_menu_select_all">Pilih semua</button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="edit_menu_clear_all">Kosongkan</button>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" id="edit_menu_reset_default">Reset ke default peran</button>
                                                    </div>
                                                    <div class="menu-access-muted w-100">Tips: gunakan checkbox parent untuk toggle submenu.</div>
                                                </div>

                                                <div class="menu-access-box menu-access-surface" id="edit_menu_access_container">
                                                    <div class="menu-access-sticky">
                                                        <div class="sticky-left">
                                                            <span class="sticky-title">Lihat</span>
                                                            <span class="sticky-help">(boleh buka halaman)</span>
                                                        </div>
                                                        <div class="sticky-right">
                                                            <span class="sticky-title">Aksi</span>
                                                            <span class="sticky-icons">
                                                                <i class="fas fa-plus" title="Tambah" aria-hidden="true"></i>
                                                                <i class="fas fa-pencil-alt" title="Ubah" aria-hidden="true"></i>
                                                                <i class="fas fa-trash-alt" title="Hapus" aria-hidden="true"></i>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="row g-2">
                                                    <div class="col-12">
                                                        <div class="border rounded p-2 h-100">
                                                            <div class="fw-semibold mb-2">Area Pengguna</div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="edit_menu_user_dashboard" name="menu_user_dashboard"
                                                                    value="1">
                                                                <label class="form-check-label"
                                                                    for="edit_menu_user_dashboard">Dashboard
                                                                    Karyawan</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="border rounded p-2 h-100">
                                                            <div class="fw-semibold mb-2">Area Admin</div>
                                                            <div class="row g-2">
                                                                <div class="col-12 col-xl-6">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_admin_dashboard"
                                                                            name="menu_admin_dashboard" value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_admin_dashboard">Dashboard
                                                                            Admin</label>
                                                                    </div>

                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_stamps" name="menu_stamps" value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_stamps">Manajemen Materai <span class="badge bg-light text-dark ms-2 menu-counter" id="edit_menu_counter_stamps"></span></label>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_stamps_master" name="menu_stamps_master" value="1">
                                                                            <label class="form-check-label" for="edit_menu_stamps_master">Master Materai</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_stamps_transactions" name="menu_stamps_transactions" value="1">
                                                                            <label class="form-check-label" for="edit_menu_stamps_transactions">Transaksi Materai</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_stamps_requests" name="menu_stamps_requests" value="1">
                                                                            <label class="form-check-label" for="edit_menu_stamps_requests">Permintaan Materai</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_stamps_validation" name="menu_stamps_validation" value="1">
                                                                            <label class="form-check-label" for="edit_menu_stamps_validation">Validasi Permintaan</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_employees" name="menu_employees"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_employees">Master
                                                                            Karyawan <span class="badge bg-light text-dark ms-2 menu-counter" id="edit_menu_counter_employees"></span></label>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_employees_index"
                                                                                name="menu_employees_index" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_employees_index">Karyawan</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_employees_deleted"
                                                                                name="menu_employees_deleted" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_employees_deleted">Terhapus</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_employees_audit"
                                                                                name="menu_employees_audit" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_employees_audit">Audit Log</label>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_daily_tasks" name="menu_daily_tasks"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_daily_tasks">Tugas Harian</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_devices" name="menu_devices"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_devices">Master Device</label>
                                                                    </div>

                                                                    <div class="fw-semibold mt-3 mb-2">Seragam Karyawan</div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_uniforms_stock" name="menu_uniforms_stock" value="1">
                                                                        <label class="form-check-label" for="edit_menu_uniforms_stock">Stok Seragam</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_uniforms_distribution" name="menu_uniforms_distribution" value="1">
                                                                        <label class="form-check-label" for="edit_menu_uniforms_distribution">Distribusi Seragam</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_uniforms_reports" name="menu_uniforms_reports" value="1">
                                                                        <label class="form-check-label" for="edit_menu_uniforms_reports">Laporan Seragam</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_uniforms_entitlements" name="menu_uniforms_entitlements" value="1">
                                                                        <label class="form-check-label" for="edit_menu_uniforms_entitlements">Kuota Seragam</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_uniforms_master" name="menu_uniforms_master" value="1">
                                                                        <label class="form-check-label" for="edit_menu_uniforms_master">Master Seragam</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_uniforms_variants" name="menu_uniforms_variants" value="1">
                                                                        <label class="form-check-label" for="edit_menu_uniforms_variants">Varian Seragam</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_uniforms_lots" name="menu_uniforms_lots" value="1">
                                                                        <label class="form-check-label" for="edit_menu_uniforms_lots">Lots Seragam</label>
                                                                    </div>

                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_master_hr"
                                                                            name="menu_master_hr" value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_master_hr">Master HR <span class="badge bg-light text-dark ms-2 menu-counter" id="edit_menu_counter_master_hr"></span></label>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_departments" name="menu_departments"
                                                                                value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_departments">Departemen</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_positions" name="menu_positions"
                                                                                value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_positions">Posisi</label>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_master_assets"
                                                                            name="menu_master_assets" value="1">
                                                                        <label class="form-check-label"
                                                                                for="edit_menu_master_assets">Master Aset <span class="badge bg-light text-dark ms-2 menu-counter" id="edit_menu_counter_master_assets"></span></label>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_asset_categories"
                                                                                name="menu_asset_categories" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_asset_categories">Kategori Aset</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_asset_locations"
                                                                                name="menu_asset_locations" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_asset_locations">Lokasi Aset</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_plant_sites" name="menu_plant_sites"
                                                                                value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_plant_sites">Plant/Site</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_asset_uoms" name="menu_asset_uoms"
                                                                                value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_asset_uoms">Satuan Aset</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_asset_vendors"
                                                                                name="menu_asset_vendors" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_asset_vendors">Vendor Aset</label>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_master_accounts"
                                                                            name="menu_master_accounts" value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_master_accounts">Master Akun <span class="badge bg-light text-dark ms-2 menu-counter" id="edit_menu_counter_master_accounts"></span></label>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_account_types"
                                                                                name="menu_account_types" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_account_types">Kategori Akun</label>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_master_daily_task"
                                                                            name="menu_master_daily_task" value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_master_daily_task">Master Tugas Harian <span class="badge bg-light text-dark ms-2 menu-counter" id="edit_menu_counter_master_daily_task"></span></label>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_daily_task_types" name="menu_daily_task_types"
                                                                                value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_daily_task_types">Tipe Tugas</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_daily_task_priorities" name="menu_daily_task_priorities"
                                                                                value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_daily_task_priorities">Prioritas</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_daily_task_statuses" name="menu_daily_task_statuses"
                                                                                value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_daily_task_statuses">Status</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_career" name="menu_career"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_career">Career</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_certificate" name="menu_certificate"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_certificate">Sertifikat</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_website_settings" name="menu_website_settings"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_website_settings">Website Settings</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_website_products" name="menu_website_products"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_website_products">Website Products</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_website_home_sections" name="menu_website_home_sections"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_website_home_sections">Website Home Sections</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_website_contact_page" name="menu_website_contact_page"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_website_contact_page">Website Contact Page</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-xl-6">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_assets" name="menu_assets"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_assets">Perlengkapan Aset <span class="badge bg-light text-dark ms-2 menu-counter" id="edit_menu_counter_assets"></span></label>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_assets_data"
                                                                                name="menu_assets_data" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_assets_data">Data
                                                                                Asset</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_accounts_data"
                                                                                name="menu_accounts_data" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_accounts_data">Data Akun</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_accounts_secrets"
                                                                                name="menu_accounts_secrets" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_accounts_secrets">Data Akun (Secrets)</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_documents_archive"
                                                                                name="menu_documents_archive" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_documents_archive">Arsip Berkas</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_documents_restricted"
                                                                                name="menu_documents_restricted" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_documents_restricted">Arsip Berkas (Terbatas)</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_assets_jababeka"
                                                                                name="menu_assets_jababeka" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_assets_jababeka">Aset
                                                                                Jababeka</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_assets_karawang"
                                                                                name="menu_assets_karawang" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_assets_karawang">Aset
                                                                                Karawang</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_assets_in"
                                                                                name="menu_assets_in" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_assets_in">Aset Masuk</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_assets_transfer"
                                                                                name="menu_assets_transfer" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_assets_transfer">Aset
                                                                                Keluar</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_settings" name="menu_settings"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_settings">Pengaturan & Log <span class="badge bg-light text-dark ms-2 menu-counter" id="edit_menu_counter_settings"></span></label>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_settings_users"
                                                                                name="menu_settings_users" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_settings_users">Pengguna</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_settings_history_user"
                                                                                name="menu_settings_history_user" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_settings_history_user">Riwayat Hapus Pengguna</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_settings_history_asset"
                                                                                name="menu_settings_history_asset" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_settings_history_asset">Riwayat Hapus Aset</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-text">Akses menu akan divalidasi juga di route
                                                                (bukan hanya hide sidebar).</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Tutup</button>
                                            <button type="submit" class="btn btn-primary" {{ $canUpdate ? '' : 'disabled' }} title="{{ $canUpdate ? '' : 'Tidak punya akses edit' }}">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- end:: Alternative Pagination Datatable -->
                    </div>
                </div>
                <!--end::card-->
            </div>
        </div><!--End row-->
    </div><!--End container-fluid-->
    </main><!--End app-wrapper-->

@endsection

@section('js')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <!--datatable js-->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script src="{{ asset('assets/js/table/datatable.init.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>

    <script>

            function defaultMenuPermissionsForRole(roleId) {
                const none = { read: false, create: false, update: false, delete: false };
                const readOnly = { read: true, create: false, update: false, delete: false };
                const all = { read: true, create: true, update: true, delete: true };

                // Keys that are effectively view-only in this app.
                // We keep them as Read-only to avoid confusing Add/Edit/Delete for dashboards/log pages.
                const viewOnlyKeys = new Set([
                    'user_dashboard',
                    'admin_dashboard',
                    'settings_history_user',
                    'settings_history_asset',
                ]);

                // Role IDs: 1 Super Admin, 2 Admin, 3 Pengguna
                if (String(roleId) === '3') {
                    return {
                        user_dashboard: readOnly,
                        admin_dashboard: none,

                        // Materai
                        stamps: none,
                        stamps_master: none,
                        stamps_transactions: none,
                        stamps_requests: { read: true, create: true, update: true, delete: false },
                        stamps_validation: none,

                        // Daily Tasks
                        daily_tasks: { read: true, create: true, update: true, delete: false },

                        // Devices
                        devices: none,

                        // Groups
                        assets: none,

                        // Assets submenus
                        assets_data: none,
                        accounts_data: none,
                        accounts_secrets: none,
                        documents_archive: none,
                        documents_restricted: none,
                        assets_jababeka: none,
                        assets_karawang: none,
                        assets_in: none,
                        assets_transfer: none,

                        employees: none,
                        employees_index: none,
                        employees_deleted: none,
                        employees_audit: none,

                        // Master groups (granular)
                        master_hr: none,
                        master_assets: none,
                        master_accounts: none,
                        master_daily_task: none,

                        master_data: none,
                        departments: none,
                        positions: none,
                        asset_categories: none,
                        account_types: none,
                        asset_locations: none,
                        plant_sites: none,
                        asset_uoms: none,
                        asset_vendors: none,

                        // Daily Tasks Masters
                        daily_task_types: none,
                        daily_task_priorities: none,
                        daily_task_statuses: none,

                        career: none,
                        certificate: none,
                        website_settings: none,
                        website_products: none,
                        website_home_sections: none,
                        website_contact_page: none,
                        settings: none,
                        settings_users: none,
                        settings_history_user: none,
                        settings_history_asset: none,

                        // Uniforms (Seragam Karyawan)
                        uniforms_stock: none,
                        uniforms_distribution: none,
                        uniforms_reports: none,
                        uniforms_master: none,
                        uniforms_variants: none,
                        uniforms_lots: none,
                        uniforms_entitlements: none,
                    };
                }

                const perms = {
                    user_dashboard: readOnly,
                    admin_dashboard: readOnly,

                    // Materai
                    stamps: all,
                    stamps_master: all,
                    stamps_transactions: all,
                    stamps_requests: all,
                    stamps_validation: all,

                    // Daily Tasks
                    daily_tasks: all,

                    // Devices
                    devices: all,

                    // Groups
                    assets: all,

                    // Assets submenus
                    assets_data: all,
                    accounts_data: all,
                    accounts_secrets: all,
                    documents_archive: all,
                    documents_restricted: readOnly,
                    assets_jababeka: all,
                    assets_karawang: all,
                    assets_in: all,
                    assets_transfer: all,

                    employees: all,
                    employees_index: all,
                    employees_deleted: all,
                    employees_audit: all,

                    // Master groups (granular)
                    master_hr: all,
                    master_assets: all,
                    master_accounts: all,
                    master_daily_task: all,

                    master_data: all,
                    departments: all,
                    positions: all,
                    asset_categories: all,
                    account_types: all,
                    asset_locations: all,
                    plant_sites: all,
                    asset_uoms: all,
                    asset_vendors: all,

                    // Daily Tasks Masters
                    daily_task_types: all,
                    daily_task_priorities: all,
                    daily_task_statuses: all,

                    career: all,
                    certificate: all,
                    website_settings: all,
                    website_products: all,
                    website_home_sections: all,
                    website_contact_page: all,
                    settings: all,
                    settings_users: all,
                    settings_history_user: readOnly,
                    settings_history_asset: readOnly,

                    // Uniforms (Seragam Karyawan)
                    uniforms_stock: all,
                    uniforms_distribution: all,
                    uniforms_reports: all,
                    uniforms_master: all,
                    uniforms_variants: all,
                    uniforms_lots: all,
                    uniforms_entitlements: all,
                };

                // Force view-only keys to read-only (even if defaults above change).
                Object.keys(perms).forEach((k) => {
                    if (viewOnlyKeys.has(k)) {
                        perms[k] = readOnly;
                    }
                });

                return perms;
            }

            function normalizeMenuPerm(value) {
                const none = { read: false, create: false, update: false, delete: false };
                const readOnly = { read: true, create: false, update: false, delete: false };
                const all = { read: true, create: true, update: true, delete: true };

                if (value === true) {
                    return all;
                }
                if (value === false || value === null || typeof value === 'undefined') {
                    return none;
                }

                if (typeof value === 'object') {
                    const read = !!value.read;
                    const create = !!value.create;
                    const update = !!value.update;
                    const del = !!value.delete;
                    const impliedRead = read || create || update || del;
                    return { read: impliedRead, create, update, delete: del };
                }

                const v = String(value).toLowerCase();
                if (v === 'write' || v === 'rw') {
                    return all;
                }
                if (v === 'read' || v === 'r') {
                    return readOnly;
                }
                if (v === 'none' || v === '0' || v === 'no') {
                    return none;
                }

                return readOnly;
            }

            function hasRead(p) {
                return !!normalizeMenuPerm(p).read;
            }

            function hasCreate(p) {
                return !!normalizeMenuPerm(p).create;
            }

            function hasUpdate(p) {
                return !!normalizeMenuPerm(p).update;
            }

            function hasDelete(p) {
                return !!normalizeMenuPerm(p).delete;
            }

            function applyMenuCheckboxes(prefix, permissions) {
                // Read checkboxes (existing)
                $('#' + prefix + '_menu_user_dashboard').prop('checked', hasRead(permissions.user_dashboard));
                $('#' + prefix + '_menu_admin_dashboard').prop('checked', hasRead(permissions.admin_dashboard));

                // Materai
                $('#' + prefix + '_menu_stamps').prop('checked', hasRead(permissions.stamps));
                $('#' + prefix + '_menu_stamps_master').prop('checked', hasRead(permissions.stamps_master));
                $('#' + prefix + '_menu_stamps_transactions').prop('checked', hasRead(permissions.stamps_transactions));
                $('#' + prefix + '_menu_stamps_requests').prop('checked', hasRead(permissions.stamps_requests));
                $('#' + prefix + '_menu_stamps_validation').prop('checked', hasRead(permissions.stamps_validation));

                $('#' + prefix + '_menu_daily_tasks').prop('checked', hasRead(permissions.daily_tasks));
                $('#' + prefix + '_menu_devices').prop('checked', hasRead(permissions.devices));
                $('#' + prefix + '_menu_assets').prop('checked', hasRead(permissions.assets));
                $('#' + prefix + '_menu_assets_data').prop('checked', hasRead(permissions.assets_data));
                $('#' + prefix + '_menu_accounts_data').prop('checked', hasRead(permissions.accounts_data));
                $('#' + prefix + '_menu_accounts_secrets').prop('checked', hasRead(permissions.accounts_secrets));
                $('#' + prefix + '_menu_documents_archive').prop('checked', hasRead(permissions.documents_archive));
                $('#' + prefix + '_menu_documents_restricted').prop('checked', hasRead(permissions.documents_restricted));
                $('#' + prefix + '_menu_assets_jababeka').prop('checked', hasRead(permissions.assets_jababeka));
                $('#' + prefix + '_menu_assets_karawang').prop('checked', hasRead(permissions.assets_karawang));
                $('#' + prefix + '_menu_assets_in').prop('checked', hasRead(permissions.assets_in));
                $('#' + prefix + '_menu_assets_transfer').prop('checked', hasRead(permissions.assets_transfer));
                $('#' + prefix + '_menu_employees').prop('checked', hasRead(permissions.employees));
                $('#' + prefix + '_menu_employees_index').prop('checked', hasRead(permissions.employees_index));
                $('#' + prefix + '_menu_employees_deleted').prop('checked', hasRead(permissions.employees_deleted));
                $('#' + prefix + '_menu_employees_audit').prop('checked', hasRead(permissions.employees_audit));
                $('#' + prefix + '_menu_master_hr').prop('checked', hasRead(permissions.master_hr));
                $('#' + prefix + '_menu_master_assets').prop('checked', hasRead(permissions.master_assets));
                $('#' + prefix + '_menu_master_accounts').prop('checked', hasRead(permissions.master_accounts));
                $('#' + prefix + '_menu_master_daily_task').prop('checked', hasRead(permissions.master_daily_task));
                $('#' + prefix + '_menu_departments').prop('checked', hasRead(permissions.departments));
                $('#' + prefix + '_menu_positions').prop('checked', hasRead(permissions.positions));
                $('#' + prefix + '_menu_asset_categories').prop('checked', hasRead(permissions.asset_categories));
                $('#' + prefix + '_menu_account_types').prop('checked', hasRead(permissions.account_types));
                $('#' + prefix + '_menu_asset_locations').prop('checked', hasRead(permissions.asset_locations));
                $('#' + prefix + '_menu_plant_sites').prop('checked', hasRead(permissions.plant_sites));
                $('#' + prefix + '_menu_asset_uoms').prop('checked', hasRead(permissions.asset_uoms));
                $('#' + prefix + '_menu_asset_vendors').prop('checked', hasRead(permissions.asset_vendors));
                $('#' + prefix + '_menu_daily_task_types').prop('checked', hasRead(permissions.daily_task_types));
                $('#' + prefix + '_menu_daily_task_priorities').prop('checked', hasRead(permissions.daily_task_priorities));
                $('#' + prefix + '_menu_daily_task_statuses').prop('checked', hasRead(permissions.daily_task_statuses));
                $('#' + prefix + '_menu_career').prop('checked', hasRead(permissions.career));
                $('#' + prefix + '_menu_certificate').prop('checked', hasRead(permissions.certificate));
                $('#' + prefix + '_menu_website_settings').prop('checked', hasRead(permissions.website_settings));
                $('#' + prefix + '_menu_website_products').prop('checked', hasRead(permissions.website_products));
                $('#' + prefix + '_menu_website_home_sections').prop('checked', hasRead(permissions.website_home_sections));
                $('#' + prefix + '_menu_website_contact_page').prop('checked', hasRead(permissions.website_contact_page));
                $('#' + prefix + '_menu_settings').prop('checked', hasRead(permissions.settings));
                $('#' + prefix + '_menu_settings_users').prop('checked', hasRead(permissions.settings_users));
                $('#' + prefix + '_menu_settings_history_user').prop('checked', hasRead(permissions.settings_history_user));
                $('#' + prefix + '_menu_settings_history_asset').prop('checked', hasRead(permissions.settings_history_asset));

                // Uniforms (Seragam Karyawan)
                $('#' + prefix + '_menu_uniforms_stock').prop('checked', hasRead(permissions.uniforms_stock));
                $('#' + prefix + '_menu_uniforms_distribution').prop('checked', hasRead(permissions.uniforms_distribution));
                $('#' + prefix + '_menu_uniforms_reports').prop('checked', hasRead(permissions.uniforms_reports));
                $('#' + prefix + '_menu_uniforms_entitlements').prop('checked', hasRead(permissions.uniforms_entitlements));
                $('#' + prefix + '_menu_uniforms_master').prop('checked', hasRead(permissions.uniforms_master));
                $('#' + prefix + '_menu_uniforms_variants').prop('checked', hasRead(permissions.uniforms_variants));
                $('#' + prefix + '_menu_uniforms_lots').prop('checked', hasRead(permissions.uniforms_lots));

                // Action toggles (injected)
                const keys = [
                    'user_dashboard', 'admin_dashboard',
                    'stamps', 'stamps_master', 'stamps_transactions', 'stamps_requests', 'stamps_validation',
                    'daily_tasks', 'devices',
                    'assets', 'assets_data', 'accounts_data', 'accounts_secrets', 'documents_archive', 'documents_restricted', 'assets_jababeka', 'assets_karawang', 'assets_in', 'assets_transfer',
                    'employees', 'employees_index', 'employees_deleted', 'employees_audit',
                    'master_hr', 'master_assets', 'master_accounts', 'master_daily_task',
                    'departments', 'positions', 'asset_categories', 'account_types', 'asset_locations', 'plant_sites', 'asset_uoms', 'asset_vendors',
                    'daily_task_types', 'daily_task_priorities', 'daily_task_statuses',
                    'career', 'certificate',
                    'website_settings', 'website_products', 'website_home_sections', 'website_contact_page',
                    'settings', 'settings_users', 'settings_history_user', 'settings_history_asset',
                    // Uniforms (Seragam Karyawan)
                    'uniforms_stock', 'uniforms_distribution', 'uniforms_reports', 'uniforms_entitlements', 'uniforms_master', 'uniforms_variants', 'uniforms_lots',
                ];
                keys.forEach((k) => {
                    $('#' + prefix + '_menu_' + k + '_create').prop('checked', hasCreate(permissions[k]));
                    $('#' + prefix + '_menu_' + k + '_update').prop('checked', hasUpdate(permissions[k]));
                    $('#' + prefix + '_menu_' + k + '_delete').prop('checked', hasDelete(permissions[k]));
                });

                updateGroupStates(prefix);
                syncActionDisables(prefix);
            }

            function syncActionDisables(prefix) {
                const $container = getMenuAccessContainer(prefix);
                if (!$container || $container.length === 0) {
                    return;
                }

                $container.find('.form-check').each(function () {
                    const $row = $(this);
                    const $read = $row
                        .find('input[type="checkbox"][id^="' + prefix + '_menu_"]')
                        .filter(function () {
                            const id = $(this).attr('id') || '';
                            return !id.endsWith('_create') && !id.endsWith('_update') && !id.endsWith('_delete');
                        })
                        .first();

                    if ($read.length === 0) {
                        return;
                    }

                    const readChecked = $read.prop('checked');
                    $row.find('input[id$="_create"], input[id$="_update"], input[id$="_delete"]').prop('disabled', !readChecked);
                });
            }

            function setIndeterminate($checkbox, isIndeterminate) {
                if (!$checkbox || $checkbox.length === 0) {
                    return;
                }
                $checkbox.prop('indeterminate', !!isIndeterminate);
            }

            function updateGroupState(prefix, groupKey, childKeys) {
                const $group = $('#' + prefix + '_menu_' + groupKey);
                const $children = childKeys.map((k) => $('#' + prefix + '_menu_' + k));
                const $groupCreate = $('#' + prefix + '_menu_' + groupKey + '_create');
                const $groupUpdate = $('#' + prefix + '_menu_' + groupKey + '_update');
                const $groupDelete = $('#' + prefix + '_menu_' + groupKey + '_delete');
                const $childrenCreate = childKeys.map((k) => $('#' + prefix + '_menu_' + k + '_create'));
                const $childrenUpdate = childKeys.map((k) => $('#' + prefix + '_menu_' + k + '_update'));
                const $childrenDelete = childKeys.map((k) => $('#' + prefix + '_menu_' + k + '_delete'));

                const total = $children.length;
                const checkedCount = $children.reduce((acc, $c) => acc + ($c.prop('checked') ? 1 : 0), 0);

                if (checkedCount === 0) {
                    $group.prop('checked', false);
                    setIndeterminate($group, false);
                } else {
                    // Any child selected => parent stays checked; partial => indeterminate
                    $group.prop('checked', true);
                    setIndeterminate($group, checkedCount !== total);
                }

                const syncAction = ($groupAction, $childrenAction) => {
                    if (!$groupAction || $groupAction.length === 0) {
                        return;
                    }
                    const totalA = $childrenAction.length;
                    const checkedA = $childrenAction.reduce((acc, $c) => acc + ($c.prop('checked') ? 1 : 0), 0);
                    if (checkedA === 0) {
                        $groupAction.prop('checked', false);
                        setIndeterminate($groupAction, false);
                    } else if (checkedA === totalA) {
                        $groupAction.prop('checked', true);
                        setIndeterminate($groupAction, false);
                    } else {
                        // Any child action selected => parent action stays checked; partial => indeterminate
                        $groupAction.prop('checked', true);
                        setIndeterminate($groupAction, true);
                    }
                };

                syncAction($groupCreate, $childrenCreate);
                syncAction($groupUpdate, $childrenUpdate);
                syncAction($groupDelete, $childrenDelete);
            }

            function updateGroupCounter(prefix, groupKey, childKeys) {
                const $badge = $('#' + prefix + '_menu_counter_' + groupKey);
                if (!$badge || $badge.length === 0) {
                    return;
                }

                const total = childKeys.length;
                const readCount = childKeys.reduce((acc, k) => {
                    const $c = $('#' + prefix + '_menu_' + k);
                    return acc + ($c.prop('checked') ? 1 : 0);
                }, 0);
                const createCount = childKeys.reduce((acc, k) => {
                    const $w = $('#' + prefix + '_menu_' + k + '_create');
                    return acc + ($w.prop('checked') ? 1 : 0);
                }, 0);
                const updateCount = childKeys.reduce((acc, k) => {
                    const $w = $('#' + prefix + '_menu_' + k + '_update');
                    return acc + ($w.prop('checked') ? 1 : 0);
                }, 0);
                const deleteCount = childKeys.reduce((acc, k) => {
                    const $w = $('#' + prefix + '_menu_' + k + '_delete');
                    return acc + ($w.prop('checked') ? 1 : 0);
                }, 0);

                $badge.text('R' + readCount + ' A' + createCount + ' E' + updateCount + ' D' + deleteCount);
                $badge.attr('title', 'Lihat ' + readCount + '/' + total + ', Tambah ' + createCount + '/' + total + ', Ubah ' + updateCount + '/' + total + ', Hapus ' + deleteCount + '/' + total);
            }

            function updateGroupCounters(prefix) {
                updateGroupCounter(prefix, 'stamps', [
                    'stamps_master',
                    'stamps_transactions',
                    'stamps_requests',
                    'stamps_validation',
                ]);

                updateGroupCounter(prefix, 'assets', [
                    'assets_data',
                    'accounts_data',
                    'accounts_secrets',
                    'documents_archive',
                    'documents_restricted',
                    'assets_jababeka',
                    'assets_karawang',
                    'assets_in',
                    'assets_transfer',
                ]);

                updateGroupCounter(prefix, 'employees', [
                    'employees_index',
                    'employees_deleted',
                    'employees_audit',
                ]);

                updateGroupCounter(prefix, 'master_hr', [
                    'departments',
                    'positions',
                ]);

                updateGroupCounter(prefix, 'master_assets', [
                    'asset_categories',
                    'asset_locations',
                    'plant_sites',
                    'asset_uoms',
                    'asset_vendors',
                ]);

                updateGroupCounter(prefix, 'master_accounts', [
                    'account_types',
                ]);

                updateGroupCounter(prefix, 'master_daily_task', [
                    'daily_task_types',
                    'daily_task_priorities',
                    'daily_task_statuses',
                ]);

                updateGroupCounter(prefix, 'settings', [
                    'settings_users',
                    'settings_history_user',
                    'settings_history_asset',
                ]);
            }

            function updateGroupStates(prefix) {
                updateGroupState(prefix, 'stamps', [
                    'stamps_master',
                    'stamps_transactions',
                    'stamps_requests',
                    'stamps_validation',
                ]);

                updateGroupState(prefix, 'assets', [
                    'assets_data',
                    'accounts_data',
                    'accounts_secrets',
                    'documents_archive',
                    'documents_restricted',
                    'assets_jababeka',
                    'assets_karawang',
                    'assets_in',
                    'assets_transfer',
                ]);

                updateGroupState(prefix, 'employees', [
                    'employees_index',
                    'employees_deleted',
                    'employees_audit',
                ]);

                updateGroupState(prefix, 'master_hr', [
                    'departments',
                    'positions',
                ]);

                updateGroupState(prefix, 'master_assets', [
                    'asset_categories',
                    'asset_locations',
                    'plant_sites',
                    'asset_uoms',
                    'asset_vendors',
                ]);

                updateGroupState(prefix, 'master_accounts', [
                    'account_types',
                ]);

                updateGroupState(prefix, 'master_daily_task', [
                    'daily_task_types',
                    'daily_task_priorities',
                    'daily_task_statuses',
                ]);

                updateGroupState(prefix, 'settings', [
                    'settings_users',
                    'settings_history_user',
                    'settings_history_asset',
                ]);

                updateGroupCounters(prefix);
            }

            function bindGroupToggle(prefix, groupKey, childKeys) {
                // Parent -> children
                $(document).on('change', '#' + prefix + '_menu_' + groupKey, function () {
                    const checked = $(this).prop('checked');
                    setIndeterminate($(this), false);
                    childKeys.forEach((k) => {
                        $('#' + prefix + '_menu_' + k).prop('checked', checked);
                        if (!checked) {
                            $('#' + prefix + '_menu_' + k + '_create').prop('checked', false);
                            $('#' + prefix + '_menu_' + k + '_update').prop('checked', false);
                            $('#' + prefix + '_menu_' + k + '_delete').prop('checked', false);
                        }
                    });
                    if (!checked) {
                        ['create', 'update', 'delete'].forEach((a) => {
                            $('#' + prefix + '_menu_' + groupKey + '_' + a).prop('checked', false);
                            setIndeterminate($('#' + prefix + '_menu_' + groupKey + '_' + a), false);
                        });
                    }
                    updateGroupStates(prefix);
                });

                // Parent action -> children action (action implies read)
                ['create', 'update', 'delete'].forEach((action) => {
                    $(document).on('change', '#' + prefix + '_menu_' + groupKey + '_' + action, function () {
                        const checked = $(this).prop('checked');
                        setIndeterminate($(this), false);
                        childKeys.forEach((k) => {
                            if (checked) {
                                $('#' + prefix + '_menu_' + k).prop('checked', true);
                            }
                            $('#' + prefix + '_menu_' + k + '_' + action).prop('checked', checked);
                        });
                        if (checked) {
                            $('#' + prefix + '_menu_' + groupKey).prop('checked', true);
                            setIndeterminate($('#' + prefix + '_menu_' + groupKey), false);
                        }
                        updateGroupStates(prefix);
                    });
                });

                // Children -> parent state
                childKeys.forEach((k) => {
                    $(document).on('change', '#' + prefix + '_menu_' + k, function () {
                        if (!$(this).prop('checked')) {
                            $('#' + prefix + '_menu_' + k + '_create').prop('checked', false);
                            $('#' + prefix + '_menu_' + k + '_update').prop('checked', false);
                            $('#' + prefix + '_menu_' + k + '_delete').prop('checked', false);
                        }
                        updateGroupStates(prefix);
                    });

                    ['create', 'update', 'delete'].forEach((action) => {
                        $(document).on('change', '#' + prefix + '_menu_' + k + '_' + action, function () {
                            if ($(this).prop('checked')) {
                                $('#' + prefix + '_menu_' + k).prop('checked', true);
                            }
                            updateGroupStates(prefix);
                        });
                    });
                });
            }

            function effectiveMenuPermissions(roleId, storedOverrides) {
                const base = defaultMenuPermissionsForRole(roleId);
                if (!storedOverrides) {
                    return base;
                }
                // Stored overrides may be legacy boolean/string or new object: {read,create,update,delete}
                const normalized = {};
                Object.keys(storedOverrides).forEach((k) => {
                    normalized[k] = normalizeMenuPerm(storedOverrides[k]);
                });
                return Object.assign({}, base, normalized);
            }

            function ensureWriteControls(prefix) {
                const $container = getMenuAccessContainer(prefix);
                if (!$container || $container.length === 0) {
                    return;
                }

                // Normalize each checkbox row so the right-side controls (counter + Write) align neatly.
                $container.find('.form-check').each(function () {
                    const $row = $(this);
                    if ($row.data('rw-prepared')) {
                        return;
                    }

                    const $read = $row.find('input[type="checkbox"]').first();
                    const $label = $row.find('label').first();
                    if ($read.length === 0 || $label.length === 0) {
                        return;
                    }

                    const $counter = $label.find('.menu-counter').first();
                    if ($counter.length > 0) {
                        $counter.detach();
                    }

                    const $left = $('<div class="menu-row-left"></div>');
                    const $right = $('<div class="menu-row-right"></div>');
                    $left.append($read.detach());
                    $left.append($label.detach());
                    if ($counter.length > 0) {
                        $right.append($counter);
                    }

                    const $wrap = $('<div class="menu-row"></div>');
                    $wrap.append($left);
                    $wrap.append($right);

                    $row.empty().append($wrap);
                    $row.data('rw-prepared', true);
                });

                // Keys that are effectively view-only in this app.
                // We keep them as Read-only to avoid confusing Add/Edit/Delete for dashboards/log pages.
                const viewOnlyKeys = new Set([
                    'user_dashboard',
                    'admin_dashboard',
                    'settings_history_user',
                    'settings_history_asset',
                ]);

                $container.find('input[type="checkbox"][id^="' + prefix + '_menu_"]').each(function () {
                    const id = $(this).attr('id');
                    if (!id || id.endsWith('_create') || id.endsWith('_update') || id.endsWith('_delete')) {
                        return;
                    }

                    const key = id.replace(prefix + '_menu_', '');
                    if (viewOnlyKeys.has(key)) {
                        return;
                    }
                    const createId = prefix + '_menu_' + key + '_create';
                    const updateId = prefix + '_menu_' + key + '_update';
                    const deleteId = prefix + '_menu_' + key + '_delete';
                    if (document.getElementById(createId) || document.getElementById(updateId) || document.getElementById(deleteId)) {
                        return;
                    }

                    const $label = $('label[for="' + id + '"]');
                    if (!$label || $label.length === 0) {
                        return;
                    }

                    const $formCheck = $(this).closest('.form-check');
                    const $right = $formCheck.find('.menu-row-right');

                    const $actions = $(
                        '<div class="rw-actions" role="group" aria-label="Aksi">' +
                        '<div class="btn-group btn-group-sm" role="group">' +
                        '<input class="btn-check" type="checkbox" id="' + createId + '" name="menu_' + key + '_create" value="1">' +
                        '<label class="btn btn-outline-secondary" for="' + createId + '" title="Tambah (boleh tambah data)">' +
                        '<i class="fas fa-plus" aria-hidden="true"></i>' +
                        '</label>' +
                        '<input class="btn-check" type="checkbox" id="' + updateId + '" name="menu_' + key + '_update" value="1">' +
                        '<label class="btn btn-outline-secondary" for="' + updateId + '" title="Ubah (boleh ubah data)">' +
                        '<i class="fas fa-pencil-alt" aria-hidden="true"></i>' +
                        '</label>' +
                        '<input class="btn-check" type="checkbox" id="' + deleteId + '" name="menu_' + key + '_delete" value="1">' +
                        '<label class="btn btn-outline-secondary" for="' + deleteId + '" title="Hapus (boleh hapus data)">' +
                        '<i class="fas fa-trash-alt" aria-hidden="true"></i>' +
                        '</label>' +
                        '</div>' +
                        '</div>'
                    );

                    if ($right && $right.length > 0) {
                        $right.append($actions);
                    } else {
                        // Fallback (shouldn't happen): append to label.
                        $label.append($actions);
                    }
                });

                // Disable action toggles when Read is unchecked to reduce confusion.
                if (!$container.data('rw-actions-bound')) {
                    $(document).on(
                        'change',
                        'input[type="checkbox"][id^="' + prefix + '_menu_"]:not([id$="_create"]):not([id$="_update"]):not([id$="_delete"])',
                        function () {
                            const $row = $(this).closest('.form-check');
                            const readChecked = $(this).prop('checked');
                            $row.find('input[id$="_create"], input[id$="_update"], input[id$="_delete"]').prop('disabled', !readChecked);
                        }
                    );
                    $container.data('rw-actions-bound', true);
                }

                // Initial sync (after injection).
                syncActionDisables(prefix);
            }

            function getMenuAccessContainer(prefix) {
                return $('#' + prefix + '_menu_access_container');
            }

            function attachMenuKeysPresentToForm(prefix) {
                const $container = getMenuAccessContainer(prefix);
                if (!$container || $container.length === 0) {
                    return;
                }

                const $form = prefix === 'edit' ? $('#editUserForm') : $('#addUserModal form');
                if (!$form || $form.length === 0) {
                    return;
                }

                // Remove previous generated inputs
                $form.find('input[name="menu_keys_present[]"]').remove();

                $container
                    .find('input[type="checkbox"][id^="' + prefix + '_menu_"]')
                    .filter(function () {
                        const id = $(this).attr('id') || '';
                        return !id.endsWith('_create') && !id.endsWith('_update') && !id.endsWith('_delete');
                    })
                    .each(function () {
                        const id = $(this).attr('id') || '';
                        const key = id.replace(prefix + '_menu_', '');
                        if (!key) {
                            return;
                        }
                        $form.append('<input type="hidden" name="menu_keys_present[]" value="' + key + '">');
                    });
            }

            function setAllMenuCheckboxes(prefix, checked) {
                const $container = getMenuAccessContainer(prefix);
                // Select/Clear should operate on READ only.
                $container
                    .find('input[type="checkbox"][id^="' + prefix + '_menu_"]:not([id$="_create"]):not([id$="_update"]):not([id$="_delete"])')
                    .prop('checked', !!checked);

                // If read is being cleared, write must be cleared too.
                if (!checked) {
                    $container
                        .find('input[type="checkbox"][id^="' + prefix + '_menu_"][id$="_create"], input[type="checkbox"][id^="' + prefix + '_menu_"][id$="_update"], input[type="checkbox"][id^="' + prefix + '_menu_"][id$="_delete"]')
                        .prop('checked', false);
                }
                updateGroupStates(prefix);
                syncActionDisables(prefix);
            }

            function clearAllMenuCheckboxes(prefix) {
                const $container = getMenuAccessContainer(prefix);
                $container
                    .find('input[type="checkbox"][id^="' + prefix + '_menu_"]')
                    .prop('checked', false);
                updateGroupStates(prefix);
                syncActionDisables(prefix);
            }

            function resetMenuToRoleDefaults(prefix) {
                const roleId = prefix === 'add' ? $('#role').val() : $('#edit_role').val();
                applyMenuCheckboxes(prefix, defaultMenuPermissionsForRole(roleId));
                syncActionDisables(prefix);
            }

            function normalizeText(s) {
                return String(s || '').toLowerCase().trim();
            }

            function applyMenuFilter(prefix, query) {
                const q = normalizeText(query);
                const $container = getMenuAccessContainer(prefix);

                if (!$container || $container.length === 0) {
                    return;
                }

                // Show everything when query is empty
                if (!q) {
                    $container.find('.form-check').show();
                    $container.find('.ms-3').show();
                    $container.find('.border.rounded').show();
                    $container.find('.col-12, .col-md-6').show();
                    return;
                }

                // Filter each checkbox line by its label text
                $container.find('.form-check').each(function () {
                    const $row = $(this);
                    const labelText = normalizeText($row.find('label').text());
                    const match = labelText.includes(q);
                    $row.toggle(match);
                });

                // Show indentation groups only if they contain visible items
                $container.find('.ms-3').each(function () {
                    const $block = $(this);
                    $block.toggle($block.find('.form-check:visible').length > 0);
                });

                // Show boxes/columns only if they contain visible items
                $container.find('.border.rounded').each(function () {
                    const $box = $(this);
                    $box.toggle($box.find('.form-check:visible').length > 0);
                });
            }

            function enforceAdminDashboardPermissionForRestrictedTabs(prefix) {
                const allTabs = ['asset', 'stamps', 'uniforms', 'documents', 'employee'];
                const checkedCount = allTabs.filter(function (key) {
                    return $('#' + prefix + '_dash_tab_' + key).is(':checked');
                }).length;

                // Only enforce when tabs are being restricted (some checked, some unchecked).
                if (checkedCount > 0 && checkedCount < allTabs.length) {
                    $('#' + prefix + '_menu_admin_dashboard').prop('checked', true);
                    updateGroupStates(prefix);
                    syncActionDisables(prefix);
                }
            }

            $(document).on('click', '.btn-edit-user', function () {
                $('#edit_user_id').val($(this).data('id'));
                $('#edit_name').val($(this).data('name'));
                const employeeId = $(this).data('employeeId') || '';
                $('#edit_employee_id').val(employeeId).trigger('change');
                $('#edit_username').val($(this).data('username'));
                $('#edit_email').val($(this).data('email'));
                $('#edit_role').val($(this).data('role'));

                if ($('#edit_stamp_validator_user_id').length) {
                    const storedValidatorId = $(this).data('stampValidatorUserId') || '';
                    const $validatorSelect = $('#edit_stamp_validator_user_id');
                    const $validatorWarning = $('#edit_stamp_validator_warning');

                    $validatorSelect.val(storedValidatorId);

                    const isStoredSet = String(storedValidatorId || '') !== '';
                    const isApplied = String($validatorSelect.val() || '') === String(storedValidatorId || '');

                    if ($validatorWarning.length) {
                        // If a stored value exists but doesn't exist in eligible candidates, show warning.
                        $validatorWarning.toggleClass('d-none', !(isStoredSet && !isApplied));
                    }
                }

                if ($('#edit_no_employee').length) {
                    const noEmployee = !employeeId;
                    $('#edit_no_employee').prop('checked', noEmployee);
                    setEditNoEmployeeMode(noEmployee);
                }

                const tabOverrides = $(this).data('dashboardTabs');
                const allTabs = ['asset', 'stamps', 'uniforms', 'documents', 'employee'];
                const enabledTabs = Array.isArray(tabOverrides) ? tabOverrides : allTabs;
                $('#edit_dash_tab_asset').prop('checked', enabledTabs.includes('asset'));
                $('#edit_dash_tab_stamps').prop('checked', enabledTabs.includes('stamps'));
                $('#edit_dash_tab_uniforms').prop('checked', enabledTabs.includes('uniforms'));
                $('#edit_dash_tab_documents').prop('checked', enabledTabs.includes('documents'));
                $('#edit_dash_tab_employee').prop('checked', enabledTabs.includes('employee'));

                enforceAdminDashboardPermissionForRestrictedTabs('edit');

                const perms = $(this).data('dashboard');
                const asset = (perms && perms.asset) ? perms.asset : { kpi: true, charts: true, recent: true };

                $('#edit_dash_asset_kpi').prop('checked', !!asset.kpi);
                $('#edit_dash_asset_charts').prop('checked', !!asset.charts);
                $('#edit_dash_asset_recent').prop('checked', !!asset.recent);

                const roleId = $(this).data('role');
                const storedMenu = $(this).data('menu');
                ensureWriteControls('edit');
                applyMenuCheckboxes('edit', effectiveMenuPermissions(roleId, storedMenu));

                $('#edit_menu_filter').val('');
                applyMenuFilter('edit', '');

                // Set form action
                $('#editUserForm').attr('action', '/admin/users/' + $(this).data('id'));
                $('#editUserModal').modal('show');
            });

            function syncNameFromEmployee($select, $nameInput) {
                const selectedName = $select.find('option:selected').data('emp-name');
                if (selectedName) {
                    $nameInput.val(selectedName);
                }
            }

            function setAddNoEmployeeMode(enabled) {
                const $employee = $('#employee_id');
                const $name = $('#name');

                if (enabled) {
                    $employee.val('').prop('required', false).prop('disabled', true);
                    $name.prop('readonly', false);
                } else {
                    $employee.prop('disabled', false).prop('required', true);
                    $name.prop('readonly', true);
                    syncNameFromEmployee($employee, $name);
                }
            }

            function setEditNoEmployeeMode(enabled) {
                const $employee = $('#edit_employee_id');
                const $name = $('#edit_name');

                if (enabled) {
                    $employee.val('').prop('required', false).prop('disabled', true);
                    $name.prop('readonly', false);
                } else {
                    $employee.prop('disabled', false).prop('required', true);
                    $name.prop('readonly', true);
                    syncNameFromEmployee($employee, $name);
                }
            }

            $(document).on('change', '#employee_id', function () {
                if ($('#add_no_employee').length && $('#add_no_employee').is(':checked')) {
                    return;
                }
                syncNameFromEmployee($(this), $('#name'));
            });

            $(document).on('change', '#edit_employee_id', function () {
                if ($('#edit_no_employee').length && $('#edit_no_employee').is(':checked')) {
                    return;
                }
                syncNameFromEmployee($(this), $('#edit_name'));
            });

            $(document).on('change', '#add_no_employee', function () {
                setAddNoEmployeeMode($(this).is(':checked'));
            });

            $(document).on('change', '#edit_no_employee', function () {
                setEditNoEmployeeMode($(this).is(':checked'));
            });

            if ($('#add_no_employee').length) {
                setAddNoEmployeeMode($('#add_no_employee').is(':checked'));
            }

            // If admin dashboard tabs are restricted, make sure Dashboard menu access is enabled.
            $(document).on('change', '#add_dash_tab_asset, #add_dash_tab_stamps, #add_dash_tab_uniforms, #add_dash_tab_documents, #add_dash_tab_employee', function () {
                enforceAdminDashboardPermissionForRestrictedTabs('add');
            });
            $(document).on('change', '#edit_dash_tab_asset, #edit_dash_tab_stamps, #edit_dash_tab_uniforms, #edit_dash_tab_documents, #edit_dash_tab_employee', function () {
                enforceAdminDashboardPermissionForRestrictedTabs('edit');
            });

            // Before submitting, send the list of menu keys shown in the UI.
            // This allows the backend to persist unchecked states (checkboxes don't submit when unchecked).
            $(document).on('submit', '#editUserForm', function () {
                attachMenuKeysPresentToForm('edit');
            });
            $(document).on('submit', '#addUserModal form', function () {
                attachMenuKeysPresentToForm('add');
            });

            // Menu toolbar actions (Add)
            $(document).on('input', '#add_menu_filter', function () {
                applyMenuFilter('add', $(this).val());
            });
            $(document).on('click', '#add_menu_select_all', function () {
                setAllMenuCheckboxes('add', true);
            });
            $(document).on('click', '#add_menu_clear_all', function () {
                clearAllMenuCheckboxes('add');
            });
            $(document).on('click', '#add_menu_reset_default', function () {
                resetMenuToRoleDefaults('add');
            });

            // Menu toolbar actions (Edit)
            $(document).on('input', '#edit_menu_filter', function () {
                applyMenuFilter('edit', $(this).val());
            });
            $(document).on('click', '#edit_menu_select_all', function () {
                setAllMenuCheckboxes('edit', true);
            });
            $(document).on('click', '#edit_menu_clear_all', function () {
                clearAllMenuCheckboxes('edit');
            });
            $(document).on('click', '#edit_menu_reset_default', function () {
                resetMenuToRoleDefaults('edit');
            });

            // Default menu permissions on Add modal role change
            $('#role').on('change', function () {
                applyMenuCheckboxes('add', defaultMenuPermissionsForRole($(this).val()));
                applyMenuFilter('add', $('#add_menu_filter').val());
            });

            // Default menu permissions on Edit modal role change
            $('#edit_role').on('change', function () {
                // When role changes, reset to role defaults (admin can fine-tune afterward)
                applyMenuCheckboxes('edit', defaultMenuPermissionsForRole($(this).val()));
                applyMenuFilter('edit', $('#edit_menu_filter').val());
            });

            // If Super Admin re-selects validator, hide warning.
            $(document).on('change', '#edit_stamp_validator_user_id', function () {
                const $warn = $('#edit_stamp_validator_warning');
                if ($warn.length) {
                    $warn.addClass('d-none');
                }
            });

            // Initialize Add modal defaults once on load
            $(document).ready(function () {
                ensureWriteControls('add');
                ensureWriteControls('edit');
                // Keep group/submenu checkboxes in sync
                bindGroupToggle('add', 'stamps', [
                    'stamps_master',
                    'stamps_transactions',
                    'stamps_requests',
                    'stamps_validation',
                ]);
                bindGroupToggle('add', 'assets', [
                    'assets_data',
                    'accounts_data',
                    'accounts_secrets',
                    'documents_archive',
                    'documents_restricted',
                    'assets_jababeka',
                    'assets_karawang',
                    'assets_in',
                    'assets_transfer',
                ]);

                bindGroupToggle('add', 'employees', [
                    'employees_index',
                    'employees_deleted',
                    'employees_audit',
                ]);

                bindGroupToggle('add', 'master_hr', [
                    'departments',
                    'positions',
                ]);

                bindGroupToggle('add', 'master_assets', [
                    'asset_categories',
                    'asset_locations',
                    'plant_sites',
                    'asset_uoms',
                    'asset_vendors',
                ]);

                bindGroupToggle('add', 'master_accounts', [
                    'account_types',
                ]);

                bindGroupToggle('add', 'master_daily_task', [
                    'daily_task_types',
                    'daily_task_priorities',
                    'daily_task_statuses',
                ]);

                bindGroupToggle('add', 'settings', [
                    'settings_users',
                    'settings_history_user',
                    'settings_history_asset',
                ]);

                bindGroupToggle('edit', 'assets', [
                    'assets_data',
                    'accounts_data',
                    'accounts_secrets',
                    'documents_archive',
                    'documents_restricted',
                    'assets_jababeka',
                    'assets_karawang',
                    'assets_in',
                    'assets_transfer',
                ]);

                bindGroupToggle('edit', 'employees', [
                    'employees_index',
                    'employees_deleted',
                    'employees_audit',
                ]);

                bindGroupToggle('edit', 'master_hr', [
                    'departments',
                    'positions',
                ]);

                bindGroupToggle('edit', 'master_assets', [
                    'asset_categories',
                    'asset_locations',
                    'plant_sites',
                    'asset_uoms',
                    'asset_vendors',
                ]);

                bindGroupToggle('edit', 'master_accounts', [
                    'account_types',
                ]);

                bindGroupToggle('edit', 'master_daily_task', [
                    'daily_task_types',
                    'daily_task_priorities',
                    'daily_task_statuses',
                ]);

                bindGroupToggle('edit', 'settings', [
                    'settings_users',
                    'settings_history_user',
                    'settings_history_asset',
                ]);

                bindGroupToggle('edit', 'stamps', [
                    'stamps_master',
                    'stamps_transactions',
                    'stamps_requests',
                    'stamps_validation',
                ]);

                applyMenuCheckboxes('add', defaultMenuPermissionsForRole($('#role').val()));
                applyMenuFilter('add', $('#add_menu_filter').val());
            });

            // SweetAlert2 for delete confirmation
            $(document).on('click', '.btn-delete-user', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Yakin?',
                    text: "Tindakan ini tidak dapat dibatalkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Toggle password visibility for Add User Modal
            function togglePassword() {
                const passwordInput = document.getElementById('password');
                const icon = document.getElementById('togglePasswordIcon');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            }
            // Toggle password visibility for Edit User Modal
            function toggleEditPassword() {
                const passwordInput = document.getElementById('edit_password');
                const icon = document.getElementById('toggleEditPasswordIcon');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            }
        </script>
@endsection