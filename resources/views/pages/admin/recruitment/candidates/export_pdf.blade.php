<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h1 { font-size: 16px; margin: 0 0 8px 0; }
        h2 { font-size: 13px; margin: 14px 0 6px 0; }
        .muted { color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; vertical-align: top; }
        th { background: #f4f4f4; text-align: left; }
        .kv th { width: 28%; }
    </style>
</head>

<body>
    <h1>Data Kandidat - {{ $submission->full_name }}</h1>
    <div class="muted">Kode: {{ $submission->candidate_code }} | Posisi: {{ $submission->position_applied }} | Status: {{ $submission->status_label }}</div>

    <h2>Data Diri</h2>
    @php
        $totalPoints = (int) (($submission->form?->questions?->sum('points')) ?? 0);
        $earnedPoints = (int) ($submission->answers->sum(function ($a) {
            return (int) ($a->points_earned ?? 0);
        }));
    @endphp
    <table class="kv">
        <tr><th>Email</th><td>{{ $submission->email }}</td></tr>
        <tr><th>No HP</th><td>{{ $submission->phone }}</td></tr>
        <tr><th>Tinggi / Berat</th><td>{{ $submission->height_cm }} cm / {{ $submission->weight_kg }} kg</td></tr>
        <tr><th>Nilai Tes</th><td>{{ $totalPoints ? ($earnedPoints . ' / ' . $totalPoints) : '-' }}</td></tr>
        <tr><th>Pendidikan</th><td>{{ $submission->last_education ?: '-' }}</td></tr>
    </table>

    <h2>Alamat</h2>
    <table>
        <tr>
            <th>Alamat sesuai KTP</th>
            <th>Alamat Domisili</th>
        </tr>
        <tr>
            <td>{!! nl2br(e($submission->address_ktp)) !!}</td>
            <td>{!! nl2br(e($submission->address_domicile)) !!}</td>
        </tr>
    </table>

    <h2>Pengalaman Kerja</h2>
    <div>{!! nl2br(e($submission->work_experience ?: '-')) !!}</div>

    <h2>Dokumen</h2>
    <table>
        <tr>
            <th>Jenis</th>
            <th>Nama File</th>
        </tr>
        @forelse($submission->files as $f)
            <tr>
                <td>{{ $f->field_label }}</td>
                <td>{{ $f->original_name }}</td>
            </tr>
        @empty
            <tr><td colspan="2" class="muted">Tidak ada dokumen.</td></tr>
        @endforelse
    </table>

    <h2>Jawaban Tes</h2>
    @php
        $answersByQ = $submission->answers->keyBy('recruitment_form_question_id');
        $questions = $submission->form?->questions?->sortBy('sort_order') ?? collect();
    @endphp
    <table>
        <tr>
            <th style="width: 45%">Pertanyaan</th>
            <th>Jawaban</th>
        </tr>
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
            <tr><td colspan="2" class="muted">Belum ada pertanyaan.</td></tr>
        @endforelse
    </table>
</body>

</html>
