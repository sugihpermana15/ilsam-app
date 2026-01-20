@extends('layouts.master')

@section('title', 'Asset Masuk | IGI')

@section('title-sub', ' Dashboard Asset Management ')
@section('pagetitle', 'Asset Masuk (Scan Barcode)')

@section('css')
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!--datatable css-->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <!--datatable responsive css-->
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
@endsection

@section('content')
  <div id="layout-wrapper">
    <div class="row">
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Success', text: @json(session('success')), timer: 1800, showConfirmButton: false });
          @endif
          @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Error', text: @json(session('error')), timer: 2500, showConfirmButton: false });
          @endif
              });
      </script>

      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">Asset Masuk (Karawang) - Scan Barcode</h5>
          </div>
          <div class="card-body">
            <form id="asset-scan-form" method="POST" action="{{ route('admin.assets.in.scan') }}"
              class="row g-2 align-items-end">
              @csrf
              <div class="col-12 col-md-6">
                <label class="form-label">Barcode / Asset Code</label>
                <input id="asset-code-input" type="text" name="asset_code"
                  class="form-control @error('asset_code') is-invalid @enderror" value="{{ old('asset_code') }}"
                  placeholder="Scan barcode di sini..." autofocus autocomplete="off">
                @error('asset_code')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-12 col-md-auto">
                <button id="btn-scan" type="submit" class="btn btn-primary">Scan</button>
              </div>
              <div class="col-12 col-md-auto">
                <a href="{{ route('admin.assets.transfer') }}" class="btn btn-light">Lihat Asset Keluar</a>
              </div>
            </form>

            <hr>

            <div class="table-responsive">
              <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Asset Code</th>
                    <th>Asset Name</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Requested At</th>
                    <th>Received At</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach(($recentReceipts ?? collect()) as $row)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $row->asset_code }}</td>
                      <td>{{ $row->asset_name }}</td>
                      <td>{{ $row->from_location ?? $row->asset_location ?? '-' }}</td>
                      <td>{{ $row->to_location ?? '-' }}</td>
                      <td>{{ $row->requested_at ? \Carbon\Carbon::parse($row->requested_at)->format('d-m-Y H:i') : '-' }}
                      </td>
                      <td>{{ $row->received_at ? \Carbon\Carbon::parse($row->received_at)->format('d-m-Y H:i') : '-' }}</td>
                      <td>{{ $row->status ?? '-' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  </div><!--End container-fluid-->
  </main><!--End app-wrapper-->
@endsection

@section('js')

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

  <!--datatable js-->
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script type="module" src="{{ asset('assets/js/app.js') }}"></script>

  <script>
    $(document).ready(function () {
      const $form = $('#asset-scan-form');
      const $input = $('#asset-code-input');
      const $btn = $('#btn-scan');

      // Most barcode scanners send an ENTER at the end.
      $input.on('keydown', function (e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          if ($btn.prop('disabled')) return;
          $btn.prop('disabled', true);
          $form.trigger('submit');
        }
      });

      // Optional: auto-submit when input is typed very fast (scanner-like) even without ENTER.
      // This avoids requiring a click while still being safe for manual typing.
      let lastInputAt = 0;
      let firstBurstAt = 0;
      let burstChars = 0;
      let timer = null;

      const resetBurst = () => {
        lastInputAt = 0;
        firstBurstAt = 0;
        burstChars = 0;
      };

      $input.on('input', function () {
        const now = Date.now();
        const val = String($input.val() || '').trim();

        // reset when empty
        if (!val) {
          resetBurst();
          if (timer) clearTimeout(timer);
          timer = null;
          return;
        }

        if (!firstBurstAt || (now - lastInputAt) > 200) {
          firstBurstAt = now;
          burstChars = 0;
        }

        burstChars += 1;
        lastInputAt = now;

        if (timer) clearTimeout(timer);
        timer = setTimeout(function () {
          const duration = now - firstBurstAt;
          const looksLikeScanner = burstChars >= 6 && duration <= 500;
          if (looksLikeScanner && !$btn.prop('disabled')) {
            $btn.prop('disabled', true);
            $form.trigger('submit');
          }
          resetBurst();
        }, 250);
      });

      // Init DataTable (hindari double-init)
      if ($.fn.dataTable && !$.fn.dataTable.isDataTable('#alternative-pagination')) {
        $('#alternative-pagination').DataTable({
          pagingType: 'full_numbers',
          searching: true,
          dom:
            "<'row'<'col-sm-12 col-md-6 mb-3'l><'col-sm-12 col-md-6 mt-5'f>>" +
            "<'table-responsive'tr>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
          language: {
            search: 'Search : ',
            searchPlaceholder: 'Type to filter...'
          }
        });
      }
    });
  </script>

@endsection