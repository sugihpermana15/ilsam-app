@php
  // Set page size for print (60x40mm = 6x4 cm)
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8">
  <title>{{ __('assets.barcode_print.title') }} - Ilsam</title>
  <!-- App favicon -->
  <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
  <style>
    :root {
      /* Adjust these if your print is slightly shifted */
      --print-offset-x: 0mm;
      --print-offset-y: 0mm;
      /* Extra blank area to avoid bottom cut-off on some thermal printers */
      --safe-bottom: 0.5mm;
      --label-width: 60mm;
      --label-height: 40mm;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    @media print {
      @page {
        size: var(--label-width) var(--label-height);
        margin: 0;
      }

      html,
      body {
        margin: 0;
        padding: 0;
        width: var(--label-width);
        height: var(--label-height);
      }

      .label-container {
        width: var(--label-width);
        height: var(--label-height);
      }

      /* Prevent browser from auto-scaling */
      body {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
    }

    body {
      width: var(--label-width);
      height: var(--label-height);
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      color: #000;
      background: white;
    }

    .label-container {
      width: var(--label-width);
      height: var(--label-height);
      padding: 1.5mm;
      padding-bottom: calc(1.5mm + var(--safe-bottom));
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      overflow: hidden;
      position: relative;
      left: var(--print-offset-x);
      top: var(--print-offset-y);
    }

    .logo-section {
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 8px;
      font-weight: bold;
      line-height: 1.1;
      padding-bottom: 0.6mm;
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
      padding: 0.2mm 0.2mm;
      background: #fff;
    }

    .barcode-img-wrap {
      width: 100%;
      height: 14mm;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }

    .barcode-img {
      width: 56mm;
      height: auto;
      display: block;
    }

    .asset-code {
      text-align: center;
      font-weight: bold;
      font-size: 8px;
      line-height: 1.2;
    }

    .barcode-divider {
      width: 100%;
      border-top: 1px solid #000;
    }

    .barcode-divider.above-code {
      margin-top: 0.2mm;
      margin-bottom: 0.2mm;
    }

    .asset-code-divider {
      width: 100%;
      border-top: 1px solid #000;
      margin-top: 0.3mm;
    }

    .property {
      text-align: center;
      font-size: 6px;
      line-height: 1.2;
      padding: 1px 0;
      border-bottom: 1px solid #000;
    }

    .footer-section {
      padding-top: 0.6mm;
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
          <img src="{{ asset('assets/img/logo-min.svg') }}" alt="{{ __('common.logo') }}" />
          <span>PT ILSAM GLOBAL INDONESIA</span>
        </div>
        <div class="barcode-section">
          <div class="barcode-img-wrap">
            <img class="barcode-img" src="{{ route('admin.assets.barcode', $asset->asset_code) }}" alt="{{ __('common.barcode') }}">
          </div>
          <div class="barcode-divider above-code"></div>
          <div class="asset-code">{{ $asset->asset_code }}</div>
          <div class="asset-code-divider"></div>
        </div>
        <div class="property">
          {{ __('assets.barcode_print.property_of') }}<br>PT ILSAM GLOBAL INDONESIA
        </div>
        <div class="footer-section">
          <div class="footer-row">
            <div class="footer-col">
              <span class="footer-label">{{ __('assets.barcode_print.labels.location') }} :</span> {{ $asset->asset_location }}
            </div>
            <div class="footer-col" style="text-align: right;">
              <span class="footer-label">{{ __('assets.barcode_print.labels.purchase_date') }} :</span>
              {{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('d-m-Y') : '-' }}
            </div>
          </div>
          <div class="footer-row">
            <div class="footer-col">
              <span class="footer-label">{{ __('assets.barcode_print.labels.serial_number') }} :</span> {{ $asset->serial_number ?? '-' }}
            </div>
            <div class="footer-col" style="text-align: right;">
              <span class="footer-label">{{ __('assets.barcode_print.labels.category') }} :</span> {{ $asset->asset_category }}
            </div>
          </div>
          <div class="footer-row">
            <div class="footer-col">
              <span class="footer-label">{{ __('assets.fields.brand_type_model') }} :</span> {{ $asset->brand_type_model ?? '-' }}
            </div>
            <div class="footer-col" style="text-align: right;">
              <span class="footer-label">{{ __('assets.fields.location_detail') }} :</span> {{ $asset->asset_location_detail ?? '-' }}
            </div>
          </div>
        </div>
      </div>
      <div style="page-break-after:always"></div>
    @endforeach
  @elseif(isset($asset))
    <div class="label-container">
      <div class="logo-section">
        <img src="{{ asset('assets/img/logo-min.svg') }}" alt="{{ __('common.logo') }}" />
        PT ILSAM GLOBAL INDONESIA
      </div>
      <div class="barcode-section">
        <div class="barcode-img-wrap">
          <img class="barcode-img" src="{{ route('admin.assets.barcode', $asset->asset_code) }}" alt="{{ __('common.barcode') }}">
        </div>
        <div class="barcode-divider above-code"></div>
        <div class="asset-code">{{ $asset->asset_code }}</div>
        <div class="asset-code-divider"></div>
      </div>
      <div class="property">
        {{ __('assets.barcode_print.property_of') }}<br>PT ILSAM GLOBAL INDONESIA
      </div>
      <div class="footer-section">
        <div class="footer-row">
          <div class="footer-col">
            <span class="footer-label">{{ __('assets.barcode_print.labels.location') }} :</span> {{ $asset->asset_location }}
          </div>
          <div class="footer-col" style="text-align: right;">
            <span class="footer-label">{{ __('assets.barcode_print.labels.purchase_date') }} :</span>
            {{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('d-m-Y') : '-' }}
          </div>
        </div>
        <div class="footer-row">
          <div class="footer-col">
            <span class="footer-label">{{ __('assets.barcode_print.labels.serial_number') }} :</span> {{ $asset->serial_number ?? '-' }}
          </div>
          <div class="footer-col" style="text-align: right;">
            <span class="footer-label">{{ __('assets.barcode_print.labels.category') }} :</span> {{ $asset->asset_category }}
          </div>
        </div>
        <div class="footer-row">
          <div class="footer-col">
            <span class="footer-label">{{ __('assets.fields.brand_type_model') }} :</span> {{ $asset->brand_type_model ?? '-' }}
          </div>
          <div class="footer-col" style="text-align: right;">
            <span class="footer-label">{{ __('assets.fields.location_detail') }} :</span> {{ $asset->asset_location_detail ?? '-' }}
          </div>
        </div>
      </div>
    </div>
  @else
    <div style="color:red;">{{ __('assets.barcode_print.not_found') }}</div>
  @endif

  <script>
    (function () {
      function waitForImages() {
        var images = Array.prototype.slice.call(document.images || []);
        if (!images.length) return Promise.resolve();

        return Promise.all(images.map(function (img) {
          if (img.complete) return Promise.resolve();
          return new Promise(function (resolve) {
            img.addEventListener('load', resolve, { once: true });
            img.addEventListener('error', resolve, { once: true });
          });
        }));
      }

      window.addEventListener('load', function () {
        waitForImages().then(function () {
          setTimeout(function () {
            window.print();
          }, 100);
        });
      });
    })();
  </script>
</body>

</html>