@extends('layouts.master')

@section('title', 'Tambah Colorant | IGI')
@section('title-sub', 'Colorants')
@section('pagetitle', 'Tambah Colorant')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Tambah Colorant</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Terdapat Kesalahan!</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <form action="{{ route('admin.colorants.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <input type="text" name="type" class="form-control" value="{{ old('type') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Color</label>
                            <input type="text" name="color" class="form-control" value="{{ old('color') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="COLORANTS">COLORANTS</option>
                                <option value="SURFACE COATING AGENTS">SURFACE COATING AGENTS</option>
                                <option value="ADDITIVE COATING">ADDITIVE COATING</option>
                                <option value="PU RESIN">PU RESIN</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Background Color</label>
                            <input type="color" name="bg_color" class="form-control form-control-color w-100" value="#1e3a8a" title="Pilih warna background badge & bar">
                        </div>
                        <div class="col-md-6 d-none d-md-block"></div>
                        <div class="col-md-6">
                            <label class="form-label">Gambar 1</label>
                            <input type="file" name="image1" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gambar 2</label>
                            <input type="file" name="image2" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12 mt-4">
                            <a href="{{ route('admin.colorants.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
