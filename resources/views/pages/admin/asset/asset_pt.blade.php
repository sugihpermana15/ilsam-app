@extends('layouts.master')

@section('title', __('assets.management') . ' | IGI')

@section('title-sub', __('assets.management'))
@section('pagetitle', __('assets.management'))
@section('css')
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!--datatable css-->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <!--datatable responsive css-->
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
  <style>
    /* Select2 -> follow Bootstrap primary theme */
    .select2-container--bootstrap-5 .select2-selection {
      border-color: var(--bs-border-color);
    }

    .select2-container--bootstrap-5.select2-container--open .select2-selection,
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5 .select2-selection:focus {
      border-color: var(--bs-primary);
      box-shadow: 0 0 0 .25rem rgba(var(--bs-primary-rgb), .25);
    }

    .select2-container--bootstrap-5 .select2-dropdown .select2-results__option--highlighted {
      background-color: var(--bs-primary);
      color: #fff;
    }

    .select2-container--bootstrap-5 .select2-dropdown .select2-results__option[aria-selected="true"] {
      background-color: rgba(var(--bs-primary-rgb), .12);
      color: inherit;
    }
  </style>
@endsection
@section('content')

  @php
    $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'assets_data', 'create');
    $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'assets_data', 'update');
    $canDelete = \App\Support\MenuAccess::can(auth()->user(), 'assets_data', 'delete');
  @endphp

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
              title: @json(__('common.success')),
              text: @json(session('success')),
              timer: 2000,
              showConfirmButton: false
            });
          @endif
          @if(session('error'))
            Swal.fire({
              icon: 'error',
              title: @json(__('common.error')),
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
            <h5 class="card-title mb-0">{{ __('assets.management') }}</h5>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-success" id="btn-open-create-asset" data-bs-toggle="modal" data-bs-target="#assetCreateModal" {{ $canCreate ? '' : 'disabled' }} title="{{ $canCreate ? '' : __('assets.actions.no_access_create') }}">
                <i class="fas fa-plus"></i> {{ __('assets.pt.add_asset') }}
              </button>
              <form id="form-print-selected-barcode" method="POST" action="{{ route('admin.assets.printSelectedBarcode') }}" target="_blank" style="display:inline-block;">
                @csrf
                <input type="hidden" name="selected_ids" id="selected-ids" value="">
                <button type="submit" id="print-selected-barcode" class="btn btn-secondary">
                  <i class="fas fa-barcode"></i> {{ __('assets.actions.print_barcode') }}
                </button>
              </form>
            </div>
          </div>
          <div class="card-body">
            <div class="row g-2 align-items-end mb-3">
              <div class="col-12 col-md-3">
                <label class="form-label mb-1">{{ __('assets.fields.location') }}</label>
                <select class="form-select" id="filter-asset-location">
                  <option value="">{{ __('common.all') }}</option>
                  @foreach(($assetLocations ?? collect()) as $l)
                    <option value="{{ $l->name }}">{{ $l->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 col-md-3">
                <label class="form-label mb-1">{{ __('assets.fields.category') }}</label>
                <select class="form-select" id="filter-asset-category">
                  <option value="">{{ __('common.all') }}</option>
                  @foreach(($assetCategories ?? collect()) as $c)
                    <option value="{{ $c->code }}">{{ $c->code }} - {{ $c->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 col-md-2">
                <label class="form-label mb-1">{{ __('assets.fields.status') }}</label>
                <select class="form-select" id="filter-asset-status">
                  <option value="">{{ __('common.all') }}</option>
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                  <option value="Sold">Sold</option>
                  <option value="Disposed">Disposed</option>
                </select>
              </div>
              <div class="col-12 col-md-2">
                <label class="form-label mb-1">{{ __('assets.fields.condition') }}</label>
                <select class="form-select" id="filter-asset-condition">
                  <option value="">{{ __('common.all') }}</option>
                  <option value="Good">{{ __('assets.options.condition.good') }}</option>
                  <option value="Minor Damage">{{ __('assets.options.condition.minor_damage') }}</option>
                  <option value="Major Damage">{{ __('assets.options.condition.major_damage') }}</option>
                </select>
              </div>
              <div class="col-12 col-md-2">
                <label class="form-label mb-1">{{ __('assets.fields.pic') }}</label>
                <select class="form-select" id="filter-asset-pic">
                  <option value="">{{ __('common.all') }}</option>
                  @foreach(($employees ?? collect()) as $e)
                    <option value="{{ $e->id }}">{{ $e->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-12 d-flex justify-content-end">
                <button type="button" class="btn btn-outline-secondary" id="btn-clear-asset-filters" title="{{ __('common.reset') }}">
                  <i class="fas fa-rotate-left"></i> {{ __('common.reset') }}
                </button>
              </div>
            </div>

            <!-- DataTables search only -->
            <!-- start:: Alternative Pagination Datatable -->
            <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100" data-dt-server="1">
              <thead>
                <tr>
                  <th><input type="checkbox" id="select-all"></th>
                  <th>{{ __('assets.pt.table.no') }}</th>
                  <th>{{ __('assets.fields.asset_code') }}</th>
                  <th>{{ __('assets.fields.asset_name') }}</th>
                  <th>{{ __('assets.fields.serial_number') }}</th>
                  <th>{{ __('assets.fields.category') }}</th>
                  <th>{{ __('assets.fields.location') }}</th>
                  <th>{{ __('assets.fields.pic') }}</th>
                  <th>{{ __('assets.fields.purchase_date') }}</th>
                  <th>{{ __('assets.fields.price') }}</th>
                  <th>{{ __('assets.fields.qty') }}</th>
                  <th>{{ __('assets.fields.uom') }}</th>
                  <th>{{ __('assets.fields.condition') }}</th>
                  <th>{{ __('assets.fields.ownership_status') }}</th>
                  <th>{{ __('assets.fields.status') }}</th>
                  <th>{{ __('assets.fields.description') }}</th>
                  <th>{{ __('assets.fields.last_updated') }}</th>
                  <th>{{ __('assets.pt.table.action') }}</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
            <!-- DataTables handles pagination -->
            <!-- end:: Alternative Pagination Datatable -->
          </div>
        </div>
        <!--end::card-->
      </div>

      <!-- Create Asset Modal (Input Lengkap) -->
      <div class="modal fade" id="assetCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ __('assets.pt.create_modal_title') }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('common.close') }}"></button>
            </div>
            <div class="modal-body">
              @if ($errors->any() && old('_create_modal'))
                <div class="alert alert-danger" role="alert">
                  <div class="fw-semibold mb-1">{{ __('assets.pt.validation_required') }}</div>
                  <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <form id="asset-create-form" action="{{ route('admin.assets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_create_modal" value="1">

                <div class="row g-3">
                  <div class="col-md-12">
                    <label class="form-label">{{ __('assets.fields.asset_name') }}</label>
                    <input type="text" class="form-control @error('asset_name') is-invalid @enderror" name="asset_name" value="{{ old('asset_name') }}" required>
                    @error('asset_name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.category') }}</label>
                    <select class="form-select js-select2-modal @error('asset_category') is-invalid @enderror" name="asset_category" required>
                      <option value="">{{ __('assets.pt.select_category') }}</option>
                      @foreach(($assetCategories ?? collect()) as $c)
                        <option value="{{ $c->code }}" {{ old('asset_category') == $c->code ? 'selected' : '' }}>
                          {{ $c->code }} - {{ $c->name }}
                        </option>
                      @endforeach
                    </select>
                    @error('asset_category')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">{{ __('assets.pt.manage_category_prefix') }} <a href="{{ route('admin.asset_categories.index') }}">{{ __('menu.master_data') }}</a>.</small>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.location') }}</label>
                    <select class="form-select js-select2-modal @error('asset_location') is-invalid @enderror" name="asset_location" required>
                      <option value="">{{ __('assets.pt.select_location') }}</option>
                      @foreach(($assetLocations ?? collect()) as $l)
                        <option value="{{ $l->name }}" {{ old('asset_location') == $l->name ? 'selected' : '' }}>{{ $l->name }}</option>
                      @endforeach
                    </select>
                    @error('asset_location')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">{{ __('assets.pt.manage_location_prefix') }} <a href="{{ route('admin.asset_locations.index') }}">{{ __('menu.master_data') }}</a>.</small>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.brand_type_model') }}</label>
                    <input type="text" class="form-control" name="brand_type_model" value="{{ old('brand_type_model') }}">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.serial_number') }}</label>
                    <input type="text" class="form-control" name="serial_number" value="{{ old('serial_number') }}">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.purchase_date') }}</label>
                    <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" name="purchase_date" value="{{ old('purchase_date') }}">
                    @error('purchase_date')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.price') }}</label>
                    <div class="input-group">
                      <span class="input-group-text">Rp.</span>
                      <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" name="price" min="0" value="{{ old('price') }}">
                    </div>
                    @error('price')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.qty') }}</label>
                    <input type="number" class="form-control @error('qty') is-invalid @enderror" name="qty" min="0" placeholder="0" value="{{ old('qty') }}">
                    @error('qty')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.uom') }}</label>
                    <select class="form-select js-select2-modal @error('satuan') is-invalid @enderror" name="satuan">
                      <option value="">{{ __('assets.pt.select_uom') }}</option>
                      @foreach(($assetUoms ?? collect()) as $u)
                        <option value="{{ $u->name }}" {{ old('satuan') == $u->name ? 'selected' : '' }}>{{ $u->name }}</option>
                      @endforeach
                    </select>
                    @error('satuan')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">{{ __('assets.pt.manage_uom_prefix') }} <a href="{{ route('admin.asset_uoms.index') }}">{{ __('menu.master_data') }}</a>.</small>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.vendor_supplier') }}</label>
                    <select class="form-select js-select2-modal @error('vendor_supplier') is-invalid @enderror" name="vendor_supplier">
                      <option value="">{{ __('assets.pt.select_vendor') }}</option>
                      @foreach(($assetVendors ?? collect()) as $v)
                        <option value="{{ $v->name }}" {{ old('vendor_supplier') == $v->name ? 'selected' : '' }}>{{ $v->name }}</option>
                      @endforeach
                    </select>
                    @error('vendor_supplier')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">{{ __('assets.pt.manage_vendor_prefix') }} <a href="{{ route('admin.asset_vendors.index') }}">{{ __('menu.master_data') }}</a>.</small>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.invoice_number') }}</label>
                    <input type="text" class="form-control" name="invoice_number" value="{{ old('invoice_number') }}">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.department') }}</label>
                    <select class="form-select js-select2-modal @error('department_id') is-invalid @enderror" name="department_id">
                      <option value="">{{ __('assets.pt.select_department') }}</option>
                      @foreach(($departments ?? collect()) as $d)
                        <option value="{{ $d->id }}" {{ (string) old('department_id') === (string) $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                      @endforeach
                    </select>
                    @error('department_id')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.pic') }}</label>
                    <select class="form-select js-select2-modal @error('person_in_charge_employee_id') is-invalid @enderror" name="person_in_charge_employee_id">
                      <option value="">{{ __('assets.pt.select_employee') }}</option>
                      @foreach(($employees ?? collect()) as $e)
                        <option value="{{ $e->id }}" {{ (string) old('person_in_charge_employee_id') === (string) $e->id ? 'selected' : '' }}>
                          {{ $e->no_id }} - {{ $e->name }}
                        </option>
                      @endforeach
                    </select>
                    @error('person_in_charge_employee_id')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.ownership_status') }}</label>
                    <select class="form-select" name="ownership_status">
                      <option value="">{{ __('assets.pt.select_status') }}</option>
                      <option value="Owned" {{ old('ownership_status') == 'Owned' ? 'selected' : '' }}>{{ __('assets.options.ownership.owned') }}</option>
                      <option value="Rented" {{ old('ownership_status') == 'Rented' ? 'selected' : '' }}>{{ __('assets.options.ownership.rented') }}</option>
                      <option value="Leased" {{ old('ownership_status') == 'Leased' ? 'selected' : '' }}>{{ __('assets.options.ownership.leased') }}</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.condition') }}</label>
                    <select class="form-select" name="asset_condition">
                      <option value="">{{ __('assets.pt.select_condition') }}</option>
                      <option value="Good" {{ old('asset_condition') == 'Good' ? 'selected' : '' }}>{{ __('assets.options.condition.good') }}</option>
                      <option value="Minor Damage" {{ old('asset_condition') == 'Minor Damage' ? 'selected' : '' }}>{{ __('assets.options.condition.minor_damage') }}</option>
                      <option value="Major Damage" {{ old('asset_condition') == 'Major Damage' ? 'selected' : '' }}>{{ __('assets.options.condition.major_damage') }}</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.status') }}</label>
                    <select class="form-select" name="asset_status">
                      <option value="">{{ __('assets.pt.select_status') }}</option>
                      <option value="Active" {{ old('asset_status') == 'Active' ? 'selected' : '' }}>{{ __('assets.options.asset_status.active') }}</option>
                      <option value="Inactive" {{ old('asset_status') == 'Inactive' ? 'selected' : '' }}>{{ __('assets.options.asset_status.inactive') }}</option>
                      <option value="Sold" {{ old('asset_status') == 'Sold' ? 'selected' : '' }}>{{ __('assets.options.asset_status.sold') }}</option>
                      <option value="Disposed" {{ old('asset_status') == 'Disposed' ? 'selected' : '' }}>{{ __('assets.options.asset_status.disposed') }}</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.start_use_date') }}</label>
                    <input type="date" class="form-control @error('start_use_date') is-invalid @enderror" name="start_use_date" value="{{ old('start_use_date') }}">
                    @error('start_use_date')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.warranty_status') }}</label>
                    <select class="form-select" name="warranty_status">
                      <option value="">{{ __('assets.pt.select') }}</option>
                      <option value="Yes" {{ old('warranty_status') == 'Yes' ? 'selected' : '' }}>{{ __('assets.options.warranty.yes') }}</option>
                      <option value="No" {{ old('warranty_status') == 'No' ? 'selected' : '' }}>{{ __('assets.options.warranty.no') }}</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.warranty_end_date') }}</label>
                    <input type="date" class="form-control" name="warranty_end_date" value="{{ old('warranty_end_date') }}">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.input_by') }}</label>
                    <input type="text" class="form-control" name="input_by" value="{{ Auth::user()->name }}" readonly>
                  </div>

                  <div class="col-md-12">
                    <label class="form-label">{{ __('assets.fields.description') }}</label>
                    <textarea class="form-control" name="description" rows="2">{{ old('description') }}</textarea>
                  </div>

                  <div class="col-md-12">
                    <label class="form-label">{{ __('assets.fields.notes') }}</label>
                    <textarea class="form-control" name="notes" rows="2">{{ old('notes') }}</textarea>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">{{ __('assets.fields.image_1') }}</label>
                    <input type="file" class="form-control" name="image_1" accept="image/*">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">{{ __('assets.fields.image_2') }}</label>
                    <input type="file" class="form-control" name="image_2" accept="image/*">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">{{ __('assets.fields.image_3') }}</label>
                    <input type="file" class="form-control" name="image_3" accept="image/*">
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.close') }}</button>
              <button type="submit" form="asset-create-form" class="btn btn-primary" {{ $canCreate ? '' : 'disabled' }} title="{{ $canCreate ? '' : __('assets.actions.no_access_create') }}">{{ __('assets.pt.save_asset') }}</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Edit Asset Modal -->
      <div class="modal fade" id="assetEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ __('assets.pt.edit_modal_title') }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('common.close') }}"></button>
            </div>
            <div class="modal-body">
              @if ($errors->any() && old('_edit_id'))
                <div class="alert alert-danger" role="alert">
                  <div class="fw-semibold mb-1">{{ __('assets.pt.validation_required') }}</div>
                  <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif
              <div class="alert alert-danger d-none" id="asset-edit-error"></div>
              <form id="asset-edit-form" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="_edit_id" id="edit-id-hidden" value="">

                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.asset_code') }}</label>
                    <input type="text" class="form-control" id="edit-asset-code" readonly>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.asset_name') }}</label>
                    <input type="text" class="form-control" name="asset_name" id="edit-asset-name" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.category') }}</label>
                    <select class="form-select js-select2-modal" name="asset_category" id="edit-asset-category" required>
                      <option value="">{{ __('assets.pt.select_category') }}</option>
                      @foreach(($assetCategories ?? collect()) as $c)
                        <option value="{{ $c->code }}">{{ $c->code }} - {{ $c->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.location') }}</label>
                    <select class="form-select js-select2-modal" name="asset_location" id="edit-asset-location" required>
                      <option value="">{{ __('assets.pt.select_location') }}</option>
                      @foreach(($assetLocations ?? collect()) as $l)
                        <option value="{{ $l->name }}">{{ $l->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">{{ __('assets.fields.qty') }}</label>
                    <input type="number" class="form-control" name="qty" id="edit-qty" min="0">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">{{ __('assets.fields.uom') }}</label>
                    <select class="form-select js-select2-modal" name="satuan" id="edit-satuan">
                      <option value="">{{ __('assets.pt.select_uom') }}</option>
                      @foreach(($assetUoms ?? collect()) as $u)
                        <option value="{{ $u->name }}">{{ $u->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">{{ __('assets.fields.vendor') }}</label>
                    <select class="form-select js-select2-modal" name="vendor_supplier" id="edit-vendor">
                      <option value="">{{ __('assets.pt.select_vendor') }}</option>
                      @foreach(($assetVendors ?? collect()) as $v)
                        <option value="{{ $v->name }}">{{ $v->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.department') }}</label>
                    <select class="form-select js-select2-modal" name="department_id" id="edit-department-id">
                      <option value="">{{ __('assets.pt.select_department') }}</option>
                      @foreach(($departments ?? collect()) as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.pic') }}</label>
                    <select class="form-select js-select2-modal" name="person_in_charge_employee_id" id="edit-pic-id">
                      <option value="">{{ __('assets.pt.select_employee') }}</option>
                      @foreach(($employees ?? collect()) as $e)
                        <option value="{{ $e->id }}">{{ $e->no_id }} - {{ $e->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.purchase_date') }}</label>
                    <input type="date" class="form-control" name="purchase_date" id="edit-purchase-date">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.price') }}</label>
                    <div class="input-group">
                      <span class="input-group-text">Rp.</span>
                      <input type="number" step="0.01" class="form-control" name="price" id="edit-price" min="0">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.brand_type_model') }}</label>
                    <input type="text" class="form-control" name="brand_type_model" id="edit-brand">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.serial_number') }}</label>
                    <input type="text" class="form-control" name="serial_number" id="edit-serial">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.invoice_number') }}</label>
                    <input type="text" class="form-control" name="invoice_number" id="edit-invoice">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.start_use_date') }}</label>
                    <input type="date" class="form-control" name="start_use_date" id="edit-start-use-date">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">{{ __('assets.fields.ownership_status') }}</label>
                    <select class="form-select" name="ownership_status" id="edit-ownership">
                      <option value="">{{ __('assets.pt.select_status') }}</option>
                      <option value="Owned">{{ __('assets.options.ownership.owned') }}</option>
                      <option value="Rented">{{ __('assets.options.ownership.rented') }}</option>
                      <option value="Leased">{{ __('assets.options.ownership.leased') }}</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">{{ __('assets.fields.condition') }}</label>
                    <select class="form-select" name="asset_condition" id="edit-condition">
                      <option value="">{{ __('assets.pt.select_condition') }}</option>
                      <option value="Good">{{ __('assets.options.condition.good') }}</option>
                      <option value="Minor Damage">{{ __('assets.options.condition.minor_damage') }}</option>
                      <option value="Major Damage">{{ __('assets.options.condition.major_damage') }}</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">{{ __('assets.fields.status') }}</label>
                    <select class="form-select" name="asset_status" id="edit-status">
                      <option value="">{{ __('assets.pt.select_status') }}</option>
                      <option value="Active">{{ __('assets.options.asset_status.active') }}</option>
                      <option value="Inactive">{{ __('assets.options.asset_status.inactive') }}</option>
                      <option value="Sold">{{ __('assets.options.asset_status.sold') }}</option>
                      <option value="Disposed">{{ __('assets.options.asset_status.disposed') }}</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.warranty_status') }}</label>
                    <select class="form-select" name="warranty_status" id="edit-warranty-status">
                      <option value="">{{ __('assets.pt.select') }}</option>
                      <option value="Yes">{{ __('assets.options.warranty.yes') }}</option>
                      <option value="No">{{ __('assets.options.warranty.no') }}</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.warranty_end_date') }}</label>
                    <input type="date" class="form-control" name="warranty_end_date" id="edit-warranty-end-date">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.description') }}</label>
                    <textarea class="form-control" name="description" id="edit-description" rows="2"></textarea>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">{{ __('assets.fields.notes') }}</label>
                    <textarea class="form-control" name="notes" id="edit-notes" rows="2"></textarea>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">{{ __('assets.fields.image_1_optional') }}</label>
                    <input type="file" class="form-control" name="image_1" accept="image/*">
                    <img id="edit-image-1" class="mt-2 d-none" style="max-width: 150px; max-height: 150px;" alt="{{ __('assets.fields.image_1') }}">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">{{ __('assets.fields.image_2_optional') }}</label>
                    <input type="file" class="form-control" name="image_2" accept="image/*">
                    <img id="edit-image-2" class="mt-2 d-none" style="max-width: 150px; max-height: 150px;" alt="{{ __('assets.fields.image_2') }}">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">{{ __('assets.fields.image_3_optional') }}</label>
                    <input type="file" class="form-control" name="image_3" accept="image/*">
                    <img id="edit-image-3" class="mt-2 d-none" style="max-width: 150px; max-height: 150px;" alt="{{ __('assets.fields.image_3') }}">
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.close') }}</button>
              <button type="submit" form="asset-edit-form" class="btn btn-primary" {{ $canUpdate ? '' : 'disabled' }} title="{{ $canUpdate ? '' : __('assets.actions.no_access_update') }}">{{ __('assets.pt.update') }}</button>
            </div>
          </div>
        </div>
      </div>
    </div><!--End row-->
  </div><!--End container-fluid-->
  </main><!--End app-wrapper-->

@endsection

@section('js')

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
      // --- DataTables server-side for assets (fix pagination / per-page size) ---
      const canUpdate = @json($canUpdate);
      const canDelete = @json($canDelete);
      const csrfToken = @json(csrf_token());
      const currentLocation = @json(request('location'));
      const dtUrl = @json(route('admin.assets.datatable'));

      // Avoid "Cannot reinitialise DataTable" if the table gets initialized twice (e.g., cached/old init script on VPS).
      if ($.fn.dataTable && $.fn.dataTable.isDataTable('#alternative-pagination')) {
        $('#alternative-pagination').DataTable().destroy();
        $('#alternative-pagination').find('tbody').empty();
      }

      // Filters (server-side)
      const $filterLocation = $('#filter-asset-location');
      const $filterCategory = $('#filter-asset-category');
      const $filterStatus = $('#filter-asset-status');
      const $filterCondition = $('#filter-asset-condition');
      const $filterPic = $('#filter-asset-pic');

      if (currentLocation) {
        $filterLocation.val(String(currentLocation));
      }

      const labels = {
        detail: @json(__('assets.actions.detail')),
        edit: @json(__('assets.actions.edit')),
        noAccessUpdate: @json(__('assets.actions.no_access_update')),
        delete: @json(__('assets.actions.delete')),
        noAccessDelete: @json(__('assets.actions.no_access_delete')),
      };

      const selectedIds = new Set();

      const syncSelectedIdsInput = () => {
        $('#selected-ids').val(Array.from(selectedIds).join(','));
      };

      const syncSelectAllState = (dt) => {
        const nodes = dt.rows({ page: 'current' }).nodes();
        const $checks = $(nodes).find('input.select-asset');
        if ($checks.length === 0) {
          $('#select-all').prop('checked', false).prop('indeterminate', false);
          return;
        }
        const total = $checks.length;
        const checked = $checks.filter(':checked').length;
        $('#select-all')
          .prop('checked', checked === total)
          .prop('indeterminate', checked > 0 && checked < total);
      };

      const statusBadgeHtml = (statusRaw) => {
        const s = (statusRaw || '').toString();
        if (!s) return '-';
        const cls = {
          'Active': 'bg-success-subtle text-success',
          'Inactive': 'bg-secondary-subtle text-secondary',
          'Sold': 'bg-warning-subtle text-warning',
          'Disposed': 'bg-danger-subtle text-danger',
        }[s] || 'bg-light-subtle text-body';
        return `<span class="badge ${cls}">${$('<div/>').text(s).html()}</span>`;
      };

      const dt = $('#alternative-pagination').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        ajax: {
          url: dtUrl,
          data: function (d) {
            if (currentLocation) d.location = currentLocation;
            const fl = ($filterLocation.val() || '').toString().trim();
            const fc = ($filterCategory.val() || '').toString().trim();
            const fs = ($filterStatus.val() || '').toString().trim();
            const fcond = ($filterCondition.val() || '').toString().trim();
            const fpic = ($filterPic.val() || '').toString().trim();
            if (fl) d.f_location = fl;
            if (fc) d.f_category = fc;
            if (fs) d.f_status = fs;
            if (fcond) d.f_condition = fcond;
            if (fpic) d.f_pic_employee_id = fpic;
          }
        },
        order: [[2, 'desc']],
        columns: [
          {
            data: 'id',
            orderable: false,
            searchable: false,
            render: function (data) {
              const id = String(data);
              const checked = selectedIds.has(id) ? 'checked' : '';
              return `<input type="checkbox" class="select-asset" value="${id}" ${checked}>`;
            }
          },
          {
            data: 'id',
            orderable: false,
            searchable: false,
            render: function (_data, _type, _row, meta) {
              return meta.row + meta.settings._iDisplayStart + 1;
            }
          },
          { data: 'asset_code', defaultContent: '-' },
          { data: 'asset_name', defaultContent: '-' },
          { data: 'serial_number', defaultContent: '-' },
          { data: 'asset_category', defaultContent: '-' },
          { data: 'asset_location', defaultContent: '-' },
          { data: 'person_in_charge', defaultContent: '-' },
          { data: 'purchase_date', defaultContent: '-' },
          { data: 'price', defaultContent: '-' },
          { data: 'qty', defaultContent: '-' },
          { data: 'satuan', defaultContent: '-' },
          { data: 'asset_condition', defaultContent: '-' },
          { data: 'ownership_status', defaultContent: '-' },
          {
            data: 'asset_status',
            defaultContent: '-',
            render: function (data) {
              return statusBadgeHtml(data);
            }
          },
          { data: 'description', defaultContent: '-' },
          { data: 'last_updated', defaultContent: '-' },
          {
            data: 'id',
            orderable: false,
            searchable: false,
            render: function (data, _type, row) {
              const id = String(data);
              const urlShow = `{{ url('/admin/assets') }}/${id}`;
              const urlDestroy = `{{ url('/admin/assets') }}/${id}`;

              const editDisabled = canUpdate ? '' : 'disabled';
              const deleteDisabled = canDelete ? '' : 'disabled';
              const editTitle = canUpdate ? labels.edit : labels.noAccessUpdate;
              const deleteTitle = canDelete ? labels.delete : labels.noAccessDelete;

              return `
                <a href="${urlShow}" class="btn btn-sm btn-info" title="${labels.detail}">
                  <i class="fas fa-eye"></i>
                </a>
                <button type="button" class="btn btn-sm btn-warning btn-edit-asset" data-id="${id}" ${editDisabled} title="${editTitle}">
                  <i class="fas fa-edit"></i>
                </button>
                <form action="${urlDestroy}" method="POST" style="display:inline-block" class="form-delete-asset" title="${labels.delete}">
                  <input type="hidden" name="_token" value="${csrfToken}">
                  <input type="hidden" name="_method" value="DELETE">
                  <button type="button" class="btn btn-sm btn-danger btn-delete-asset" ${deleteDisabled} title="${deleteTitle}">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </form>
              `.trim();
            }
          },
        ],
        drawCallback: function () {
          const api = this.api();
          // re-apply selected state after redraw
          api.rows({ page: 'current' }).nodes().to$().find('input.select-asset').each(function () {
            const id = String(this.value);
            this.checked = selectedIds.has(id);
          });
          syncSelectAllState(api);
        }
      });

      const redrawAssets = () => {
        dt.ajax.reload(null, true);
      };

      $filterLocation.on('change', redrawAssets);
      $filterCategory.on('change', redrawAssets);
      $filterStatus.on('change', redrawAssets);
      $filterCondition.on('change', redrawAssets);
      $filterPic.on('change', redrawAssets);

      $('#btn-clear-asset-filters').on('click', function () {
        $filterCategory.val('');
        $filterStatus.val('');
        $filterCondition.val('');
        $filterPic.val('');
        $filterLocation.val(currentLocation ? String(currentLocation) : '');
        redrawAssets();
      });

      const initSelect2InModal = ($modal) => {
        const $modalSelects = $modal.find('.js-select2-modal');
        if (!$modalSelects.length || !$.fn.select2) {
          return;
        }
        $modalSelects.each(function() {
          const $el = $(this);
          if ($el.hasClass('select2-hidden-accessible')) {
            return;
          }
          $el.select2({
            theme: 'bootstrap-5',
            width: '100%',
            allowClear: true,
            dropdownParent: $modal,
          });
        });
      };

      const $createModal = $('#assetCreateModal');
      const $editModal = $('#assetEditModal');
      const ensureEditModalSelect2 = () => initSelect2InModal($editModal);
      const ensureCreateModalSelect2 = () => initSelect2InModal($createModal);
      const notRegisteredPrefix = @json(__('common.not_registered_prefix'));

      const setSelectValueWithFallback = (selector, value, fallbackLabel) => {
        const $sel = $(selector);
        if (!$sel.length) return;
        const val = (value === null || value === undefined) ? '' : String(value);
        if (val === '') {
          $sel.val('').trigger('change');
          return;
        }

        const has = $sel.find('option').filter(function() { return String($(this).attr('value')) === val; }).length > 0;
        if (!has) {
          const label = fallbackLabel ? `${notRegisteredPrefix} ${fallbackLabel}` : `${notRegisteredPrefix} ${val}`;
          $sel.append(new Option(label, val, true, true));
        }
        $sel.val(val).trigger('change');
      };

      const openEditModal = async (id) => {
        const $err = $('#asset-edit-error');
        $err.addClass('d-none').text('');

        ensureEditModalSelect2();

        try {
          const url = `{{ url('/admin/assets') }}/${id}/json`;
          const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
          if (!res.ok) {
            throw new Error(@json(__('assets.pt.alerts.fetch_failed')));
          }
          const data = await res.json();

          $('#edit-asset-code').val(data.asset_code || '');
          $('#edit-asset-name').val(data.asset_name || '');
          $('#edit-qty').val(data.qty ?? '');
          $('#edit-purchase-date').val(data.purchase_date || '');
          $('#edit-price').val(data.price ?? '');
          $('#edit-brand').val(data.brand_type_model || '');
          $('#edit-serial').val(data.serial_number || '');
          $('#edit-invoice').val(data.invoice_number || '');
          $('#edit-start-use-date').val(data.start_use_date || '');
          $('#edit-description').val(data.description || '');
          $('#edit-notes').val(data.notes || '');
          $('#edit-ownership').val(data.ownership_status || '');
          $('#edit-condition').val(data.asset_condition || '');
          $('#edit-status').val(data.asset_status || '');
          $('#edit-warranty-status').val(data.warranty_status || '');
          $('#edit-warranty-end-date').val(data.warranty_end_date || '');

          setSelectValueWithFallback('#edit-asset-category', data.asset_category, data.asset_category);
          setSelectValueWithFallback('#edit-asset-location', data.asset_location, data.asset_location);
          setSelectValueWithFallback('#edit-satuan', data.satuan, data.satuan);
          setSelectValueWithFallback('#edit-vendor', data.vendor_supplier, data.vendor_supplier);
          setSelectValueWithFallback('#edit-department-id', data.department_id, data.department);
          setSelectValueWithFallback('#edit-pic-id', data.person_in_charge_employee_id, data.person_in_charge);

          const form = document.getElementById('asset-edit-form');
          form.action = `{{ url('/admin/assets') }}/${id}`;
          const idEl = document.getElementById('edit-id-hidden');
          if (idEl) idEl.value = String(id);

          const setImg = (imgSelector, urlValue) => {
            const el = document.querySelector(imgSelector);
            if (!el) return;
            if (urlValue) {
              el.src = urlValue;
              el.classList.remove('d-none');
            } else {
              el.src = '';
              el.classList.add('d-none');
            }
          };
          setImg('#edit-image-1', data.image_1_url);
          setImg('#edit-image-2', data.image_2_url);
          setImg('#edit-image-3', data.image_3_url);

          if (window.bootstrap && bootstrap.Modal) {
            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('assetEditModal'));
            modal.show();
          } else {
            $editModal.modal('show');
          }
        } catch (e) {
          $err.removeClass('d-none').text(e.message || @json(__('assets.pt.alerts.generic_error')));
        }
      };

      $(document).on('click', '.btn-edit-asset', function () {
        const id = $(this).data('id');
        if (!id) return;
        openEditModal(id);
      });

      const openCreateModal = () => {
        ensureCreateModalSelect2();
        if (window.bootstrap && bootstrap.Modal) {
          const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('assetCreateModal'));
          modal.show();
        } else {
          $createModal.modal('show');
        }
      };

      $(document).on('click', '#btn-open-create-asset', function () {
        openCreateModal();
      });

      // Re-open create modal on validation error
      const shouldOpenCreate = @json(old('_create_modal') ? true : (request('open') === 'create'));
      if (shouldOpenCreate) {
        openCreateModal();
      }

      // Allow /admin/assets/{id}/edit to redirect to /admin/assets?edit={id} and auto-open modal
      const editId = @json(request('edit') ?: old('_edit_id'));
      if (editId) {
        openEditModal(editId);
      }

      // Select all checkbox (current page only)
      $('#select-all').on('change', function() {
        const checked = $(this).prop('checked');
        const nodes = dt.rows({ page: 'current' }).nodes();
        $(nodes).find('input.select-asset').each(function () {
          const id = String(this.value);
          this.checked = checked;
          if (checked) {
            selectedIds.add(id);
          } else {
            selectedIds.delete(id);
          }
        });
        syncSelectedIdsInput();
        syncSelectAllState(dt);
      });

      // On any checkbox change (delegated, works across DataTables redraw)
      $(document).on('change', '.select-asset', function() {
        const id = String(this.value);
        if (this.checked) {
          selectedIds.add(id);
        } else {
          selectedIds.delete(id);
        }
        syncSelectedIdsInput();
        syncSelectAllState(dt);
      });

      // On form submit, check if any selected
      $('#form-print-selected-barcode').on('submit', function(e) {
        const selected = Array.from(selectedIds);
        if (selected.length === 0) {
          e.preventDefault();
          Swal.fire({ icon: 'warning', title: @json(__('assets.pt.alerts.no_asset_selected_title')), text: @json(__('assets.pt.alerts.no_asset_selected_text')) });
          return false;
        }
        $('#selected-ids').val(selected.join(','));
      });
    });
  </script>

  <script>
    // SweetAlert2 for delete confirmation (asset)
    $(document).on('click', '.btn-delete-asset', function (e) {
      e.preventDefault();
      var form = $(this).closest('form');
      Swal.fire({
        title: @json(__('assets.pt.alerts.delete_confirm_title')),
        text: @json(__('assets.pt.alerts.delete_confirm_text')),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: @json(__('common.ok')),
        cancelButtonText: @json(__('common.cancel'))
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  </script>

@endsection