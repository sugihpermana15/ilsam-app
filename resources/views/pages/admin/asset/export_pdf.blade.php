<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Assets Export</title>
  <style>
    @page { size: A4 landscape; margin: 10mm; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9px; line-height: 1.35; color: #111; }
    .header { width: 100%; margin: 0 0 10px 0; }
    .header td { vertical-align: middle; }
    .logo { width: 70px; }
    h1 { font-size: 16px; margin: 0; }
    .meta { margin: 2px 0 0 0; color: #444; }
    .desc { margin: 8px 0 10px 0; color: #333; }
    .filters { margin: 0 0 12px 0; color: #333; }
    .filters span { display: inline-block; margin-right: 12px; }
    table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    th, td { border: 0.5pt solid #cfcfcf; padding: 5px 4px; line-height: 1.35; vertical-align: top; word-wrap: break-word; overflow-wrap: anywhere; }
    th { background: #f3f4f6; font-weight: 700; }
    .muted { color: #666; }
    .nowrap { white-space: nowrap; }
    .cell-code { word-break: break-all; }
    .cell-pic { white-space: normal; }
    .cell-price { white-space: normal; word-break: break-all; overflow-wrap: anywhere; }
    .cell-updated { white-space: normal; }
    .cell-desc { word-break: break-word; }
  </style>
</head>
<body>
  @php
    $filters = $filters ?? [];
    $selectedCount = is_array($filters['selected_ids'] ?? null) ? count($filters['selected_ids']) : 0;
    $scope = (string) ($filters['scope'] ?? 'all');
    $scopeLabel = $scope === 'page' ? 'Halaman ini saja' : 'Semua hasil (sesuai filter)';
  @endphp

  <table class="header" cellspacing="0" cellpadding="0">
    <tr>
      <td class="logo">
        <img src="file://{{ public_path('assets/img/logo.png') }}" style="width: 64px; height: auto;" alt="IGI">
      </td>
      <td>
        <h1>Assets Export</h1>
        <div class="meta">
          <span class="muted">Generated:</span> <span class="nowrap">{{ $generatedAt ?? '-' }}</span>
          <span class="muted" style="margin-left: 12px;">Total:</span> {{ isset($assets) ? count($assets) : 0 }}
        </div>
      </td>
      <td style="text-align: right;" class="nowrap">
        <span class="muted">Scope:</span> {{ $scopeLabel }}
      </td>
    </tr>
  </table>

  <div class="desc">
    <span class="muted">Keterangan:</span>
    PDF berisi data assets berdasarkan pencarian/filter. Jika ada selection, hanya item yang dipilih yang diexport.
  </div>

  <div class="filters">
    @if($selectedCount > 0)
      <span><strong>Selected:</strong> {{ $selectedCount }} item(s)</span>
    @else
      @if(($filters['location'] ?? '') !== '')
        <span><strong>Location:</strong> {{ $filters['location'] }}</span>
      @endif
      @if(($filters['f_location'] ?? '') !== '')
        <span><strong>Location:</strong> {{ $filters['f_location'] }}</span>
      @endif
      @if(($filters['f_category'] ?? '') !== '')
        <span><strong>Category:</strong> {{ $filters['f_category'] }}</span>
      @endif
      @if(($filters['f_status'] ?? '') !== '')
        <span><strong>Status:</strong> {{ $filters['f_status'] }}</span>
      @endif
      @if(($filters['q'] ?? '') !== '')
        <span><strong>Search:</strong> {{ $filters['q'] }}</span>
      @endif
    @endif
  </div>

  <table>
    <thead>
      <tr>
        <th class="nowrap" style="width: 3%">No</th>
        <th style="width: 13%">Code</th>
        <th style="width: 16%">Name</th>
        <th style="width: 13%">Serial</th>
        <th class="nowrap" style="width: 6%">Category</th>
        <th class="nowrap" style="width: 6%">Location</th>
        <th style="width: 11%">PIC</th>
        <th class="nowrap" style="width: 7%">Purchase</th>
        <th style="width: 8%">Price</th>
        <th class="nowrap" style="width: 6%">Condition</th>
        <th class="nowrap" style="width: 6%">Ownership</th>
        <th class="nowrap" style="width: 6%">Status</th>
        <th style="width: 12%">Description</th>
        <th style="width: 8%">Updated</th>
      </tr>
    </thead>
    <tbody>
      @forelse(($assets ?? collect()) as $i => $a)
        <tr>
          <td class="nowrap">{{ $i + 1 }}</td>
          <td class="cell-code">{{ $a['asset_code'] !== '' ? $a['asset_code'] : '-' }}</td>
          <td>{{ $a['asset_name'] !== '' ? $a['asset_name'] : '-' }}</td>
          <td>{{ $a['serial_number'] !== '' ? $a['serial_number'] : '-' }}</td>
          <td class="nowrap">{{ $a['asset_category'] !== '' ? $a['asset_category'] : '-' }}</td>
          <td class="nowrap">{{ $a['asset_location'] !== '' ? $a['asset_location'] : '-' }}</td>
          <td class="cell-pic">{{ $a['person_in_charge'] !== '' ? $a['person_in_charge'] : '-' }}</td>
          <td class="nowrap">{{ $a['purchase_date'] ?? '-' }}</td>
          <td class="cell-price">{{ $a['price'] ?? '-' }}</td>
          <td class="nowrap">{{ $a['asset_condition'] !== '' ? $a['asset_condition'] : '-' }}</td>
          <td class="nowrap">{{ $a['ownership_status'] !== '' ? $a['ownership_status'] : '-' }}</td>
          <td class="nowrap">{{ $a['asset_status'] !== '' ? $a['asset_status'] : '-' }}</td>
          <td class="cell-desc">{{ $a['description'] !== '' ? $a['description'] : '-' }}</td>
          <td class="cell-updated">{{ $a['last_updated'] ?? '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="14" class="muted">Tidak ada data.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
