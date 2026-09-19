<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Stok {{ strtoupper($category) }} - {{ $siteLabel }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #111
        }

        h2 {
            margin-bottom: 4px
        }

        .report {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px
        }

        .report th,
        .report td {
            border: 1px solid #555;
            padding: 7px
        }

        .report th {
            background: #eee
        }

        .num {
            text-align: right
        }

        .low {
            background: #fff3cd
        }

        .signature {
            margin-top: 60px;
            width: 100%
        }

        .signature td {
            text-align: center;
            width: 50%;
            padding-top: 45px
        }

        @media print {
            .no-print {
                display: none
            }

            body {
                margin: 15mm
            }
        }
    </style>
</head>

<body><button class="no-print" onclick="window.print()">Print</button>
    <h2>LAPORAN STOK {{ strtoupper($category) }}</h2>
    <div>Site: <strong>{{ $siteLabel }}</strong> | Dibuat: {{ $generatedAt }}</div>
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
            <td>Disiapkan oleh,<br><br>____________________</td>
            <td>Disetujui oleh Management,<br><br>____________________</td>
        </tr>
    </table>
</body>

</html>
