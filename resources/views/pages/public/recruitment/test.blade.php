@extends('layouts.recruitment_public')

@section('title', 'Tes Pengetahuan - ' . ($form?->position_name ?? 'Rekrutmen'))

@section('recruitment_heading')
    REKRUTMEN {{ mb_strtoupper($form?->position_name ?? 'KANDIDAT') }}
@endsection

@section('content')
    <div class="mb-3">
        <div class="fw-semibold">Tes Pengetahuan - {{ $form?->title }}</div>
        <div class="text-muted small">Kode Kandidat: <span class="fw-semibold">{{ $submission->candidate_code }}</span></div>
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

    <form method="POST" action="{{ route('recruitment.test.submit', ['submissionToken' => $submission->public_token]) }}">
        @csrf

        <div class="row g-3">
                            @forelse($questions as $i => $q)
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <div class="fw-semibold mb-2">
                                            {{ $loop->iteration }}. {!! nl2br(e($q->question_text)) !!}
                                            @if($q->is_required)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </div>

                                        <input type="hidden" name="answers[{{ $i }}][question_id]" value="{{ $q->id }}">

                                        @if($q->type === 'multiple_choice')
                                            @foreach($q->options->sortBy('sort_order') as $opt)
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input" type="radio" name="answers[{{ $i }}][option_id]" id="q{{ $q->id }}_opt{{ $opt->id }}" value="{{ $opt->id }}" @checked(old('answers.' . $i . '.option_id') == $opt->id) {{ $q->is_required ? 'required' : '' }}>
                                                    <label class="form-check-label" for="q{{ $q->id }}_opt{{ $opt->id }}">{{ $opt->option_text }}</label>
                                                </div>
                                            @endforeach
                                        @elseif($q->type === 'short_text')
                                            <input type="text" class="form-control" name="answers[{{ $i }}][answer_text]" value="{{ old('answers.' . $i . '.answer_text') }}" {{ $q->is_required ? 'required' : '' }}>
                                        @else
                                            <textarea class="form-control" rows="4" name="answers[{{ $i }}][answer_text]" {{ $q->is_required ? 'required' : '' }}>{{ old('answers.' . $i . '.answer_text') }}</textarea>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-warning mb-0">Belum ada pertanyaan untuk form ini.</div>
                                </div>
                            @endforelse
                        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary">Kirim Jawaban</button>
        </div>
    </form>
@endsection
