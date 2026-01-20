@extends('technology')

@section('page_title', 'Ilsam - Certification Status')
@section('breadcrumb_title', 'Certification Status')

@section('rnd_content')
  @php
    $statusCounts = [
      'active' => 0,
      'expiring_soon' => 0,
      'expired' => 0,
    ];

    foreach (($certificates ?? []) as $row) {
      $key = $row['status'] ?? 'active';
      if (!array_key_exists($key, $statusCounts)) {
        $key = 'active';
      }
      $statusCounts[$key]++;
    }

    $uniqueSuppliers = collect($certificates ?? [])->pluck('supplier')->filter()->unique()->sort()->values();
    $uniqueTypes = collect($certificates ?? [])->pluck('certification_type')->filter()->unique()->sort()->values();
    $uniqueScopes = collect($certificates ?? [])->pluck('scope')->filter()->unique()->sort()->values();
  @endphp

  <style>
    :root {
      --ilsam-cert-ink: #0b1220;
      --ilsam-cert-muted: rgba(15, 23, 42, .70);
      --ilsam-cert-line: rgba(15, 23, 42, .12);
      --ilsam-cert-surface: rgba(255, 255, 255, .92);
      --ilsam-cert-surface-2: rgba(2, 6, 23, .03);
      --ilsam-cert-shadow: 0 14px 44px rgba(2, 6, 23, .12);
      --ilsam-cert-accent: #f59e0b;
      --ilsam-cert-accent-2: #16a34a;
    }

    .ilsam-cert-panel {
      background: var(--ilsam-cert-surface);
      border-radius: 14px;
      padding: 18px;
      box-shadow: var(--ilsam-cert-shadow);
      border: 1px solid rgba(245, 158, 11, .10);
    }

    .ilsam-cert-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 14px;
    }

    @media (min-width: 992px) {
      .ilsam-cert-grid {
        grid-template-columns: 1fr 1fr 1fr 1fr;
      }
    }

    .ilsam-cert-input,
    .ilsam-cert-select {
      width: 100%;
      border: 1px solid var(--ilsam-cert-line);
      border-radius: 12px;
      padding: 10px 12px;
      background: #fff;
      color: var(--ilsam-cert-ink);
    }

    .ilsam-cert-input:focus,
    .ilsam-cert-select:focus {
      outline: none;
      border-color: rgba(245, 158, 11, .45);
      box-shadow: 0 0 0 3px rgba(245, 158, 11, .14);
    }

    .ilsam-cert-label {
      font-size: 12px;
      opacity: .7;
      margin-bottom: 6px;
      display: block;
    }

    .ilsam-cert-badges {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }

    .ilsam-cert-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 12px;
      line-height: 1;
      border: 1px solid rgba(15, 23, 42, .12);
      background: rgba(255, 255, 255, .9);
    }

    .ilsam-cert-badge--active {
      color: #067647;
      border-color: rgba(6, 118, 71, .25);
      background: rgba(6, 118, 71, .08);
    }

    .ilsam-cert-badge--soon {
      color: #b54708;
      border-color: rgba(181, 71, 8, .25);
      background: rgba(181, 71, 8, .08);
    }

    .ilsam-cert-badge--expired {
      color: #b42318;
      border-color: rgba(180, 35, 24, .25);
      background: rgba(180, 35, 24, .08);
    }

    .ilsam-cert-badge--proof {
      color: #1d4ed8;
      border-color: rgba(29, 78, 216, .25);
      background: rgba(29, 78, 216, .08);
    }

    .ilsam-cert-badge--missing {
      color: #475569;
      border-color: rgba(71, 85, 105, .25);
      background: rgba(71, 85, 105, .08);
    }

    .ilsam-cert-table th {
      white-space: nowrap;
    }

    .ilsam-cert-table thead th {
      position: sticky;
      top: 0;
      background: #fff;
      z-index: 1;
    }

    .cert-row:hover {
      background: rgba(245, 158, 11, .06);
    }

    .ilsam-cert-actions {
      display: inline-flex;
      gap: 8px;
      align-items: center;
    }

    .ilsam-cert-action {
      font-size: 13px;
      text-decoration: underline;
      cursor: pointer;
    }

    .ilsam-cert-detail {
      padding: 14px;
      border-radius: 14px;
      background: var(--ilsam-cert-surface-2);
      border: 1px solid rgba(15, 23, 42, .10);
    }

    .ilsam-cert-detail__grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 10px;
    }

    @media (min-width: 992px) {
      .ilsam-cert-detail__grid {
        grid-template-columns: 1fr 1fr;
      }
    }

    .ilsam-cert-detail__k {
      font-size: 12px;
      opacity: .7;
    }

    .ilsam-cert-detail__v {
      font-size: 14px;
    }

    .ilsam-cert-proof {
      margin-top: 12px;
      border-radius: 12px;
      overflow: hidden;
      border: 1px solid rgba(15, 23, 42, .10);
      background: #fff;
    }

    .ilsam-cert-proof iframe {
      width: 100%;
      height: 520px;
      border: 0;
      display: block;
      background: #fff;
    }

    .ilsam-cert-proof img {
      width: 100%;
      height: auto;
      display: block;
    }
  </style>

  <section class="pt-60">
    <div class="ilsam-cert-panel">
      <div class="row align-items-end g-20">
        <div class="col-lg-8">
          <h3 class="mb-10">Certificate Register</h3>
          <p class="mb-0 ilsam-muted-sm">Filter by status, supplier, type, or scope. Click “Details” to view proof.</p>
        </div>
        <div class="col-lg-4 d-flex justify-content-lg-end">
          <button type="button" class="rr-btn rr-btn__transparent w-auto" id="certResetBtn">
            <span class="btn-wrap">
              <span class="text-one">Reset Filters</span>
              <span class="text-two">Reset Filters</span>
            </span>
          </button>
        </div>
      </div>

      <div class="mt-20 ilsam-cert-grid">
        <div>
          <label class="ilsam-cert-label" for="certSearch">Search</label>
          <input id="certSearch" class="ilsam-cert-input" type="text" placeholder="Chemical / Supplier / Certificate No"
            autocomplete="off" />
        </div>

        <div>
          <label class="ilsam-cert-label" for="certStatus">Status</label>
          <select id="certStatus" class="ilsam-cert-select">
            <option value="">All</option>
            <option value="active">Active</option>
            <option value="expiring_soon">Expiring Soon</option>
            <option value="expired">Expired</option>
          </select>
        </div>

        <div>
          <label class="ilsam-cert-label" for="certSupplier">Supplier</label>
          <select id="certSupplier" class="ilsam-cert-select">
            <option value="">All</option>
            @foreach ($uniqueSuppliers as $supplier)
              <option value="{{ $supplier }}">{{ $supplier }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="ilsam-cert-label" for="certType">Certification Type</label>
          <select id="certType" class="ilsam-cert-select">
            <option value="">All</option>
            @foreach ($uniqueTypes as $type)
              <option value="{{ $type }}">{{ $type }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="ilsam-cert-label" for="certScope">Scope</label>
          <select id="certScope" class="ilsam-cert-select">
            <option value="">All</option>
            @foreach ($uniqueScopes as $scope)
              <option value="{{ $scope }}">{{ $scope }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="ilsam-cert-label" for="certProof">Proof</label>
          <select id="certProof" class="ilsam-cert-select">
            <option value="">All</option>
            <option value="pdf">PDF</option>
            <option value="missing">Missing</option>
          </select>
        </div>

        <div style="grid-column: 1 / -1;">
          <div class="ilsam-muted-sm" id="certResultsMeta">Showing all records.</div>
        </div>
      </div>

      <div class="table-responsive mt-20">
        <table class="table mb-0 ilsam-table ilsam-cert-table">
          <thead>
            <tr>
              <th>Chemical Name</th>
              <th>Supplier</th>
              <th>Certification Type</th>
              <th>Certificate No.</th>
              <th>Issued</th>
              <th>Expiry</th>
              <th>Status</th>
              <th>Proof</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="certTbody">
            @forelse (($certificates ?? []) as $c)
              @php
                $issued = !empty($c['issued_date']) ? \Carbon\Carbon::parse($c['issued_date']) : null;
                $expiry = !empty($c['expiry_date']) ? \Carbon\Carbon::parse($c['expiry_date']) : null;
                $status = $c['status'] ?? 'active';
                $statusLabel = match ($status) {
                  'expired' => 'Expired',
                  'expiring_soon' => 'Expiring Soon',
                  default => 'Active',
                };
                $statusClass = match ($status) {
                  'expired' => 'ilsam-cert-badge--expired',
                  'expiring_soon' => 'ilsam-cert-badge--soon',
                  default => 'ilsam-cert-badge--active',
                };
                $proofType = $c['proof_type'] ?? 'missing';
              @endphp

              <tr class="cert-row" data-cert-id="{{ $c['id'] }}" data-chemical="{{ strtolower($c['chemical_name'] ?? '') }}"
                data-supplier="{{ strtolower($c['supplier'] ?? '') }}"
                data-type="{{ strtolower($c['certification_type'] ?? '') }}"
                data-scope="{{ strtolower($c['scope'] ?? '') }}" data-certno="{{ strtolower($c['certificate_no'] ?? '') }}"
                data-status="{{ $status }}" data-proof="{{ $proofType }}">
                <td>
                  <div class="fw-semibold">{{ $c['chemical_name'] }}</div>
                  <div class="ilsam-muted-sm">{{ $c['scope'] }}</div>
                </td>
                <td>{{ $c['supplier'] }}</td>
                <td>{{ $c['certification_type'] }}</td>
                <td>{{ $c['certificate_no'] }}</td>
                <td>{{ $issued ? $issued->format('d M Y') : '—' }}</td>
                <td>{{ $expiry ? $expiry->format('d M Y') : '—' }}</td>
                <td>
                  <span class="ilsam-cert-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                </td>
                <td>
                  <div class="ilsam-cert-badges">
                    @if ($proofType === 'pdf')
                      <span class="ilsam-cert-badge ilsam-cert-badge--proof">PDF</span>
                    @else
                      <span class="ilsam-cert-badge ilsam-cert-badge--missing">Missing</span>
                    @endif
                  </div>
                </td>
                <td>
                  <div class="ilsam-cert-actions">
                    <a href="#" class="ilsam-cert-action" data-cert-action="toggle"
                      data-cert-id="{{ $c['id'] }}">Details</a>
                    @if (!empty($c['zdhc_link']))
                      <a class="ilsam-cert-action" href="{{ $c['zdhc_link'] }}" target="_blank" rel="noopener">Verify</a>
                    @endif
                  </div>
                </td>
              </tr>

              <tr class="cert-detail-row" data-detail-for="{{ $c['id'] }}" style="display:none;">
                <td colspan="9">
                  <div class="ilsam-cert-detail">
                    <div class="row g-20 align-items-start">
                      <div class="col-lg-6">
                        <h4 class="mb-10">Detail</h4>
                        <div class="ilsam-cert-detail__grid">
                          <div>
                            <div class="ilsam-cert-detail__k">Chemical</div>
                            <div class="ilsam-cert-detail__v">{{ $c['chemical_name'] }}</div>
                          </div>
                          <div>
                            <div class="ilsam-cert-detail__k">Supplier</div>
                            <div class="ilsam-cert-detail__v">{{ $c['supplier'] }}</div>
                          </div>
                          <div>
                            <div class="ilsam-cert-detail__k">Type</div>
                            <div class="ilsam-cert-detail__v">{{ $c['certification_type'] }}</div>
                          </div>
                          <div>
                            <div class="ilsam-cert-detail__k">Certificate No.</div>
                            <div class="ilsam-cert-detail__v">{{ $c['certificate_no'] }}</div>
                          </div>
                          <div>
                            <div class="ilsam-cert-detail__k">Issued</div>
                            <div class="ilsam-cert-detail__v">{{ $issued ? $issued->format('d M Y') : '—' }}</div>
                          </div>
                          <div>
                            <div class="ilsam-cert-detail__k">Expiry</div>
                            <div class="ilsam-cert-detail__v">{{ $expiry ? $expiry->format('d M Y') : '—' }}</div>
                          </div>
                          <div>
                            <div class="ilsam-cert-detail__k">Status</div>
                            <div class="ilsam-cert-detail__v">{{ $statusLabel }}</div>
                          </div>
                          <div>
                            <div class="ilsam-cert-detail__k">Scope</div>
                            <div class="ilsam-cert-detail__v">{{ $c['scope'] }}</div>
                          </div>
                        </div>
                      </div>

                      <div class="col-lg-6">
                        <h4 class="mb-10">Proof</h4>
                        @if ($proofType === 'pdf' && !empty($c['proof_url']))
                          <div class="d-flex gap-2 flex-wrap mb-10">
                            <a class="rr-btn rr-btn__transparent w-auto" href="{{ $c['proof_url'] }}" target="_blank"
                              rel="noopener">
                              <span class="btn-wrap">
                                <span class="text-one">Open PDF</span>
                                <span class="text-two">Open PDF</span>
                              </span>
                            </a>
                          </div>
                          <div class="ilsam-cert-proof"><iframe src="{{ $c['proof_url'] }}"></iframe></div>
                        @else
                          <div class="ilsam-muted-sm">No proof uploaded for this certificate.</div>
                        @endif
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center py-4">No certificate data available.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <script>
    (function () {
      const searchEl = document.getElementById('certSearch');
      const statusEl = document.getElementById('certStatus');
      const supplierEl = document.getElementById('certSupplier');
      const typeEl = document.getElementById('certType');
      const scopeEl = document.getElementById('certScope');
      const proofEl = document.getElementById('certProof');
      const resetBtn = document.getElementById('certResetBtn');
      const resultsMeta = document.getElementById('certResultsMeta');
      const tbody = document.getElementById('certTbody');

      if (!tbody) return;

      function getValue(el) {
        return (el && (el.value || '')).toString().trim();
      }

      function setDetailVisible(certId, visible) {
        const detailRow = tbody.querySelector(`.cert-detail-row[data-detail-for="${certId}"]`);
        if (!detailRow) return;
        detailRow.style.display = visible ? '' : 'none';
      }

      function closeAllDetails() {
        tbody.querySelectorAll('.cert-detail-row').forEach((row) => (row.style.display = 'none'));
      }

      function matchesRow(row, query, status, supplier, type, scope, proof) {
        const chemical = row.dataset.chemical || '';
        const supplierVal = row.dataset.supplier || '';
        const typeVal = row.dataset.type || '';
        const scopeVal = row.dataset.scope || '';
        const certNo = row.dataset.certno || '';
        const statusVal = row.dataset.status || '';
        const proofVal = row.dataset.proof || '';

        const q = query.toLowerCase();
        const queryOk = !q ||
          chemical.includes(q) ||
          supplierVal.includes(q) ||
          typeVal.includes(q) ||
          certNo.includes(q) ||
          scopeVal.includes(q);

        const statusOk = !status || statusVal === status;
        const supplierOk = !supplier || supplierVal === supplier.toLowerCase();
        const typeOk = !type || typeVal === type.toLowerCase();
        const scopeOk = !scope || scopeVal === scope.toLowerCase();
        const proofOk = !proof || proofVal === proof;

        return queryOk && statusOk && supplierOk && typeOk && scopeOk && proofOk;
      }

      function applyFilters() {
        closeAllDetails();

        const query = getValue(searchEl);
        const status = getValue(statusEl);
        const supplier = getValue(supplierEl);
        const type = getValue(typeEl);
        const scope = getValue(scopeEl);
        const proof = getValue(proofEl);

        const rows = Array.from(tbody.querySelectorAll('tr.cert-row'));
        let visibleCount = 0;

        rows.forEach((row) => {
          const certId = row.dataset.certId;
          const show = matchesRow(row, query, status, supplier, type, scope, proof);
          row.style.display = show ? '' : 'none';
          setDetailVisible(certId, false);
          if (show) visibleCount++;
        });

        const totalCount = rows.length;
        if (!resultsMeta) return;
        resultsMeta.textContent = `Showing ${visibleCount} of ${totalCount} records.`;
      }

      function resetFilters() {
        if (searchEl) searchEl.value = '';
        if (statusEl) statusEl.value = '';
        if (supplierEl) supplierEl.value = '';
        if (typeEl) typeEl.value = '';
        if (scopeEl) scopeEl.value = '';
        if (proofEl) proofEl.value = '';
        applyFilters();
      }

      tbody.addEventListener('click', function (e) {
        const target = e.target;
        if (!(target instanceof HTMLElement)) return;
        const actionEl = target.closest('[data-cert-action="toggle"]');
        if (!actionEl) return;
        e.preventDefault();

        const certId = actionEl.getAttribute('data-cert-id');
        if (!certId) return;

        const detailRow = tbody.querySelector(`.cert-detail-row[data-detail-for="${certId}"]`);
        if (!detailRow) return;

        const currentlyVisible = detailRow.style.display !== 'none';
        closeAllDetails();
        detailRow.style.display = currentlyVisible ? 'none' : '';
      });

      [searchEl, statusEl, supplierEl, typeEl, scopeEl, proofEl].forEach((el) => {
        if (!el) return;
        el.addEventListener('input', applyFilters);
        el.addEventListener('change', applyFilters);
      });

      if (resetBtn) resetBtn.addEventListener('click', resetFilters);
      applyFilters();
    })();
  </script>
@endsection