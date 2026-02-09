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
                            return (int) ($a->question?->points ?? 0);
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
                                        <td>{!! nl2br(e($val)) !!}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-muted">Belum ada pertanyaan.</td>
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
