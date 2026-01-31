<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Device;
use App\Models\DeviceMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::query()
            ->orderByDesc('id')
            ->get();

        return view('pages.admin.device.devices_index', compact('devices'));
    }

    public function create()
    {
        $assets = Asset::query()
            ->select(['id', 'asset_code', 'asset_name'])
            ->orderBy('asset_code')
            ->get();

        return view('pages.admin.device.devices_create', compact('assets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_code' => ['required', 'string', 'max:255'],

            'device_name' => ['nullable', 'string', 'max:255'],
            'device_id' => ['nullable', 'string', 'max:255'],
            'product_id' => ['nullable', 'string', 'max:255'],

            'os_name' => ['nullable', 'string', 'max:255'],
            'os_edition' => ['nullable', 'string', 'max:255'],
            'os_version' => ['nullable', 'string', 'max:255'],
            'domain_workgroup' => ['nullable', 'string', 'max:255'],
            'domain_join_status' => ['nullable', 'string', 'max:20'],
            'domain_name' => ['nullable', 'string', 'max:255'],
            'workgroup_name' => ['nullable', 'string', 'max:255'],

            'processor' => ['nullable', 'string', 'max:255'],
            'ram_gb' => ['nullable', 'integer', 'min:0', 'max:2048'],
            'storage_total_gb' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'storage_type' => ['nullable', 'string', 'max:50'],
            'storage_items' => ['nullable', 'array', 'max:10'],
            'storage_items.*.type' => ['nullable', 'string', 'max:50'],
            'storage_items.*.size_gb' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'storage_items.*.model' => ['nullable', 'string', 'max:255'],
            'storage_items.*.serial' => ['nullable', 'string', 'max:255'],
            'storage_items.*.notes' => ['nullable', 'string', 'max:1000'],
            'gpu' => ['nullable', 'string', 'max:255'],

            'location_site' => ['nullable', 'string', 'max:255'],
            'location_room' => ['nullable', 'string', 'max:255'],

            'owner_type' => ['nullable', 'string', 'max:50'],
            'device_role' => ['nullable', 'string', 'max:50'],

            'mac_lan' => ['nullable', 'string', 'max:255'],
            'mac_wifi' => ['nullable', 'string', 'max:255'],
            'ip_address' => ['nullable', 'string', 'max:255'],
            'subnet_mask' => ['nullable', 'string', 'max:255'],
            'gateway' => ['nullable', 'string', 'max:255'],
            'dns_primary' => ['nullable', 'string', 'max:255'],
            'dns_secondary' => ['nullable', 'string', 'max:255'],
            'connectivity' => ['nullable', 'string', 'max:50'],
            'ssid' => ['nullable', 'string', 'max:255'],
            'internet_download_mbps' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'internet_upload_mbps' => ['nullable', 'numeric', 'min:0', 'max:100000'],

            'remote_app_type' => ['nullable', 'string', 'max:50'],
            'remote_id' => ['nullable', 'string', 'max:255'],
            'remote_password' => ['nullable', 'string', 'max:1000'],
            'remote_unattended' => ['nullable', 'boolean'],
            'remote_notes' => ['nullable', 'string', 'max:5000'],

            'vault_mode' => ['nullable', 'boolean'],
            'local_admin_username' => ['nullable', 'string', 'max:255'],
            'local_admin_password' => ['nullable', 'string', 'max:1000'],
        ]);

        $asset = Asset::query()->where('asset_code', $validated['asset_code'])->firstOrFail();

        $device = new Device();
        $this->normalizeStorageItems($validated);
        $device->fill($validated);
        $device->remote_unattended = (bool) ($request->boolean('remote_unattended'));
        $device->vault_mode = (bool) ($request->boolean('vault_mode'));

        $this->fillFromAsset($device, $asset);

        if (!$device->vault_mode) {
            $device->remote_password = null;
            $device->local_admin_password = null;
        }

        $device->created_by = Auth::id();
        $device->updated_by = Auth::id();
        $device->save();

        return redirect()->route('admin.devices.show', $device)->with('success', 'Device berhasil dibuat.');
    }

    public function show(Device $device)
    {
        $device->load(['maintenances']);

        return view('pages.admin.device.devices_show', compact('device'));
    }

    public function edit(Device $device)
    {
        $assets = Asset::query()
            ->select(['id', 'asset_code', 'asset_name'])
            ->orderBy('asset_code')
            ->get();

        return view('pages.admin.device.devices_edit', compact('device', 'assets'));
    }

    public function update(Request $request, Device $device)
    {
        $validated = $request->validate([
            'asset_code' => ['required', 'string', 'max:255'],

            'device_name' => ['nullable', 'string', 'max:255'],
            'device_id' => ['nullable', 'string', 'max:255'],
            'product_id' => ['nullable', 'string', 'max:255'],

            'os_name' => ['nullable', 'string', 'max:255'],
            'os_edition' => ['nullable', 'string', 'max:255'],
            'os_version' => ['nullable', 'string', 'max:255'],
            'domain_workgroup' => ['nullable', 'string', 'max:255'],
            'domain_join_status' => ['nullable', 'string', 'max:20'],
            'domain_name' => ['nullable', 'string', 'max:255'],
            'workgroup_name' => ['nullable', 'string', 'max:255'],

            'processor' => ['nullable', 'string', 'max:255'],
            'ram_gb' => ['nullable', 'integer', 'min:0', 'max:2048'],
            'storage_total_gb' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'storage_type' => ['nullable', 'string', 'max:50'],
            'storage_items' => ['nullable', 'array', 'max:10'],
            'storage_items.*.type' => ['nullable', 'string', 'max:50'],
            'storage_items.*.size_gb' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'storage_items.*.model' => ['nullable', 'string', 'max:255'],
            'storage_items.*.serial' => ['nullable', 'string', 'max:255'],
            'storage_items.*.notes' => ['nullable', 'string', 'max:1000'],
            'gpu' => ['nullable', 'string', 'max:255'],

            'location_site' => ['nullable', 'string', 'max:255'],
            'location_room' => ['nullable', 'string', 'max:255'],

            'owner_type' => ['nullable', 'string', 'max:50'],
            'device_role' => ['nullable', 'string', 'max:50'],

            'mac_lan' => ['nullable', 'string', 'max:255'],
            'mac_wifi' => ['nullable', 'string', 'max:255'],
            'ip_address' => ['nullable', 'string', 'max:255'],
            'subnet_mask' => ['nullable', 'string', 'max:255'],
            'gateway' => ['nullable', 'string', 'max:255'],
            'dns_primary' => ['nullable', 'string', 'max:255'],
            'dns_secondary' => ['nullable', 'string', 'max:255'],
            'connectivity' => ['nullable', 'string', 'max:50'],
            'ssid' => ['nullable', 'string', 'max:255'],
            'internet_download_mbps' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'internet_upload_mbps' => ['nullable', 'numeric', 'min:0', 'max:100000'],

            'remote_app_type' => ['nullable', 'string', 'max:50'],
            'remote_id' => ['nullable', 'string', 'max:255'],
            'remote_password' => ['nullable', 'string', 'max:1000'],
            'remote_unattended' => ['nullable', 'boolean'],
            'remote_notes' => ['nullable', 'string', 'max:5000'],

            'vault_mode' => ['nullable', 'boolean'],
            'local_admin_username' => ['nullable', 'string', 'max:255'],
            'local_admin_password' => ['nullable', 'string', 'max:1000'],
        ]);

        // Keep existing encrypted passwords unless user provides a new one.
        if (empty((string) ($validated['remote_password'] ?? ''))) {
            unset($validated['remote_password']);
        }
        if (empty((string) ($validated['local_admin_password'] ?? ''))) {
            unset($validated['local_admin_password']);
        }

        $asset = Asset::query()->where('asset_code', $validated['asset_code'])->firstOrFail();

        $this->normalizeStorageItems($validated);
        $device->fill($validated);
        $device->remote_unattended = (bool) ($request->boolean('remote_unattended'));
        $device->vault_mode = (bool) ($request->boolean('vault_mode'));

        $this->fillFromAsset($device, $asset);

        if (!$device->vault_mode) {
            $device->remote_password = null;
            $device->local_admin_password = null;
        }

        $device->updated_by = Auth::id();
        $device->save();

        return redirect()->route('admin.devices.show', $device)->with('success', 'Device berhasil diupdate.');
    }

    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()->route('admin.devices.index')->with('success', 'Device berhasil dihapus.');
    }

    public function revealVault(Request $request, Device $device)
    {
        $validated = $request->validate([
            'password' => ['required', 'string'],
            'field' => ['required', 'in:remote_password,local_admin_password'],
        ]);

        if (!$device->vault_mode) {
            return response()
                ->json(['message' => 'Vault mode nonaktif untuk device ini.'], 422)
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        }

        $user = $request->user();
        if (!$user || !Hash::check($validated['password'], (string) ($user->password ?? ''))) {
            return response()
                ->json([
                    'message' => 'Password tidak valid.',
                    'errors' => ['password' => ['Password tidak valid.']],
                ], 422)
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        }

        $field = $validated['field'];
        $value = $device->{$field};

        return response()
            ->json([
                'field' => $field,
                'value' => $value,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function lookupAsset(Asset $asset)
    {
        return response()->json([
            'id' => $asset->id,
            'asset_code' => $asset->asset_code,
            'asset_name' => $asset->asset_name,
            'serial_number' => $asset->serial_number,
            'asset_status' => $asset->asset_status,
            'asset_location' => $asset->asset_location,
            'department' => $asset->department,
            'department_id' => $asset->department_id,
            'person_in_charge' => $asset->person_in_charge,
            'person_in_charge_employee_id' => $asset->person_in_charge_employee_id,
            'ownership_status' => $asset->ownership_status,
        ]);
    }

    public function lookupAssetByCode(string $asset_code)
    {
        $asset = Asset::query()->where('asset_code', $asset_code)->firstOrFail();
        return $this->lookupAsset($asset);
    }

    public function storeMaintenance(Request $request, Device $device)
    {
        $validated = $request->validate([
            'maintenance_at' => ['nullable', 'date'],
            'type' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:10000'],
            'performed_by' => ['nullable', 'string', 'max:255'],
            'next_schedule_at' => ['nullable', 'date'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,pdf'],
        ]);

        $maintenance = new DeviceMaintenance();
        $maintenance->device_id = $device->id;
        $maintenance->maintenance_at = !empty($validated['maintenance_at']) ? $validated['maintenance_at'] : now();
        $maintenance->type = $validated['type'];
        $maintenance->description = $validated['description'];
        $maintenance->performed_by = $validated['performed_by'] ?? null;
        $maintenance->next_schedule_at = $validated['next_schedule_at'] ?? null;
        $maintenance->created_by = Auth::id();

        if ($request->hasFile('attachment')) {
            $maintenance->attachment_path = $request->file('attachment')->store('device_maintenances', 'public');
        }

        $maintenance->save();

        // Update last maintenance timestamp on device.
        $device->last_maintenance_at = $maintenance->maintenance_at;
        $device->save();

        return redirect()->route('admin.devices.show', $device)->with('success', 'Maintenance berhasil ditambahkan.');
    }

    public function downloadMaintenanceAttachment(DeviceMaintenance $maintenance)
    {
        if (empty($maintenance->attachment_path)) {
            abort(404);
        }

        $fullPath = Storage::disk('public')->path($maintenance->attachment_path);
        if (!is_file($fullPath)) {
            abort(404);
        }

        return response()->download($fullPath);
    }

    public function destroyMaintenance(DeviceMaintenance $maintenance)
    {
        $deviceId = $maintenance->device_id;

        if (!empty($maintenance->attachment_path)) {
            Storage::disk('public')->delete($maintenance->attachment_path);
        }

        $maintenance->delete();

        $device = Device::query()->find($deviceId);
        if ($device) {
            $latest = DeviceMaintenance::query()
                ->where('device_id', $device->id)
                ->orderByDesc('maintenance_at')
                ->first();
            $device->last_maintenance_at = $latest?->maintenance_at;
            $device->save();
        }

        return redirect()->route('admin.devices.show', ['device' => $deviceId])->with('success', 'Maintenance berhasil dihapus.');
    }

    private function fillFromAsset(Device $device, Asset $asset): void
    {
        $device->asset_id = $asset->id;
        $device->asset_code = $asset->asset_code;
        $device->asset_name = $asset->asset_name;
        $device->asset_serial_number = $asset->serial_number;
        $device->asset_status = $asset->asset_status;
        $device->asset_location = $asset->asset_location;
        $device->asset_department = $asset->department;
        $device->asset_department_id = $asset->department_id;
        $device->asset_person_in_charge = $asset->person_in_charge;
        $device->asset_person_in_charge_employee_id = $asset->person_in_charge_employee_id;
        $device->asset_payload = $asset->toArray();
    }

    private function normalizeStorageItems(array &$validated): void
    {
        if (!array_key_exists('storage_items', $validated)) {
            return;
        }

        $items = is_array($validated['storage_items']) ? $validated['storage_items'] : [];

        $normalized = [];
        foreach ($items as $row) {
            if (!is_array($row)) {
                continue;
            }

            $type = trim((string) ($row['type'] ?? ''));
            $model = trim((string) ($row['model'] ?? ''));
            $serial = trim((string) ($row['serial'] ?? ''));
            $notes = trim((string) ($row['notes'] ?? ''));

            $sizeRaw = $row['size_gb'] ?? null;
            $size = is_numeric($sizeRaw) ? (int) $sizeRaw : null;

            $hasAny = ($type !== '') || ($model !== '') || ($serial !== '') || ($notes !== '') || ($size !== null);
            if (!$hasAny) {
                continue;
            }

            $normalized[] = [
                'type' => $type !== '' ? $type : null,
                'size_gb' => $size,
                'model' => $model !== '' ? $model : null,
                'serial' => $serial !== '' ? $serial : null,
                'notes' => $notes !== '' ? $notes : null,
            ];
        }

        $validated['storage_items'] = $normalized;
    }
}
