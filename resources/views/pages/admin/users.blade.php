@extends('layouts.master')

@section('title', 'Ilsam - Users Management')

@section('title-sub', 'Settings & UI')
@section('pagetitle', 'Users Management')
@section('css')
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
@endsection
@section('content')

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
                        <h5 class="card-title mb-0"> Users Management </h5>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">Add
                            User</button>
                    </div>
                    <div class="card-body">
                        <!-- Add User Modal -->
                        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.users.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="dash_permissions_present" value="1">
                                        <input type="hidden" name="menu_permissions_present" value="1">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="name" name="name" required>
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
                                                        <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="role" class="form-label">Role</label>
                                                <select class="form-select" id="role" name="role_id" required>
                                                    <option value="">Select Role</option>
                                                    <option value="1">Super Admin</option>
                                                    <option value="2">Admin</option>
                                                    <option value="3">User</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Dashboard Widgets</label>
                                                <div class="row g-2">
                                                    <div class="col-12 col-md-6">
                                                        <div class="border rounded p-2 h-100">
                                                            <div class="fw-semibold mb-2">Asset</div>
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
                                                                    for="add_dash_asset_charts">Charts</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="add_dash_asset_recent" name="dash_asset_recent"
                                                                    value="1" checked>
                                                                <label class="form-check-label"
                                                                    for="add_dash_asset_recent">Recent</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <div class="border rounded p-2 h-100">
                                                            <div class="fw-semibold mb-2">Uniform</div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="add_dash_uniform_kpi" name="dash_uniform_kpi"
                                                                    value="1" checked>
                                                                <label class="form-check-label"
                                                                    for="add_dash_uniform_kpi">KPI</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="add_dash_uniform_charts" name="dash_uniform_charts"
                                                                    value="1" checked>
                                                                <label class="form-check-label"
                                                                    for="add_dash_uniform_charts">Charts</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="add_dash_uniform_recent" name="dash_uniform_recent"
                                                                    value="1" checked>
                                                                <label class="form-check-label"
                                                                    for="add_dash_uniform_recent">Recent</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-text">Jika semua dicentang, akses dashboard default (full).
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Menu Access</label>
                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <div class="border rounded p-2 h-100">
                                                            <div class="fw-semibold mb-2">User Area</div>
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
                                                            <div class="fw-semibold mb-2">Admin Area</div>
                                                            <div class="row g-2">
                                                                <div class="col-12 col-md-6">
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
                                                                            id="add_menu_employees" name="menu_employees"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_employees">Master Karyawan</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_master_data"
                                                                            name="menu_master_data" value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_master_data">Master Data</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-md-6">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_assets" name="menu_assets"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_assets">Perlengkapan Aset</label>
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
                                                                            id="add_menu_uniforms" name="menu_uniforms"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_uniforms">Stok Seragam</label>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_uniforms_master"
                                                                                name="menu_uniforms_master" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_uniforms_master">Master
                                                                                Seragam</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_uniforms_stock"
                                                                                name="menu_uniforms_stock" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_uniforms_stock">Stok
                                                                                Masuk</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_uniforms_distribution"
                                                                                name="menu_uniforms_distribution" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_uniforms_distribution">Distribusi</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="add_menu_uniforms_history"
                                                                                name="menu_uniforms_history" value="1">
                                                                            <label class="form-check-label"
                                                                                for="add_menu_uniforms_history">Riwayat</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="add_menu_settings" name="menu_settings"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="add_menu_settings">Settings & Log</label>
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
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save</button>
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
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->role->role_name ?? '-' }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary btn-edit-user"
                                                data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                                data-username="{{ $user->username }}" data-email="{{ $user->email }}"
                                                data-role="{{ $user->role_id }}"
                                                data-dashboard='@json($user->dashboard_permissions)'
                                                data-menu='@json($user->menu_permissions)'>
                                                Edit
                                            </button>
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                style="display:inline-block" class="form-delete-user">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-sm btn-danger btn-delete-user">Delete</button>
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
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form id="editUserForm" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="dash_permissions_present" value="1">
                                        <input type="hidden" name="menu_permissions_present" value="1">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" id="edit_user_id" name="user_id">
                                            <div class="mb-3">
                                                <label for="edit_name" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="edit_name" name="name" required>
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
                                                        name="password" placeholder="Leave blank to keep current password">
                                                    <button class="btn btn-primary" type="button" tabindex="-1"
                                                        onclick="toggleEditPassword()">
                                                        <i class="bi bi-eye-slash" id="toggleEditPasswordIcon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_role" class="form-label">Role</label>
                                                <select class="form-select" id="edit_role" name="role_id" required>
                                                    <option value="">Select Role</option>
                                                    <option value="1">Super Admin</option>
                                                    <option value="2">Admin</option>
                                                    <option value="3">User</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Dashboard Widgets</label>
                                                <div class="row g-2">
                                                    <div class="col-12 col-md-6">
                                                        <div class="border rounded p-2 h-100">
                                                            <div class="fw-semibold mb-2">Asset</div>
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
                                                                    for="edit_dash_asset_charts">Charts</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="edit_dash_asset_recent" name="dash_asset_recent"
                                                                    value="1">
                                                                <label class="form-check-label"
                                                                    for="edit_dash_asset_recent">Recent</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <div class="border rounded p-2 h-100">
                                                            <div class="fw-semibold mb-2">Uniform</div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="edit_dash_uniform_kpi" name="dash_uniform_kpi"
                                                                    value="1">
                                                                <label class="form-check-label"
                                                                    for="edit_dash_uniform_kpi">KPI</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="edit_dash_uniform_charts" name="dash_uniform_charts"
                                                                    value="1">
                                                                <label class="form-check-label"
                                                                    for="edit_dash_uniform_charts">Charts</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="edit_dash_uniform_recent" name="dash_uniform_recent"
                                                                    value="1">
                                                                <label class="form-check-label"
                                                                    for="edit_dash_uniform_recent">Recent</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-text">Matikan bagian tertentu untuk menyembunyikannya dari
                                                    user.</div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Menu Access</label>
                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <div class="border rounded p-2 h-100">
                                                            <div class="fw-semibold mb-2">User Area</div>
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
                                                            <div class="fw-semibold mb-2">Admin Area</div>
                                                            <div class="row g-2">
                                                                <div class="col-12 col-md-6">
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
                                                                            id="edit_menu_employees" name="menu_employees"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_employees">Master
                                                                            Karyawan</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_master_data"
                                                                            name="menu_master_data" value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_master_data">Master Data</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-md-6">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_assets" name="menu_assets"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_assets">Perlengkapan Aset</label>
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
                                                                            id="edit_menu_uniforms" name="menu_uniforms"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_uniforms">Stok Seragam</label>
                                                                    </div>
                                                                    <div class="ms-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_uniforms_master"
                                                                                name="menu_uniforms_master" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_uniforms_master">Master
                                                                                Seragam</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_uniforms_stock"
                                                                                name="menu_uniforms_stock" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_uniforms_stock">Stok
                                                                                Masuk</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_uniforms_distribution"
                                                                                name="menu_uniforms_distribution" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_uniforms_distribution">Distribusi</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_menu_uniforms_history"
                                                                                name="menu_uniforms_history" value="1">
                                                                            <label class="form-check-label"
                                                                                for="edit_menu_uniforms_history">Riwayat</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="edit_menu_settings" name="menu_settings"
                                                                            value="1">
                                                                        <label class="form-check-label"
                                                                            for="edit_menu_settings">Settings & Log</label>
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
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Update</button>
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
                // Role IDs: 1 Super Admin, 2 Admin, 3 User
                if (String(roleId) === '3') {
                    return {
                        user_dashboard: true,
                        admin_dashboard: false,
                        // Groups
                        assets: false,
                        uniforms: false,

                        // Assets submenus
                        assets_data: false,
                        assets_jababeka: false,
                        assets_karawang: false,
                        assets_in: false,
                        assets_transfer: false,

                        // Uniforms submenus
                        uniforms_master: false,
                        uniforms_stock: false,
                        uniforms_distribution: false,
                        uniforms_history: false,

                        employees: false,
                        master_data: false,
                        settings: false,
                    };
                }

                return {
                    user_dashboard: true,
                    admin_dashboard: true,
                    // Groups
                    assets: true,
                    uniforms: true,

                    // Assets submenus
                    assets_data: true,
                    assets_jababeka: true,
                    assets_karawang: true,
                    assets_in: true,
                    assets_transfer: true,

                    // Uniforms submenus
                    uniforms_master: true,
                    uniforms_stock: true,
                    uniforms_distribution: true,
                    uniforms_history: true,

                    employees: true,
                    master_data: true,
                    settings: true,
                };
            }

            function applyMenuCheckboxes(prefix, permissions) {
                $('#' + prefix + '_menu_user_dashboard').prop('checked', !!permissions.user_dashboard);
                $('#' + prefix + '_menu_admin_dashboard').prop('checked', !!permissions.admin_dashboard);
                $('#' + prefix + '_menu_assets').prop('checked', !!permissions.assets);
                $('#' + prefix + '_menu_assets_data').prop('checked', !!permissions.assets_data);
                $('#' + prefix + '_menu_assets_jababeka').prop('checked', !!permissions.assets_jababeka);
                $('#' + prefix + '_menu_assets_karawang').prop('checked', !!permissions.assets_karawang);
                $('#' + prefix + '_menu_assets_in').prop('checked', !!permissions.assets_in);
                $('#' + prefix + '_menu_assets_transfer').prop('checked', !!permissions.assets_transfer);
                $('#' + prefix + '_menu_uniforms').prop('checked', !!permissions.uniforms);
                $('#' + prefix + '_menu_uniforms_master').prop('checked', !!permissions.uniforms_master);
                $('#' + prefix + '_menu_uniforms_stock').prop('checked', !!permissions.uniforms_stock);
                $('#' + prefix + '_menu_uniforms_distribution').prop('checked', !!permissions.uniforms_distribution);
                $('#' + prefix + '_menu_uniforms_history').prop('checked', !!permissions.uniforms_history);
                $('#' + prefix + '_menu_employees').prop('checked', !!permissions.employees);
                $('#' + prefix + '_menu_master_data').prop('checked', !!permissions.master_data);
                $('#' + prefix + '_menu_settings').prop('checked', !!permissions.settings);

                updateGroupStates(prefix);
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

                const total = $children.length;
                const checkedCount = $children.reduce((acc, $c) => acc + ($c.prop('checked') ? 1 : 0), 0);

                if (checkedCount === 0) {
                    $group.prop('checked', false);
                    setIndeterminate($group, false);
                    return;
                }

                if (checkedCount === total) {
                    $group.prop('checked', true);
                    setIndeterminate($group, false);
                    return;
                }

                // Partial selection
                $group.prop('checked', false);
                setIndeterminate($group, true);
            }

            function updateGroupStates(prefix) {
                updateGroupState(prefix, 'assets', [
                    'assets_data',
                    'assets_jababeka',
                    'assets_karawang',
                    'assets_in',
                    'assets_transfer',
                ]);

                updateGroupState(prefix, 'uniforms', [
                    'uniforms_master',
                    'uniforms_stock',
                    'uniforms_distribution',
                    'uniforms_history',
                ]);
            }

            function bindGroupToggle(prefix, groupKey, childKeys) {
                // Parent -> children
                $(document).on('change', '#' + prefix + '_menu_' + groupKey, function () {
                    const checked = $(this).prop('checked');
                    setIndeterminate($(this), false);
                    childKeys.forEach((k) => {
                        $('#' + prefix + '_menu_' + k).prop('checked', checked);
                    });
                });

                // Children -> parent state
                childKeys.forEach((k) => {
                    $(document).on('change', '#' + prefix + '_menu_' + k, function () {
                        updateGroupStates(prefix);
                    });
                });
            }

            function effectiveMenuPermissions(roleId, storedOverrides) {
                const base = defaultMenuPermissionsForRole(roleId);
                if (!storedOverrides) {
                    return base;
                }
                // Stored overrides are an object like { assets: true/false, ... }
                return Object.assign({}, base, storedOverrides);
            }

            $(document).on('click', '.btn-edit-user', function () {
                $('#edit_user_id').val($(this).data('id'));
                $('#edit_name').val($(this).data('name'));
                $('#edit_username').val($(this).data('username'));
                $('#edit_email').val($(this).data('email'));
                $('#edit_role').val($(this).data('role'));

                const perms = $(this).data('dashboard');
                const asset = (perms && perms.asset) ? perms.asset : { kpi: true, charts: true, recent: true };
                const uniform = (perms && perms.uniform) ? perms.uniform : { kpi: true, charts: true, recent: true };

                $('#edit_dash_asset_kpi').prop('checked', !!asset.kpi);
                $('#edit_dash_asset_charts').prop('checked', !!asset.charts);
                $('#edit_dash_asset_recent').prop('checked', !!asset.recent);
                $('#edit_dash_uniform_kpi').prop('checked', !!uniform.kpi);
                $('#edit_dash_uniform_charts').prop('checked', !!uniform.charts);
                $('#edit_dash_uniform_recent').prop('checked', !!uniform.recent);

                const roleId = $(this).data('role');
                const storedMenu = $(this).data('menu');
                applyMenuCheckboxes('edit', effectiveMenuPermissions(roleId, storedMenu));

                // Set form action
                $('#editUserForm').attr('action', '/admin/users/' + $(this).data('id'));
                $('#editUserModal').modal('show');
            });

            // Default menu permissions on Add modal role change
            $('#role').on('change', function () {
                applyMenuCheckboxes('add', defaultMenuPermissionsForRole($(this).val()));
            });

            // Default menu permissions on Edit modal role change
            $('#edit_role').on('change', function () {
                // When role changes, reset to role defaults (admin can fine-tune afterward)
                applyMenuCheckboxes('edit', defaultMenuPermissionsForRole($(this).val()));
            });

            // Initialize Add modal defaults once on load
            $(document).ready(function () {
                // Keep group/submenu checkboxes in sync
                bindGroupToggle('add', 'assets', [
                    'assets_data',
                    'assets_jababeka',
                    'assets_karawang',
                    'assets_in',
                    'assets_transfer',
                ]);
                bindGroupToggle('add', 'uniforms', [
                    'uniforms_master',
                    'uniforms_stock',
                    'uniforms_distribution',
                    'uniforms_history',
                ]);

                bindGroupToggle('edit', 'assets', [
                    'assets_data',
                    'assets_jababeka',
                    'assets_karawang',
                    'assets_in',
                    'assets_transfer',
                ]);
                bindGroupToggle('edit', 'uniforms', [
                    'uniforms_master',
                    'uniforms_stock',
                    'uniforms_distribution',
                    'uniforms_history',
                ]);

                applyMenuCheckboxes('add', defaultMenuPermissionsForRole($('#role').val()));
            });

            // SweetAlert2 for delete confirmation
            $(document).on('click', '.btn-delete-user', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Cancel'
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
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            }
            // Toggle password visibility for Edit User Modal
            function toggleEditPassword() {
                const passwordInput = document.getElementById('edit_password');
                const icon = document.getElementById('toggleEditPasswordIcon');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            }
        </script>
@endsection