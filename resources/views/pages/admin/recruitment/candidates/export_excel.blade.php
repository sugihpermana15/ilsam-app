<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
</head>

<body>
    <table>
        <tr>
            <th colspan="2">Data Kandidat</th>
        </tr>
        @php
            $totalPoints = (int) (($submission->form?->questions?->sum('points')) ?? 0);
            $earnedPoints = (int) ($submission->answers->sum(function ($a) {
                return (int) ($a->question?->points ?? 0);
            }));
        @endphp
        <tr><td>Nama</td><td>{{ $submission->full_name }}</td></tr>
        <tr><td>Kode Kandidat</td><td>{{ $submission->candidate_code }}</td></tr>
        <tr><td>Posisi</td><td>{{ $submission->position_applied }}</td></tr>
        <tr><td>Status</td><td>{{ $submission->status_label }}</td></tr>
        <tr><td>Nilai Tes</td><td>{{ $totalPoints ? ($earnedPoints . ' / ' . $totalPoints) : '-' }}</td></tr>
        <tr><td>Email</td><td>{{ $submission->email }}</td></tr>
        <tr><td>No HP</td><td>{{ $submission->phone }}</td></tr>
        <tr><td>Tinggi (cm)</td><td>{{ $submission->height_cm }}</td></tr>
        <tr><td>Berat (kg)</td><td>{{ $submission->weight_kg }}</td></tr>
        <tr><td>Pendidikan</td><td>{{ $submission->last_education ?: '-' }}</td></tr>
        <tr><td>Alamat sesuai KTP</td><td>{!! nl2br(e($submission->address_ktp)) !!}</td></tr>
        <tr><td>Alamat Domisili</td><td>{!! nl2br(e($submission->address_domicile)) !!}</td></tr>
        <tr><td>Pengalaman Kerja</td><td>{!! nl2br(e($submission->work_experience ?: '-')) !!}</td></tr>
    </table>

    <br>

    <table>
        <tr>
            <th colspan="2">Dokumen</th>
        </tr>
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
            <tr><td colspan="2">Tidak ada dokumen.</td></tr>
        @endforelse
    </table>

    <br>

    @php
        $answersByQ = $submission->answers->keyBy('recruitment_form_question_id');
        $questions = $submission->form?->questions?->sortBy('sort_order') ?? collect();
    @endphp

    <table>
        <tr>
            <th colspan="2">Jawaban Tes</th>
        </tr>
        <tr>
            <th>Pertanyaan</th>
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
            <tr><td colspan="2">Belum ada pertanyaan.</td></tr>
        @endforelse
    </table>
</body>

</html>
