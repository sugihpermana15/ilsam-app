@extends('layouts.master')

@section('title', __('accounts.index.page_title'))

@section('title-sub', __('accounts.index.title_sub'))
@section('pagetitle', __('accounts.index.pagetitle'))

@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
  <style>
    .select2-container--bootstrap-5 .select2-selection { border-color: var(--bs-border-color); }
    .select2-container--bootstrap-5.select2-container--open .select2-selection,
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5 .select2-selection:focus {
      border-color: var(--bs-primary);
      box-shadow: 0 0 0 .25rem rgba(var(--bs-primary-rgb), .25);
    }

    .accounts-filter .form-label { margin-bottom: .25rem; font-size: .78rem; color: var(--bs-secondary-color); }
    .accounts-filter .form-control,
    .accounts-filter .form-select { min-height: 38px; }
  </style>
@endsection

@section('content')
  @php
    $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'accounts_data', 'create');
    $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'accounts_data', 'update');
    $canDelete = \App\Support\MenuAccess::can(auth()->user(), 'accounts_data', 'delete');
  @endphp

  <div id="layout-wrapper">
    <div class="row">
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          @if(session('success'))
            Swal.fire({ icon: 'success', title: @json(__('common.success')), text: @json(session('success')), timer: 2000, showConfirmButton: false });
          @endif
          @if(session('error'))
            Swal.fire({ icon: 'error', title: @json(__('common.error')), text: @json(session('error')), timer: 2500, showConfirmButton: false });
          @endif
        });
      </script>

      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('accounts.index.card_title') }}</h5>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-success" id="btn-open-create-account" data-bs-toggle="modal" data-bs-target="#accountCreateModal" {{ $canCreate ? '' : 'disabled' }} title="{{ $canCreate ? '' : __('common.no_access_create') }}">
                <i class="fas fa-plus"></i> {{ __('accounts.index.add_account') }}
              </button>
            </div>
          </div>

          <div class="card-body">
            <form class="row g-2 align-items-end mb-3 accounts-filter" method="GET" action="{{ route('admin.accounts.index') }}">
              <div class="col-12 col-md-3 col-lg-2">
                <label class="form-label">{{ __('accounts.index.filters.plant_site') }}</label>
                <input class="form-control" name="plant" placeholder="{{ __('accounts.index.filters.plant_placeholder') }}" value="{{ request('plant') }}">
              </div>
              <div class="col-12 col-md-4 col-lg-3">
                <label class="form-label">{{ __('accounts.index.filters.category') }}</label>
                <select class="form-select" name="account_type_id">
                  <option value="">{{ __('accounts.index.filters.all') }}</option>
                  @foreach($types as $t)
                    <option value="{{ $t->id }}" {{ (string) request('account_type_id') === (string) $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 col-md-3 col-lg-2">
                <label class="form-label">{{ __('accounts.index.filters.asset_code') }}</label>
                <input class="form-control" name="asset_code" placeholder="{{ __('accounts.index.filters.asset_code_placeholder') }}" value="{{ request('asset_code') }}">
              </div>
              <div class="col-12 col-md-4 col-lg-3">
                <label class="form-label">{{ __('accounts.index.filters.location') }}</label>
                <input class="form-control" name="location" placeholder="{{ __('accounts.index.filters.location_placeholder') }}" value="{{ request('location') }}">
              </div>
              <div class="col-12 col-md-3 col-lg-2">
                <label class="form-label">{{ __('accounts.index.filters.status') }}</label>
                <select class="form-select" name="status">
                  <option value="">{{ __('accounts.index.filters.all') }}</option>
                  @foreach(['active' => __('accounts.status.active'), 'rotated' => __('accounts.status.rotated'), 'deprecated' => __('accounts.status.deprecated')] as $k => $v)
                    <option value="{{ $k }}" {{ request('status')===$k ? 'selected' : '' }}>{{ $v }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> {{ __('common.filter') }}</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.accounts.index') }}">{{ __('common.reset') }}</a>
              </div>
            </form>

            @if($accounts->isEmpty())
              <div class="alert alert-info d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold">{{ __('accounts.index.empty.title') }}</div>
                  <div class="small">{{ __('accounts.index.empty.hint') }}</div>
                </div>
              </div>
            @endif

            <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
              <thead>
                <tr>
                  <th>{{ __('accounts.index.table.category') }}</th>
                  <th>{{ __('accounts.index.table.ip') }}</th>
                  <th>{{ __('accounts.index.table.endpoint') }}</th>
                  <th>{{ __('accounts.index.table.username') }}</th>
                  <th>{{ __('accounts.index.table.password') }}</th>
                  <th>{{ __('accounts.index.table.mac_address') }}</th>
                  <th>{{ __('accounts.index.table.area_location') }}</th>
                  <th>{{ __('accounts.index.table.status') }}</th>
                  <th>{{ __('accounts.index.table.notes') }}</th>
                  <th>{{ __('accounts.index.table.action') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($accounts as $a)
                  @php
                    $typeName = $a->type?->name ?? '';
                    $eps = $a->endpoints ?? collect();
                    $mgmt = $eps->firstWhere('service', 'management') ?? $eps->firstWhere('is_management', true);
                    $web = $eps->firstWhere('service', 'web') ?? $eps->first();
                    $isRouter = ($typeName === 'Router/WiFi');
                    $chosenEp = $isRouter ? $mgmt : $web;
                    $endpointLabel = $isRouter ? 'management' : 'web';
                    $ipLocal = $chosenEp->ip_local ?? null;
                    $ipPublic = $chosenEp->ip_public ?? null;

                    $epProtocol = $chosenEp?->protocol ? strtolower((string) $chosenEp->protocol) : null;
                    $epHasHost = (bool) ($ipLocal || $ipPublic || ($chosenEp?->hostname));
                    $canOpenEndpoint = (bool) ($chosenEp && $epProtocol && in_array($epProtocol, ['http','https'], true) && $epHasHost);
                    $epHasLocal = !empty($ipLocal);
                    $epHasPublic = !empty($ipPublic);

                    $secrets = $a->secrets ?? collect();
                    $activeCurrentSecrets = $secrets->filter(fn($s) => ($s->kind ?? null) === 'current' && (bool) ($s->is_active ?? true))->values();
                    $username = $activeCurrentSecrets->first()?->username;
                    $usernameExtraCount = max(0, $activeCurrentSecrets->count() - 1);

                    $mac = $a->metadata['router_mac_address'] ?? null;
                    $area = $a->metadata['router_area_location'] ?? null;
                    $note = $a->note;
                    $hasSecret = $activeCurrentSecrets->isNotEmpty();
                  @endphp
                  <tr>
                    <td>{{ $a->type?->name ?? '-' }}</td>
                    <td>
                      <div class="small">
                        <div><span class="text-muted">{{ __('accounts.index.table.local') }}:</span> {{ $ipLocal ?: '-' }}</div>
                        <div><span class="text-muted">{{ __('accounts.index.table.public') }}:</span> {{ $ipPublic ?: '-' }}</div>
                      </div>
                    </td>
                    <td>
                      @if($chosenEp)
                        <span class="badge {{ $endpointLabel === 'management' ? 'bg-primary-subtle text-primary' : 'bg-info-subtle text-info' }}">
                          {{ $endpointLabel === 'management' ? __('accounts.index.endpoint_labels.management') : __('accounts.index.endpoint_labels.web') }}
                        </span>
                        @if($canOpenEndpoint)
                          @if($epHasLocal && $epHasPublic)
                            <div class="btn-group ms-1" role="group">
                              <a class="btn btn-sm btn-outline-primary py-0" href="{{ route('admin.accounts.endpoints.open', ['endpointId' => $chosenEp->id, 'target' => 'local']) }}" target="_blank" rel="noopener" title="Buka">
                                <i class="fas fa-arrow-up-right-from-square"></i>
                              </a>
                              <button type="button" class="btn btn-sm btn-outline-primary py-0 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="visually-hidden">{{ __('accounts.index.actions.toggle_dropdown') }}</span>
                              </button>
                              <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                  <a class="dropdown-item" href="{{ route('admin.accounts.endpoints.open', ['endpointId' => $chosenEp->id, 'target' => 'local']) }}" target="_blank" rel="noopener">{{ __('accounts.index.actions.open_local') }}</a>
                                </li>
                                <li>
                                  <a class="dropdown-item" href="{{ route('admin.accounts.endpoints.open', ['endpointId' => $chosenEp->id, 'target' => 'public']) }}" target="_blank" rel="noopener">{{ __('accounts.index.actions.open_public') }}</a>
                                </li>
                              </ul>
                            </div>
                          @else
                            <a class="btn btn-sm btn-outline-primary py-0 ms-1" href="{{ route('admin.accounts.endpoints.open', ['endpointId' => $chosenEp->id]) }}" target="_blank" rel="noopener" title="{{ __('accounts.index.actions.open') }}">
                              <i class="fas fa-arrow-up-right-from-square"></i>
                            </a>
                          @endif
                        @endif
                      @else
                        <span class="badge bg-light text-muted">-</span>
                      @endif
                    </td>
                    <td>
                      @if($usernameExtraCount > 0)
                        <div>
                          <span>{{ $username ?: '-' }}</span>
                          <span class="badge bg-light text-muted">+{{ $usernameExtraCount }}</span>
                        </div>
                      @else
                        {{ $username ?: '-' }}
                      @endif
                    </td>
                    <td>
                      @if($hasSecret)
                        <span class="badge bg-secondary-subtle text-secondary">{{ __('accounts.index.badge.saved') }}</span>
                      @else
                        <span class="badge bg-light text-muted">-</span>
                      @endif
                    </td>
                    <td>{{ $mac ?: '-' }}</td>
                    <td>{{ $area ?: '-' }}</td>
                    <td>
                      @php
                        $statusLabel = [
                          'active' => __('accounts.status.active'),
                          'rotated' => __('accounts.status.rotated'),
                          'deprecated' => __('accounts.status.deprecated'),
                        ][$a->status] ?? ucfirst($a->status);
                      @endphp
                      <span class="badge {{ $a->status==='active' ? 'bg-success-subtle text-success' : ($a->status==='deprecated' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning') }}">
                        {{ $statusLabel }}
                      </span>
                    </td>
                    <td class="text-wrap" style="min-width: 220px; white-space: normal;">
                      {{ $note ?: '-' }}
                    </td>
                    <td>
                      <a href="{{ route('admin.accounts.show', $a->id) }}" class="btn btn-sm btn-info" title="{{ __('common.details') }}"><i class="fas fa-eye"></i></a>
                      <button type="button" class="btn btn-sm btn-warning btn-edit-account" data-id="{{ $a->id }}" {{ $canUpdate ? '' : 'disabled' }} title="{{ $canUpdate ? __('common.edit') : __('common.no_access_update') }}"><i class="fas fa-edit"></i></button>
                      <form action="{{ route('admin.accounts.destroy', $a->id) }}" method="POST" style="display:inline-block" class="form-delete-account">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger btn-delete-account" {{ $canDelete ? '' : 'disabled' }} title="{{ $canDelete ? __('common.delete') : __('common.no_access_delete') }}"><i class="fas fa-trash-alt"></i></button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Create Modal -->
      <div class="modal fade" id="accountCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ __('accounts.index.modals.create_title') }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <form id="account-create-form" action="{{ route('admin.accounts.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">{{ __('accounts.index.form.category') }}</label>
                    <select class="form-select js-select2-modal" name="account_type_id" id="create-account-type" required>
                      <option value="">{{ __('accounts.index.form.select_category') }}</option>
                      @foreach($types as $t)
                        <option value="{{ $t->id }}" data-type-name="{{ $t->name }}">{{ $t->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">{{ __('accounts.index.form.asset') }}</label>
                    <select class="form-select js-select2-modal" name="asset_id" id="create-asset-id">
                      <option value="">{{ __('accounts.index.form.select_asset') }}</option>
                      @foreach($assets as $as)
                        <option value="{{ $as->id }}">{{ $as->asset_code }} - {{ $as->asset_name }} ({{ $as->asset_location }})</option>
                      @endforeach
                    </select>
                    <small class="text-muted">{{ __('accounts.index.form.asset_required_hint') }}</small>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">{{ __('accounts.index.form.status') }}</label>
                    <select class="form-select" name="status" required>
                      <option value="active">{{ __('accounts.status.active') }}</option>
                      <option value="rotated">{{ __('accounts.status.rotated') }}</option>
                      <option value="deprecated">{{ __('accounts.status.deprecated') }}</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">{{ __('accounts.index.form.environment') }}</label>
                    <select class="form-select" name="environment">
                      <option value="">-</option>
                      <option value="prod">{{ __('accounts.index.form.environment_options.prod') }}</option>
                      <option value="nonprod">{{ __('accounts.index.form.environment_options.nonprod') }}</option>
                      <option value="internal">{{ __('accounts.index.form.environment_options.internal') }}</option>
                      <option value="external">{{ __('accounts.index.form.environment_options.external') }}</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">{{ __('accounts.index.form.criticality') }}</label>
                    <select class="form-select" name="criticality">
                      <option value="">-</option>
                      <option value="low">{{ __('accounts.index.form.criticality_options.low') }}</option>
                      <option value="medium">{{ __('accounts.index.form.criticality_options.medium') }}</option>
                      <option value="high">{{ __('accounts.index.form.criticality_options.high') }}</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('accounts.index.form.vendor_installer') }}</label>
                    <input class="form-control" name="vendor_installer" placeholder="{{ __('accounts.index.form.optional') }}">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">{{ __('accounts.index.form.department_owner') }}</label>
                    <input class="form-control" name="department_owner" placeholder="{{ __('accounts.index.form.optional') }}">
                  </div>

                  <div class="col-12"><hr></div>

                  <div class="col-12 account-section account-section-none">
                    <div class="alert alert-light border mb-0 small">{{ __('accounts.index.form.pick_category_hint') }}</div>
                  </div>

                  <!-- Generic fields -->
                  <div class="col-12 account-section account-section-general">
                    <div class="fw-semibold">{{ __('accounts.index.form.general.title') }}</div>
                    <div class="text-muted small">{{ __('accounts.index.form.general.hint_create') }}</div>
                  </div>
                  <div class="col-md-6 account-section account-section-general">
                    <label class="form-label">{{ __('accounts.index.form.general.username') }}</label>
                    <input class="form-control" name="general_username" placeholder="{{ __('accounts.index.form.general.username_optional') }}">
                  </div>
                  <div class="col-md-6 account-section account-section-general">
                    <label class="form-label">{{ __('accounts.index.form.general.password_secret') }}</label>
                    <input class="form-control" name="general_password" type="password" autocomplete="new-password" placeholder="{{ __('accounts.index.form.general.password_required_for_category') }}">
                  </div>

                  <div class="col-12 account-section account-section-general"><hr></div>

                  <!-- CCTV fields -->
                  <div class="col-12 account-section account-section-cctv">
                    <div class="fw-semibold">{{ __('accounts.index.form.cctv.title') }}</div>
                    <div class="text-muted small">{{ __('accounts.index.form.cctv.hint') }}</div>
                  </div>
                  <div class="col-md-3 account-section account-section-cctv">
                    <label class="form-label">{{ __('accounts.index.form.cctv.ip_local') }}</label>
                    <input class="form-control" name="cctv_ip_local" placeholder="192.168.1.10">
                  </div>
                  <div class="col-md-3 account-section account-section-cctv">
                    <label class="form-label">{{ __('accounts.index.form.cctv.ip_public') }}</label>
                    <input class="form-control" name="cctv_ip_public" placeholder="103.x.x.x">
                  </div>
                  <div class="col-md-3 account-section account-section-cctv">
                    <label class="form-label">{{ __('accounts.index.form.cctv.web_port') }}</label>
                    <input class="form-control" type="number" name="cctv_port_web" min="1" max="65535">
                  </div>
                  <div class="col-md-3 account-section account-section-cctv">
                    <label class="form-label">{{ __('accounts.index.form.cctv.hikconnect_port') }}</label>
                    <input class="form-control" type="number" name="cctv_port_hikconnect" min="1" max="65535">
                  </div>
                  <div class="col-12 account-section account-section-cctv">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <div class="fw-semibold">Daftar User CCTV</div>
                        <div class="text-muted small">{{ __('accounts.index.form.cctv.users_hint_create') }}</div>
                      </div>
                      <button type="button" class="btn btn-sm btn-outline-primary btn-add-cctv-user" data-target="#create-cctv-users-container">
                        <i class="fas fa-plus"></i> {{ __('accounts.index.form.cctv.add_user') }}
                      </button>
                    </div>
                  </div>
                  <div class="col-12 account-section account-section-cctv">
                    <label class="form-label">{{ __('accounts.index.form.cctv.bulk_paste') }}</label>
                    <div class="input-group">
                      <textarea class="form-control" id="create-cctv-users-bulk" rows="2" placeholder="{{ __('accounts.index.form.cctv.bulk_placeholder') }}"></textarea>
                      <button type="button" class="btn btn-outline-secondary btn-parse-cctv-users" data-target="#create-cctv-users-container" data-source="#create-cctv-users-bulk">{{ __('accounts.index.form.cctv.parse') }}</button>
                    </div>
                  </div>
                  <div class="col-12 account-section account-section-cctv" id="create-cctv-users-container">
                    <div class="row g-2 align-items-end cctv-user-row">
                      <div class="col-md-3">
                        <label class="form-label">{{ __('accounts.index.form.cctv.role_label') }}</label>
                        <input class="form-control" name="cctv_users[0][label]" placeholder="{{ __('accounts.index.form.cctv.role_placeholder') }}">
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">{{ __('accounts.index.form.cctv.username') }}</label>
                        <input class="form-control" name="cctv_users[0][username]" placeholder="admin">
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">{{ __('accounts.index.form.cctv.password') }}</label>
                        <input class="form-control cctv-user-password" name="cctv_users[0][password]" type="password" autocomplete="new-password" placeholder="{{ __('accounts.index.form.cctv.password_required') }}">
                      </div>
                      <div class="col-md-1 d-grid">
                        <button type="button" class="btn btn-outline-danger btn-remove-cctv-user" title="{{ __('accounts.index.form.cctv.remove') }}"><i class="fas fa-trash"></i></button>
                      </div>
                    </div>
                  </div>

                  <div class="col-12 account-section account-section-cctv"><hr></div>

                  <!-- Router fields -->
                  <div class="col-12 account-section account-section-router">
                    <div class="fw-semibold">{{ __('accounts.index.form.router.title') }}</div>
                    <div class="text-muted small">{{ __('accounts.index.form.router.hint') }}</div>
                  </div>
                  <div class="col-md-4 account-section account-section-router">
                    <label class="form-label">{{ __('accounts.index.form.router.area_location') }}</label>
                    <input class="form-control" name="router_area_location" list="router-area-options" placeholder="{{ __('accounts.index.form.router.area_location_placeholder') }}">
                    <datalist id="router-area-options">
                      <option value="Main Office"></option>
                      <option value="Toner"></option>
                      <option value="Resin"></option>
                      <option value="WH"></option>
                      <option value="Produksi"></option>
                      <option value="Gudang"></option>
                      <option value="Workshop"></option>
                      <option value="Security"></option>
                    </datalist>
                  </div>
                  <div class="col-md-4 account-section account-section-router">
                    <label class="form-label">{{ __('accounts.index.form.router.mac_address') }}</label>
                    <input class="form-control" name="router_mac" placeholder="00:11:22:33:44:55">
                  </div>
                  <div class="col-md-4 account-section account-section-router">
                    <label class="form-label">{{ __('accounts.index.form.router.protocol') }}</label>
                    <select class="form-select" name="router_protocol">
                      <option value="">-</option>
                      <option value="http">HTTP</option>
                      <option value="https">HTTPS</option>
                      <option value="ssh">SSH</option>
                      <option value="telnet">Telnet</option>
                      <option value="vpn">VPN</option>
                    </select>
                  </div>

                  <div class="col-md-4 account-section account-section-router">
                    <label class="form-label">{{ __('accounts.index.form.router.ip_local') }}</label>
                    <input class="form-control" name="router_ip_local" placeholder="192.168.1.1">
                  </div>
                  <div class="col-md-4 account-section account-section-router">
                    <label class="form-label">{{ __('accounts.index.form.router.ip_public') }}</label>
                    <input class="form-control" name="router_ip_public" placeholder="{{ __('accounts.index.form.optional') }}">
                  </div>
                  <div class="col-md-4 account-section account-section-router">
                    <label class="form-label">{{ __('accounts.index.form.router.port') }}</label>
                    <input class="form-control" type="number" name="router_port" min="1" max="65535">
                  </div>

                  <div class="col-md-4 account-section account-section-router">
                    <label class="form-label">{{ __('accounts.index.form.router.default_username') }}</label>
                    <input class="form-control" name="router_default_username">
                  </div>
                  <div class="col-md-4 account-section account-section-router">
                    <label class="form-label">{{ __('accounts.index.form.router.default_password') }}</label>
                    <input class="form-control" name="router_default_password" type="password" autocomplete="new-password">
                  </div>
                  <div class="col-md-4 account-section account-section-router">
                    <label class="form-label">{{ __('accounts.index.form.router.current_username') }}</label>
                    <input class="form-control" name="router_current_username">
                  </div>

                  <div class="col-12 account-section account-section-router">
                    <label class="form-label">{{ __('accounts.index.form.router.current_password') }}</label>
                    <input class="form-control" name="router_current_password" type="password" autocomplete="new-password">
                  </div>

                  <div class="col-12">
                    <label class="form-label">{{ __('accounts.index.form.notes') }}</label>
                    <textarea class="form-control" name="note" rows="2"></textarea>
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('accounts.index.form.close') }}</button>
              <button type="submit" form="account-create-form" class="btn btn-primary" {{ $canCreate ? '' : 'disabled' }}>{{ __('accounts.index.form.save') }}</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Edit Modal -->
      <div class="modal fade" id="accountEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ __('accounts.index.modals.edit_title') }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="alert alert-danger d-none" id="account-edit-error"></div>
              <form id="account-edit-form" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">{{ __('accounts.index.form.category') }}</label>
                    <select class="form-select js-select2-modal" name="account_type_id" id="edit-account-type" required>
                      <option value="">{{ __('accounts.index.form.select_category') }}</option>
                      @foreach($types as $t)
                        <option value="{{ $t->id }}" data-type-name="{{ $t->name }}">{{ $t->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">{{ __('accounts.index.form.asset') }}</label>
                    <select class="form-select js-select2-modal" name="asset_id" id="edit-asset-id">
                      <option value="">{{ __('accounts.index.form.select_asset') }}</option>
                      @foreach($assets as $as)
                        <option value="{{ $as->id }}">{{ $as->asset_code }} - {{ $as->asset_name }} ({{ $as->asset_location }})</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">{{ __('accounts.index.form.status') }}</label>
                    <select class="form-select" name="status" id="edit-status" required>
                      <option value="active">{{ __('accounts.status.active') }}</option>
                      <option value="rotated">{{ __('accounts.status.rotated') }}</option>
                      <option value="deprecated">{{ __('accounts.status.deprecated') }}</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">{{ __('accounts.index.form.environment') }}</label>
                    <select class="form-select" name="environment" id="edit-environment">
                      <option value="">-</option>
                      <option value="prod">{{ __('accounts.index.form.environment_options.prod') }}</option>
                      <option value="nonprod">{{ __('accounts.index.form.environment_options.nonprod') }}</option>
                      <option value="internal">{{ __('accounts.index.form.environment_options.internal') }}</option>
                      <option value="external">{{ __('accounts.index.form.environment_options.external') }}</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">{{ __('accounts.index.form.criticality') }}</label>
                    <select class="form-select" name="criticality" id="edit-criticality">
                      <option value="">-</option>
                      <option value="low">{{ __('accounts.index.form.criticality_options.low') }}</option>
                      <option value="medium">{{ __('accounts.index.form.criticality_options.medium') }}</option>
                      <option value="high">{{ __('accounts.index.form.criticality_options.high') }}</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">{{ __('accounts.index.form.vendor_installer') }}</label>
                    <input class="form-control" name="vendor_installer" id="edit-vendor">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">{{ __('accounts.index.form.department_owner') }}</label>
                    <input class="form-control" name="department_owner" id="edit-dept-owner">
                  </div>

                  <div class="col-12"><hr></div>

                  <div class="col-12 account-section account-section-none">
                    <div class="alert alert-light border mb-0 small">{{ __('accounts.index.form.pick_category_hint') }}</div>
                  </div>

                  <div class="col-12 account-section account-section-general">
                    <div class="fw-semibold">{{ __('accounts.index.form.general.title') }}</div>
                    <div class="text-muted small">{{ __('accounts.index.form.general.hint_edit') }}</div>
                  </div>
                  <div class="col-md-6 account-section account-section-general">
                    <label class="form-label">{{ __('accounts.index.form.general.current_username') }}</label>
                    <input class="form-control" name="general_username" id="edit-general-username">
                  </div>
                  <div class="col-md-6 account-section account-section-general">
                    <label class="form-label">{{ __('accounts.index.form.general.password_secret') }}</label>
                    <input class="form-control" type="password" placeholder="{{ __('accounts.index.form.general.password_use_rotate') }}" disabled>
                  </div>

                  <div class="col-12 account-section account-section-general"><hr></div>
                  <div class="col-12 account-section account-section-cctv"><div class="fw-semibold">{{ __('accounts.index.form.cctv.title') }}</div></div>
                  <div class="col-md-3 account-section account-section-cctv"><label class="form-label">{{ __('accounts.index.form.cctv.ip_local') }}</label><input class="form-control" name="cctv_ip_local" id="edit-cctv-ip-local"></div>
                  <div class="col-md-3 account-section account-section-cctv"><label class="form-label">{{ __('accounts.index.form.cctv.ip_public') }}</label><input class="form-control" name="cctv_ip_public" id="edit-cctv-ip-public"></div>
                  <div class="col-md-3 account-section account-section-cctv"><label class="form-label">{{ __('accounts.index.form.cctv.web_port') }}</label><input class="form-control" type="number" name="cctv_port_web" id="edit-cctv-port-web" min="1" max="65535"></div>
                  <div class="col-md-3 account-section account-section-cctv"><label class="form-label">{{ __('accounts.index.form.cctv.hikconnect_port') }}</label><input class="form-control" type="number" name="cctv_port_hikconnect" id="edit-cctv-port-hik" min="1" max="65535"></div>
                  <div class="col-12 account-section account-section-cctv">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <div class="fw-semibold">{{ __('accounts.index.form.cctv.users_title') }} ({{ __('accounts.index.form.optional') }})</div>
                        <div class="text-muted small">{{ __('accounts.index.form.cctv.users_hint_edit') }}</div>
                      </div>
                      <button type="button" class="btn btn-sm btn-outline-primary btn-add-cctv-user" data-target="#edit-cctv-users-container">
                        <i class="fas fa-plus"></i> {{ __('accounts.index.form.cctv.add_user') }}
                      </button>
                    </div>
                  </div>
                  <div class="col-12 account-section account-section-cctv">
                    <label class="form-label">{{ __('accounts.index.form.cctv.bulk_paste') }}</label>
                    <div class="input-group">
                      <textarea class="form-control" id="edit-cctv-users-bulk" rows="2" placeholder="{{ __('accounts.index.form.cctv.bulk_placeholder') }}"></textarea>
                      <button type="button" class="btn btn-outline-secondary btn-parse-cctv-users" data-target="#edit-cctv-users-container" data-source="#edit-cctv-users-bulk">{{ __('accounts.index.form.cctv.parse') }}</button>
                    </div>
                  </div>
                  <div class="col-12 account-section account-section-cctv" id="edit-cctv-users-container"></div>

                  <div class="col-12 account-section account-section-cctv"><hr></div>
                  <div class="col-12 account-section account-section-router"><div class="fw-semibold">{{ __('accounts.index.form.router.title') }}</div></div>
                  <div class="col-md-4 account-section account-section-router">
                    <label class="form-label">{{ __('accounts.index.form.router.area_location') }}</label>
                    <input class="form-control" name="router_area_location" id="edit-router-area" list="router-area-options" placeholder="{{ __('accounts.index.form.router.area_location_placeholder') }}">
                  </div>
                  <div class="col-md-4 account-section account-section-router"><label class="form-label">{{ __('accounts.index.form.router.mac_address') }}</label><input class="form-control" name="router_mac" id="edit-router-mac"></div>
                  <div class="col-md-4 account-section account-section-router"><label class="form-label">{{ __('accounts.index.form.router.protocol') }}</label>
                    <select class="form-select" name="router_protocol" id="edit-router-protocol">
                      <option value="">-</option>
                      <option value="http">HTTP</option>
                      <option value="https">HTTPS</option>
                      <option value="ssh">SSH</option>
                      <option value="telnet">Telnet</option>
                      <option value="vpn">VPN</option>
                    </select>
                  </div>
                  <div class="col-md-4 account-section account-section-router"><label class="form-label">{{ __('accounts.index.form.router.ip_local') }}</label><input class="form-control" name="router_ip_local" id="edit-router-ip-local"></div>
                  <div class="col-md-4 account-section account-section-router"><label class="form-label">{{ __('accounts.index.form.router.ip_public') }}</label><input class="form-control" name="router_ip_public" id="edit-router-ip-public"></div>
                  <div class="col-md-4 account-section account-section-router"><label class="form-label">{{ __('accounts.index.form.router.port') }}</label><input class="form-control" type="number" name="router_port" id="edit-router-port" min="1" max="65535"></div>

                  <div class="col-md-6 account-section account-section-router"><label class="form-label">{{ __('accounts.index.form.router.current_username') }}</label><input class="form-control" name="router_current_username" id="edit-router-current-username"></div>
                  <div class="col-md-6 account-section account-section-router"><label class="form-label">{{ __('accounts.index.form.router.current_password') }}</label><input class="form-control" type="password" placeholder="{{ __('accounts.index.form.general.password_use_rotate') }}" disabled></div>

                  <div class="col-12"><label class="form-label">{{ __('accounts.index.form.notes') }}</label><textarea class="form-control" name="note" id="edit-note" rows="2"></textarea></div>
                </div>
              </form>
              <div class="form-text mt-2">{{ __('accounts.index.form.rotate_hint') }}</div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('accounts.index.form.close') }}</button>
              <button type="submit" form="account-edit-form" class="btn btn-primary" {{ $canUpdate ? '' : 'disabled' }}>{{ __('accounts.index.form.save_changes') }}</button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
@endsection

@section('js')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
  <script src="{{ asset('assets/js/table/datatable.init.js') }}"></script>
  <script type="module" src="{{ asset('assets/js/app.js') }}"></script>

  <script>
    $(document).ready(function() {
      const getSelectedTypeName = ($select) => {
        const opt = $select.find('option:selected').get(0);
        if (!opt) return '';
        return (opt.dataset && opt.dataset.typeName) ? opt.dataset.typeName : ($(opt).text() || '').trim();
      };

      const typeNameToSection = (typeName) => {
        if (!typeName) return 'none';
        if (typeName === 'CCTV') return 'cctv';
        if (typeName === 'Router/WiFi') return 'router';
        return 'general';
      };

      const clearSectionInputs = ($sectionRoot) => {
        if (!$sectionRoot || !$sectionRoot.length) return;

        $sectionRoot.find('input, select, textarea').each(function() {
          const el = this;
          const $el = $(el);
          const tag = (el.tagName || '').toLowerCase();
          const type = ($el.attr('type') || '').toLowerCase();

          if (type === 'checkbox' || type === 'radio') {
            $el.prop('checked', false);
            return;
          }
          if (type === 'file') {
            $el.val(null);
            return;
          }

          if (tag === 'select') {
            $el.val('').trigger('change');
            return;
          }

          $el.val('');
        });
      };

      const applySectionVisibility = ($modal, typeName) => {
        const section = typeNameToSection(typeName);
        const $all = $modal.find('.account-section');

        // Hide all sections and disable their inputs to avoid submitting irrelevant fields
        $all.addClass('d-none').find('input, select, textarea').prop('disabled', true);

        // Clear values for sections that will be hidden (prevents "nyangkut" when switching category)
        $all.not(`.account-section-${section}`).each(function() {
          clearSectionInputs($(this));
        });

        // Show selected section and enable inputs
        $modal.find(`.account-section-${section}`).removeClass('d-none').find('input, select, textarea').prop('disabled', false);

        // Asset required only for CCTV/Router
        const $asset = $modal.find('select[name="asset_id"]');
        if ($asset.length) $asset.prop('required', section === 'cctv' || section === 'router');

        // Required secret fields only on create modal
        if ($modal.is('#accountCreateModal')) {
          const $routerPass = $modal.find('input[name="router_current_password"]');
          const $generalPass = $modal.find('input[name="general_password"]');
          const $cctvFirstPass = $modal.find('.cctv-user-password').first();
          if ($cctvFirstPass.length) $cctvFirstPass.prop('required', section === 'cctv');
          $routerPass.prop('required', section === 'router');
          $generalPass.prop('required', section === 'general');
        }
      };

      const reindexCctvUserRows = ($container) => {
        const $rows = $container.find('.cctv-user-row');
        $rows.each(function(idx) {
          const $row = $(this);
          $row.find('input, textarea, select').each(function() {
            const $inp = $(this);
            const name = $inp.attr('name') || '';
            const replaced = name
              .replace(/cctv_users\[\d+\]\[label\]/, `cctv_users[${idx}][label]`)
              .replace(/cctv_users\[\d+\]\[username\]/, `cctv_users[${idx}][username]`)
              .replace(/cctv_users\[\d+\]\[password\]/, `cctv_users[${idx}][password]`);
            if (replaced !== name) $inp.attr('name', replaced);
          });
        });
      };

      const i18nCctvRow = {
        roleLabel: @json(__('accounts.index.form.cctv.role_label')),
        rolePlaceholder: @json(__('accounts.index.form.cctv.role_placeholder')),
        usernameLabel: @json(__('accounts.index.form.cctv.username')),
        usernamePlaceholder: @json(__('accounts.index.form.cctv.username_placeholder')),
        passwordLabel: @json(__('accounts.index.form.cctv.password')),
        passwordRequired: @json(__('accounts.index.form.cctv.password_required')),
        remove: @json(__('accounts.index.form.cctv.remove')),
      };

      const buildCctvUserRowHtml = (row) => {
        const label = (row && row.label) ? row.label : '';
        const username = (row && row.username) ? row.username : '';
        const password = (row && row.password) ? row.password : '';
        return `
          <div class="row g-2 align-items-end cctv-user-row">
            <div class="col-md-3">
              <label class="form-label">${i18nCctvRow.roleLabel}</label>
              <input class="form-control" name="cctv_users[0][label]" placeholder="${i18nCctvRow.rolePlaceholder}" value="${_.escape(label)}">
            </div>
            <div class="col-md-4">
              <label class="form-label">${i18nCctvRow.usernameLabel}</label>
              <input class="form-control" name="cctv_users[0][username]" placeholder="${i18nCctvRow.usernamePlaceholder}" value="${_.escape(username)}">
            </div>
            <div class="col-md-4">
              <label class="form-label">${i18nCctvRow.passwordLabel}</label>
              <input class="form-control cctv-user-password" name="cctv_users[0][password]" type="password" autocomplete="new-password" placeholder="${i18nCctvRow.passwordRequired}" value="${_.escape(password)}">
            </div>
            <div class="col-md-1 d-grid">
              <button type="button" class="btn btn-outline-danger btn-remove-cctv-user" title="${i18nCctvRow.remove}"><i class="fas fa-trash"></i></button>
            </div>
          </div>
        `;
      };

      const ensureCctvUsersContainerHasRow = ($container) => {
        if (!$container.length) return;
        if ($container.find('.cctv-user-row').length === 0) {
          $container.append(buildCctvUserRowHtml({}));
          reindexCctvUserRows($container);
        }

        // On create modal, require at least 1 password when CCTV is selected
        if ($container.attr('id') === 'create-cctv-users-container') {
          $container.find('.cctv-user-password').first().prop('required', true);
        }
      };

      const parseCctvUsersBulk = (text) => {
        const lines = (text || '').split(/\r?\n/).map(l => l.trim()).filter(Boolean);
        const out = [];
        for (const line of lines) {
          let parts = [];
          if (line.includes('|')) parts = line.split('|');
          else if (line.includes(';')) parts = line.split(';');
          else if (line.includes(',')) parts = line.split(',');
          else if (line.includes(':')) parts = line.split(':');
          else parts = [line];

          const username = (parts[0] || '').trim();
          const password = (parts[1] || '').trim();
          const label = (parts[2] || '').trim();
          if (!username && !password && !label) continue;
          out.push({ username, password, label });
        }
        return out;
      };

      // Lodash may not exist; provide a minimal escape fallback
      const _ = window._ || {
        escape: (s) => (String(s || ''))
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#39;'),
      };

      $(document).on('click', '.btn-add-cctv-user', function() {
        const target = $(this).data('target');
        const $container = $(target);
        $container.append(buildCctvUserRowHtml({}));
        reindexCctvUserRows($container);
        ensureCctvUsersContainerHasRow($container);
      });

      $(document).on('click', '.btn-remove-cctv-user', function() {
        const $row = $(this).closest('.cctv-user-row');
        const $container = $row.parent();
        const rowCount = $container.find('.cctv-user-row').length;
        if (rowCount <= 1) {
          $row.find('input').val('');
        } else {
          $row.remove();
        }
        reindexCctvUserRows($container);
        ensureCctvUsersContainerHasRow($container);
      });

      $(document).on('click', '.btn-parse-cctv-users', function() {
        const target = $(this).data('target');
        const source = $(this).data('source');
        const $container = $(target);
        const text = $(source).val();
        const rows = parseCctvUsersBulk(text);
        if (!rows.length) return;
        $container.empty();
        rows.forEach(r => $container.append(buildCctvUserRowHtml(r)));
        reindexCctvUserRows($container);
        ensureCctvUsersContainerHasRow($container);
      });

      const initSelect2InModal = ($modal) => {
        const $sel = $modal.find('.js-select2-modal');
        if (!$sel.length || !$.fn.select2) return;
        $sel.each(function() {
          const $el = $(this);
          if ($el.hasClass('select2-hidden-accessible')) return;
          $el.select2({ theme: 'bootstrap-5', width: '100%', dropdownParent: $modal, allowClear: true });
        });
      };

      const $createModal = $('#accountCreateModal');
      const $editModal = $('#accountEditModal');
      initSelect2InModal($createModal);
      initSelect2InModal($editModal);

      $createModal.on('shown.bs.modal', function() {
        initSelect2InModal($createModal);
        applySectionVisibility($createModal, getSelectedTypeName($('#create-account-type')));
        ensureCctvUsersContainerHasRow($('#create-cctv-users-container'));
      });
      $editModal.on('shown.bs.modal', function() {
        initSelect2InModal($editModal);
        applySectionVisibility($editModal, getSelectedTypeName($('#edit-account-type')));
        ensureCctvUsersContainerHasRow($('#edit-cctv-users-container'));
      });

      $('#create-account-type').on('change', function() {
        applySectionVisibility($createModal, getSelectedTypeName($(this)));
        ensureCctvUsersContainerHasRow($('#create-cctv-users-container'));
      });
      $('#edit-account-type').on('change', function() {
        applySectionVisibility($editModal, getSelectedTypeName($(this)));
        ensureCctvUsersContainerHasRow($('#edit-cctv-users-container'));
      });

      $(document).on('click', '.btn-edit-account', async function() {
        const id = $(this).data('id');
        const $err = $('#account-edit-error');
        $err.addClass('d-none').text('');

        try {
          const res = await fetch(`{{ url('/admin/accounts') }}/${id}/json`, { headers: { 'Accept': 'application/json' } });
          if (!res.ok) throw new Error(@json(__('accounts.index.swal.fetch_account_error')));
          const data = await res.json();

          const form = document.getElementById('account-edit-form');
          form.action = `{{ url('/admin/accounts') }}/${id}`;

          $('#edit-account-type').val(data.account_type_id || '').trigger('change');
          $('#edit-asset-id').val(data.asset_id || '').trigger('change');
          $('#edit-status').val(data.status || 'active');
          $('#edit-environment').val(data.environment || '');
          $('#edit-criticality').val(data.criticality || '');
          $('#edit-vendor').val(data.vendor_installer || '');
          $('#edit-dept-owner').val(data.department_owner || '');
          $('#edit-note').val(data.note || '');

          // endpoints mapping
          const eps = Array.isArray(data.endpoints) ? data.endpoints : [];
          const web = eps.find(e => e.service === 'web') || {};
          const hik = eps.find(e => e.service === 'hikconnect') || {};
          const mgmt = eps.find(e => e.service === 'management') || {};

          $('#edit-cctv-ip-local').val(web.ip_local || '');
          $('#edit-cctv-ip-public').val(web.ip_public || '');
          $('#edit-cctv-port-web').val(web.port || '');
          $('#edit-cctv-port-hik').val(hik.port || '');

          // username mapping from secrets
          const secrets = Array.isArray(data.secrets) ? data.secrets : [];
          const typeName = (data.account_type || '').toString();

          const findSecret = (pred) => secrets.find(pred) || {};
          const anyCurrent = findSecret(s => s.kind === 'current');

          const cctvCurrent = typeName === 'CCTV'
            ? (findSecret(s => s.kind === 'current' && (s.label === 'admin' || !s.label)) || anyCurrent)
            : {};

          const routerCurrent = typeName === 'Router/WiFi'
            ? (findSecret(s => s.kind === 'current' && (s.label === 'current' || !s.label)) || anyCurrent)
            : {};

          $('#edit-general-username').val((typeName !== 'CCTV' && typeName !== 'Router/WiFi') ? (anyCurrent.username || '') : '');
          // CCTV can have many users; edit modal only supports adding new users (see detail for existing)
          $('#edit-cctv-users-container').empty();
          ensureCctvUsersContainerHasRow($('#edit-cctv-users-container'));

          $('#edit-router-mac').val((data.metadata && data.metadata.router_mac_address) ? data.metadata.router_mac_address : '');
          $('#edit-router-area').val((data.metadata && data.metadata.router_area_location) ? data.metadata.router_area_location : '');
          $('#edit-router-ip-local').val(mgmt.ip_local || '');
          $('#edit-router-ip-public').val(mgmt.ip_public || '');
          $('#edit-router-protocol').val((data.metadata && data.metadata.router_management_protocol) ? data.metadata.router_management_protocol : (mgmt.protocol || ''));
          $('#edit-router-port').val(mgmt.port || '');
          $('#edit-router-current-username').val(routerCurrent.username || '');

          // ensure UI sections match type
          applySectionVisibility($editModal, typeName);

          if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('accountEditModal')).show();
          } else {
            $editModal.modal('show');
          }
        } catch (e) {
          $err.removeClass('d-none').text(e.message || @json(__('accounts.index.swal.generic_error')));
        }
      });

      // Delete confirmation
      $(document).on('click', '.btn-delete-account', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
          title: @json(__('accounts.index.swal.confirm_delete_title')),
          text: @json(__('accounts.index.swal.confirm_delete_text')),
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: @json(__('accounts.index.swal.confirm_delete_yes')),
          cancelButtonText: @json(__('accounts.index.swal.confirm_delete_cancel'))
        }).then((r) => { if (r.isConfirmed) form.submit(); });
      });
    });
  </script>
@endsection
