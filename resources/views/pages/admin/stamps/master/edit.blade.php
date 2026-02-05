@extends('layouts.master')

@section('title', 'Ilsam - Edit Materai')

@section('title-sub', 'Application')
@section('pagetitle', 'Edit Materai')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-end mb-3">
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.stamps.master.index') }}">Kembali</a>
        </div>

        <form class="card" method="POST" action="{{ route('admin.stamps.master.update', $stamp) }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label">Kode</label>
                        <input name="code" class="form-control" value="{{ old('code', $stamp->code) }}" required>
                        @error('code')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-8">
                        <label class="form-label">Nama</label>
                        <input name="name" class="form-control" value="{{ old('name', $stamp->name) }}" required>
                        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Nominal</label>
                        <input type="number" min="1" name="face_value" class="form-control" value="{{ old('face_value', $stamp->face_value) }}" required>
                        @error('face_value')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Aktif</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $stamp->is_active))>
                            <label class="form-check-label" for="is_active">Materai aktif</label>
                        </div>
                        @error('is_active')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <a class="btn btn-light" href="{{ route('admin.stamps.master.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection
