@extends('layouts.master')

@section('title', 'Ilsam - History Delete User')
@section('title-sub', 'Settings & UI')
@section('pagetitle', 'History Delete User')
@section('css')
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
@endsection
@section('content')
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
                        <h5 class="card-title mb-0"> History Delete User </h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="mb-3">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search deleted users..."
                                    value="{{ $search ?? '' }}">
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </form>
                        <div class="table-responsive" style="overflow-x:auto;">
                            <table id="alternative-pagination"
                                class="table table-nowrap table-striped table-bordered w-100">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Deleted At</th>
                                        <th>Deleted By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deletedUsers as $user)
                                        <tr>
                                            <td>{{ $user->user_id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->username }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @php
                                                    $roleName = '-';
                                                    if (isset($user->role_id)) {
                                                        $role = DB::table('roles')->where('id', $user->role_id)->first();
                                                        $roleName = $role ? $role->role_name : '-';
                                                    }
                                                   @endphp
                                                {{ $roleName }}
                                            </td>
                                            <td>{{ $user->deleted_at }}</td>
                                            <td>
                                                @php
                                                    $deletedBy = $user->deleted_by ? DB::table('users')->where('id', $user->deleted_by)->first() : null;
                                                @endphp
                                                {{ $deletedBy ? $deletedBy->name : '-' }}
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.users.restore', $user->user_id) }}" method="POST"
                                                    style="display:inline-block" class="form-restore-user">
                                                    @csrf
                                                    <button type="button"
                                                        class="btn btn-sm btn-success btn-restore-user">Restore</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $deletedUsers->links() }}
                        </div>
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
        $(document).ready(function () {
            if ($.fn.DataTable.isDataTable('#alternative-pagination')) {
                $('#alternative-pagination').DataTable().destroy();
            }
            $('#alternative-pagination').DataTable({
                responsive: true,
                scrollX: true,
                paging: false,
                searching: false,
                info: false
            });

            // SweetAlert2 for restore confirmation
            $(document).on('click', '.btn-restore-user', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Restore this user?",
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
        });
    </script>
@endsection