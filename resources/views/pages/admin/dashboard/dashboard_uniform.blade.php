@extends('layouts.master')

@section('title', 'Ilsam - Uniform Dashboard')
@section('title-sub', 'Dashboard')
@section('pagetitle', 'Uniform Dashboard')

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
            <h5 class="card-title mb-0"> Uniform Dashboard </h5>
            <div class="d-flex gap-2">
              <a href="{{ route('admin.dashboard.assets') }}" class="btn btn-outline-secondary btn-sm"><i
                  class="fas fa-chart-line"></i> Assets</a>
              <a href="{{ route('admin.uniforms.master') }}" class="btn btn-primary btn-sm"><i
                  class="fas fa-boxes-stacked"></i> Uniform Management</a>
            </div>
          </div>
          <div class="card-body">
            <div class="row g-3 row-cols-1 row-cols-md-2 row-cols-xl-4">
              <div class="col">
                <div class="card overflow-hidden h-100 kpi-card">
                  <div class="card-body position-relative z-1">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <div class="text-muted">Total Items</div>
                        <div class="fs-4 fw-semibold">{{ number_format($kpi['total_items']) }}</div>
                      </div>
                      <div class="text-primary fs-3"><i class="fas fa-shirt"></i></div>
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
                        <div class="text-muted">Total Stock</div>
                        <div class="fs-4 fw-semibold">{{ number_format($kpi['total_stock']) }}</div>
                      </div>
                      <div class="text-success fs-3"><i class="fas fa-warehouse"></i></div>
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
                        <div class="text-muted">Low Stock Items</div>
                        <div class="fs-4 fw-semibold">{{ number_format($kpi['low_stock_items']) }}</div>
                      </div>
                      <div class="text-danger fs-3"><i class="fas fa-triangle-exclamation"></i></div>
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
                        <div class="text-muted">Issues (30 days)</div>
                        <div class="fs-4 fw-semibold">{{ number_format($kpi['issues_30d']) }}</div>
                      </div>
                      <div class="text-info fs-3"><i class="fas fa-people-carry-box"></i></div>
                    </div>
                  </div>
                  <img src="{{ asset('assets/img/dashboard/academy-bg4.png') }}" alt="" class="position-absolute bottom-0 end-0 h-100 w-100 object-fit-cover kpi-bg-img">
                </div>
              </div>

            </div>

            <div class="row g-3 mt-0">

              <div class="col-xl-6">
                <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Stock by Location</h6>
                  </div>
                  <div class="card-body">
                    <div id="uniformStockByLocation" style="min-height: 320px;"></div>
                  </div>
                </div>
              </div>

              <div class="col-xl-6">
                <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Stock by Category</h6>
                  </div>
                  <div class="card-body">
                    <div id="uniformStockByCategory" style="min-height: 320px;"></div>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Monthly Movements (IN vs OUT)</h6>
                  </div>
                  <div class="card-body">
                    <div id="uniformMonthlyMovements" style="min-height: 320px;"></div>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Recent Issues</h6>
                    <a href="{{ route('admin.uniforms.history') }}" class="btn btn-outline-secondary btn-sm">Open
                      History</a>
                  </div>
                  <div class="card-body table-responsive">
                    <table class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>Date</th>
                          <th>Issue Code</th>
                          <th>Item</th>
                          <th>Employee</th>
                          <th>Qty</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($recentIssues as $iss)
                          <tr>
                            <td>{{ $iss->issued_at ? \Carbon\Carbon::parse($iss->issued_at)->format('d-m-Y H:i') : '-' }}
                            </td>
                            <td>{{ $iss->issue_code }}</td>
                            <td>{{ $iss->item?->item_code }} - {{ $iss->item?->item_name }}</td>
                            <td>{{ $iss->issuedTo?->name ?? '-' }}</td>
                            <td>{{ $iss->qty }}</td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="5" class="text-center">No data</td>
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
    window.uniformDashboardData = @json($charts);

    (function () {
      if (typeof ApexCharts === 'undefined') return;

      const data = window.uniformDashboardData || {};

      const donut = (el, labels, series) => {
        const opt = {
          chart: { type: 'donut', height: 320 },
          labels,
          series,
          legend: { position: 'bottom' },
          dataLabels: { enabled: true },
        };
        new ApexCharts(document.querySelector(el), opt).render();
      };

      const bar = (el, categories, series, name) => {
        const opt = {
          chart: { type: 'bar', height: 320, toolbar: { show: false } },
          series: [{ name, data: series }],
          xaxis: { categories },
          plotOptions: { bar: { borderRadius: 6, columnWidth: '45%' } },
        };
        new ApexCharts(document.querySelector(el), opt).render();
      };

      const line = (el, categories, seriesIn, seriesOut) => {
        const opt = {
          chart: { type: 'line', height: 320, toolbar: { show: false } },
          stroke: { curve: 'smooth', width: 3 },
          series: [
            { name: 'IN', data: seriesIn },
            { name: 'OUT', data: seriesOut },
          ],
          xaxis: { categories },
        };
        new ApexCharts(document.querySelector(el), opt).render();
      };

      donut('#uniformStockByLocation', data.stockByLocation.labels || [], data.stockByLocation.series || []);
      bar('#uniformStockByCategory', data.stockByCategory.categories || [], data.stockByCategory.series || [], 'Stock');
      line('#uniformMonthlyMovements', data.monthlyMovements.categories || [], data.monthlyMovements.inSeries || [], data.monthlyMovements.outSeries || []);
    })();
  </script>
  <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection