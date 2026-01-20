@extends('layouts.master')

@section('title', 'Out Asset Jababeka | IGI')

@section('title-sub', ' Dashboard Asset Management ')
@section('pagetitle', 'Out Asset Jababeka')
@section('css')
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!--datatable css-->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <!--datatable responsive css-->
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
@endsection
@section('content')

  <!--begin::App-->
  <div id="layout-wrapper">
    <div class="row">
      {{-- SweetAlert2 notification --}}
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          @if(session('success'))
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: @json(session('success')),
              timer: 2000,
              showConfirmButton: false
            });
          @endif
          @if(session('error'))
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: @json(session('error')),
              timer: 2500,
              showConfirmButton: false
            });
          @endif
                                        });
      </script>
      <div class="col-12">
        <div class="card">
          <!--start::card-->
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Out Asset Jababeka</h5>
            <div class="d-flex gap-2">
              <form id="form-cancel-transfer" method="POST" action="{{ route('admin.assets.transfer.cancel') }}" style="display:inline-block;">
                @csrf
                <input type="hidden" name="selected_transfer_ids" id="selected-transfer-ids" value="">
                <button type="submit" class="btn btn-danger" id="btn-cancel-transfer">
                  <i class="fas fa-undo"></i> Batalkan
                </button>
              </form>
              <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-cari-asset">
                <i class="fas fa-search"></i> Cari Asset
              </button>
            </div>
          </div>
          <div class="card-body">
            <!-- DataTables search only -->
            <!-- start:: Alternative Pagination Datatable -->
            <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
              <thead>
                <tr>
                  <th><input type="checkbox" id="select-all-transfer"></th>
                  <th>No</th>
                  <th>Asset Code</th>
                  <th>Asset Name</th>
                  <th>Category</th>
                  <th>Location</th>
                  <th>Person In Charge</th>
                  <th>Purchase Date</th>
                  <th>Price</th>
                  <th>Condition</th>
                  <th>Ownership Status</th>
                  <th>Status Asset</th>
                  <th>Status Transfer</th>
                  <th>Description</th>
                  <th>Last Updated</th>
                  <th>Transferred At</th>
                </tr>
              </thead>
              <tbody>
                  @foreach($transfers as $asset)
                    @php
                      $transferStatus = $asset->status ?? 'OUT_REQUESTED';
                      $isCancellable = ($transferStatus === 'OUT_REQUESTED') && empty($asset->received_at) && empty($asset->cancelled_at);
                      $transferBadge = match($transferStatus) {
                        'OUT_REQUESTED' => 'bg-warning-subtle text-warning',
                        'RECEIVED' => 'bg-success-subtle text-success',
                        'CANCELLED' => 'bg-danger-subtle text-danger',
                        default => 'bg-light-subtle text-body',
                      };
                    @endphp
                    <tr>
                      <td><input type="checkbox" class="select-transfer" value="{{ $asset->id }}" {{ $isCancellable ? '' : 'disabled' }}></td>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $asset->asset_code }}</td>
                      <td>{{ $asset->asset_name }}</td>
                      <td>{{ $asset->asset_category }}</td>
                      <td>{{ $asset->asset_location }}</td>
                      <td>{{ $asset->person_in_charge }}</td>
                      <td>{{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('d-m-Y') : '-' }}</td>
                      <td>
                        @if($asset->price !== null)
                          Rp. {{ number_format($asset->price, 0, ',', '.') }}
                        @else
                          -
                        @endif
                      </td>
                      <td>{{ $asset->asset_condition }}</td>
                      <td>{{ $asset->ownership_status }}</td>
                      <td>
                        @php
                          $status = $asset->asset_status;
                          $badgeClass = match($status) {
                            'Active' => 'bg-success-subtle text-success',
                            'Inactive' => 'bg-secondary-subtle text-secondary',
                            'Sold' => 'bg-warning-subtle text-warning',
                            'Disposed' => 'bg-danger-subtle text-danger',
                            default => 'bg-light-subtle text-body',
                          };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                      </td>
                      <td>
                        <span class="badge {{ $transferBadge }}">{{ $transferStatus }}</span>
                      </td>
                      <td>{{ $asset->description }}</td>
                      <td>{{ $asset->last_updated ? \Carbon\Carbon::parse($asset->last_updated)->format('d-m-Y H:i') : '-' }}</td>
                      <td>{{ $asset->transferred_at ? \Carbon\Carbon::parse($asset->transferred_at)->format('d-m-Y H:i') : '-' }}</td>
                    </tr>
                  @endforeach
                </tbody>
            </table>
            <!-- DataTables handles pagination -->
            <!-- end:: Alternative Pagination Datatable -->
          </div>
        </div>
        <!--end::card-->
      </div>
    </div><!--End row-->
  </div><!--End container-fluid-->
  </main><!--End app-wrapper-->

  <!-- Modal Cari Asset -->
  <div class="modal fade" id="modal-cari-asset" tabindex="-1" aria-labelledby="modal-cari-asset-label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal-cari-asset-label">Cari Asset</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          @if($assets->isEmpty())
            <div class="text-center text-muted mb-3">Tidak ada asset tersedia.</div>
          @endif
          <table id="alternative-pagination-modal" class="table table-nowrap table-striped table-bordered w-100">
            <thead>
              <tr>
                <th><input type="checkbox" id="select-all-modal"></th>
                <th>No</th>
                <th>Asset Code</th>
                <th>Asset Name</th>
                <th>Category</th>
                <th>Location</th>
                <th>Person In Charge</th>
                <th>Purchase Date</th>
                <th>Price</th>
                <th>Condition</th>
                <th>Ownership Status</th>
                <th>Status</th>
                <th>Description</th>
                <th>Last Updated</th>
              </tr>
            </thead>
            <tbody>
              @foreach($assets as $a)
                <tr>
                  <td><input type="checkbox" class="select-asset-modal" value="{{ $a->id }}"></td>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $a->asset_code }}</td>
                  <td>{{ $a->asset_name }}</td>
                  <td>{{ $a->asset_category }}</td>
                  <td>{{ $a->asset_location }}</td>
                  <td>{{ $a->person_in_charge }}</td>
                  <td>{{ $a->purchase_date ? \Carbon\Carbon::parse($a->purchase_date)->format('d-m-Y') : '-' }}</td>
                  <td>
                    @if($a->price !== null)
                      Rp. {{ number_format($a->price, 0, ',', '.') }}
                    @else
                      -
                    @endif
                  </td>
                  <td>{{ $a->asset_condition }}</td>
                  <td>{{ $a->ownership_status }}</td>
                  <td>
                    @php
                      $status = $a->asset_status;
                      $badgeClass = match($status) {
                        'Active' => 'bg-success-subtle text-success',
                        'Inactive' => 'bg-secondary-subtle text-secondary',
                        'Sold' => 'bg-warning-subtle text-warning',
                        'Disposed' => 'bg-danger-subtle text-danger',
                        default => 'bg-light-subtle text-body',
                      };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                  </td>
                  <td>{{ $a->description }}</td>
                  <td>{{ $a->last_updated ? \Carbon\Carbon::parse($a->last_updated)->format('d-m-Y H:i') : '-' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="modal-footer">
          <form id="form-save-transfer" method="POST" action="{{ route('admin.assets.transfer.save') }}" class="d-flex w-100 justify-content-end gap-2">
            @csrf
            <input type="hidden" name="selected_ids" id="selected-ids-modal" value="">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary" id="btn-save-transfer">
              <i class="fas fa-save"></i> Save
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

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

  <script src="{{ asset('assets/js/table/datatable.init.js') }}"></script>
  <script type="module" src="{{ asset('assets/js/app.js') }}"></script>

  <script>
    $(document).ready(function() {
      // ===== Checkbox tabel utama (samakan dengan asset_pt) =====
      $('#select-all-transfer').on('change', function() {
        const checked = $(this).prop('checked');
        $('.select-transfer').each(function () {
          if ($(this).prop('disabled')) return;
          $(this).prop('checked', checked);
        });
        updateSelectedTransferIds();
      });

      $(document).on('change', '.select-transfer', function () {
        updateSelectedTransferIds();
      });

      function updateSelectedTransferIds() {
        var selected = $('.select-transfer:checked').map(function() { return this.value; }).get();
        $('#selected-transfer-ids').val(selected.join(','));
      }

      $('#form-cancel-transfer').on('submit', function (e) {
        var selected = $('.select-transfer:checked').map(function() { return this.value; }).get();
        if (selected.length === 0) {
          e.preventDefault();
          Swal.fire({ icon: 'warning', title: 'No data selected', text: 'Pilih minimal 1 asset untuk dibatalkan.' });
          return false;
        }

        e.preventDefault();
        $('#selected-transfer-ids').val(selected.join(','));
        var form = this;
        Swal.fire({
          title: 'Batalkan pengajuan?',
          text: 'Asset akan dikembalikan ke daftar asset.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'OK',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      });

      // ===== Checkbox tabel modal (samakan dengan asset_pt) =====
      $('#select-all-modal').on('change', function() {
        $('.select-asset-modal').prop('checked', $(this).prop('checked'));
        updateSelectedIdsModal();
      });

      $(document).on('change', '.select-asset-modal', function() {
        updateSelectedIdsModal();
      });

      function updateSelectedIdsModal() {
        var selected = $('.select-asset-modal:checked').map(function() { return this.value; }).get();
        $('#selected-ids-modal').val(selected.join(','));
      }

      // Init DataTable untuk modal saat dibuka (hindari double-init)
      $('#modal-cari-asset').on('shown.bs.modal', function () {
        if ($.fn.dataTable && !$.fn.dataTable.isDataTable('#alternative-pagination-modal')) {
          $('#alternative-pagination-modal').DataTable({
            responsive: true,
            pageLength: 10,
            searching: true,
            dom:
              "<'row'<'col-sm-12 col-md-6 mb-3'l><'col-sm-12 col-md-6 mt-2'f>>" +
              "<'table-responsive'tr>" +
              "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            language: {
              search: 'Search : ',
              searchPlaceholder: 'Type to filter...'
            }
          });
        }
      });

      // Submit Save transfer
      $('#form-save-transfer').on('submit', function(e) {
        var selected = $('.select-asset-modal:checked').map(function() { return this.value; }).get();
        if (selected.length === 0) {
          e.preventDefault();
          Swal.fire({ icon: 'warning', title: 'No asset selected', text: 'Silakan pilih minimal 1 asset.' });
          return false;
        }
        $('#selected-ids-modal').val(selected.join(','));
      });
    });
  </script>

  <script>
    // SweetAlert2 for delete confirmation (asset)
    $(document).on('click', '.btn-delete-asset', function (e) {
      e.preventDefault();
      var form = $(this).closest('form');
      Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  </script>

@endsection