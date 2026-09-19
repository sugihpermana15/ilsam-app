<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Stok</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222
        }

        h2 {
            margin: 0 0 5px
        }

        p {
            margin: 3px 0 14px
        }

        .meta {
            width: 100%;
            margin-bottom: 12px
        }

        .meta td {
            padding: 3px
        }

        .report {
            width: 100%;
            border-collapse: collapse
        }

        .report th,
        .report td {
            border: 1px solid #555;
            padding: 6px
        }

        .report th {
            background: #e9ecef;
            text-align: center
        }

        .num {
            text-align: right
        }

        .low {
            background: #fff3cd
        }

        .signature {
            margin-top: 35px;
            width: 100%
        }

        .signature td {
            text-align: center;
            width: 50%;
            padding-top: 35px
        }
    </style>
</head>

<body>
    <h2>LAPORAN STOK {{ strtoupper($category) }}</h2>
    <p>Site: <strong>{{ $siteLabel }}</strong> | Dibuat: {{ $generatedAt }}</p>
    <table class="report">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Harga Satuan</th>
                <th>Stok Saat Ini</th>
                <th>Batas Minimum</th>
                <th>Usulan Penambahan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $index => $item)
                <tr class="{{ $item->report_stock <= $item->low_stock_threshold ? 'low' : '' }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->unit }}</td>
                    <td class="num">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="num">{{ $item->report_stock }} {{ $item->unit }}</td>
                    <td class="num">{{ $item->low_stock_threshold }} {{ $item->unit }}</td>
                    <td class="num">{{ $item->suggested_qty }} {{ $item->unit }}</td>
                    <td>{{ $item->report_stock <= $item->low_stock_threshold ? 'PERLU PENAMBAHAN' : 'CUKUP' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <table class="signature">
        <tr>
            <td>Disiapkan oleh,<br><br><br>____________________</td>
            <td>Disetujui oleh Management,<br><br><br>____________________</td>
        </tr>
    </table>
</body>

</html>
