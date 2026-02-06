@extends('layouts.master')

@section('title', 'Ilsam - Asset Dashboard')
@section('title-sub', 'Dashboard')
@section('pagetitle', 'Asset Dashboard')

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
  <div id="layout-wrapper">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"> Asset Dashboard </h5>
            <div class="d-flex gap-2">
              <a href="{{ route('admin.assets.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-box"></i> Asset Management
              </a>
            </div>
          </div>

          <div class="card-body">
            <div class="row g-3 row-cols-1 row-cols-md-2 row-cols-xl-3 row-cols-xxl-5">
              <div class="col">
                <div class="card overflow-hidden h-100 kpi-card">
                  <div class="card-body position-relative z-1">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <div class="text-muted">Total Assets</div>
                        <div class="fs-4 fw-semibold">{{ number_format($kpi['total_assets']) }}</div>
                      </div>
                      <div class="text-primary fs-3"><i class="fas fa-boxes-stacked"></i></div>
                    </div>
                  </div>
                  <img src="{{ asset('assets/img/dashboard/academy-bg1.png') }}" alt="" class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                </div>
              </div>

              <div class="col">
                <div class="card overflow-hidden h-100 kpi-card">
                  <div class="card-body position-relative z-1">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <div class="text-muted">Active</div>
                        <div class="fs-4 fw-semibold">{{ number_format($kpi['active_assets']) }}</div>
                      </div>
                      <div class="text-success fs-3"><i class="fas fa-circle-check"></i></div>
                    </div>
                  </div>
                  <img src="{{ asset('assets/img/dashboard/academy-bg2.png') }}" alt="" class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                </div>
              </div>

              <div class="col">
                <div class="card overflow-hidden h-100 kpi-card">
                  <div class="card-body position-relative z-1">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <div class="text-muted">Inactive / Sold / Disposed</div>
                        <div class="fs-4 fw-semibold">{{ number_format($kpi['inactive_assets']) }}</div>
                      </div>
                      <div class="text-warning fs-3"><i class="fas fa-triangle-exclamation"></i></div>
                    </div>
                  </div>
                  <img src="{{ asset('assets/img/dashboard/academy-bg3.png') }}" alt="" class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                </div>
              </div>

              <div class="col">
                <div class="card overflow-hidden h-100 kpi-card">
                  <div class="card-body position-relative z-1">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <div class="text-muted">Total Asset Value</div>
                        <div class="fs-4 fw-semibold">Rp {{ number_format($kpi['total_value'], 0, ',', '.') }}</div>
                      </div>
                      <div class="text-info fs-3"><i class="fas fa-coins"></i></div>
                    </div>
                  </div>
                  <img src="{{ asset('assets/img/dashboard/academy-bg4.png') }}" alt="" class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                </div>
              </div>

              <div class="col">
                <div class="card overflow-hidden h-100 kpi-card">
                  <div class="card-body position-relative z-1">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <div class="text-muted">New Assets (30 days)</div>
                        <div class="fs-4 fw-semibold">{{ number_format($kpi['new_assets_30d'] ?? 0) }}</div>
                      </div>
                      <div class="text-secondary fs-3"><i class="fas fa-calendar-plus"></i></div>
                    </div>
                  </div>
                  <img src="{{ asset('assets/img/dashboard/academy-bg5.png') }}" alt="" class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                </div>
              </div>

            </div>

            <div class="row g-3 mt-0">

              <div class="col-xl-6">
                <div class="card">
                  <div class="card-header">
                    <h6 class="mb-0">Assets by Status</h6>
                  </div>
                  <div class="card-body">
                    <div id="assetByStatus" style="min-height: 320px;"></div>
                  </div>
                </div>
              </div>

              <div class="col-xl-6">
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
                    <h6 class="mb-0">Monthly New Assets</h6>
                  </div>
                  <div class="card-body">
                    <div id="assetMonthlyNew" style="min-height: 320px;"></div>
                  </div>
                </div>
              </div>

              <div class="col-12">
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
                        @forelse($recentAssets as $a)
                          <tr>
                            <td>{{ $a->asset_code }}</td>
                            <td>{{ $a->asset_name }}</td>
                            <td>{{ $a->asset_category }}</td>
                            <td>{{ $a->asset_location ?? '-' }}</td>
                            <td>{{ $a->asset_status ?? '-' }}</td>
                            <td>{{ $a->last_updated ? \Carbon\Carbon::parse($a->last_updated)->format('d-m-Y H:i') : '-' }}</td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="6" class="text-center">No data</td>
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
      </div>
    </div>
  </div><!--End container-fluid-->
  </main><!--End app-wrapper-->
@endsection

@section('js')
  <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
  <script>
    window.assetDashboardData = @json($charts);

    (function () {
      if (typeof ApexCharts === 'undefined') return;

      const data = window.assetDashboardData || {};

      const donut = (el, labels, series) => {
        const opt = {
          chart: { type: 'donut', height: 320 },
          labels,
          series,
          legend: { position: 'bottom' },
        };
        new ApexCharts(document.querySelector(el), opt).render();
      };

      const line = (el, categories, series) => {
        const opt = {
          chart: { type: 'line', height: 320, toolbar: { show: false } },
          stroke: { curve: 'smooth', width: 3 },
          series: [{ name: 'New Assets', data: series }],
          xaxis: { categories },
        };
        new ApexCharts(document.querySelector(el), opt).render();
      };

      donut('#assetByStatus', data.byStatus?.labels || [], data.byStatus?.series || []);
      donut('#assetByLocation', data.byLocation?.labels || [], data.byLocation?.series || []);
      line('#assetMonthlyNew', data.monthlyNew?.categories || [], data.monthlyNew?.series || []);
    })();
  </script>
  <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
