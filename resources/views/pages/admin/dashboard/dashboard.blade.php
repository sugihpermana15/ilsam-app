@extends('layouts.master')

@php
  $isUser = auth()->check() && (
    (auth()->user()->role?->role_name ?? null) === 'Users' ||
    (int) auth()->user()->role_id === 3
  );
@endphp

@section('title', $isUser ? 'Ilsam - Dashboard Karyawan' : 'Ilsam - Dashboard')
@section('title-sub', 'Dashboard')
@section('pagetitle', $isUser ? 'Dashboard Karyawan' : 'Dashboard')

@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    .kpi-card {
      border: 1px solid rgba(15, 23, 42, 0.14) !important;
      box-shadow: none;
    }

    .kpi-card:hover {
      border-color: rgba(15, 23, 42, 0.22) !important;
    }

    .kpi-bg-img {
      opacity: .08;
      pointer-events: none;
    }
  </style>
@endsection

@section('content')
  @if($isUser)
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div
            class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
            <h5 class="card-title mb-0">Dashboard Karyawan</h5>
            <div class="text-muted small">
              Halo, {{ auth()->user()->name }}
            </div>
          </div>

          <div class="card-body">
            <div class="row g-3 row-cols-1 row-cols-md-2 row-cols-xl-4">
              <div class="col">
                <div class="card overflow-hidden h-100 kpi-card">
                  <div class="card-body position-relative z-1">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <div class="text-muted">Status Akun</div>
                        <div class="fs-6 fw-semibold">{{ auth()->user()->role?->role_name ?? '-' }}</div>
                      </div>
                      <div class="text-warning fs-3"><i class="fas fa-user"></i></div>
                    </div>
                  </div>
                  <img src="{{ asset('assets/img/dashboard/academy-bg4.png') }}" alt=""
                    class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  @else
    @php
      $canManage = auth()->check() && (int) auth()->user()->role_id === 1;
      $canManageEmployee = auth()->check() && in_array((int) auth()->user()->role_id, [1, 2], true);

      $tabOverrides = auth()->check() && is_array(auth()->user()->dashboard_tabs) ? auth()->user()->dashboard_tabs : null;
      $tabAllowed = function (string $key) use ($tabOverrides): bool {
        return $tabOverrides === null || in_array($key, $tabOverrides, true);
      };

      $showEmployee = isset($employee) && !empty($employee['kpi']);
      $showAsset = !empty($permissions['asset']) && (
        !empty($permissions['asset']['kpi']) ||
        !empty($permissions['asset']['charts']) ||
        !empty($permissions['asset']['recent'])
      );

      $showStamps = auth()->check() && (
        \App\Support\MenuAccess::can(auth()->user(), 'stamps_transactions', 'read') ||
        \App\Support\MenuAccess::can(auth()->user(), 'stamps_master', 'read')
      );

      $showUniforms = auth()->check() && (
        \App\Support\MenuAccess::can(auth()->user(), 'uniforms_stock', 'read') ||
        \App\Support\MenuAccess::can(auth()->user(), 'uniforms_distribution', 'read') ||
        \App\Support\MenuAccess::can(auth()->user(), 'uniforms_reports', 'read') ||
        \App\Support\MenuAccess::can(auth()->user(), 'uniforms_master', 'read') ||
        \App\Support\MenuAccess::can(auth()->user(), 'uniforms_variants', 'read') ||
        \App\Support\MenuAccess::can(auth()->user(), 'uniforms_lots', 'read') ||
        \App\Support\MenuAccess::can(auth()->user(), 'uniforms_entitlements', 'read')
      );

      $showDocuments = !empty($showDocuments);

      // Apply per-user tab access overrides.
      $showAsset = $tabAllowed('asset') && $showAsset;
      $showStamps = $tabAllowed('stamps') && $showStamps;
      $showUniforms = $tabAllowed('uniforms') && $showUniforms;
      $showDocuments = $tabAllowed('documents') && $showDocuments;
      $showEmployee = $tabAllowed('employee') && $showEmployee;

      $tabs = [];
      if ($showAsset)
        $tabs[] = 'asset';
      if ($showStamps)
        $tabs[] = 'stamps';
      if ($showUniforms)
        $tabs[] = 'uniforms';
      if ($showDocuments)
        $tabs[] = 'documents';
      if ($showEmployee)
        $tabs[] = 'employee';

      $activeTab = in_array(($tab ?? ''), $tabs, true) ? $tab : ($tabs[0] ?? null);
    @endphp

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div
            class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
            <h5 class="card-title mb-0">Dashboard</h5>
          </div>

          <div class="card-body">
            @if(!$showAsset && !$showStamps && !$showUniforms && !$showEmployee && !$showDocuments)
              <div class="alert alert-warning mb-0">
                Anda tidak memiliki akses untuk melihat KPI/Chart Dashboard.
              </div>
            @else
              @if(count($tabs) > 1)
                <ul class="nav nav-tabs mb-3" role="tablist">
                  @if($showAsset)
                    <li class="nav-item" role="presentation">
                      <button class="nav-link {{ $activeTab === 'asset' ? 'active' : '' }}" data-bs-toggle="tab"
                        data-bs-target="#tab-asset" type="button" role="tab">
                        <i class="fas fa-chart-line me-1"></i> Asset
                      </button>
                    </li>
                  @endif
                  @if($showStamps)
                    <li class="nav-item" role="presentation">
                      <button class="nav-link {{ $activeTab === 'stamps' ? 'active' : '' }}" data-bs-toggle="tab"
                        data-bs-target="#tab-stamps" type="button" role="tab">
                        <i class="fas fa-stamp me-1"></i> Materai
                      </button>
                    </li>
                  @endif
                  @if($showUniforms)
                    <li class="nav-item" role="presentation">
                      <button class="nav-link {{ $activeTab === 'uniforms' ? 'active' : '' }}" data-bs-toggle="tab"
                        data-bs-target="#tab-uniforms" type="button" role="tab">
                        <i class="fas fa-shirt me-1"></i> Seragam
                      </button>
                    </li>
                  @endif
                  @if($showEmployee)
                    <li class="nav-item" role="presentation">
                      <button class="nav-link {{ $activeTab === 'employee' ? 'active' : '' }}" data-bs-toggle="tab"
                        data-bs-target="#tab-employee" type="button" role="tab">
                        <i class="fas fa-users me-1"></i> Karyawan
                      </button>
                    </li>
                  @endif
                  @if($showDocuments)
                    <li class="nav-item" role="presentation">
                      <button class="nav-link {{ $activeTab === 'documents' ? 'active' : '' }}" data-bs-toggle="tab"
                        data-bs-target="#tab-documents" type="button" role="tab">
                        <i class="fas fa-folder-open me-1"></i> Arsip Berkas
                      </button>
                    </li>
                  @endif
                </ul>
              @endif

              <div class="tab-content">
                @if($showAsset)
                  <div class="tab-pane fade {{ $activeTab === 'asset' ? 'show active' : '' }}" id="tab-asset" role="tabpanel">
                    @if(!empty($permissions['asset']['kpi']))
                      <div class="row g-3 row-cols-1 row-cols-md-2 row-cols-xl-3 row-cols-xxl-5">
                        <div class="col">
                          <div class="card overflow-hidden h-100 kpi-card">
                            <div class="card-body position-relative z-1">
                              <div class="d-flex justify-content-between align-items-center">
                                <div>
                                  <div class="text-muted">Total Assets</div>
                                  <div class="fs-4 fw-semibold">{{ number_format($asset['kpi']['total_assets'] ?? 0) }}</div>
                                </div>
                                <div class="text-primary fs-3"><i class="fas fa-boxes-stacked"></i></div>
                              </div>
                            </div>
                            <img src="{{ asset('assets/img/dashboard/academy-bg1.png') }}" alt=""
                              class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                          </div>
                        </div>

                        <div class="col">
                          <div class="card overflow-hidden h-100 kpi-card">
                            <div class="card-body position-relative z-1">
                              <div class="d-flex justify-content-between align-items-center">
                                <div>
                                  <div class="text-muted">Active</div>
                                  <div class="fs-4 fw-semibold">{{ number_format($asset['kpi']['active_assets'] ?? 0) }}</div>
                                </div>
                                <div class="text-success fs-3"><i class="fas fa-circle-check"></i></div>
                              </div>
                            </div>
                            <img src="{{ asset('assets/img/dashboard/academy-bg2.png') }}" alt=""
                              class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                          </div>
                        </div>

                        <div class="col">
                          <div class="card overflow-hidden h-100 kpi-card">
                            <div class="card-body position-relative z-1">
                              <div class="d-flex justify-content-between align-items-center">
                                <div>
                                  <div class="text-muted">Inactive / Sold / Disposed</div>
                                  <div class="fs-4 fw-semibold">{{ number_format($asset['kpi']['inactive_assets'] ?? 0) }}</div>
                                </div>
                                <div class="text-warning fs-3"><i class="fas fa-circle-minus"></i></div>
                              </div>
                            </div>
                            <img src="{{ asset('assets/img/dashboard/academy-bg3.png') }}" alt=""
                              class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                          </div>
                        </div>

                        <div class="col">
                          <div class="card overflow-hidden h-100 kpi-card">
                            <div class="card-body position-relative z-1">
                              <div class="d-flex justify-content-between align-items-center">
                                <div>
                                  <div class="text-muted">Total Value</div>
                                  <div class="fs-4 fw-semibold">Rp
                                    {{ number_format((float) ($asset['kpi']['total_value'] ?? 0), 0, ',', '.') }}
                                  </div>
                                </div>
                                <div class="text-info fs-3"><i class="fas fa-sack-dollar"></i></div>
                              </div>
                            </div>
                            <img src="{{ asset('assets/img/dashboard/academy-bg4.png') }}" alt=""
                              class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                          </div>
                        </div>

                        <div class="col">
                          <div class="card overflow-hidden h-100 kpi-card">
                            <div class="card-body position-relative z-1">
                              <div class="d-flex justify-content-between align-items-center">
                                <div>
                                  <div class="text-muted">New (30 days)</div>
                                  <div class="fs-4 fw-semibold">{{ number_format($asset['kpi']['new_assets_30d'] ?? 0) }}</div>
                                </div>
                                <div class="text-primary fs-3"><i class="fas fa-sparkles"></i></div>
                              </div>
                            </div>
                            <img src="{{ asset('assets/img/dashboard/academy-bg5.png') }}" alt=""
                              class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                          </div>
                        </div>
                      </div>

                      <hr class="my-4" />
                    @endif

                    @if(!empty($permissions['asset']['charts']))
                      <div class="row g-3">
                        <div class="col-12 col-xl-6">
                          <div class="card">
                            <div class="card-header">
                              <h6 class="mb-0">Assets by Status</h6>
                            </div>
                            <div class="card-body">
                              <div id="assetByStatus" style="min-height: 320px;"></div>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-xl-6">
                          <div class="card">
                            <div class="card-header">
                              <h6 class="mb-0">Assets by Location</h6>
                            </div>
                            <div class="card-body">
                              <div id="assetByLocation" style="min-height: 320px;"></div>
                            </div>
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="card">
                            <div class="card-header">
                              <h6 class="mb-0">New Assets (Monthly)</h6>
                            </div>
                            <div class="card-body">
                              <div id="assetMonthlyNew" style="min-height: 320px;"></div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <hr class="my-4" />
                    @endif

                    @if(!empty($permissions['asset']['recent']))
                      <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                          <h6 class="mb-0">Recently Updated Assets</h6>
                          <a href="{{ route('admin.assets.index') }}" class="btn btn-outline-secondary btn-sm">Open Assets</a>
                        </div>
                        <div class="card-body table-responsive">
                          <table class="table table-striped table-bordered">
                            <thead>
                              <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach(($asset['recent'] ?? collect()) as $a)
                                <tr>
                                  <td>{{ $a->asset_code }}</td>
                                  <td>{{ $a->asset_name }}</td>
                                  <td>{{ $a->asset_category }}</td>
                                  <td>{{ $a->asset_location ?? '-' }}</td>
                                  <td>{{ $a->asset_status ?? '-' }}</td>
                                  <td>{{ $a->last_updated ? \Carbon\Carbon::parse($a->last_updated)->format('d-m-Y H:i') : '-' }}
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                    @endif
                  </div>
                @endif

                @if($showStamps)
                  @php
                    $stKpi = $stamps['kpi'] ?? ['total_qty' => 0, 'total_value' => 0, 'total_out_qty' => 0];
                    $stTop = $stamps['topStamps'] ?? collect();
                    $stRecent = $stamps['recentTransactions'] ?? collect();

                    $canReadLedger = \App\Support\MenuAccess::can(auth()->user(), 'stamps_transactions', 'read');
                    $canReadMaster = \App\Support\MenuAccess::can(auth()->user(), 'stamps_master', 'read');
                  @endphp

                  <div class="tab-pane fade {{ $activeTab === 'stamps' ? 'show active' : '' }}" id="tab-stamps" role="tabpanel">
                    <div class="d-flex flex-wrap gap-2 justify-content-end mb-3">
                      @if($canReadLedger)
                        <a href="{{ route('admin.stamps.transactions.index') }}" class="btn btn-outline-primary btn-sm">
                          <i class="fas fa-book"></i> Ledger
                        </a>
                      @endif
                      @if($canReadMaster)
                        <a href="{{ route('admin.stamps.master.index') }}" class="btn btn-outline-secondary btn-sm">
                          <i class="fas fa-list"></i> Master
                        </a>
                      @endif
                    </div>

                    <div class="row g-3 row-cols-1 row-cols-md-2 row-cols-xl-3">
                      <div class="col">
                        <div class="card overflow-hidden h-100 kpi-card">
                          <div class="card-body position-relative z-1">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <div class="text-muted">Sisa Stock (Qty)</div>
                                <div class="fs-4 fw-semibold">{{ number_format((int) ($stKpi['total_qty'] ?? 0), 0, ',', '.') }}</div>
                              </div>
                              <div class="text-primary fs-3"><i class="fas fa-boxes-stacked"></i></div>
                            </div>
                          </div>
                          <img src="{{ asset('assets/img/dashboard/academy-bg1.png') }}" alt=""
                            class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                        </div>
                      </div>

                      <div class="col">
                        <div class="card overflow-hidden h-100 kpi-card">
                          <div class="card-body position-relative z-1">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <div class="text-muted">Total Nilai (Rp)</div>
                                <div class="fs-4 fw-semibold">Rp {{ number_format((int) ($stKpi['total_value'] ?? 0), 0, ',', '.') }}</div>
                              </div>
                              <div class="text-success fs-3"><i class="fas fa-sack-dollar"></i></div>
                            </div>
                          </div>
                          <img src="{{ asset('assets/img/dashboard/academy-bg2.png') }}" alt=""
                            class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                        </div>
                      </div>

                      <div class="col">
                        <div class="card overflow-hidden h-100 kpi-card">
                          <div class="card-body position-relative z-1">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <div class="text-muted">Total Materai Keluar (Qty)</div>
                                <div class="fs-4 fw-semibold">{{ number_format((int) ($stKpi['total_out_qty'] ?? 0), 0, ',', '.') }}</div>
                              </div>
                              <div class="text-danger fs-3"><i class="fas fa-arrow-up"></i></div>
                            </div>
                          </div>
                          <img src="{{ asset('assets/img/dashboard/academy-bg3.png') }}" alt=""
                            class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                        </div>
                      </div>
                    </div>

                    <hr class="my-4" />

                    <div class="row g-3">
                      <div class="col-12 col-xl-5">
                        <div class="card h-100">
                          <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Ringkasan Cepat</h6>
                            @if($canReadLedger)
                              <a href="{{ route('admin.stamps.transactions.index') }}" class="btn btn-outline-secondary btn-sm">Buka ledger</a>
                            @endif
                          </div>
                          <div class="card-body table-responsive">
                            <table class="table table-striped table-bordered align-middle">
                              <thead>
                                <tr>
                                  <th>Materai</th>
                                  <th style="width: 90px;" class="text-end">Saldo</th>
                                </tr>
                              </thead>
                              <tbody>
                                @forelse($stTop as $stamp)
                                  <tr>
                                    <td>
                                      <div class="fw-semibold">{{ $stamp->name }}</div>
                                      <div class="text-muted small">{{ $stamp->code }} • Rp {{ number_format((int) $stamp->face_value, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="text-end">{{ number_format((int) ($stamp->balance?->on_hand_qty ?? 0), 0, ',', '.') }}</td>
                                  </tr>
                                @empty
                                  <tr>
                                    <td colspan="2" class="text-center text-muted">Belum ada master materai</td>
                                  </tr>
                                @endforelse
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>

                      <div class="col-12 col-xl-7">
                        <div class="card h-100">
                          <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Transaksi Terbaru</h6>
                            @if($canReadLedger)
                              <a href="{{ route('admin.stamps.transactions.index') }}" class="btn btn-outline-secondary btn-sm">Buka ledger</a>
                            @endif
                          </div>
                          <div class="card-body table-responsive">
                            <table class="table table-striped table-bordered align-middle">
                              <thead>
                                <tr>
                                  <th style="width: 110px;">Tanggal</th>
                                  <th style="width: 140px;">No. Trx</th>
                                  <th>Materai</th>
                                  <th style="width: 70px;">Tipe</th>
                                  <th style="width: 80px;" class="text-end">Qty</th>
                                  <th style="width: 90px;" class="text-end">Saldo</th>
                                </tr>
                              </thead>
                              <tbody>
                                @forelse($stRecent as $trx)
                                  <tr>
                                    <td>{{ optional($trx->trx_date)->format('d-m-Y') }}</td>
                                    <td class="text-nowrap">{{ $trx->trx_no }}</td>
                                    <td>
                                      <div class="fw-semibold">{{ $trx->stamp?->name }}</div>
                                      <div class="text-muted small">{{ $trx->stamp?->code }}</div>
                                    </td>
                                    <td>
                                      <span class="badge {{ $trx->trx_type === 'IN' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                        {{ $trx->trx_type }}
                                      </span>
                                    </td>
                                    <td class="text-end">{{ number_format((int) $trx->qty, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format((int) $trx->on_hand_after, 0, ',', '.') }}</td>
                                  </tr>
                                @empty
                                  <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada transaksi</td>
                                  </tr>
                                @endforelse
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @endif

                @if($showUniforms)
                  @php
                    $unKpi = $uniforms['kpi'] ?? [];
                    $unRecent = $uniforms['recentAllocations'] ?? collect();

                    $canReadStock = \App\Support\MenuAccess::can(auth()->user(), 'uniforms_stock', 'read');
                    $canReadDist = \App\Support\MenuAccess::can(auth()->user(), 'uniforms_distribution', 'read');
                    $canReadReports = \App\Support\MenuAccess::can(auth()->user(), 'uniforms_reports', 'read');
                  @endphp

                  <div class="tab-pane fade {{ $activeTab === 'uniforms' ? 'show active' : '' }}" id="tab-uniforms" role="tabpanel">
                    <div class="d-flex flex-wrap gap-2 justify-content-end mb-3">
                      @if($canReadStock)
                        <a href="{{ route('admin.uniforms.stock.index') }}" class="btn btn-outline-secondary btn-sm">
                          <i class="fas fa-boxes-stacked"></i> Stok
                        </a>
                      @endif
                      @if($canReadDist)
                        <a href="{{ route('admin.uniforms.distributions.index') }}" class="btn btn-outline-primary btn-sm">
                          <i class="fas fa-people-carry-box"></i> Distribusi
                        </a>
                        <a href="{{ route('admin.uniforms.distributions.dashboard') }}" class="btn btn-primary btn-sm">
                          <i class="fas fa-chart-simple"></i> Dashboard Distribusi
                        </a>
                      @endif
                      @if($canReadReports)
                        <a href="{{ route('admin.uniforms.reports.pivot.index') }}" class="btn btn-outline-secondary btn-sm">
                          <i class="fas fa-table"></i> Pivot
                        </a>
                        <a href="{{ route('admin.uniforms.stock.lots.index') }}" class="btn btn-outline-secondary btn-sm">
                          <i class="fas fa-layer-group"></i> Lots
                        </a>
                      @endif
                    </div>

                    <div class="row g-3 row-cols-1 row-cols-md-2 row-cols-xl-3 row-cols-xxl-5">
                      <div class="col">
                        <div class="card overflow-hidden h-100 kpi-card">
                          <div class="card-body position-relative z-1">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <div class="text-muted">Total Seragam</div>
                                <div class="fs-4 fw-semibold">{{ number_format((int) ($unKpi['total_uniforms'] ?? 0), 0, ',', '.') }}</div>
                              </div>
                              <div class="text-primary fs-3"><i class="fas fa-shirt"></i></div>
                            </div>
                          </div>
                          <img src="{{ asset('assets/img/dashboard/academy-bg1.png') }}" alt=""
                            class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                        </div>
                      </div>

                      <div class="col">
                        <div class="card overflow-hidden h-100 kpi-card">
                          <div class="card-body position-relative z-1">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <div class="text-muted">Total Varian</div>
                                <div class="fs-4 fw-semibold">{{ number_format((int) ($unKpi['total_variants'] ?? 0), 0, ',', '.') }}</div>
                              </div>
                              <div class="text-success fs-3"><i class="fas fa-tags"></i></div>
                            </div>
                          </div>
                          <img src="{{ asset('assets/img/dashboard/academy-bg2.png') }}" alt=""
                            class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                        </div>
                      </div>

                      <div class="col">
                        <div class="card overflow-hidden h-100 kpi-card">
                          <div class="card-body position-relative z-1">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <div class="text-muted">Stok On Hand</div>
                                <div class="fs-4 fw-semibold">{{ number_format((int) ($unKpi['total_on_hand'] ?? 0), 0, ',', '.') }}</div>
                              </div>
                              <div class="text-warning fs-3"><i class="fas fa-boxes-stacked"></i></div>
                            </div>
                          </div>
                          <img src="{{ asset('assets/img/dashboard/academy-bg3.png') }}" alt=""
                            class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                        </div>
                      </div>

                      <div class="col">
                        <div class="card overflow-hidden h-100 kpi-card">
                          <div class="card-body position-relative z-1">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <div class="text-muted">Qty Distribusi (30 hari)</div>
                                <div class="fs-4 fw-semibold">{{ number_format((int) ($unKpi['allocated_qty_30d'] ?? 0), 0, ',', '.') }}</div>
                              </div>
                              <div class="text-danger fs-3"><i class="fas fa-hand-holding"></i></div>
                            </div>
                          </div>
                          <img src="{{ asset('assets/img/dashboard/academy-bg4.png') }}" alt=""
                            class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                        </div>
                      </div>

                      <div class="col">
                        <div class="card overflow-hidden h-100 kpi-card">
                          <div class="card-body position-relative z-1">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <div class="text-muted">Transaksi Distribusi (30 hari)</div>
                                <div class="fs-4 fw-semibold">{{ number_format((int) ($unKpi['allocations_30d'] ?? 0), 0, ',', '.') }}</div>
                              </div>
                              <div class="text-info fs-3"><i class="fas fa-receipt"></i></div>
                            </div>
                          </div>
                          <img src="{{ asset('assets/img/dashboard/academy-bg5.png') }}" alt=""
                            class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                        </div>
                      </div>
                    </div>

                    @if(!empty($uniforms['charts']['allocatedDaily30d'] ?? null))
                      <div class="row g-3 mt-1">
                        <div class="col-12">
                          <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                              <h6 class="mb-0">Grafik Distribusi (30 hari terakhir)</h6>
                            </div>
                            <div class="card-body">
                              <div id="uniformsAllocatedDaily30d"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    @endif

                    <div class="row g-3 mt-1">
                      <div class="col-12">
                        <div class="card">
                          <div class="card-header d-flex align-items-center justify-content-between">
                            <h6 class="mb-0">Distribusi Terbaru</h6>
                            @if($canReadDist)
                              <a href="{{ route('admin.uniforms.distributions.index') }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                            @endif
                          </div>
                          <div class="card-body">
                            <div class="table-responsive">
                              <table class="table table-striped table-bordered align-middle mb-0">
                                <thead>
                                  <tr>
                                    <th style="width: 130px;">Tanggal</th>
                                    <th style="width: 180px;">No. ID</th>
                                    <th style="width: 220px;">Karyawan</th>
                                    <th>Seragam / Ukuran / Qty</th>
                                    <th style="width: 90px;" class="text-end">Qty</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @forelse($unRecent as $row)
                                    <tr>
                                      <td>{{ !empty($row['allocated_at']) ? \Carbon\Carbon::parse($row['allocated_at'])->format('d-m-Y') : '-' }}</td>
                                      <td class="text-nowrap">{{ $row['employee_no_id'] ?? '-' }}</td>
                                      <td>
                                        <div class="fw-semibold">{{ $row['employee_name'] ?? '-' }}</div>
                                        <div class="text-muted small">{{ $row['allocation_no'] ?? '-' }}</div>
                                      </td>
                                      <td>
                                        @php $lines = $row['items_lines'] ?? collect(); @endphp
                                        @if($lines instanceof \Illuminate\Support\Collection)
                                          @foreach($lines as $line)
                                            <div>{{ $line }}</div>
                                          @endforeach
                                        @elseif(is_array($lines))
                                          @foreach($lines as $line)
                                            <div>{{ $line }}</div>
                                          @endforeach
                                        @else
                                          <div class="text-muted">-</div>
                                        @endif
                                      </td>
                                      <td class="text-end">{{ number_format((int) ($row['total_qty'] ?? 0), 0, ',', '.') }}</td>
                                    </tr>
                                  @empty
                                    <tr>
                                      <td colspan="5" class="text-center text-muted">Belum ada distribusi</td>
                                    </tr>
                                  @endforelse
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @endif

                @if($showDocuments)
                  @php
                    $docExpiring = $documents['expiring'] ?? collect();
                    $docLatest = $documents['latest'] ?? collect();
                    $docActiveByMonth = $documents['activeByMonth'] ?? collect();
                    $canCreateDoc = \App\Support\MenuAccess::can(auth()->user(), 'documents_archive', 'create');
                  @endphp

                  <div class="tab-pane fade {{ $activeTab === 'documents' ? 'show active' : '' }}" id="tab-documents"
                    role="tabpanel">
                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                      <div class="text-muted small">Ringkasan kontrak/lampiran dari Arsip Berkas.</div>
                      <div class="d-flex gap-2">
                        <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary btn-sm">Open List</a>
                        @if($canCreateDoc)
                          <a href="{{ route('admin.documents.create') }}" class="btn btn-primary btn-sm">Upload Dokumen</a>
                        @endif
                      </div>
                    </div>

                    <div class="row g-3">
                      <div class="col-12 col-xl-6">
                        <div class="card">
                          <div class="card-header d-flex align-items-center justify-content-between">
                            <h6 class="mb-0">Kontrak/Langganan Akan Habis (≤ 90 hari)</h6>
                            <a href="{{ route('admin.documents.index', ['status' => 'Active']) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                          </div>
                          <div class="card-body">
                            @if($docExpiring->isEmpty())
                              <div class="text-muted">Tidak ada yang akan habis dalam 90 hari.</div>
                            @else
                              <div class="table-responsive">
                                <table class="table table-sm table-striped align-middle mb-0">
                                  <thead>
                                    <tr>
                                      <th>Judul</th>
                                      <th>Vendor</th>
                                      <th>End Date</th>
                                      <th>Status</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @foreach($docExpiring as $d)
                                      <tr>
                                        <td>
                                          <a href="{{ route('admin.documents.show', $d->document_id) }}">{{ $d->document_title }}</a>
                                          <div class="text-muted small">{{ $d->document_type }} • {{ $d->document_number }}</div>
                                        </td>
                                        <td>{{ $d->vendor?->name }}</td>
                                        <td>{{ optional($d->contractTerms?->end_date)->format('Y-m-d') }}</td>
                                        <td>
                                          <span class="badge bg-{{ $d->status === 'Active' ? 'success' : ($d->status === 'Draft' ? 'secondary' : 'warning') }}">{{ $d->status }}</span>
                                        </td>
                                      </tr>
                                    @endforeach
                                  </tbody>
                                </table>
                              </div>
                            @endif
                          </div>
                        </div>
                      </div>

                      <div class="col-12 col-xl-6">
                        <div class="card">
                          <div class="card-header d-flex align-items-center justify-content-between">
                            <h6 class="mb-0">Dokumen Terbaru</h6>
                            <a href="{{ route('admin.documents.index') }}" class="btn btn-sm btn-outline-primary">List</a>
                          </div>
                          <div class="card-body">
                            @if($docLatest->isEmpty())
                              <div class="text-muted">Belum ada dokumen.</div>
                            @else
                              <ul class="list-group list-group-flush">
                                @foreach($docLatest as $d)
                                  <li class="list-group-item d-flex align-items-center justify-content-between">
                                    <div>
                                      <a href="{{ route('admin.documents.show', $d->document_id) }}">{{ $d->document_title }}</a>
                                      <div class="text-muted small">{{ $d->vendor?->name }} • {{ $d->document_type }} • {{ $d->document_number }}</div>
                                    </div>
                                    <div class="text-muted small">{{ $d->updated_at?->format('Y-m-d H:i') }}</div>
                                  </li>
                                @endforeach
                              </ul>
                            @endif
                          </div>
                        </div>
                      </div>

                      <div class="col-12">
                        <div class="card">
                          <div class="card-header">
                            <h6 class="mb-0">Langganan Aktif per Bulan (berdasarkan End Date)</h6>
                          </div>
                          <div class="card-body">
                            @if($docActiveByMonth->isEmpty())
                              <div class="text-muted">Belum ada data.</div>
                            @else
                              <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                  <thead>
                                    <tr>
                                      <th>Bulan</th>
                                      <th class="text-end">Total</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @foreach($docActiveByMonth as $row)
                                      <tr>
                                        <td>{{ $row->ym }}</td>
                                        <td class="text-end">{{ $row->total }}</td>
                                      </tr>
                                    @endforeach
                                  </tbody>
                                </table>
                              </div>
                            @endif
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @endif

                @if($showEmployee)
                  <div class="tab-pane fade {{ $activeTab === 'employee' ? 'show active' : '' }}" id="tab-employee"
                    role="tabpanel">
                    <div class="row g-3 row-cols-1 row-cols-md-2 row-cols-xl-3 row-cols-xxl-6">
                      <div class="col">
                        <div class="card overflow-hidden h-100 kpi-card">
                          <div class="card-body position-relative z-1">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <div class="text-muted">Total Karyawan</div>
                                <div class="fs-4 fw-semibold">{{ number_format($employee['kpi']['total'] ?? 0) }}</div>
                              </div>
                              <div class="text-primary fs-3"><i class="fas fa-users"></i></div>
                            </div>
                          </div>
                          <img src="{{ asset('assets/img/dashboard/academy-bg1.png') }}" alt=""
                            class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                        </div>
                      </div>

                      <div class="col">
                        <div class="card overflow-hidden h-100 kpi-card">
                          <div class="card-body position-relative z-1">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <div class="text-muted">Aktif</div>
                                <div class="fs-4 fw-semibold">{{ number_format($employee['kpi']['active'] ?? 0) }}</div>
                              </div>
                              <div class="text-success fs-3"><i class="fas fa-circle-check"></i></div>
                            </div>
                          </div>
                          <img src="{{ asset('assets/img/dashboard/academy-bg2.png') }}" alt=""
                            class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                        </div>
                      </div>

                      <div class="col">
                        <div class="card overflow-hidden h-100 kpi-card">
                          <div class="card-body position-relative z-1">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <div class="text-muted">Non Aktif</div>
                                <div class="fs-4 fw-semibold">{{ number_format($employee['kpi']['inactive'] ?? 0) }}</div>
                              </div>
                              <div class="text-warning fs-3"><i class="fas fa-circle-minus"></i></div>
                            </div>
                          </div>
                          <img src="{{ asset('assets/img/dashboard/academy-bg3.png') }}" alt=""
                            class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                        </div>
                      </div>

                      <div class="col">
                        <div class="card overflow-hidden h-100 kpi-card">
                          <div class="card-body position-relative z-1">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <div class="text-muted">PKWT (Aktif)</div>
                                <div class="fs-4 fw-semibold">{{ number_format($employee['kpi']['active_pkwt'] ?? 0) }}</div>
                              </div>
                              <div class="text-info fs-3"><i class="fas fa-id-badge"></i></div>
                            </div>
                          </div>
                          <img src="{{ asset('assets/img/dashboard/academy-bg4.png') }}" alt=""
                            class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                        </div>
                      </div>

                      <div class="col">
                        <div class="card overflow-hidden h-100 kpi-card">
                          <div class="card-body position-relative z-1">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <div class="text-muted">PKWTT (Aktif)</div>
                                <div class="fs-4 fw-semibold">{{ number_format($employee['kpi']['active_pkwtt'] ?? 0) }}</div>
                              </div>
                              <div class="text-primary fs-3"><i class="fas fa-user-tie"></i></div>
                            </div>
                          </div>
                          <img src="{{ asset('assets/img/dashboard/academy-bg5.png') }}" alt=""
                            class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                        </div>
                      </div>

                      <div class="col">
                        <div class="card overflow-hidden h-100 kpi-card">
                          <div class="card-body position-relative z-1">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <div class="text-muted">Join (30 hari)</div>
                                <div class="fs-4 fw-semibold">{{ number_format($employee['kpi']['joined_30d'] ?? 0) }}</div>
                              </div>
                              <div class="text-success fs-3"><i class="fas fa-calendar-plus"></i></div>
                            </div>
                          </div>
                          <img src="{{ asset('assets/img/dashboard/academy-bg1.png') }}" alt=""
                            class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                        </div>
                      </div>
                    </div>

                    @if(!empty($employee['recent']) && $employee['recent']->count())
                      <hr class="my-4" />
                      <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                          <h6 class="mb-0">Karyawan Terbaru</h6>
                          @if($canManageEmployee)
                            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary btn-sm">Kelola</a>
                          @endif
                        </div>
                        <div class="card-body table-responsive">
                          <table class="table table-striped table-bordered">
                            <thead>
                              <tr>
                                <th>No ID</th>
                                <th>Nama</th>
                                <th>Departemen</th>
                                <th>Posisi</th>
                                <th>Status</th>
                                <th>Tgl Join</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($employee['recent'] as $emp)
                                <tr>
                                  <td>{{ $emp->no_id ?? '-' }}</td>
                                  <td>{{ $emp->name ?? '-' }}</td>
                                  <td>{{ optional($emp->department)->name ?? '-' }}</td>
                                  <td>{{ optional($emp->position)->name ?? '-' }}</td>
                                  <td>{{ $emp->employment_status ?? '-' }}</td>
                                  <td>{{ $emp->join_date ? \Illuminate\Support\Carbon::parse($emp->join_date)->format('d-m-Y') : '-' }}</td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                    @endif
                  </div>
                @endif
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  @endif
@endsection

@section('js')
  @php
    $shouldLoadCharts = !$isUser && (
      !empty($permissions['asset']['charts']) ||
      !empty($uniforms['charts'] ?? null)
    );
  @endphp

  @if($shouldLoadCharts)
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script>
      window.combinedDashboardData = @json([
        'asset' => $asset['charts'] ?? null,
        'uniforms' => $uniforms['charts'] ?? null,
      ]);

      (function () {
        if (typeof ApexCharts === 'undefined') return;

        const all = window.combinedDashboardData || {};

        const donut = (el, labels, series) => {
          const node = document.querySelector(el);
          if (!node) return;
          const opt = {
            chart: { type: 'donut', height: 320 },
            labels,
            series,
            legend: { position: 'bottom' },
            dataLabels: { enabled: true },
          };
          new ApexCharts(node, opt).render();
        };

        const bar = (el, categories, series, name) => {
          const node = document.querySelector(el);
          if (!node) return;
          const opt = {
            chart: { type: 'bar', height: 320, toolbar: { show: false } },
            series: [{ name, data: series }],
            xaxis: { categories },
            plotOptions: { bar: { borderRadius: 6, columnWidth: '45%' } },
          };
          new ApexCharts(node, opt).render();
        };

        const line = (el, categories, seriesA, seriesB, nameA, nameB) => {
          const node = document.querySelector(el);
          if (!node) return;
          const series = [];
          if (Array.isArray(seriesA)) series.push({ name: nameA, data: seriesA });
          if (Array.isArray(seriesB)) series.push({ name: nameB, data: seriesB });
          const opt = {
            chart: { type: 'line', height: 320, toolbar: { show: false } },
            stroke: { curve: 'smooth', width: 3 },
            series,
            xaxis: { categories },
          };
          new ApexCharts(node, opt).render();
        };

        const asset = all.asset || {};
        donut('#assetByStatus', asset.byStatus?.labels || [], asset.byStatus?.series || []);
        donut('#assetByLocation', asset.byLocation?.labels || [], asset.byLocation?.series || []);
        line('#assetMonthlyNew', asset.monthlyNew?.categories || [], asset.monthlyNew?.series || [], null, 'New Assets');

        const uniforms = all.uniforms || {};
        bar('#uniformsAllocatedDaily30d', uniforms.allocatedDaily30d?.categories || [], uniforms.allocatedDaily30d?.series || [], 'Qty');
      })();
    </script>
  @endif
  <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection