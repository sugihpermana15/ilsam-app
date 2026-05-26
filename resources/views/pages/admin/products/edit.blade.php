@extends('layouts.master')

@section('title', 'Edit Colorant | IGI')
@section('title-sub', 'Colorants')
@section('pagetitle', 'Edit Colorant')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Colorant</h5>
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
                <form action="{{ route('admin.colorants.update', $colorant) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $colorant->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <input type="text" name="type" class="form-control" value="{{ old('type', $colorant->type) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Color</label>
                            <input type="text" name="color" class="form-control" value="{{ old('color', $colorant->color) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="COLORANTS" @if($colorant->category=='COLORANTS') selected @endif>COLORANTS</option>
                                <option value="SURFACE COATING AGENTS" @if($colorant->category=='SURFACE COATING AGENTS') selected @endif>SURFACE COATING AGENTS</option>
                                <option value="ADDITIVE COATING" @if($colorant->category=='ADDITIVE COATING') selected @endif>ADDITIVE COATING</option>
                                <option value="PU RESIN" @if($colorant->category=='PU RESIN') selected @endif>PU RESIN</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Background Color</label>
                            <input type="color" name="bg_color" class="form-control form-control-color w-100" value="{{ old('bg_color', $colorant->bg_color ?? '#1e3a8a') }}" title="Pilih warna background badge & bar">
                        </div>
                        <div class="col-md-6 d-none d-md-block"></div>
                        <div class="col-md-6">
                            <label class="form-label">Gambar 1</label><br>
                            @if($colorant->image1)
                                <img src="{{ asset('storage/'.$colorant->image1) }}" width="80" class="mb-2 rounded"><br>
                            @endif
                            <input type="file" name="image1" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gambar 2</label><br>
                            @if($colorant->image2)
                                <img src="{{ asset('storage/'.$colorant->image2) }}" width="80" class="mb-2 rounded"><br>
                            @endif
                            <input type="file" name="image2" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12 mt-4">
                            <a href="{{ route('admin.colorants.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
