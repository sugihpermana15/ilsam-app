@extends('layouts.master')

@section('title', 'Detail Device | IGI')
@section('title-sub', 'Master Device')
@section('pagetitle', 'Detail Device')

@section('css')
  <style>
    .card-header {
      background: var(--bs-gray-100);
      border-bottom: 1px solid var(--bs-border-color-translucent);
    }
    .kv-table th {
      width: 38%;
      font-weight: 600;
      color: var(--bs-gray-700);
      white-space: nowrap;
      vertical-align: top;
    }
    .kv-table td {
      color: var(--bs-gray-900);
      vertical-align: top;
    }
    .kv-table tbody tr + tr {
      border-top: 1px dashed var(--bs-border-color);
    }
    .kv-table tbody tr.kv-section {
      border-top: 1px solid var(--bs-border-color) !important;
    }
    .kv-table tbody tr.kv-section th {
      width: auto;
      padding-top: .85rem;
      padding-bottom: .55rem;
      text-transform: uppercase;
      letter-spacing: .02em;
      font-size: .82rem;
      color: var(--bs-gray-700);
    }
    .kv-table tbody tr.kv-section th i {
      color: var(--bs-primary);
    }
    .kv-table th,
    .kv-table td {
      padding-top: .45rem;
      padding-bottom: .45rem;
      line-height: 1.35;
    }
    .kv-table td {
      font-size: 1rem;
    }
    .kv-table th {
      font-size: .95rem;
    }
    .kpi-badge {
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      padding: .35rem .6rem;
      border: 1px solid var(--bs-border-color-translucent);
      border-radius: .5rem;
      background: var(--bs-gray-100);
      font-size: .875rem;
    }
    .kpi-badge i {
      color: var(--bs-gray-600) !important;
      font-size: .95em;
    }
    .kpi-strip {
      display: flex;
      flex-wrap: wrap;
      gap: .5rem .6rem;
    }

    .kv-split {
      display: grid;
      grid-template-columns: 1fr;
      gap: .75rem;
    }
    @media (min-width: 992px) {
      .kv-split {
        grid-template-columns: 1fr 1fr;
      }
    }

    .group-block {
      border: 1px solid var(--bs-border-color-translucent);
      border-radius: .75rem;
      background: var(--bs-body-bg);
      padding: .85rem;
      height: 100%;
    }
    .group-block .group-title {
      display: flex;
      align-items: center;
      gap: .5rem;
      font-weight: 700;
      color: var(--bs-gray-800);
      margin-bottom: .6rem;
    }
    .btn-copy {
      padding: .15rem .45rem;
      line-height: 1.2;
    }
    .mono-row {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      align-items: center;
      gap: .5rem;
    }
    .mono-val {
      display: block;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 100%;
    }
    .kv-table td {
      overflow-wrap: break-word;
      word-break: normal;
    }

    .storage-list {
      display: grid;
      gap: .5rem;
    }
    .storage-item {
      border: 1px solid var(--bs-border-color-translucent);
      border-radius: .6rem;
      padding: .55rem .65rem;
      background: var(--bs-gray-100);
    }
    .storage-item .top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: .5rem;
      margin-bottom: .25rem;
    }
    .storage-item .meta {
      display: grid;
      gap: .15rem;
      color: var(--bs-gray-800);
      font-size: .9rem;
    }
    .storage-item .meta .label {
      color: var(--bs-gray-600);
      font-weight: 600;
    }

    .swal2-popup .btn:focus {
      box-shadow: none !important;
    }

    .swal2-popup .vault-note {
      font-size: .9rem;
      color: var(--bs-gray-700);
      margin-top: .25rem;
      text-align: left;
    }
    .swal2-popup .vault-reveal {
      text-align: left;
    }
    .swal2-popup .vault-reveal .vault-title {
      display: flex;
      align-items: flex-start;
      gap: .6rem;
      margin-bottom: .65rem;
    }
    .swal2-popup .vault-reveal .vault-title .icon {
      color: var(--bs-warning);
      margin-top: .1rem;
    }
    .swal2-popup .vault-reveal .vault-title .meta {
      line-height: 1.15;
    }
    .swal2-popup .vault-reveal .vault-title .meta .h {
      font-weight: 700;
      color: var(--bs-gray-900);
    }
    .swal2-popup .vault-reveal .vault-title .meta .s {
      font-size: .875rem;
      color: var(--bs-gray-600);
    }
    .swal2-popup .vault-reveal .vault-actions {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: .75rem;
      margin-top: .25rem;
    }
    .swal2-popup .vault-reveal .vault-toggle-link {
      color: var(--bs-primary);
      text-decoration: none;
      font-size: .9rem;
      font-weight: 600;
      user-select: none;
    }
    .swal2-popup .vault-reveal .vault-toggle-link:hover {
      text-decoration: underline;
    }
  </style>
