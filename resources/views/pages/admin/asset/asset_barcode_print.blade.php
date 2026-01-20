@php
  // Set page size for print (60x40mm = 6x4 cm)
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Print Barcode - Ilsam</title>
  <!-- App favicon -->
  <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    @media print {
      @page {
        size: 6cm 4cm;
        margin: 0;
      }

      body {
        margin: 0;
        padding: 0;
      }

      .label-container {
        width: 100%;
        height: 100%;
      }
    }

    body {
      width: 6cm;
      height: 4cm;
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      color: #000;
      background: white;
    }

    .label-container {
      width: 100%;
      height: 100%;
      border: 1px solid #000;
      padding: 3px;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
    }

    .logo-section {
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 8px;
      font-weight: bold;
      line-height: 1.1;
      padding-bottom: 2px;
      border-bottom: 1px solid #000;
      gap: 6px;
    }
    .logo-section img {
      height: 12px;
      display: inline-block;
      margin-right: 0;
    }

    .barcode-section {
      text-align: center;
      padding: 3px 0;
      border-bottom: 1px solid #000;
      flex-grow: 1;
      flex-direction: column;
      justify-content: center;
    }

    .barcode-img {
      max-width: 90%;
      width: auto;
      height: 60px;
      max-height: 100%;
      margin: 0 auto;
      display: block;
    }

    .asset-code {
      text-align: center;
      font-weight: bold;
      font-size: 8px;
      margin-top: 7px;
      line-height: 1;
    }

    .property {
      text-align: center;
      font-size: 6px;
      line-height: 1.2;
      padding: 1px 0;
      border-bottom: 1px solid #000;
    }

    .footer-section {
      padding-top: 2px;
      font-size: 6px;
    }

    .footer-row {
      display: flex;
      justify-content: space-between;
      line-height: 1.2;
      margin-bottom: 1px;
    }

    .footer-col {
      flex: 1;
      word-break: break-word;
    }

    .footer-label {
      font-weight: normal;
      display: inline;
    }

    .footer-value {
      display: inline;
    }
  </style>
</head>

<body>
  @if(isset($assets) && is_iterable($assets) && !empty($assets))
    @foreach($assets as $asset)
      <div class="label-container">
        <div class="logo-section">
          <img src="{{ asset('assets/img/logo-min.svg') }}" alt="Logo" />
          <span>PT ILSAM GLOBAL INDONESIA</span>
        </div>
        <div class="barcode-section">
          <img class="barcode-img" src="{{ route('admin.assets.barcode', $asset->asset_code) }}?v={{ uniqid() }}" alt="Barcode">
          <div style="border-top:1px solid #000; margin:5px 0 0 0;"></div>
          <div class="asset-code">{{ $asset->asset_code }}</div>
        </div>
        <div class="property">
          ASSET OF<br>PT ILSAM GLOBAL INDONESIA
        </div>
        <div class="footer-section">
          <div class="footer-row">
            <div class="footer-col">
              <span class="footer-label">Lokasi :</span> {{ $asset->asset_location }}
            </div>
            <div class="footer-col" style="text-align: right;">
              <span class="footer-label">Tgl Pembelian :</span>
              {{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('d-m-Y') : '-' }}
            </div>
          </div>
          <div class="footer-row">
            <div class="footer-col">
              <span class="footer-label">Serial Number :</span> {{ $asset->serial_number ?? '-' }}
            </div>
            <div class="footer-col" style="text-align: right;">
              <span class="footer-label">Kategori :</span> {{ $asset->asset_category }}
            </div>
          </div>
        </div>
      </div>
      <div style="page-break-after:always"></div>
    @endforeach
  @elseif(isset($asset))
    <div class="label-container">
      <div class="logo-section">
        <img src="{{ asset('assets/img/logo-min.svg') }}" alt="Logo" />
        PT ILSAM GLOBAL INDONESIA
      </div>
      <div class="barcode-section">
        <img class="barcode-img" src="{{ route('admin.assets.barcode', $asset->asset_code) }}?v={{ uniqid() }}" alt="Barcode">
        <div style="border-top:1px solid #000; margin:5px 0 0 0;"></div>
        <div class="asset-code">{{ $asset->asset_code }}</div>
      </div>
      <div class="property">
        PROPERTY OF<br>PT ILSAM GLOBAL INDONESIA
      </div>
      <div class="footer-section">
        <div class="footer-row">
          <div class="footer-col">
            <span class="footer-label">Lokasi :</span> {{ $asset->asset_location }}
          </div>
          <div class="footer-col" style="text-align: right;">
            <span class="footer-label">Tgl Pembelian :</span>
            {{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('d-m-Y') : '-' }}
          </div>
        </div>
        <div class="footer-row">
          <div class="footer-col">
            <span class="footer-label">Serial Number :</span> {{ $asset->serial_number ?? '-' }}
          </div>
          <div class="footer-col" style="text-align: right;">
            <span class="footer-label">Kategori :</span> {{ $asset->asset_category }}
          </div>
        </div>
      </div>
    </div>
  @else
    <div style="color:red;">Asset data not found.</div>
  @endif

  <script>
    window.print();
  </script>
</body>

</html>