<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Laporan Daily Tasks</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
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
    .box { border: 1px solid #bbb; padding: 8px 10px; margin-bottom: 10px; }
    table.data { width: 100%; border-collapse: collapse; }
    table.data th, table.data td { border: 1px solid #999; padding: 6px 6px; vertical-align: top; }
    table.data th { background: #f3f4f6; font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 0.2px; }
    table.data td { font-size: 10.5px; }
    table.data tr:nth-child(even) td { background: #fafafa; }
    .nowrap { white-space: nowrap; }
    .muted { color: #666; }
    .small { font-size: 10px; }
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
      // Most reliable for dompdf: embed PNG as data URI.
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
      <td style="width: 260px; text-align:right;">
        <p class="report-title">Laporan Daily Tasks</p>
        <div class="meta"><strong>Periode</strong>: {{ $periodLabel }}</div>
        <div class="meta"><strong>Dibuat</strong>: {{ $generatedAt }}</div>
        <div class="meta"><strong>Total</strong>: {{ count($tasks ?? []) }}</div>
      </td>
    </tr>
  </table>

  <table class="data">
    <thead>
      <tr>
        <th class="nowrap">ID</th>
        <th class="nowrap">Tipe</th>
        <th>Judul</th>
        <th class="nowrap">Mulai</th>
        <th class="nowrap">Selesai</th>
        <th class="nowrap">Status</th>
        <th class="nowrap">Prioritas</th>
        <th class="nowrap">Ditugaskan</th>
        <th class="nowrap">Dibuat Oleh</th>
        <th class="nowrap">Dibuat</th>
        <th class="nowrap">Diubah</th>
      </tr>
    </thead>
    <tbody>
      @forelse(($tasks ?? []) as $t)
        <tr>
          <td class="nowrap">{{ $t['id'] }}</td>
          <td class="nowrap">{{ $t['task_type'] }}</td>
          <td>{{ $t['title'] }}</td>
          <td class="nowrap">{{ $t['due_start'] }}</td>
          <td class="nowrap">{{ $t['due_end'] }}</td>
          <td class="nowrap">{{ $t['status'] }}</td>
          <td class="nowrap">{{ $t['priority'] }}</td>
          <td class="nowrap">{{ $t['assigned_to'] }}</td>
          <td class="nowrap">{{ $t['created_by'] }}</td>
          <td class="nowrap">{{ $t['created_at'] }}</td>
          <td class="nowrap">{{ $t['updated_at'] }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="11" style="text-align:center; padding: 16px;">Tidak ada data.</td>
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
