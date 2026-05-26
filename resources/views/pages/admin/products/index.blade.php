@extends('layouts.master')

@section('title', 'Colorants Management | IGI')
@section('title-sub', 'Colorants')
@section('pagetitle', 'Colorants Management')

@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Colorants List</h5>
                <a href="{{ route('admin.colorants.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Tambah Colorant
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <table id="products-table" class="table table-bordered table-hover align-middle w-100 mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Gambar 1</th>
                            <th>Gambar 2</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Color</th>
                            <th>Category</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($colorants as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>@if($item->image1)<img src="{{ asset('storage/'.$item->image1) }}" width="60" class="rounded">@else <span class="text-muted small">No Image</span> @endif</td>
                            <td>@if($item->image2)<img src="{{ asset('storage/'.$item->image2) }}" width="60" class="rounded">@else <span class="text-muted small">No Image</span> @endif</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->type }}</td>
                            <td>{{ $item->color }}</td>
                            <td><span class="badge bg-primary">{{ $item->category }}</span></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.colorants.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('admin.colorants.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#products-table').DataTable({
                responsive: true,
                pagingType: "full_numbers",
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                dom:
                    "<'row align-items-center'<'col-sm-12 col-md-6 mb-3'l><'col-sm-12 col-md-6 mb-3'f>>" +
                    "<'table-responsive'tr>" +
                    "<'row align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                language: {
                    search: "Search : ",
                    searchPlaceholder: "Type to filter...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Showing _START_ to _END_ of _TOTAL_ records",
                    infoEmpty: "Showing 0 to 0 of 0 records",
                    zeroRecords: "Data tidak ditemukan"
                }
            });
        });
    </script>
@endsection