@endsection

@section('content')
  @php
    $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'devices', 'update');
    $canDelete = \App\Support\MenuAccess::can(auth()->user(), 'devices', 'delete');

    $assetStatusRaw = (string) ($device->asset_status ?? '');
    $assetStatusBadge = match($assetStatusRaw) {
      'Active' => 'bg-success-subtle text-success',
      'Inactive' => 'bg-secondary-subtle text-secondary',
      'Sold' => 'bg-warning-subtle text-warning',
      'Disposed' => 'bg-danger-subtle text-danger',
      default => 'bg-light-subtle text-body',
    };

    $domainJoinRaw = (string) ($device->domain_join_status ?? '');
    $domainJoinBadge = match(strtolower(trim($domainJoinRaw))) {
      'domain', 'joined' => 'bg-success-subtle text-success',
      'workgroup' => 'bg-info-subtle text-info',
      'none', 'not joined', 'not_joined' => 'bg-secondary-subtle text-secondary',
      '' => 'bg-light-subtle text-body',
      default => 'bg-light-subtle text-body',
    };
  @endphp

  <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.all.js') }}"></script>
  <script>
    function escapeHtml(str) {
      return String(str)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
    }

    document.addEventListener('DOMContentLoaded', function () {
      @if(session('success'))
        Swal.fire({ icon: 'success', title: @json(__('common.success')), text: @json(session('success')), timer: 2000, showConfirmButton: false });
      @endif
      @if(session('error'))
        Swal.fire({ icon: 'error', title: @json(__('common.error')), text: @json(session('error')), timer: 2500, showConfirmButton: false });
      @endif

      document.querySelectorAll('[data-reveal-vault]')?.forEach(function (btn) {
        btn.addEventListener('click', async function () {
          const field = btn.getAttribute('data-field');
          const label = btn.getAttribute('data-label') || 'Password';
          const url = btn.getAttribute('data-url');
          if (!field || !url) return;

          const { value, isConfirmed } = await Swal.fire({
            title: 'Konfirmasi Password Login',
            html: '<div class="vault-note"><i class="fas fa-shield-alt me-1"></i>Masukkan password akun kamu untuk membuka vault.</div>',
            input: 'password',
            inputAttributes: {
              autocapitalize: 'off',
              autocomplete: 'current-password',
              placeholder: 'Password login…',
            },
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<i class="fas fa-lock-open me-1"></i> Buka Vault',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading(),
            preConfirm: async (currentPassword) => {
              if (!currentPassword) {
                Swal.showValidationMessage('Password wajib diisi.');
                return;
              }

              const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || @json(csrf_token());
              const resp = await fetch(url, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ field: field, password: currentPassword }),
              });

              const payload = await resp.json().catch(() => ({}));
              if (!resp.ok) {
                Swal.showValidationMessage(payload?.message || 'Gagal membuka vault.');
                return;
              }

              return payload?.value || '';
            }
          });

          if (!isConfirmed) return;

          if (!value) {
            await Swal.fire({
              icon: 'info',
              title: 'Tidak ada password',
              text: `${label} belum diisi.`,
            });
            return;
          }

          const revealResult = await Swal.fire({
            title: label,
            html: `
              <div class="vault-reveal">
                <div class="vault-title">
                  <div class="icon"><i class="fas fa-shield-alt"></i></div>
                  <div class="meta">
                    <div class="h">Rahasia (Vault)</div>
                    <div class="s">Gunakan tombol <b>Copy</b>. Hindari screenshot / share.</div>
                  </div>
                </div>
                <input id="vault-value" type="password" class="swal2-input font-monospace" value="${escapeHtml(value)}" readonly style="margin-left:0;margin-right:0;">
                <div class="vault-actions">
                  <a href="#" class="vault-toggle-link" id="vault-toggle">
                    <i class="fas fa-eye me-1"></i> Lihat / Sembunyi
                  </a>
                  <div class="small text-muted">Tip: jangan screenshot / share.</div>
                </div>
              </div>
            `,
            showCloseButton: true,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<i class="fas fa-copy me-1"></i> Copy',
            cancelButtonText: 'Tutup',
            width: 560,
            heightAuto: false,
            didOpen: () => {
              const toggleLink = document.getElementById('vault-toggle');
              const input = document.getElementById('vault-value');
              if (!toggleLink || !input) return;

              toggleLink.addEventListener('click', (e) => {
                e.preventDefault();
                const isHidden = input.getAttribute('type') === 'password';
                input.setAttribute('type', isHidden ? 'text' : 'password');
                toggleLink.innerHTML = isHidden
                  ? '<i class="fas fa-eye-slash me-1"></i> Sembunyikan'
                  : '<i class="fas fa-eye me-1"></i> Lihat';
              });
            },
            preConfirm: async () => {
              const input = document.getElementById('vault-value');
              const val = input?.value || '';
              if (!val) {
                Swal.showValidationMessage('Tidak ada nilai untuk di-copy.');
                return;
              }
              const ok = await copyTextToClipboard(val);
              if (!ok) {
                Swal.showValidationMessage('Tidak bisa copy ke clipboard.');
                return;
              }
              return true;
            }
          });

          if (revealResult.isConfirmed) {
            Swal.fire({
              icon: 'success',
              title: 'Copied',
              text: 'Password berhasil dicopy.',
              timer: 1200,
              showConfirmButton: false,
            });
          }
        });
      });

      async function copyTextToClipboard(text) {
        try {
          await navigator.clipboard.writeText(text);
          return true;
        } catch (e) {
          try {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.setAttribute('readonly', '');
            ta.style.position = 'absolute';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            return true;
          } catch (e2) {
            return false;
          }
        }
      }

      document.querySelectorAll('[data-copy-target]')?.forEach(function (btn) {
        btn.addEventListener('click', async function () {
          const direct = (btn.getAttribute('data-copy-text') || '').trim();
          const sel = btn.getAttribute('data-copy-target');
          const target = sel ? document.querySelector(sel) : null;
          const text = direct || (target?.textContent || '').trim();
          if (!text || text === '-') return;

          const ok = await copyTextToClipboard(text);
          if (ok) {
            Swal.fire({
              toast: true,
              position: 'bottom-end',
              icon: 'success',
              title: 'Copied',
              showConfirmButton: false,
              timer: 1200,
              timerProgressBar: true,
            });
          } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak bisa copy ke clipboard.' });
          }
        });
      });

      document.querySelectorAll('form[data-confirm-submit]')?.forEach(function (form) {
        form.addEventListener('submit', async function (e) {
          e.preventDefault();

          const title = form.getAttribute('data-confirm-title') || 'Konfirmasi';
          const text = form.getAttribute('data-confirm-text') || 'Yakin lanjutkan aksi ini?';
          const confirmText = form.getAttribute('data-confirm-button') || 'Ya, lanjutkan';

          const result = await Swal.fire({
            icon: 'warning',
            title: title,
            text: text,
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
          });

          if (result.isConfirmed) {
            form.submit();
          }
        });
      });
    });
  </script>

  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <h5 class="card-title mb-0">Detail Device</h5>
        <div class="text-muted small">Master Device</div>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('admin.devices.index') }}" class="btn btn-light">
          <i class="fas fa-arrow-left"></i> {{ __('common.back') }}
        </a>
        <a href="{{ route('admin.devices.edit', $device) }}" class="btn btn-warning" {{ $canUpdate ? '' : 'disabled' }}>
          <i class="fas fa-edit"></i> {{ __('common.edit') }}
        </a>
        <form
          action="{{ route('admin.devices.destroy', $device) }}"
          method="POST"
          data-confirm-submit
          data-confirm-title="Hapus Device"
          data-confirm-text="Yakin hapus device ini? Data yang sudah dihapus tidak bisa dikembalikan."
          data-confirm-button="Ya, hapus"
        >
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger" {{ $canDelete ? '' : 'disabled' }}>
            <i class="fas fa-trash-alt"></i> {{ __('common.delete') }}
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="kpi-strip mb-3">
    @if(!empty($device->asset_code))
      <span class="kpi-badge"><i class="fas fa-tag"></i><span class="fw-semibold font-monospace">{{ $device->asset_code }}</span></span>
    @endif
    @if(!empty($device->device_name))
      <span class="kpi-badge"><i class="fas fa-desktop"></i><span class="fw-semibold">{{ $device->device_name }}</span></span>
    @endif
    @if(!empty($device->os_name) || !empty($device->os_edition))
      <span class="kpi-badge"><i class="fab fa-windows"></i><span>{{ trim(($device->os_name ?? '') . ' ' . ($device->os_edition ?? '')) }}</span></span>
    @endif
    @if($device->ram_gb !== null)
      <span class="kpi-badge"><i class="fas fa-memory"></i><span>{{ $device->ram_gb }} GB RAM</span></span>
    @endif
    @if($device->storage_total_gb !== null)
      <span class="kpi-badge"><i class="fas fa-hdd"></i><span>{{ $device->storage_total_gb }} GB Storage</span></span>
    @endif
    @if(!empty($device->connectivity))
      <span class="kpi-badge"><i class="fas fa-wifi"></i><span>{{ $device->connectivity }}</span></span>
    @endif
  </div>

  <div class="row g-3 mb-3">
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header">
          <div class="d-flex align-items-center gap-2">
            <i class="fas fa-box text-primary"></i>
            <h6 class="card-title mb-0">Ringkasan Aset</h6>
          </div>
        </div>
        <div class="card-body">
          <div class="kv-split">
            <div class="table-responsive">
              <table class="table table-sm table-borderless mb-0 kv-table">
                <tbody>
                  <tr>
                    <th>Kode Aset</th>
                    <td><span class="font-monospace">{{ $device->asset_code ?? '-' }}</span></td>
                  </tr>
                  <tr>
                    <th>Nama Aset</th>
                    <td>{{ $device->asset_name ?? '-' }}</td>
                  </tr>
                  <tr>
                    <th>Nomor Seri</th>
                    <td><span class="font-monospace">{{ $device->asset_serial_number ?? '-' }}</span></td>
                  </tr>
                  <tr>
                    <th>Status Aset</th>
                    <td>
                      @if(($device->asset_status ?? null) !== null && $device->asset_status !== '')
                        <span class="badge {{ $assetStatusBadge }}">{{ $device->asset_status }}</span>
                      @else
                        -
                      @endif
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="table-responsive">
              <table class="table table-sm table-borderless mb-0 kv-table">
                <tbody>
                  <tr>
                    <th>Lokasi</th>
                    <td>{{ $device->asset_location ?? '-' }}</td>
                  </tr>
                  <tr>
                    <th>Pengguna/PIC</th>
                    <td>{{ $device->asset_person_in_charge ?? '-' }}</td>
                  </tr>
                  <tr>
                    <th>Departemen</th>
                    <td>{{ $device->asset_department ?? '-' }}</td>
                  </tr>
                  <tr>
                    <th>Site / Ruangan</th>
                    <td>{{ trim(($device->location_site ?? '') . ' ' . ($device->location_room ?? '')) ?: '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header">
          <div class="d-flex align-items-center gap-2">
            <i class="fas fa-desktop text-primary"></i>
            <h6 class="card-title mb-0">Device & OS</h6>
          </div>
        </div>
        <div class="card-body">
          <div class="kv-split">
            <div class="table-responsive">
              <table class="table table-sm table-borderless mb-0 kv-table">
                <tbody>
                  <tr>
                    <th>Nama Perangkat (Hostname)</th>
                    <td>{{ $device->device_name ?? '-' }}</td>
                  </tr>
                  <tr>
                    <th>Device ID</th>
                    <td>
                      <div class="mono-row">
                        <span id="device-id-value" class="font-monospace mono-val" title="{{ $device->device_id ?? '' }}">{{ $device->device_id ?? '-' }}</span>
                        @if(!empty($device->device_id))
                          <button type="button" class="btn btn-sm btn-outline-secondary btn-copy" data-copy-text="{{ $device->device_id }}" data-copy-target="#device-id-value" title="Copy">
                            <i class="fas fa-copy"></i>
                          </button>
                        @endif
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>Product ID</th>
                    <td><span class="font-monospace">{{ $device->product_id ?? '-' }}</span></td>
                  </tr>
                  <tr>
                    <th>OS</th>
                    <td>{{ $device->os_name ?? '-' }}</td>
                  </tr>
                  <tr>
                    <th>Edition</th>
                    <td>{{ $device->os_edition ?? '-' }}</td>
                  </tr>
                  <tr>
                    <th>Build/Version</th>
                    <td><span class="font-monospace">{{ $device->os_version ?? '-' }}</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="table-responsive">
              <table class="table table-sm table-borderless mb-0 kv-table">
                <tbody>
                  <tr>
                    <th>Domain Join</th>
                    <td>
                      @if(($device->domain_join_status ?? '') !== '')
                        <span class="badge {{ $domainJoinBadge }}">{{ $device->domain_join_status }}</span>
                      @else
                        -
                      @endif
                    </td>
                  </tr>
                  <tr>
                    <th>Domain Name</th>
                    <td>{{ $device->domain_name ?? '-' }}</td>
                  </tr>
                  <tr>
                    <th>Workgroup</th>
                    <td>{{ $device->workgroup_name ?? '-' }}</td>
                  </tr>
                  <tr>
                    <th>Domain/Workgroup (Legacy)</th>
                    <td>{{ $device->domain_workgroup ?? '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header">
          <div class="d-flex align-items-center gap-2">
            <i class="fas fa-microchip text-primary"></i>
            <h6 class="card-title mb-0">Hardware</h6>
          </div>
        </div>
        <div class="card-body">
          <div class="kv-split">
            <div class="table-responsive">
              <table class="table table-sm table-borderless mb-0 kv-table">
                <tbody>
                  <tr>
                    <th>Processor</th>
                    <td>{{ $device->processor ?? '-' }}</td>
                  </tr>
                  <tr>
                    <th>RAM</th>
                    <td>{{ $device->ram_gb !== null ? $device->ram_gb . ' GB' : '-' }}</td>
                  </tr>
                  <tr>
                    <th>Storage</th>
                    <td>{{ $device->storage_total_gb !== null ? $device->storage_total_gb . ' GB' : '-' }}</td>
                  </tr>
                  <tr>
                    <th>Storage Type</th>
                    <td>{{ $device->storage_type ?? '-' }}</td>
                  </tr>
                  <tr>
                    <th>GPU</th>
                    <td>{{ $device->gpu ?? '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="table-responsive">
              <table class="table table-sm table-borderless mb-0 kv-table">
                <tbody>
                  <tr>
                    <th>Storage Items</th>
                    <td>
                      @php
                        $items = $device->storage_items;
                        if (!is_array($items)) {
                          $items = [];
                        }
                      @endphp

                      @if(count($items) === 0)
                        <span class="text-muted">-</span>
                      @else
                        <div class="storage-list">
                          @foreach($items as $it)
                            <div class="storage-item">
                              <div class="top">
                                <div class="d-flex align-items-center gap-2">
                                  <span class="badge bg-light-subtle text-body">{{ $it['type'] ?? '-' }}</span>
                                  <span class="fw-semibold">{{ isset($it['size_gb']) && $it['size_gb'] !== null ? ($it['size_gb'] . ' GB') : '-' }}</span>
                                </div>
                                @if(!empty($it['serial'] ?? null))
                                  <span class="font-monospace text-muted">{{ $it['serial'] }}</span>
                                @endif
                              </div>
                              <div class="meta">
                                <div><span class="label">Model:</span> {{ $it['model'] ?? '-' }}</div>
                                <div><span class="label">Notes:</span> {{ $it['notes'] ?? '-' }}</div>
                              </div>
                            </div>
                          @endforeach
                        </div>
                      @endif
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header">
          <div class="d-flex align-items-center gap-2">
            <i class="fas fa-network-wired text-primary"></i>
            <h6 class="card-title mb-0">Network</h6>
          </div>
        </div>
        <div class="card-body">
          <table class="table table-sm table-borderless mb-0 kv-table">
            <tbody>
              <tr class="kv-section">
                <th colspan="2"><i class="fas fa-sitemap me-2"></i> IP Config</th>
              </tr>
              <tr>
                <th>IP Address</th>
                <td>
                  <div class="mono-row">
                    <span id="ip-address-value" class="font-monospace mono-val">{{ $device->ip_address ?? '-' }}</span>
                    @if(!empty($device->ip_address))
                      <button type="button" class="btn btn-sm btn-outline-secondary btn-copy" data-copy-text="{{ (string) ($device->ip_address ?? '') }}" data-copy-target="#ip-address-value" title="Copy">
                        <i class="fas fa-copy"></i>
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
              <tr>
                <th>Subnet Mask</th>
                <td><span class="font-monospace">{{ $device->subnet_mask ?? '-' }}</span></td>
              </tr>
              <tr>
                <th>Gateway</th>
                <td><span class="font-monospace">{{ $device->gateway ?? '-' }}</span></td>
              </tr>
              <tr>
                <th>DNS Primary</th>
                <td><span class="font-monospace">{{ $device->dns_primary ?? '-' }}</span></td>
              </tr>
              <tr>
                <th>DNS Secondary</th>
                <td><span class="font-monospace">{{ $device->dns_secondary ?? '-' }}</span></td>
              </tr>
              @if(!empty($device->dns))
                <tr>
                  <th>DNS (Legacy)</th>
                  <td><span class="font-monospace">{{ $device->dns }}</span></td>
                </tr>
              @endif

              <tr class="kv-section">
                <th colspan="2"><i class="fas fa-wifi me-2"></i> WiFi</th>
              </tr>
              <tr>
                <th>Connectivity</th>
                <td>{{ $device->connectivity ?? '-' }}</td>
              </tr>
              <tr>
                <th>SSID</th>
                <td>{{ $device->ssid ?? '-' }}</td>
              </tr>

              <tr class="kv-section">
                <th colspan="2"><i class="fas fa-ethernet me-2"></i> MAC</th>
              </tr>
              <tr>
                <th>MAC (LAN)</th>
                <td>
                  <div class="mono-row">
                    <span id="mac-lan-value" class="font-monospace mono-val">{{ $device->mac_lan ?? '-' }}</span>
                    @if(!empty($device->mac_lan))
                      <button type="button" class="btn btn-sm btn-outline-secondary btn-copy" data-copy-text="{{ (string) ($device->mac_lan ?? '') }}" data-copy-target="#mac-lan-value" title="Copy">
                        <i class="fas fa-copy"></i>
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
              <tr>
                <th>MAC (WiFi)</th>
                <td>
                  <div class="mono-row">
                    <span id="mac-wifi-value" class="font-monospace mono-val">{{ $device->mac_wifi ?? '-' }}</span>
                    @if(!empty($device->mac_wifi))
                      <button type="button" class="btn btn-sm btn-outline-secondary btn-copy" data-copy-text="{{ (string) ($device->mac_wifi ?? '') }}" data-copy-target="#mac-wifi-value" title="Copy">
                        <i class="fas fa-copy"></i>
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex align-items-center gap-2">
            <i class="fas fa-key text-primary"></i>
            <h6 class="card-title mb-0">Remote Access & Vault</h6>
          </div>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-lg-6">
              <div class="table-responsive">
                <table class="table table-sm table-borderless mb-0 kv-table">
                  <tbody>
                    <tr>
                      <th>Vault Mode</th>
                      <td>
                        @if($device->vault_mode)
                          <span class="badge bg-success-subtle text-success">Enabled</span>
                        @else
                          <span class="badge bg-secondary-subtle text-secondary">Disabled</span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Remote App Type</th>
                      <td>{{ $device->remote_app_type ?? '-' }}</td>
                    </tr>
                    <tr>
                      <th>Remote ID</th>
                      <td>{{ $device->remote_id ?? '-' }}</td>
                    </tr>
                    <tr>
                      <th>Remote Password</th>
                      <td>
                        @if(!$device->vault_mode)
                          <span class="badge bg-secondary-subtle text-secondary">Vault Disabled</span>
                        @elseif(!empty($device->getRawOriginal('remote_password')))
                          <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light-subtle text-body">Encrypted</span>
                            @if($canUpdate)
                              <button
                                type="button"
                                class="btn btn-sm btn-outline-primary"
                                data-reveal-vault
                                data-field="remote_password"
                                data-label="Remote Password"
                                data-url="{{ route('admin.devices.vault.reveal', $device) }}"
                              >
                                <i class="fas fa-eye"></i> Lihat
                              </button>
                            @endif
                          </div>
                        @else
                          -
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Unattended Access</th>
                      <td>
                        @if($device->remote_unattended)
                          <span class="badge bg-success-subtle text-success">Yes</span>
                        @else
                          <span class="badge bg-secondary-subtle text-secondary">No</span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Remote Notes</th>
                      <td>{{ $device->remote_notes ?? '-' }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="table-responsive">
                <table class="table table-sm table-borderless mb-0 kv-table">
                  <tbody>
                    <tr>
                      <th>Local Admin Username</th>
                      <td>{{ $device->local_admin_username ?? '-' }}</td>
                    </tr>
                    <tr>
                      <th>Password</th>
                      <td>
                        @if(!$device->vault_mode)
                          <span class="badge bg-secondary-subtle text-secondary">Vault Disabled</span>
                        @elseif(!empty($device->getRawOriginal('local_admin_password')))
                          <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light-subtle text-body">Encrypted</span>
                            @if($canUpdate)
                              <button
                                type="button"
                                class="btn btn-sm btn-outline-primary"
                                data-reveal-vault
                                data-field="local_admin_password"
                                data-label="Local Admin Password"
                                data-url="{{ route('admin.devices.vault.reveal', $device) }}"
                              >
                                <i class="fas fa-eye"></i> Lihat
                              </button>
                            @endif
                          </div>
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="mt-2">
                <small class="text-muted">Catatan: password disimpan terenkripsi (vault). Untuk mengganti, gunakan menu Edit.</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2">
        <i class="fas fa-tools text-primary"></i>
        <h5 class="card-title mb-0">Maintenance & Riwayat</h5>
      </div>
      <div class="text-muted small">Last: {{ $device->last_maintenance_at ? $device->last_maintenance_at->format('d-m-Y H:i') : '-' }}</div>
    </div>
    <div class="card-body">
      <form action="{{ route('admin.devices.maintenances.store', $device) }}" method="POST" enctype="multipart/form-data" class="mb-4">
        @csrf
        <div class="row g-2">
          <div class="col-md-3">
            <label class="form-label">Tanggal</label>
            <input type="datetime-local" name="maintenance_at" class="form-control" value="{{ now()->format('Y-m-d\\TH:i') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select" required>
              <option value="Preventive">Preventive</option>
              <option value="Corrective">Corrective</option>
              <option value="Upgrade">Upgrade</option>
              <option value="Reimage">Reimage</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Performed by</label>
            <input type="text" name="performed_by" class="form-control" placeholder="Nama teknisi">
          </div>
          <div class="col-md-3">
            <label class="form-label">Next schedule</label>
            <input type="date" name="next_schedule_at" class="form-control">
          </div>
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="2" required></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Attachment (jpg/png/pdf)</label>
            <input type="file" name="attachment" class="form-control">
          </div>
          <div class="col-md-6 d-flex align-items-end justify-content-end">
            <button class="btn btn-primary" type="submit" {{ $canUpdate ? '' : 'disabled' }}>Tambah Maintenance</button>
          </div>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle mb-0">
          <thead>
            <tr class="table-light">
              <th>Tanggal</th>
              <th>Type</th>
              <th>Description</th>
              <th>Performed by</th>
              <th>Attachment</th>
              <th>Next</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($device->maintenances as $m)
              <tr>
                <td>{{ $m->maintenance_at ? $m->maintenance_at->format('d-m-Y H:i') : '-' }}</td>
                <td>{{ $m->type }}</td>
                <td style="white-space: pre-wrap;">{{ $m->description }}</td>
                <td>{{ $m->performed_by ?? '-' }}</td>
                <td>
                  @if($m->attachment_path)
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.devices.maintenances.download', $m) }}">
                      <i class="fas fa-download"></i> Download
                    </a>
                  @else
                    -
                  @endif
                </td>
                <td>{{ $m->next_schedule_at ? $m->next_schedule_at->format('d-m-Y') : '-' }}</td>
                <td class="text-nowrap">
                  <form
                    action="{{ route('admin.devices.maintenances.destroy', $m) }}"
                    method="POST"
                    style="display:inline-block"
                    data-confirm-submit
                    data-confirm-title="Hapus Maintenance"
                    data-confirm-text="Yakin hapus record maintenance ini?"
                    data-confirm-button="Ya, hapus"
                  >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" {{ $canUpdate ? '' : 'disabled' }}>
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted">Belum ada riwayat maintenance.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
