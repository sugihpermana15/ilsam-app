<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Ledger Materai</title>
    <style>
        @page { margin: 22px 24px 42px 24px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        .company { width: 100%; border-bottom: 2px solid #111; padding-bottom: 10px; margin-bottom: 12px; }
        .company td { vertical-align: top; }
        .logo { width: 110px; }
        .logo img { max-width: 100px; max-height: 60px; }
        .company-name { font-size: 14px; font-weight: 700; margin: 0 0 2px 0; }
        .company-address { font-size: 10px; color: #222; line-height: 1.25; margin: 0; }
        .report-title { font-size: 15px; font-weight: 800; margin: 0; }
        .meta { font-size: 10px; color: #333; margin-top: 3px; line-height: 1.3; }
        .meta strong { color: #111; }
        table.data { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.data th, table.data td { border: 1px solid #999; padding: 7px 6px; vertical-align: top; }
        table.data th { background: #f3f4f6; font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 0.2px; }
        table.data td {
            font-size: 10.5px;
            line-height: 1.45;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
        }
        table.data tr:nth-child(even) td { background: #fafafa; }
        .nowrap { white-space: nowrap; }
        .muted { color: #666; }
        .wrap { white-space: normal; word-break: break-word; overflow-wrap: anywhere; }
        .num { text-align: right; }
        .badge-in { color: #065f46; font-weight: 700; }
        .badge-out { color: #991b1b; font-weight: 700; }
        .cell-title { font-weight: 700; }
        .cell-sub { font-size: 9.5px; margin-top: 2px; }
    </style>
</head>
<body>
    @php
        $companyName = 'PT ILSAM GLOBAL INDONESIA';
        $companyAddress = 'Jl. Trans Heksa Artha Industrial Hill Area Block E No.13 Wanajaya Village,  District Telukjambe Barat, Karawang Regency, West Java, 41361';
        $companyPhone = '+62 21 89830313';
        $companyPhoneAlt = '+62 21 89830314';
        $companyEmail = 'market.ilsamindonesia@yahoo.com';

        $logoSrc = null;
        try {
            $logoPathMinPng = public_path('assets/img/logo-min.png');
            $logoPathPng = public_path('assets/img/logo.png');

            $picked = null;
            if (is_file($logoPathMinPng)) {
                $picked = $logoPathMinPng;
            } elseif (is_file($logoPathPng)) {
                $picked = $logoPathPng;
            }

            if ($picked) {
                $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents($picked));
            }
        } catch (Throwable $e) {
            $logoSrc = null;
        }

        $selectedStampName = optional($stamps->firstWhere('id', $filter['stamp_id']))?->name ?? 'Semua';
        $periodLabel = (!empty($filter['date_from']) || !empty($filter['date_to']))
            ? (($filter['date_from'] ?? '-') . ' s/d ' . ($filter['date_to'] ?? '-'))
            : '-';
        $generatedAt = now()->format('Y-m-d H:i');
    @endphp

    <table class="company" cellspacing="0" cellpadding="0">
        <tr>
            <td class="logo">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" alt="Logo">
                @endif
            </td>
            <td>
                <p class="company-name">{{ $companyName }}</p>
                <p class="company-address">{{ $companyAddress }}</p>
                <p class="company-address">Telp: {{ $companyPhone }} / {{ $companyPhoneAlt }} &nbsp; | &nbsp; Email: {{ $companyEmail }}</p>
            </td>
            <td style="width: 320px; text-align:right;">
                <p class="report-title">Laporan Ledger Materai</p>
                <div class="meta"><strong>Materai</strong>: {{ $selectedStampName }}</div>
                <div class="meta"><strong>Periode</strong>: {{ $periodLabel }}</div>
                <div class="meta"><strong>Dibuat</strong>: {{ $generatedAt }}</div>
                <div class="meta"><strong>User</strong>: {{ auth()->user()->name ?? '-' }}</div>
                <div class="meta">
                    <strong>IN</strong>: {{ number_format((int) ($totals['in'] ?? 0), 0, ',', '.') }}
                    &nbsp; | &nbsp; <strong>OUT</strong>: {{ number_format((int) ($totals['out'] ?? 0), 0, ',', '.') }}
                    &nbsp; | &nbsp; <strong>Baris</strong>: {{ is_countable($transactions) ? count($transactions) : 0 }}
                </div>
                @if (!is_null($balances['opening']) && !is_null($balances['closing']))
                    <div class="meta"><strong>Opening</strong>: {{ number_format((int) $balances['opening'], 0, ',', '.') }} &nbsp; | &nbsp; <strong>Closing</strong>: {{ number_format((int) $balances['closing'], 0, ',', '.') }}</div>
                @endif
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 85px;">Tanggal</th>
                <th style="width: 130px;">No. Trx</th>
                <th style="width: 210px;">Materai</th>
                <th style="width: 45px;">Tipe</th>
                <th style="width: 60px;" class="num">Qty</th>
                <th style="width: 70px;" class="num">Saldo</th>
                <th style="width: 160px;">PIC</th>
                <th>Catatan</th>
                <th style="width: 140px;">Dibuat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $trx)
                <tr>
                    <td class="nowrap">{{ optional($trx->trx_date)->format('Y-m-d') }}</td>
                    <td class="nowrap">{{ $trx->trx_no }}</td>
                    <td class="wrap">
                        <div class="cell-title">{{ $trx->stamp?->name }}</div>
                        <div class="cell-sub muted">{{ $trx->stamp?->code }} • Rp {{ number_format((int) ($trx->stamp?->face_value ?? 0), 0, ',', '.') }}</div>
                    </td>
                    <td class="nowrap {{ $trx->trx_type === 'IN' ? 'badge-in' : 'badge-out' }}">{{ $trx->trx_type }}</td>
                    <td class="num nowrap">{{ number_format((int) $trx->qty, 0, ',', '.') }}</td>
                    <td class="num nowrap">{{ number_format((int) $trx->on_hand_after, 0, ',', '.') }}</td>
                    <td class="wrap">{{ $trx->pic?->name ?? '-' }}</td>
                    <td class="wrap">{{ $trx->notes ?? '-' }}</td>
                    <td class="wrap">{{ $trx->creator?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="muted" style="text-align: center;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script type="text/php">
        if (isset($pdf)) {
            $w = $pdf->get_width();
            $h = $pdf->get_height();
            $font = $fontMetrics->get_font('DejaVu Sans', 'normal');
            // Footer line
            $pdf->line(24, $h - 30, $w - 24, $h - 30, [0.75, 0.75, 0.75], 1);
            // Left footer text
            $pdf->page_text(24, $h - 22, 'Dokumen ini dihasilkan otomatis dari sistem.', $font, 9, [0.25,0.25,0.25]);
            // Right footer page numbering
            $text = 'Halaman {PAGE_NUM} / {PAGE_COUNT}';
            $textWidth = $fontMetrics->getTextWidth($text, $font, 9);
            $pdf->page_text($w - 24 - $textWidth, $h - 22, $text, $font, 9, [0.25,0.25,0.25]);
        }
    </script>
</body>
</html>
