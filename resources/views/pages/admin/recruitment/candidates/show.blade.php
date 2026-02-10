@extends('layouts.master')

@section('title', 'Rekrutmen - Detail Kandidat')

@section('title-sub', 'Recruitment')
@section('pagetitle', 'Detail Kandidat')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                    <div>
                        <h5 class="card-title mb-0">{{ $submission->full_name }}</h5>
                        <div class="text-muted small">Kode: <span class="fw-semibold">{{ $submission->candidate_code }}</span></div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.recruitment.candidates.index') }}">Kembali</a>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.recruitment.forms.show', $submission->recruitment_form_id) }}">Detail Form</a>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.recruitment.candidates.export.pdf', $submission->id) }}">Download PDF</a>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.recruitment.candidates.export.excel', $submission->id) }}">Download Excel</a>
                    </div>
                </div>

                <div class="card-body">
                    @php
                        $totalPoints = (int) (($submission->form?->questions?->sum('points')) ?? 0);
                        $earnedPoints = (int) ($submission->answers->sum(function ($a) {
                            return (int) ($a->points_earned ?? 0);
                        }));
                    @endphp
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="fw-semibold mb-2">Data Diri</div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <tbody>
                                        <tr><th style="width: 35%">Email</th><td>{{ $submission->email }}</td></tr>
                                        <tr><th>No HP</th><td>{{ $submission->phone }}</td></tr>
                                        <tr><th>Posisi</th><td>{{ $submission->position_applied }}</td></tr>
                                        <tr><th>Status</th><td>{{ $submission->status_label }}</td></tr>
                                        <tr><th>Nilai Tes</th><td>{{ $totalPoints ? ($earnedPoints . ' / ' . $totalPoints) : '-' }}</td></tr>
                                        <tr><th>Tinggi (cm)</th><td>{{ $submission->height_cm }}</td></tr>
                                        <tr><th>Berat (kg)</th><td>{{ $submission->weight_kg }}</td></tr>
                                        <tr><th>Pendidikan</th><td>{{ $submission->last_education ?: '-' }}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="fw-semibold mb-2">Alamat</div>
                            <div class="mb-2">
                                <div class="text-muted small">Alamat sesuai KTP</div>
                                <div class="border rounded p-2">{!! nl2br(e($submission->address_ktp)) !!}</div>
                            </div>
                            <div>
                                <div class="text-muted small">Alamat Domisili</div>
                                <div class="border rounded p-2">{!! nl2br(e($submission->address_domicile)) !!}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="fw-semibold mb-2">Pengalaman Kerja</div>
                            <div class="border rounded p-2">{!! nl2br(e($submission->work_experience ?: '-')) !!}</div>
                        </div>
                    </div>

                    <hr class="my-4" />

                    <div class="fw-semibold mb-2">Dokumen</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Jenis</th>
                                    <th>Nama File</th>
                                    <th>Ukuran</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($submission->files as $f)
                                    <tr>
                                        <td>{{ $f->field_label }}</td>
                                        <td>{{ $f->original_name }}</td>
                                        <td class="text-end">{{ $f->size ? number_format($f->size / 1024, 0) . ' KB' : '-' }}</td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.recruitment.candidates.files.download', $f->id) }}">Download</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted">Tidak ada dokumen.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-4" />

                    <div class="fw-semibold mb-2">Jawaban Tes</div>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.recruitment.candidates.scores.update', $submission->id) }}">
                        @csrf
                        @method('PUT')
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 45%">Pertanyaan</th>
                                    <th>Jawaban</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $answersByQ = $submission->answers->keyBy('recruitment_form_question_id');
                                    $questions = $submission->form?->questions?->sortBy('sort_order') ?? collect();
                                @endphp
                                @forelse($questions as $q)
                                    @php
                                        $ans = $answersByQ->get($q->id);
                                        $val = '-';
                                        if ($ans) {
                                            if ($ans->recruitment_form_question_option_id) {
                                                $val = $ans->selectedOption?->option_text ?? '-';
                                            } else {
                                                $val = $ans->answer_text ?: '-';
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td>{!! nl2br(e($q->question_text)) !!}</td>
                                        <td>
                                            <div>{!! nl2br(e($val)) !!}</div>

                                            @if(!$ans)
                                                <span class="badge bg-secondary-subtle text-secondary">BELUM DIJAWAB</span>
                                            @elseif($q->type === 'multiple_choice')
                                                @if($ans->is_correct === true)
                                                    <span class="badge bg-success-subtle text-success">BENAR</span>
                                                @elseif($ans->is_correct === false)
                                                    <span class="badge bg-danger-subtle text-danger">SALAH</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">BELUM DINILAI</span>
                                                @endif
                                            @else
                                                <div class="d-flex align-items-center gap-2 flex-wrap mt-2">
                                                    <span class="badge bg-secondary-subtle text-secondary">BELUM DINILAI</span>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="text-muted small">Nilai</span>
                                                        <input
                                                            type="number"
                                                            class="form-control form-control-sm"
                                                            style="width: 110px"
                                                            name="scores[{{ $q->id }}]"
                                                            min="0"
                                                            max="{{ (int) ($q->points ?? 0) }}"
                                                            value="{{ old('scores.' . $q->id, (int) ($ans->points_earned ?? 0)) }}"
                                                        >
                                                        <span class="text-muted small">/ {{ (int) ($q->points ?? 0) }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-muted">Belum ada pertanyaan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-sm btn-primary">Simpan Nilai</button>
                    </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
