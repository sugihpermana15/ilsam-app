<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountAccessLog;
use App\Models\AccountEndpoint;
use App\Models\AccountSecret;
use App\Models\AccountType;
use App\Models\ApprovalRequest;
use App\Models\Asset;
use App\Models\User;
use App\Support\AccountAudit;
use App\Support\AccountSecretCrypto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $types = AccountType::query()->where('is_active', true)->orderBy('name')->get();

        $q = Account::query()->with([
            'type',
            'asset',
            'endpoints',
            'secrets' => function ($q) {
                $q->where('is_active', true);
            },
        ]);

        if ($request->filled('plant')) {
            $q->where('plant_site_snapshot', $request->string('plant')->toString());
        }
        if ($request->filled('account_type_id')) {
            $q->where('account_type_id', (int) $request->input('account_type_id'));
        }
        if ($request->filled('asset_code')) {
            $assetCode = $request->string('asset_code')->toString();
            $q->where('asset_code_snapshot', 'like', '%' . $assetCode . '%');
        }
        if ($request->filled('location')) {
            $location = $request->string('location')->toString();
            $q->where('location_name_snapshot', 'like', '%' . $location . '%');
        }
        if ($request->filled('status')) {
            $q->where('status', $request->string('status')->toString());
        }

        $accounts = $q->orderByDesc('id')->get();

        // For simple select, limit assets list to keep page light.
        $assets = Asset::query()->orderByDesc('id')->limit(500)->get();

        return view('pages.admin.accounts.accounts_index', compact('accounts', 'types', 'assets'));
    }

    public function show(Request $request, int $id)
    {
        $account = Account::query()
            ->with([
                'type',
                'asset',
                'endpoints' => fn($q) => $q->orderBy('is_management', 'desc')->orderBy('id'),
                'secrets' => fn($q) => $q->where('is_active', true)->orderByDesc('id'),
            ])
            ->findOrFail($id);

        $pendingApprovals = ApprovalRequest::query()
            ->where('account_id', $account->id)
            ->where('status', 'pending')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $auditLogs = AccountAccessLog::query()
            ->with('actor')
            ->where('account_id', $account->id)
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        AccountAudit::log($request, 'ACCOUNT_DETAIL_VIEW', 'success', [
            'account_id' => $account->id,
            'target_type' => 'account',
            'target_id' => $account->id,
        ]);

        return view('pages.admin.accounts.accounts_show', compact('account', 'pendingApprovals', 'auditLogs'));
    }

    public function json(Request $request, int $id)
    {
        $account = Account::query()
            ->with([
                'type',
                'asset',
                'endpoints' => fn($q) => $q->orderBy('is_management', 'desc')->orderBy('id'),
                'secrets' => fn($q) => $q->where('is_active', true)->orderByDesc('id'),
            ])
            ->findOrFail($id);

        AccountAudit::log($request, 'ACCOUNT_DETAIL_VIEW', 'success', [
            'account_id' => $account->id,
            'target_type' => 'account',
            'target_id' => $account->id,
            'metadata' => ['channel' => 'json'],
        ]);

        $secrets = $account->secrets->map(fn(AccountSecret $s) => [
            'id' => $s->id,
            'label' => $s->label,
            'kind' => $s->kind,
            'username' => $s->username,
            'masked' => true,
            'is_active' => $s->is_active,
        ])->values();

        return response()->json([
            'id' => $account->id,
            'account_type_id' => $account->account_type_id,
            'account_type' => $account->type?->name,
            'environment' => $account->environment,
            'asset_id' => $account->asset_id,
            'asset_code' => $account->asset_code_snapshot,
            'asset_name' => $account->asset_name_snapshot,
            'plant' => $account->plant_site_snapshot,
            'location' => $account->location_name_snapshot,
            'department_owner' => $account->department_owner,
            'criticality' => $account->criticality,
            'status' => $account->status,
            'vendor_installer' => $account->vendor_installer,
            'last_verified_at' => optional($account->last_verified_at)->toDateTimeString(),
            'note' => $account->note,
            'metadata' => $account->metadata,
            'endpoints' => $account->endpoints,
            'secrets' => $secrets,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateAccount($request, true);

        $userId = Auth::id();

        DB::beginTransaction();
        try {
            $asset = null;
            if (!empty($validated['asset_id'])) {
                $asset = Asset::query()->find($validated['asset_id']);
            }

            $account = Account::create([
                'account_type_id' => (int) $validated['account_type_id'],
                'environment' => $validated['environment'] ?? null,
                'asset_id' => $validated['asset_id'] ?? null,
                'department_owner' => $validated['department_owner'] ?? null,
                'criticality' => $validated['criticality'] ?? null,
                'status' => $validated['status'],
                'vendor_installer' => $validated['vendor_installer'] ?? null,
                'note' => $validated['note'] ?? null,
                'metadata' => $validated['metadata'] ?? [],
                'created_by' => $userId,
                'updated_by' => $userId,
                'asset_code_snapshot' => $asset?->asset_code,
                'asset_name_snapshot' => $asset?->asset_name,
                'plant_site_snapshot' => $asset?->asset_location,
                'location_name_snapshot' => $asset?->asset_location,
            ]);

            $this->syncEndpointsAndSecretsFromForm($account, $validated, true);

            AccountAudit::log($request, 'ACCOUNT_CREATE', 'success', [
                'account_id' => $account->id,
                'target_type' => 'account',
                'target_id' => $account->id,
            ]);

            DB::commit();
            return redirect()->route('admin.accounts.index')->with('success', 'Account berhasil dibuat.');
        } catch (\Throwable $e) {
            DB::rollBack();
            AccountAudit::log($request, 'ACCOUNT_CREATE', 'failed', [
                'reason' => 'exception',
                'metadata' => ['message' => $e->getMessage()],
            ]);
            return redirect()->route('admin.accounts.index')->with('error', 'Gagal membuat account.');
        }
    }

    public function update(Request $request, int $id)
    {
        $account = Account::query()->findOrFail($id);
        $validated = $this->validateAccount($request, false);

        $userId = Auth::id();

        DB::beginTransaction();
        try {
            $asset = null;
            if (!empty($validated['asset_id'])) {
                $asset = Asset::query()->find($validated['asset_id']);
            }

            $account->fill([
                'account_type_id' => (int) $validated['account_type_id'],
                'environment' => $validated['environment'] ?? null,
                'asset_id' => $validated['asset_id'] ?? null,
                'department_owner' => $validated['department_owner'] ?? null,
                'criticality' => $validated['criticality'] ?? null,
                'status' => $validated['status'],
                'vendor_installer' => $validated['vendor_installer'] ?? null,
                'note' => $validated['note'] ?? null,
                'metadata' => $validated['metadata'] ?? [],
                'updated_by' => $userId,
                'asset_code_snapshot' => $asset?->asset_code ?? $account->asset_code_snapshot,
                'asset_name_snapshot' => $asset?->asset_name ?? $account->asset_name_snapshot,
                'plant_site_snapshot' => $asset?->asset_location ?? $account->plant_site_snapshot,
                'location_name_snapshot' => $asset?->asset_location ?? $account->location_name_snapshot,
            ]);
            $account->save();

            $this->syncEndpointsAndSecretsFromForm($account, $validated, false);

            AccountAudit::log($request, 'ACCOUNT_UPDATE', 'success', [
                'account_id' => $account->id,
                'target_type' => 'account',
                'target_id' => $account->id,
            ]);

            DB::commit();
            return redirect()->route('admin.accounts.index')->with('success', 'Account berhasil diupdate.');
        } catch (\Throwable $e) {
            DB::rollBack();
            AccountAudit::log($request, 'ACCOUNT_UPDATE', 'failed', [
                'account_id' => $account->id,
                'reason' => 'exception',
                'metadata' => ['message' => $e->getMessage()],
            ]);
            return redirect()->route('admin.accounts.index')->with('error', 'Gagal update account.');
        }
    }

    public function destroy(Request $request, int $id)
    {
        $account = Account::query()->findOrFail($id);
        $account->delete();

        AccountAudit::log($request, 'ACCOUNT_DELETE', 'success', [
            'account_id' => $account->id,
            'target_type' => 'account',
            'target_id' => $account->id,
        ]);

        return redirect()->route('admin.accounts.index')->with('success', 'Account berhasil dihapus.');
    }

    public function openEndpoint(Request $request, int $endpointId)
    {
        $endpoint = AccountEndpoint::query()
            ->with('account')
            ->findOrFail($endpointId);

        $target = strtolower((string) $request->query('target', 'auto'));
        if (!in_array($target, ['auto', 'local', 'public', 'hostname'], true)) {
            $target = 'auto';
        }

        $host = null;
        $hostSource = null;
        if ($target === 'local') {
            $host = $endpoint->ip_local;
            $hostSource = 'ip_local';
        } elseif ($target === 'public') {
            $host = $endpoint->ip_public;
            $hostSource = 'ip_public';
        } elseif ($target === 'hostname') {
            $host = $endpoint->hostname;
            $hostSource = 'hostname';
        } else {
            if ($endpoint->ip_local) {
                $host = $endpoint->ip_local;
                $hostSource = 'ip_local';
            } elseif ($endpoint->ip_public) {
                $host = $endpoint->ip_public;
                $hostSource = 'ip_public';
            } else {
                $host = $endpoint->hostname;
                $hostSource = 'hostname';
            }
        }
        $protocol = $endpoint->protocol ? strtolower((string) $endpoint->protocol) : null;

        if ($target !== 'auto' && (!$host || trim((string) $host) === '')) {
            AccountAudit::log($request, 'ENDPOINT_OPEN', 'denied', [
                'account_id' => $endpoint->account_id,
                'target_type' => 'endpoint',
                'target_id' => $endpoint->id,
                'reason' => 'missing_target_host',
                'metadata' => [
                    'target' => $target,
                ],
            ]);

            return redirect()->back()->with('error', 'IP/host yang dipilih belum diisi.');
        }

        $url = null;
        if ($host && $protocol && in_array($protocol, ['http', 'https'], true)) {
            $url = $protocol . '://' . $host . ($endpoint->port ? (':' . $endpoint->port) : '') . ($endpoint->path ?: '');
        }

        if (!$url) {
            AccountAudit::log($request, 'ENDPOINT_OPEN', 'denied', [
                'account_id' => $endpoint->account_id,
                'target_type' => 'endpoint',
                'target_id' => $endpoint->id,
                'reason' => 'invalid_url',
                'metadata' => [
                    'target' => $target,
                    'host_source' => $hostSource,
                ],
            ]);

            return redirect()->back()->with('error', 'Endpoint URL tidak valid.');
        }

        AccountAudit::log($request, 'ENDPOINT_OPEN', 'success', [
            'account_id' => $endpoint->account_id,
            'target_type' => 'endpoint',
            'target_id' => $endpoint->id,
            'metadata' => [
                'target' => $target,
                'host_source' => $hostSource,
                'protocol' => $protocol,
                'host' => $host,
                'port' => $endpoint->port,
                'path' => $endpoint->path,
            ],
        ]);

        return redirect()->away($url);
    }

    public function verify(Request $request, int $id)
    {
        $validated = $request->validate([
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $account = Account::query()->findOrFail($id);

        $account->last_verified_at = now();
        $account->last_verified_by = Auth::id();
        $account->save();

        AccountAudit::log($request, 'ACCOUNT_VERIFY', 'success', [
            'account_id' => $account->id,
            'target_type' => 'account',
            'target_id' => $account->id,
            'metadata' => [
                'note' => $validated['note'] ?? null,
            ],
        ]);

        return redirect()->back()->with('success', 'Account berhasil diverifikasi.');
    }

    public function rotateSecret(Request $request, int $accountId)
    {
        $validated = $request->validate([
            'kind' => ['required', 'string', 'max:20'],
            'label' => ['nullable', 'string', 'max:50'],
            'username' => ['nullable', 'string', 'max:150'],
            'new_secret' => ['required', 'string', 'min:1'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $account = Account::query()->findOrFail($accountId);

        DB::beginTransaction();
        try {
            $kind = strtolower($validated['kind']);
            $label = $validated['label'] ?? null;

            $old = AccountSecret::query()
                ->where('account_id', $account->id)
                ->where('kind', $kind)
                ->when($label !== null && $label !== '', fn($q) => $q->where('label', $label))
                ->where('is_active', true)
                ->orderByDesc('id')
                ->first();

            if ($old) {
                $old->is_active = false;
                $old->valid_to = now();
                $old->save();
            }

            $new = AccountSecret::create([
                'account_id' => $account->id,
                'label' => $label,
                'kind' => $kind,
                'username' => $validated['username'] ?? null,
                'secret_ciphertext' => AccountSecretCrypto::encrypt($validated['new_secret']),
                'secret_algo' => 'laravel-crypt',
                'secret_key_version' => 1,
                'valid_from' => now(),
                'is_active' => true,
                'created_by' => Auth::id(),
                'rotated_from_secret_id' => $old?->id,
                'metadata' => ['reason' => $validated['reason'] ?? null],
            ]);

            AccountAudit::log($request, 'SECRET_ROTATE', 'success', [
                'account_id' => $account->id,
                'target_type' => 'secret',
                'target_id' => $new->id,
            ]);

            DB::commit();
            return redirect()->route('admin.accounts.show', $account->id)->with('success', 'Secret berhasil di-rotate.');
        } catch (\Throwable $e) {
            DB::rollBack();
            AccountAudit::log($request, 'SECRET_ROTATE', 'failed', [
                'account_id' => $account->id,
                'reason' => 'exception',
                'metadata' => ['message' => $e->getMessage()],
            ]);
            return redirect()->route('admin.accounts.show', $account->id)->with('error', 'Gagal rotate secret.');
        }
    }

    public function addSecrets(Request $request, int $accountId)
    {
        $validated = $request->validate([
            'secrets' => ['required', 'array'],
            'secrets.*.label' => ['nullable', 'string', 'max:50'],
            'secrets.*.username' => ['nullable', 'string', 'max:150'],
            'secrets.*.new_secret' => ['nullable', 'string', 'min:1'],
            'kind' => ['nullable', 'string', 'max:20'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $account = Account::query()->with('type')->findOrFail($accountId);

        $kind = strtolower((string) ($validated['kind'] ?? 'current'));
        if (!in_array($kind, ['current', 'default'], true)) {
            $kind = 'current';
        }

        $rows = is_array($validated['secrets'] ?? null) ? $validated['secrets'] : [];
        $toCreate = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $pw = $row['new_secret'] ?? null;
            if (!$pw || trim((string) $pw) === '') {
                continue;
            }
            $u = $row['username'] ?? null;
            if (!$u || trim((string) $u) === '') {
                continue;
            }

            $toCreate[] = [
                'label' => ($row['label'] ?? null) ?: null,
                'username' => $u,
                'new_secret' => $pw,
            ];
        }

        if (empty($toCreate)) {
            return redirect()->back()->with('error', 'Minimal 1 kredensial (username + password) harus diisi.');
        }

        DB::beginTransaction();
        try {
            $createdIds = [];
            foreach ($toCreate as $row) {
                $new = AccountSecret::create([
                    'account_id' => $account->id,
                    'label' => $row['label'],
                    'kind' => $kind,
                    'username' => $row['username'],
                    'secret_ciphertext' => AccountSecretCrypto::encrypt($row['new_secret']),
                    'secret_algo' => 'laravel-crypt',
                    'secret_key_version' => 1,
                    'valid_from' => now(),
                    'is_active' => true,
                    'created_by' => Auth::id(),
                    'metadata' => ['reason' => $validated['reason'] ?? null],
                ]);
                $createdIds[] = $new->id;
            }

            AccountAudit::log($request, 'SECRET_ADD', 'success', [
                'account_id' => $account->id,
                'target_type' => 'account',
                'target_id' => $account->id,
                'metadata' => [
                    'kind' => $kind,
                    'count' => count($createdIds),
                    'secret_ids' => $createdIds,
                ],
            ]);

            DB::commit();
            return redirect()->route('admin.accounts.show', $account->id)->with('success', 'Kredensial berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            AccountAudit::log($request, 'SECRET_ADD', 'failed', [
                'account_id' => $account->id,
                'reason' => 'exception',
                'metadata' => ['message' => $e->getMessage()],
            ]);
            return redirect()->route('admin.accounts.show', $account->id)->with('error', 'Gagal menambahkan kredensial.');
        }
    }

    public function requestRevealApproval(Request $request, int $secretId)
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $secret = AccountSecret::query()->findOrFail($secretId);

        $approval = ApprovalRequest::create([
            'requester_id' => Auth::id(),
            'request_type' => 'reveal_secret',
            'account_id' => $secret->account_id,
            'secret_id' => $secret->id,
            'reason' => $validated['reason'] ?? null,
            'status' => 'pending',
        ]);

        AccountAudit::log($request, 'APPROVAL_REQUEST_CREATE', 'success', [
            'account_id' => $secret->account_id,
            'target_type' => 'approval_request',
            'target_id' => $approval->id,
        ]);

        return redirect()->back()->with('success', 'Request approval berhasil dibuat.');
    }

    public function approve(Request $request, int $approvalId)
    {
        $approval = ApprovalRequest::query()->findOrFail($approvalId);
        if ($approval->status !== 'pending') {
            return redirect()->back()->with('error', 'Approval sudah diproses.');
        }

        $approval->status = 'approved';
        $approval->approver_id = Auth::id();
        $approval->approved_at = now();
        $approval->expires_at = now()->addMinutes(10);
        $approval->save();

        AccountAudit::log($request, 'APPROVAL_REQUEST_APPROVE', 'success', [
            'account_id' => $approval->account_id,
            'target_type' => 'approval_request',
            'target_id' => $approval->id,
        ]);

        return redirect()->back()->with('success', 'Approval berhasil disetujui (valid 10 menit).');
    }

    public function revealSecret(Request $request, int $secretId)
    {
        $validated = $request->validate([
            'password_confirm' => ['required', 'string'],
        ]);

        /** @var User|null $user */
        $user = Auth::user();
        if (!$user || !Hash::check($validated['password_confirm'], $user->password)) {
            AccountAudit::log($request, 'SECRET_REVEAL', 'denied', [
                'target_type' => 'secret',
                'target_id' => $secretId,
                'reason' => 'reauth_failed',
            ]);
            return response()->json(['message' => 'Konfirmasi password tidak valid.'], 422);
        }

        if ($user && !$user->relationLoaded('role')) {
            $user->load('role');
        }
        $isSuperAdmin = strtolower((string) ($user->role?->role_name ?? '')) === 'super admin';

        $secret = AccountSecret::query()->where('is_active', true)->findOrFail($secretId);

        if (!$isSuperAdmin) {
            $ok = ApprovalRequest::query()
                ->where('request_type', 'reveal_secret')
                ->where('secret_id', $secret->id)
                ->where('requester_id', $user->id)
                ->where('status', 'approved')
                ->whereNotNull('expires_at')
                ->where('expires_at', '>', now())
                ->exists();

            if (!$ok) {
                AccountAudit::log($request, 'SECRET_REVEAL', 'denied', [
                    'account_id' => $secret->account_id,
                    'target_type' => 'secret',
                    'target_id' => $secret->id,
                    'reason' => 'approval_required',
                ]);

                return response()->json([
                    'message' => 'Approval diperlukan untuk reveal secret.',
                    'requires_approval' => true,
                ], 403);
            }
        }

        $plaintext = AccountSecretCrypto::decrypt($secret->secret_ciphertext);

        AccountAudit::log($request, 'SECRET_REVEAL', 'success', [
            'account_id' => $secret->account_id,
            'target_type' => 'secret',
            'target_id' => $secret->id,
        ]);

        return response()
            ->json([
                'secret_id' => $secret->id,
                'username' => $secret->username,
                'secret' => $plaintext,
            ])
            ->header('Cache-Control', 'no-store');
    }

    public function copyUsername(Request $request, int $secretId)
    {
        $secret = AccountSecret::query()->where('is_active', true)->findOrFail($secretId);

        AccountAudit::log($request, 'USERNAME_COPY', 'success', [
            'account_id' => $secret->account_id,
            'target_type' => 'secret',
            'target_id' => $secret->id,
        ]);

        return response()
            ->json([
                'secret_id' => $secret->id,
                'username' => $secret->username,
            ])
            ->header('Cache-Control', 'no-store');
    }

    public function deactivateSecret(Request $request, int $secretId)
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $secret = AccountSecret::query()->where('is_active', true)->findOrFail($secretId);
        $secret->is_active = false;
        $secret->valid_to = now();
        $secret->save();

        AccountAudit::log($request, 'SECRET_DEACTIVATE', 'success', [
            'account_id' => $secret->account_id,
            'target_type' => 'secret',
            'target_id' => $secret->id,
            'metadata' => [
                'reason' => $validated['reason'] ?? null,
            ],
        ]);

        return redirect()->back()->with('success', 'Kredensial berhasil dinonaktifkan.');
    }

    private function validateAccount(Request $request, bool $isCreate): array
    {
        $typeRule = Rule::exists('m_igi_account_types', 'id')->where(fn($q) => $q->where('is_active', true));

        $validated = $request->validate([
            'account_type_id' => ['required', 'integer', $typeRule],
            'environment' => ['nullable', 'string', 'max:50'],
            'asset_id' => ['nullable', 'integer', Rule::exists('m_igi_asset', 'id')],
            'department_owner' => ['nullable', 'string', 'max:100'],
            'criticality' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'string', 'max:30'],
            'vendor_installer' => ['nullable', 'string', 'max:150'],
            'note' => ['nullable', 'string'],

            // Generic (Email/NAS/Hotspot/etc)
            'general_username' => ['nullable', 'string', 'max:150'],
            'general_password' => ['nullable', 'string'],

            // CCTV
            'cctv_ip_local' => ['nullable', 'ip'],
            'cctv_ip_public' => ['nullable', 'ip'],
            'cctv_port_web' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'cctv_port_hikconnect' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'cctv_username' => ['nullable', 'string', 'max:150'],
            'cctv_password' => ['nullable', 'string'],
            'cctv_users' => ['nullable', 'array'],
            'cctv_users.*.label' => ['nullable', 'string', 'max:50'],
            'cctv_users.*.username' => ['nullable', 'string', 'max:150'],
            'cctv_users.*.password' => ['nullable', 'string'],

            // Router/WiFi
            'router_mac' => ['nullable', 'regex:/^([0-9A-Fa-f]{2}:){5}([0-9A-Fa-f]{2})$/'],
            'router_ip_local' => ['nullable', 'ip'],
            'router_ip_public' => ['nullable', 'ip'],
            'router_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'router_protocol' => ['nullable', 'string', 'max:20'],
            'router_area_location' => ['nullable', 'string', 'max:100'],
            'router_default_username' => ['nullable', 'string', 'max:150'],
            'router_default_password' => ['nullable', 'string'],
            'router_current_username' => ['nullable', 'string', 'max:150'],
            'router_current_password' => ['nullable', 'string'],
        ]);

        // Enforce asset for CCTV/Router
        $type = AccountType::query()->find($validated['account_type_id']);
        if ($type && in_array($type->name, ['CCTV', 'Router/WiFi'], true)) {
            if (empty($validated['asset_id'])) {
                throw ValidationException::withMessages([
                    'asset_id' => 'Asset wajib untuk kategori perangkat.',
                ]);
            }
        }

        // Enforce secret on create (per type)
        if ($isCreate && $type) {
            $hasCctvUsersPassword = false;
            if (!empty($validated['cctv_users']) && is_array($validated['cctv_users'])) {
                foreach ($validated['cctv_users'] as $row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    if (!empty($row['password'])) {
                        $hasCctvUsersPassword = true;
                        break;
                    }
                }
            }

            if ($type->name === 'CCTV' && empty($validated['cctv_password']) && !$hasCctvUsersPassword) {
                throw ValidationException::withMessages([
                    'cctv_users' => 'Minimal 1 user CCTV (username + password) wajib diisi.',
                ]);
            }
            if ($type->name === 'Router/WiFi' && empty($validated['router_current_password'])) {
                throw ValidationException::withMessages([
                    'router_current_password' => 'Password saat ini wajib untuk Router/WiFi.',
                ]);
            }
            if (!in_array($type->name, ['CCTV', 'Router/WiFi'], true) && empty($validated['general_password'])) {
                throw ValidationException::withMessages([
                    'general_password' => 'Password/secret wajib untuk kategori ini.',
                ]);
            }
        }

        // Minimal endpoint validation per type
        if ($type && $type->name === 'CCTV') {
            if (empty($validated['cctv_ip_local']) && empty($validated['cctv_ip_public'])) {
                throw ValidationException::withMessages([
                    'cctv_ip_local' => 'Minimal salah satu IP (local/public) harus diisi untuk CCTV.',
                ]);
            }

            // If using multi-user input, require username for any row that has a password.
            if (!empty($validated['cctv_users']) && is_array($validated['cctv_users'])) {
                $errs = [];
                foreach ($validated['cctv_users'] as $i => $row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    $pw = $row['password'] ?? null;
                    if ($pw && trim((string) $pw) !== '') {
                        $u = $row['username'] ?? null;
                        if (!$u || trim((string) $u) === '') {
                            $errs["cctv_users.$i.username"] = 'Username wajib diisi untuk user CCTV.';
                        }
                    }
                }
                if (!empty($errs)) {
                    throw ValidationException::withMessages($errs);
                }
            }
        }
        if ($type && $type->name === 'Router/WiFi') {
            if (empty($validated['router_ip_local'])) {
                throw ValidationException::withMessages([
                    'router_ip_local' => 'IP local wajib untuk Router/WiFi.',
                ]);
            }
        }

        $validated['metadata'] = array_filter([
            'router_mac_address' => $validated['router_mac'] ?? null,
            'router_management_protocol' => $validated['router_protocol'] ?? null,
            'router_area_location' => $validated['router_area_location'] ?? null,
        ], fn($v) => $v !== null && $v !== '');

        return $validated;
    }

    private function syncEndpointsAndSecretsFromForm(Account $account, array $validated, bool $isCreate): void
    {
        $typeName = $account->type?->name;

        // Replace endpoints on create/update (simple & predictable)
        AccountEndpoint::query()->where('account_id', $account->id)->delete();

        if ($typeName === 'CCTV') {
            if (!empty($validated['cctv_ip_local']) || !empty($validated['cctv_ip_public'])) {
                AccountEndpoint::create([
                    'account_id' => $account->id,
                    'service' => 'web',
                    'protocol' => 'http',
                    'ip_local' => $validated['cctv_ip_local'] ?? null,
                    'ip_public' => $validated['cctv_ip_public'] ?? null,
                    'port' => $validated['cctv_port_web'] ?? null,
                    'is_management' => true,
                ]);
            }
            if (!empty($validated['cctv_port_hikconnect'])) {
                AccountEndpoint::create([
                    'account_id' => $account->id,
                    'service' => 'hikconnect',
                    'protocol' => null,
                    'ip_local' => $validated['cctv_ip_local'] ?? null,
                    'ip_public' => $validated['cctv_ip_public'] ?? null,
                    'port' => $validated['cctv_port_hikconnect'] ?? null,
                    'is_management' => false,
                ]);
            }

            // CCTV credentials:
            // - Preferred: cctv_users[] (each row becomes a secret)
            // - Backward compatible: cctv_username + cctv_password (single secret)
            $rows = [];
            if (!empty($validated['cctv_users']) && is_array($validated['cctv_users'])) {
                $rows = $validated['cctv_users'];
            }

            if (!empty($rows)) {
                foreach ($rows as $row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    $pw = $row['password'] ?? null;
                    if (!$pw || trim((string) $pw) === '') {
                        continue;
                    }

                    AccountSecret::create([
                        'account_id' => $account->id,
                        'label' => ($row['label'] ?? null) ?: null,
                        'kind' => 'current',
                        'username' => ($row['username'] ?? null) ?: null,
                        'secret_ciphertext' => AccountSecretCrypto::encrypt($pw),
                        'secret_algo' => 'laravel-crypt',
                        'secret_key_version' => 1,
                        'valid_from' => now(),
                        'is_active' => true,
                        'created_by' => Auth::id(),
                    ]);
                }
            } elseif ($isCreate && !empty($validated['cctv_password'])) {
                AccountSecret::create([
                    'account_id' => $account->id,
                    'label' => 'admin',
                    'kind' => 'current',
                    'username' => $validated['cctv_username'] ?? null,
                    'secret_ciphertext' => AccountSecretCrypto::encrypt($validated['cctv_password']),
                    'secret_algo' => 'laravel-crypt',
                    'secret_key_version' => 1,
                    'valid_from' => now(),
                    'is_active' => true,
                    'created_by' => Auth::id(),
                ]);
            }
        }

        if ($typeName === 'Router/WiFi') {
            AccountEndpoint::create([
                'account_id' => $account->id,
                'service' => 'management',
                'protocol' => $validated['router_protocol'] ?? null,
                'ip_local' => $validated['router_ip_local'] ?? null,
                'ip_public' => $validated['router_ip_public'] ?? null,
                'port' => $validated['router_port'] ?? null,
                'is_management' => true,
            ]);

            if ($isCreate && !empty($validated['router_default_password'])) {
                AccountSecret::create([
                    'account_id' => $account->id,
                    'label' => 'default',
                    'kind' => 'default',
                    'username' => $validated['router_default_username'] ?? null,
                    'secret_ciphertext' => AccountSecretCrypto::encrypt($validated['router_default_password']),
                    'secret_algo' => 'laravel-crypt',
                    'secret_key_version' => 1,
                    'valid_from' => now(),
                    'is_active' => true,
                    'created_by' => Auth::id(),
                ]);
            }

            if ($isCreate && !empty($validated['router_current_password'])) {
                AccountSecret::create([
                    'account_id' => $account->id,
                    'label' => 'current',
                    'kind' => 'current',
                    'username' => $validated['router_current_username'] ?? null,
                    'secret_ciphertext' => AccountSecretCrypto::encrypt($validated['router_current_password']),
                    'secret_algo' => 'laravel-crypt',
                    'secret_key_version' => 1,
                    'valid_from' => now(),
                    'is_active' => true,
                    'created_by' => Auth::id(),
                ]);
            }
        }

        if ($typeName && !in_array($typeName, ['CCTV', 'Router/WiFi'], true)) {
            if ($isCreate && !empty($validated['general_password'])) {
                AccountSecret::create([
                    'account_id' => $account->id,
                    'label' => 'current',
                    'kind' => 'current',
                    'username' => $validated['general_username'] ?? null,
                    'secret_ciphertext' => AccountSecretCrypto::encrypt($validated['general_password']),
                    'secret_algo' => 'laravel-crypt',
                    'secret_key_version' => 1,
                    'valid_from' => now(),
                    'is_active' => true,
                    'created_by' => Auth::id(),
                ]);
            }
        }

        // On update: allow updating username (without changing secret) for active current secret
        if (!$isCreate) {
            if ($typeName === 'CCTV' && empty($validated['cctv_users']) && !empty($validated['cctv_username'])) {
                AccountSecret::query()
                    ->where('account_id', $account->id)
                    ->where('kind', 'current')
                    ->where('is_active', true)
                    ->limit(1)
                    ->update(['username' => $validated['cctv_username'] ?? null]);
            }
            if ($typeName === 'Router/WiFi' && array_key_exists('router_current_username', $validated)) {
                AccountSecret::query()
                    ->where('account_id', $account->id)
                    ->where('kind', 'current')
                    ->where('is_active', true)
                    ->limit(1)
                    ->update(['username' => $validated['router_current_username'] ?? null]);
            }
            if ($typeName && !in_array($typeName, ['CCTV', 'Router/WiFi'], true) && array_key_exists('general_username', $validated)) {
                AccountSecret::query()
                    ->where('account_id', $account->id)
                    ->where('kind', 'current')
                    ->where('is_active', true)
                    ->limit(1)
                    ->update(['username' => $validated['general_username'] ?? null]);
            }
        }
    }
}
