@extends('layouts.recruitment_public')

@section('title', 'Rekrutmen - ' . $form->position_name)

@section('recruitment_heading')
    REKRUTMEN {{ mb_strtoupper($form->position_name) }}
@endsection

@section('content')
    <div class="mb-3">
        <div class="fw-semibold">{{ $form->title }}</div>
        <div class="text-muted small">Silakan isi data berikut dengan benar.</div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('recruitment.form.submit', ['token' => $form->public_token]) }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="is_security_position" value="{{ $form->is_security_position ? '1' : '0' }}">

        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="full_name" value="{{ old('full_name') }}" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Nomor HP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="phone" value="{{ old('phone') }}" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Posisi yang Dilamar <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="position_applied" value="{{ $form->position_name }}" readonly>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Tinggi Badan (cm) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="height_cm" value="{{ old('height_cm') }}" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Berat Badan (kg) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="weight_kg" value="{{ old('weight_kg') }}" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Alamat sesuai KTP <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="address_ktp" rows="3" required>{{ old('address_ktp') }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Alamat Domisili <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="address_domicile" rows="3" required>{{ old('address_domicile') }}</textarea>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Pendidikan Terakhir (opsional)</label>
                                <input type="text" class="form-control" name="last_education" value="{{ old('last_education') }}">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Pengalaman Kerja (opsional)</label>
                                <textarea class="form-control" name="work_experience" rows="2">{{ old('work_experience') }}</textarea>
                            </div>

                            <div class="col-12">
                                <hr class="my-2" />
                                <div class="fw-semibold mb-2">Upload Dokumen</div>

                                @if($form->is_security_position)
                                    <div class="mb-3">
                                        <label class="form-label">Sertifikat Garda Pratama <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" name="security_garda_pratama" required>
                                        <div class="form-text">PDF/JPG/PNG, max 4MB.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">KTA Security <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" name="security_kta" required>
                                        <div class="form-text">PDF/JPG/PNG, max 4MB.</div>
                                    </div>
                                @else
                                    <div class="mb-3">
                                        <label class="form-label">CV <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" name="cv" required>
                                        <div class="form-text">PDF/JPG/PNG, max 4MB.</div>
                                    </div>
                                @endif
                            </div>
                        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary">Lanjut Tes</button>
        </div>
    </form>
@endsection
