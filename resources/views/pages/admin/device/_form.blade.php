@php
  /** @var \App\Models\Device|null $device */
  $isEdit = isset($device) && $device && $device->exists;
  $storageItems = old('storage_items', $device->storage_items ?? []);
  if (!is_array($storageItems)) {
    $storageItems = [];
  }
@endphp

<div class="row g-3">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <h6 class="mb-0">Asset</h6>
      <span class="text-muted small">Pilih kode asset untuk auto-fill.</span>
    </div>
    <hr class="mt-2 mb-0">
  </div>

  <div class="col-12">
    <label class="form-label">Kode Asset</label>
    <select class="form-select" name="asset_code" id="asset_code" required>
      <option value="">-- pilih kode asset --</option>
      @foreach($assets as $asset)
        <option value="{{ $asset->asset_code }}" {{ old('asset_code', $device->asset_code ?? '') == $asset->asset_code ? 'selected' : '' }}>
          {{ $asset->asset_code }} — {{ $asset->asset_name }}
        </option>
      @endforeach
    </select>
    @error('asset_code')
      <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-6">
    <label class="form-label">Asset Name (auto)</label>
    <input type="text" class="form-control" id="asset_name_preview" value="" disabled>
  </div>
  <div class="col-md-6">
    <label class="form-label">Serial Number (auto)</label>
    <input type="text" class="form-control" id="asset_serial_preview" value="" disabled>
  </div>
  <div class="col-md-4">
    <label class="form-label">Status Device (auto)</label>
    <input type="text" class="form-control" id="asset_status_preview" value="" disabled>
  </div>
  <div class="col-md-4">
    <label class="form-label">User/PIC (auto)</label>
    <input type="text" class="form-control" id="asset_pic_preview" value="" disabled>
  </div>
  <div class="col-md-4">
    <label class="form-label">Department/Divisi (auto)</label>
    <input type="text" class="form-control" id="asset_department_preview" value="" disabled>
  </div>
  <div class="col-md-6">
    <label class="form-label">Location/Site (auto)</label>
    <input type="text" class="form-control" id="asset_location_preview" value="" disabled>
  </div>

  <div class="col-12">
    <h6 class="mb-0">Lokasi (Manual)</h6>
    <hr class="mt-2 mb-0">
  </div>
  <div class="col-md-3">
    <label class="form-label">Site (opsional)</label>
    <input type="text" class="form-control" name="location_site" value="{{ old('location_site', $device->location_site ?? '') }}">
  </div>
  <div class="col-md-3">
    <label class="form-label">Ruangan (opsional)</label>
    <input type="text" class="form-control" name="location_room" value="{{ old('location_room', $device->location_room ?? '') }}">
  </div>

  <div class="col-12">
    <h6 class="mb-0">Klasifikasi</h6>
    <hr class="mt-2 mb-0">
  </div>

  <div class="col-md-4">
    <label class="form-label">Device Role</label>
    <select class="form-select" name="device_role">
      @php $role = old('device_role', $device->device_role ?? ''); @endphp
      <option value="">-- pilih --</option>
      @foreach(['Laptop','Desktop','MiniPC','VM'] as $opt)
        <option value="{{ $opt }}" {{ $role === $opt ? 'selected' : '' }}>{{ $opt }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label">Owner Type</label>
    <select class="form-select" name="owner_type">
      @php $ownerType = old('owner_type', $device->owner_type ?? ''); @endphp
      <option value="">-- pilih --</option>
      @foreach(['Company','Personal'] as $opt)
        <option value="{{ $opt }}" {{ $ownerType === $opt ? 'selected' : '' }}>{{ $opt }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-md-4">
    <label class="form-label">Device Name (hostname)</label>
    <input type="text" class="form-control" name="device_name" value="{{ old('device_name', $device->device_name ?? '') }}">
  </div>
  <div class="col-md-4">
    <label class="form-label">Device ID (UUID/BIOS/Windows ID)</label>
    <input type="text" class="form-control" name="device_id" value="{{ old('device_id', $device->device_id ?? '') }}">
  </div>
  <div class="col-md-4">
    <label class="form-label">Product ID (Windows)</label>
    <input type="text" class="form-control" name="product_id" value="{{ old('product_id', $device->product_id ?? '') }}">
  </div>

  <div class="col-12">
    <h6 class="mb-0">OS & Domain</h6>
    <hr class="mt-2 mb-0">
  </div>

  <div class="col-md-4">
    <label class="form-label">OS</label>
    <input type="text" class="form-control" name="os_name" placeholder="Windows 10 / Windows 11" value="{{ old('os_name', $device->os_name ?? '') }}">
  </div>
  <div class="col-md-4">
    <label class="form-label">Edition</label>
    <input type="text" class="form-control" name="os_edition" placeholder="Pro / Enterprise" value="{{ old('os_edition', $device->os_edition ?? '') }}">
  </div>
  <div class="col-md-4">
    <label class="form-label">Build/Version</label>
    <input type="text" class="form-control" name="os_version" value="{{ old('os_version', $device->os_version ?? '') }}">
  </div>

  <div class="col-md-4">
    <label class="form-label">Domain Join Status</label>
    <select class="form-select" name="domain_join_status">
      @php $djs = old('domain_join_status', $device->domain_join_status ?? ''); @endphp
      <option value="">-- pilih --</option>
      @foreach(['Domain','Workgroup','None'] as $opt)
        <option value="{{ $opt }}" {{ $djs === $opt ? 'selected' : '' }}>{{ $opt }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label">Domain Name</label>
    <input type="text" class="form-control" name="domain_name" value="{{ old('domain_name', $device->domain_name ?? '') }}" placeholder="contoh: corp.local">
  </div>
  <div class="col-md-4">
    <label class="form-label">Workgroup Name</label>
    <input type="text" class="form-control" name="workgroup_name" value="{{ old('workgroup_name', $device->workgroup_name ?? '') }}" placeholder="contoh: WORKGROUP">
  </div>

  <div class="col-md-6">
    <label class="form-label">Domain/Workgroup (legacy)</label>
    <input type="text" class="form-control" name="domain_workgroup" value="{{ old('domain_workgroup', $device->domain_workgroup ?? '') }}">
  </div>

  <div class="col-md-6">
    <label class="form-label">Processor</label>
    <input type="text" class="form-control" name="processor" value="{{ old('processor', $device->processor ?? '') }}">
  </div>

  <div class="col-12">
    <h6 class="mb-0">Hardware</h6>
    <hr class="mt-2 mb-0">
  </div>
  <div class="col-md-3">
    <label class="form-label">RAM (GB)</label>
    <input type="number" class="form-control" name="ram_gb" value="{{ old('ram_gb', $device->ram_gb ?? '') }}" min="0">
  </div>
  <div class="col-md-3">
    <label class="form-label">Storage Total (GB)</label>
    <input type="number" class="form-control" name="storage_total_gb" value="{{ old('storage_total_gb', $device->storage_total_gb ?? '') }}" min="0">
  </div>
  <div class="col-md-3">
    <label class="form-label">Storage Type</label>
    <select class="form-select" name="storage_type">
      @php $st = old('storage_type', $device->storage_type ?? ''); @endphp
      <option value="">-- pilih --</option>
      @foreach(['HDD','SSD','NVMe'] as $opt)
        <option value="{{ $opt }}" {{ $st === $opt ? 'selected' : '' }}>{{ $opt }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3">
    <label class="form-label">GPU (opsional)</label>
    <input type="text" class="form-control" name="gpu" value="{{ old('gpu', $device->gpu ?? '') }}">
  </div>

  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <div class="fw-semibold">Storage (Multiple)</div>
        <div class="text-muted small">Gunakan ini jika device memiliki lebih dari 1 storage (mis. NVMe + SSD).</div>
      </div>
      <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddStorageRow">
        <i class="fas fa-plus"></i> Tambah
      </button>
    </div>
    <div class="table-responsive mt-2">
      <table class="table table-bordered table-sm align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width: 140px;">Type</th>
            <th style="width: 140px;">Size (GB)</th>
            <th>Model</th>
            <th>Serial</th>
            <th>Notes</th>
            <th style="width: 70px;">Aksi</th>
          </tr>
        </thead>
        <tbody id="storageItemsTbody">
          @php
            $rows = $storageItems;
            if (count($rows) === 0) {
              $rows = [ [] ];
            }
          @endphp
          @foreach($rows as $i => $row)
            @php
              $rowType = old("storage_items.$i.type", $row['type'] ?? '');
              $rowSize = old("storage_items.$i.size_gb", $row['size_gb'] ?? '');
              $rowModel = old("storage_items.$i.model", $row['model'] ?? '');
              $rowSerial = old("storage_items.$i.serial", $row['serial'] ?? '');
              $rowNotes = old("storage_items.$i.notes", $row['notes'] ?? '');
            @endphp
            <tr data-storage-row="1">
              <td>
                <select class="form-select form-select-sm" name="storage_items[{{ $i }}][type]">
                  <option value="">-- pilih --</option>
                  @foreach(['HDD','SSD','NVMe','Other'] as $opt)
                    <option value="{{ $opt }}" {{ (string)$rowType === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                  @endforeach
                </select>
              </td>
              <td>
                <input type="number" class="form-control form-control-sm" name="storage_items[{{ $i }}][size_gb]" value="{{ $rowSize }}" min="0">
              </td>
              <td>
                <input type="text" class="form-control form-control-sm" name="storage_items[{{ $i }}][model]" value="{{ $rowModel }}" placeholder="contoh: Samsung 970 EVO">
              </td>
              <td>
                <input type="text" class="form-control form-control-sm" name="storage_items[{{ $i }}][serial]" value="{{ $rowSerial }}">
              </td>
              <td>
                <input type="text" class="form-control form-control-sm" name="storage_items[{{ $i }}][notes]" value="{{ $rowNotes }}">
              </td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btnRemoveStorageRow" title="Hapus">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div class="col-12"><hr class="my-2"></div>

  <div class="col-12">
    <h6 class="mb-0">Network</h6>
    <hr class="mt-2 mb-0">
  </div>

  <div class="col-md-6">
    <label class="form-label">MAC Address (LAN)</label>
    <input type="text" class="form-control" name="mac_lan" value="{{ old('mac_lan', $device->mac_lan ?? '') }}">
  </div>
  <div class="col-md-6">
    <label class="form-label">MAC Address (WiFi)</label>
    <input type="text" class="form-control" name="mac_wifi" value="{{ old('mac_wifi', $device->mac_wifi ?? '') }}">
  </div>
  <div class="col-md-3">
    <label class="form-label">IP Address</label>
    <input type="text" class="form-control" name="ip_address" value="{{ old('ip_address', $device->ip_address ?? '') }}">
  </div>
  <div class="col-md-3">
    <label class="form-label">Subnet Mask</label>
    <input type="text" class="form-control" name="subnet_mask" value="{{ old('subnet_mask', $device->subnet_mask ?? '') }}">
  </div>
  <div class="col-md-3">
    <label class="form-label">Gateway</label>
    <input type="text" class="form-control" name="gateway" value="{{ old('gateway', $device->gateway ?? '') }}">
  </div>
  <div class="col-md-3">
    <label class="form-label">DNS Primary</label>
    <input type="text" class="form-control" name="dns_primary" value="{{ old('dns_primary', $device->dns_primary ?? '') }}">
  </div>
  <div class="col-md-3">
    <label class="form-label">DNS Secondary</label>
    <input type="text" class="form-control" name="dns_secondary" value="{{ old('dns_secondary', $device->dns_secondary ?? '') }}">
  </div>

  <div class="col-md-3">
    <label class="form-label">Connectivity</label>
    <select class="form-select" name="connectivity">
      @php $conn = old('connectivity', $device->connectivity ?? ''); @endphp
      <option value="">-- pilih --</option>
      @foreach(['LAN','WiFi'] as $opt)
        <option value="{{ $opt }}" {{ $conn === $opt ? 'selected' : '' }}>{{ $opt }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3">
    <label class="form-label">SSID (WiFi)</label>
    <input type="text" class="form-control" name="ssid" value="{{ old('ssid', $device->ssid ?? '') }}">
  </div>
  <div class="col-md-3">
    <label class="form-label">Internet Speed Download (Mbps)</label>
    <input type="number" step="0.01" class="form-control" name="internet_download_mbps" value="{{ old('internet_download_mbps', $device->internet_download_mbps ?? '') }}" min="0">
  </div>
  <div class="col-md-3">
    <label class="form-label">Internet Speed Upload (Mbps)</label>
    <input type="number" step="0.01" class="form-control" name="internet_upload_mbps" value="{{ old('internet_upload_mbps', $device->internet_upload_mbps ?? '') }}" min="0">
  </div>

  <div class="col-12"><hr class="my-2"></div>

  <div class="col-12">
    <h6 class="mb-0">Remote & Vault</h6>
    <hr class="mt-2 mb-0">
  </div>

  <div class="col-md-3">
    <div class="form-check mt-4">
      @php $vault = (bool) old('vault_mode', $device->vault_mode ?? true); @endphp
      <input class="form-check-input" type="checkbox" value="1" id="vault_mode" name="vault_mode" {{ $vault ? 'checked' : '' }}>
      <label class="form-check-label" for="vault_mode">Vault mode (encrypt)</label>
    </div>
  </div>

  <div class="col-md-3">
    <label class="form-label">Remote App Type</label>
    <input type="text" class="form-control" name="remote_app_type" value="{{ old('remote_app_type', $device->remote_app_type ?? '') }}" placeholder="AnyDesk/TeamViewer/...">
  </div>
  <div class="col-md-3">
    <label class="form-label">Remote ID</label>
    <input type="text" class="form-control" name="remote_id" value="{{ old('remote_id', $device->remote_id ?? '') }}">
  </div>
  <div class="col-md-3">
    <label class="form-label">Unattended Access</label>
    @php $ua = (bool) old('remote_unattended', $device->remote_unattended ?? false); @endphp
    <select class="form-select" name="remote_unattended">
      <option value="0" {{ !$ua ? 'selected' : '' }}>No</option>
      <option value="1" {{ $ua ? 'selected' : '' }}>Yes</option>
    </select>
  </div>

  <div class="col-md-6">
    <label class="form-label">Remote Password / Access Code {{ $isEdit ? '(isi jika ingin ganti)' : '' }}</label>
    <input type="password" class="form-control sensitive" name="remote_password" autocomplete="new-password">
  </div>
  <div class="col-md-6">
    <label class="form-label">Notes</label>
    <input type="text" class="form-control" name="remote_notes" value="{{ old('remote_notes', $device->remote_notes ?? '') }}">
  </div>

  <div class="col-md-6">
    <label class="form-label">Local Admin Username</label>
    <input type="text" class="form-control" name="local_admin_username" value="{{ old('local_admin_username', $device->local_admin_username ?? '') }}">
  </div>
  <div class="col-md-6">
    <label class="form-label">Local Admin Password {{ $isEdit ? '(isi jika ingin ganti)' : '' }}</label>
    <input type="password" class="form-control sensitive" name="local_admin_password" autocomplete="new-password">
  </div>
</div>

@section('js')
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    (function () {
      const assetSelect = document.getElementById('asset_code');
      const vaultMode = document.getElementById('vault_mode');
      const sensitiveFields = Array.from(document.querySelectorAll('input.sensitive'));

      const preview = {
        name: document.getElementById('asset_name_preview'),
        serial: document.getElementById('asset_serial_preview'),
        status: document.getElementById('asset_status_preview'),
        pic: document.getElementById('asset_pic_preview'),
        dept: document.getElementById('asset_department_preview'),
        loc: document.getElementById('asset_location_preview')
      };

      const lookupTemplate = @json(route('admin.devices.assets.lookup_by_code', ['asset_code' => '__ASSET_CODE__']));
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

      function setPreview(data) {
        preview.name.value = data?.asset_name ?? '';
        preview.serial.value = data?.serial_number ?? '';
        preview.status.value = data?.asset_status ?? '';
        preview.pic.value = data?.person_in_charge ?? '';
        preview.dept.value = data?.department ?? '';
        preview.loc.value = data?.asset_location ?? '';
      }

      async function fetchAsset(assetCode) {
        if (!assetCode) {
          setPreview(null);
          return;
        }
        const url = lookupTemplate.replace('__ASSET_CODE__', encodeURIComponent(assetCode));
        const res = await fetch(url, {
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf || ''
          }
        });
        if (!res.ok) {
          setPreview(null);
          return;
        }
        const data = await res.json();
        setPreview(data);
      }

      function applyVaultMode() {
        const enabled = !!vaultMode?.checked;
        sensitiveFields.forEach((el) => {
          el.disabled = !enabled;
        });
      }

      if (window.jQuery && window.jQuery.fn && window.jQuery.fn.select2) {
        window.jQuery(assetSelect).select2({
          theme: 'bootstrap-5',
          width: '100%'
        });
        window.jQuery(assetSelect).on('change', function () {
          fetchAsset(this.value);
        });
      } else {
        assetSelect?.addEventListener('change', (e) => fetchAsset(e.target.value));
      }

      vaultMode?.addEventListener('change', applyVaultMode);
      applyVaultMode();

      // Initial fill
      fetchAsset(assetSelect?.value);

      // Multi-storage rows
      const storageTbody = document.getElementById('storageItemsTbody');
      const btnAddStorageRow = document.getElementById('btnAddStorageRow');

      function nextStorageIndex() {
        const inputs = storageTbody?.querySelectorAll('[name^="storage_items["]') || [];
        let maxIdx = -1;
        inputs.forEach((el) => {
          const m = (el.getAttribute('name') || '').match(/^storage_items\[(\d+)\]/);
          if (m) maxIdx = Math.max(maxIdx, parseInt(m[1], 10));
        });
        return maxIdx + 1;
      }

      function bindRemoveButtons() {
        storageTbody?.querySelectorAll('.btnRemoveStorageRow').forEach((btn) => {
          if (btn.dataset.bound === '1') return;
          btn.dataset.bound = '1';
          btn.addEventListener('click', () => {
            const row = btn.closest('tr');
            if (!row) return;
            const rows = storageTbody.querySelectorAll('tr[data-storage-row="1"]');
            if (rows.length <= 1) {
              // Keep at least one row; just clear inputs.
              row.querySelectorAll('input,select').forEach((el) => {
                if (el.tagName === 'SELECT') el.value = '';
                else el.value = '';
              });
              return;
            }
            row.remove();
          });
        });
      }

      function addStorageRow() {
        const idx = nextStorageIndex();
        const tr = document.createElement('tr');
        tr.setAttribute('data-storage-row', '1');
        tr.innerHTML = `
          <td>
            <select class="form-select form-select-sm" name="storage_items[${idx}][type]">
              <option value="">-- pilih --</option>
              <option value="HDD">HDD</option>
              <option value="SSD">SSD</option>
              <option value="NVMe">NVMe</option>
              <option value="Other">Other</option>
            </select>
          </td>
          <td><input type="number" class="form-control form-control-sm" name="storage_items[${idx}][size_gb]" min="0"></td>
          <td><input type="text" class="form-control form-control-sm" name="storage_items[${idx}][model]" placeholder="contoh: Samsung 970 EVO"></td>
          <td><input type="text" class="form-control form-control-sm" name="storage_items[${idx}][serial]"></td>
          <td><input type="text" class="form-control form-control-sm" name="storage_items[${idx}][notes]"></td>
          <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger btnRemoveStorageRow" title="Hapus">
              <i class="fas fa-trash-alt"></i>
            </button>
          </td>
        `;
        storageTbody?.appendChild(tr);
        bindRemoveButtons();
      }

      btnAddStorageRow?.addEventListener('click', addStorageRow);
      bindRemoveButtons();
    })();
  </script>
@endsection
