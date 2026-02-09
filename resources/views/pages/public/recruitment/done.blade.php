@extends('layouts.recruitment_public')

@section('title', 'Rekrutmen - Selesai')

@section('recruitment_heading')
    REKRUTMEN {{ mb_strtoupper($submission->form?->position_name ?? 'KANDIDAT') }}
@endsection

@section('content')
    <div class="fw-semibold mb-2">Terima kasih</div>
    <div class="mb-2">Data kandidat dan tes pengetahuan sudah terkirim.</div>
    <div class="text-muted">Kode Kandidat: <span class="fw-semibold">{{ $submission->candidate_code }}</span></div>
@endsection
