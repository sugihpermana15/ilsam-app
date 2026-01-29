@extends('layouts.master')

@section('title', __('accounts.show.page_title'))
@section('title-sub', __('accounts.show.title_sub'))
@section('pagetitle', __('accounts.show.pagetitle'))

@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
@endsection

@section('content')
  @php
    $canSecretsRead = \App\Support\MenuAccess::can(auth()->user(), 'accounts_secrets', 'read');
    $canSecretsUpdate = \App\Support\MenuAccess::can(auth()->user(), 'accounts_secrets', 'update');
    $canDataUpdate = \App\Support\MenuAccess::can(auth()->user(), 'accounts_data', 'update');
    $isSuperAdmin = strtolower((string) (auth()->user()?->role?->role_name ?? '')) === 'super admin';
  @endphp

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0">{{ __('accounts.show.header.title', ['id' => $account->id, 'type' => $account->type?->name]) }}</h5>
            <div class="text-muted small">{{ __('accounts.show.header.asset_line', ['code' => $account->asset_code_snapshot ?? '-', 'name' => $account->asset_name_snapshot ?? '-']) }}</div>
          </div>
          <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('admin.accounts.index') }}"><i class="fas fa-arrow-left"></i> {{ __('common.back') }}</a>
          </div>
        </div>

        <div class="card-body">
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

          <div class="row g-3">
            <div class="col-md-4">
              <div class="border rounded p-3">
                <div class="fw-semibold mb-2">{{ __('accounts.show.sections.metadata') }}</div>
                <div><span class="text-muted">{{ __('accounts.show.labels.category') }}:</span> {{ $account->type?->name }}</div>
                <div><span class="text-muted">{{ __('accounts.show.labels.status') }}:</span> {{ __('accounts.status.' . ($account->status ?? 'active')) }}</div>
                <div><span class="text-muted">{{ __('accounts.show.labels.plant_site') }}:</span> {{ $account->plant_site_snapshot ?? '-' }}</div>
                <div><span class="text-muted">{{ __('accounts.show.labels.location') }}:</span> {{ $account->location_name_snapshot ?? '-' }}</div>
                @if(($account->type?->name ?? '') === 'Router/WiFi')
                  <div><span class="text-muted">{{ __('accounts.show.labels.area_location') }}:</span> {{ data_get($account->metadata, 'router_area_location') ?: '-' }}</div>
                @endif
                <div><span class="text-muted">{{ __('accounts.show.labels.environment') }}:</span> {{ $account->environment ? __('accounts.index.form.environment_options.' . $account->environment) : '-' }}</div>
                <div><span class="text-muted">{{ __('accounts.show.labels.criticality') }}:</span> {{ $account->criticality ? __('accounts.index.form.criticality_options.' . $account->criticality) : '-' }}</div>
                <div><span class="text-muted">{{ __('accounts.show.labels.vendor_installer') }}:</span> {{ $account->vendor_installer ?? '-' }}</div>
                <div><span class="text-muted">{{ __('accounts.show.labels.last_verified_at') }}:</span> {{ $account->last_verified_at ? \Carbon\Carbon::parse($account->last_verified_at)->format('d-m-Y H:i') : '-' }}</div>

                @if($canDataUpdate && $isSuperAdmin)
                  <form method="POST" action="{{ route('admin.accounts.verify', $account->id) }}" class="mt-3" id="form-verify-account">
                    @csrf
                    <div class="input-group">
                      <input class="form-control" name="note" placeholder="{{ __('accounts.show.labels.verification_note_optional') }}">
                      <button class="btn btn-outline-success" type="submit"><i class="fas fa-check"></i> {{ __('accounts.show.actions.verify') }}</button>
                    </div>
                  </form>
                @endif
              </div>
            </div>

            <div class="col-md-8">
              <div class="border rounded p-3 mb-3">
                <div class="fw-semibold mb-2">{{ __('accounts.show.sections.endpoint') }}</div>
                @forelse($account->endpoints as $ep)
                  @php
                    $protocol = $ep->protocol ? strtolower((string) $ep->protocol) : null;
                    $isHttp = $protocol && in_array($protocol, ['http', 'https'], true);
                    $hasLocal = !empty($ep->ip_local);
                    $hasPublic = !empty($ep->ip_public);
                    $hasHost = $hasLocal || $hasPublic || !empty($ep->hostname);
                    $canOpen = $isHttp && $hasHost;
                  @endphp
                  <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                    <div>
                      <div class="fw-semibold">{{ strtoupper($ep->service) }}</div>
                      <div class="text-muted small">{{ $ep->protocol ?? '-' }} | {{ $ep->ip_local ?? '-' }} | {{ $ep->ip_public ?? '-' }} | {{ __('accounts.show.labels.port') }}: {{ $ep->port ?? '-' }}</div>
                    </div>
                    <div class="d-flex gap-2">
                      @if($canOpen)
                        @if($hasLocal && $hasPublic)
                          <div class="btn-group">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.accounts.endpoints.open', ['endpointId' => $ep->id, 'target' => 'local']) }}" target="_blank" rel="noopener">{{ __('accounts.show.endpoint.open') }}</a>
                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                              <span class="visually-hidden">{{ __('accounts.show.endpoint.toggle_dropdown') }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                              <li>
                                <a class="dropdown-item" href="{{ route('admin.accounts.endpoints.open', ['endpointId' => $ep->id, 'target' => 'local']) }}" target="_blank" rel="noopener">{{ __('accounts.show.endpoint.open_local') }}</a>
                              </li>
                              <li>
                                <a class="dropdown-item" href="{{ route('admin.accounts.endpoints.open', ['endpointId' => $ep->id, 'target' => 'public']) }}" target="_blank" rel="noopener">{{ __('accounts.show.endpoint.open_public') }}</a>
                              </li>
                            </ul>
                          </div>
                        @else
                          <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.accounts.endpoints.open', ['endpointId' => $ep->id]) }}" target="_blank" rel="noopener">{{ __('accounts.show.endpoint.open') }}</a>
                        @endif
                      @endif
                    </div>
                  </div>
                @empty
                  <div class="text-muted">{{ __('accounts.show.empty.endpoint') }}</div>
                @endforelse
              </div>

              <div class="border rounded p-3">
                <div class="fw-semibold mb-2">{{ __('accounts.show.sections.credentials') }}</div>
                <div class="text-muted small mb-2">{{ __('accounts.show.secrets.masked_note') }}</div>

                @if($canSecretsUpdate)
                  <div class="border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <div class="fw-semibold">{{ __('accounts.show.secrets.add_credential') }}</div>
                        <div class="text-muted small">{{ __('accounts.show.secrets.add_credential_hint') }}</div>
                      </div>
                      <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-secret-row">
                        <i class="fas fa-plus"></i> {{ __('accounts.show.secrets.add_row') }}
                      </button>
                    </div>

                    <form method="POST" action="{{ route('admin.accounts.secrets.add', $account->id) }}" class="mt-2" id="form-add-secrets">
                      @csrf
                      <input type="hidden" name="kind" value="current">
                      <div class="mb-2">
                        <label class="form-label">{{ __('accounts.show.secrets.bulk_paste_label') }}</label>
                        <div class="input-group">
                          <textarea class="form-control" id="bulk-secrets" rows="2" placeholder="{{ __('accounts.show.secrets.bulk_placeholder') }}"></textarea>
                          <button type="button" class="btn btn-outline-secondary" id="btn-parse-bulk">{{ __('accounts.show.secrets.parse') }}</button>
                        </div>
                      </div>

                      <div id="secrets-container"></div>

                      <div class="row g-2 mt-1">
                        <div class="col-12">
                          <input class="form-control" name="reason" placeholder="{{ __('accounts.show.secrets.add_reason_optional') }}" maxlength="500">
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                          <button type="submit" class="btn btn-primary">{{ __('accounts.show.secrets.save_credentials') }}</button>
                        </div>
                      </div>
                    </form>
                  </div>
                @endif

                @forelse($account->secrets as $s)
                  <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                    <div>
                      <div class="fw-semibold">
                        {{ $s->label ?? '-' }} ({{ $s->kind }})
                        @if(isset($s->is_active) && !$s->is_active)
                          <span class="badge bg-light text-muted">{{ __('accounts.show.secrets.inactive') }}</span>
                        @endif
                      </div>
                      <div class="text-muted small">{{ __('accounts.show.labels.username') }}: <span class="font-monospace">{{ $s->username ?? '-' }}</span></div>
                      <div class="text-muted small">{{ __('accounts.show.labels.secret') }}: <span class="font-monospace">********</span></div>
                    </div>
                    <div class="d-flex gap-2">
                      <button type="button" class="btn btn-sm btn-outline-secondary btn-copy-username" data-secret-id="{{ $s->id }}">{{ __('accounts.show.secrets.copy_username') }}</button>
                      @if($canSecretsRead)
                        <button type="button" class="btn btn-sm btn-outline-danger btn-reveal" data-secret-id="{{ $s->id }}">{{ __('accounts.show.secrets.reveal') }}</button>
                      @endif
                      @if($isSuperAdmin && $canSecretsUpdate && ($s->is_active ?? true))
                        <form method="POST" action="{{ route('admin.accounts.secrets.deactivate', $s->id) }}" class="d-inline form-deactivate-secret">
                          @csrf
                          <input type="hidden" name="reason" value="manual_deactivate">
                          <button type="button" class="btn btn-sm btn-outline-warning btn-deactivate-secret">{{ __('accounts.show.secrets.deactivate') }}</button>
                        </form>
                      @endif
                    </div>
                  </div>
                @empty
                  <div class="text-muted">{{ __('accounts.show.empty.credentials') }}</div>
                @endforelse

                @if($isSuperAdmin && $canSecretsUpdate)
                  <hr>
                  <div class="fw-semibold mb-2">{{ __('accounts.show.sections.pending_approvals') }}</div>
                  @if(($pendingApprovals ?? collect())->isEmpty())
                    <div class="text-muted">{{ __('accounts.show.empty.pending_approvals') }}</div>
                  @else
                    @foreach($pendingApprovals as $ap)
                      <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                        <div>
                          <div class="fw-semibold">#{{ $ap->id }} - {{ $ap->request_type }}</div>
                          <div class="text-muted small">{{ __('accounts.show.approvals.requester') }}: {{ $ap->requester_id }} | {{ __('accounts.show.approvals.secret') }}: {{ $ap->secret_id }} | {{ __('accounts.show.approvals.created_at') }}: {{ $ap->created_at?->format('d-m-Y H:i') }}</div>
                          @if($ap->reason)
                            <div class="text-muted small">{{ __('accounts.show.approvals.reason') }}: {{ $ap->reason }}</div>
                          @endif
                        </div>
                        <form method="POST" action="{{ route('admin.accounts.approvals.approve', $ap->id) }}">
                          @csrf
                          <button class="btn btn-sm btn-success" type="submit">{{ __('accounts.show.approvals.approve') }}</button>
                        </form>
                      </div>
                    @endforeach
                  @endif
                @endif

                @if($canSecretsUpdate)
                  <hr>
                  <div class="fw-semibold mb-2">{{ __('accounts.show.secrets.rotate_title') }}</div>
                  <form method="POST" action="{{ route('admin.accounts.secrets.rotate', $account->id) }}" class="row g-2">
                    @csrf
                    <div class="col-md-2">
                      <select class="form-select" name="kind" required>
                        <option value="current">{{ __('accounts.show.secrets.kind_current') }}</option>
                        <option value="default">{{ __('accounts.show.secrets.kind_default') }}</option>
                      </select>
                    </div>
                    <div class="col-md-2"><input class="form-control" name="label" placeholder="{{ __('accounts.show.secrets.label_optional') }}"></div>
                    <div class="col-md-3"><input class="form-control" name="username" placeholder="{{ __('accounts.show.secrets.username_optional') }}"></div>
                    <div class="col-md-3"><input class="form-control" name="new_secret" type="password" placeholder="{{ __('accounts.show.secrets.new_secret') }}" required></div>
                    <div class="col-md-2"><button class="btn btn-danger w-100" type="submit">{{ __('accounts.show.secrets.rotate') }}</button></div>
                    <div class="col-12"><input class="form-control" name="reason" placeholder="{{ __('accounts.show.secrets.reason_optional') }}"></div>
                  </form>
                @endif
              </div>
            </div>

            <div class="col-12">
              <div class="border rounded p-3">
                <div class="fw-semibold mb-2">{{ __('accounts.show.sections.notes') }}</div>
                <div class="text-muted">{{ $account->note ?: '-' }}</div>
              </div>
            </div>

            <div class="col-12">
              <div class="border rounded p-3">
                <div class="fw-semibold mb-2">{{ __('accounts.show.sections.audit_logs') }}</div>
                @php $logs = $auditLogs ?? collect(); @endphp
                @if($logs->isEmpty())
                  <div class="text-muted">{{ __('accounts.show.empty.audit_logs') }}</div>
                @else
                  <div class="table-responsive">
                    <table class="table table-sm table-striped table-bordered mb-0">
                      <thead>
                        <tr>
                          <th>{{ __('accounts.show.audit.time') }}</th>
                          <th>{{ __('accounts.show.audit.actor') }}</th>
                          <th>{{ __('accounts.show.audit.action') }}</th>
                          <th>{{ __('accounts.show.audit.result') }}</th>
                          <th>{{ __('accounts.show.audit.target') }}</th>
                          <th>{{ __('accounts.show.audit.reason') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($logs as $l)
                          <tr>
                            <td>{{ $l->created_at?->format('d-m-Y H:i') }}</td>
                            <td>{{ $l->actor?->name ?? ($l->actor_user_id ?? '-') }}</td>
                            <td>{{ $l->action }}</td>
                            <td>{{ $l->result }}</td>
                            <td>{{ ($l->target_type ?? '-') }}#{{ ($l->target_id ?? '-') }}</td>
                            <td>{{ $l->reason ?? '-' }}</td>
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
    </div>
  </div>

  <!-- Reveal Modal -->
  <div class="modal fade" id="revealModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{ __('accounts.show.reauth.modal_title') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger d-none" id="reveal-error"></div>
            <div class="mb-2 text-muted small">{{ __('accounts.show.reauth.subtitle') }}</div>
            <input type="password" class="form-control" id="password-confirm" placeholder="{{ __('accounts.show.reauth.password_confirm') }}">
          <input type="hidden" id="secret-id">
          <div class="mt-3">
              <div class="fw-semibold">{{ __('accounts.show.reauth.result_label') }}</div>
              <pre class="border rounded p-2 mb-0" id="reveal-result" style="min-height: 60px;">{{ __('accounts.show.reauth.result_empty') }}</pre>
          </div>
            <div class="form-text mt-2">{{ __('accounts.show.reauth.audit_note') }}</div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.close') }}</button>
            <button type="button" class="btn btn-danger" id="btn-do-reveal">{{ __('accounts.show.secrets.reveal') }}</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('js')
  <script>
    (function() {
      const i18n = {
        copied: @json(__('accounts.show.swal.copied')),
        copyFailed: @json(__('accounts.show.swal.copy_failed')),
        noAccess: @json(__('accounts.show.swal.no_access')),
        deactivateTitle: @json(__('accounts.show.swal.deactivate_title')),
        deactivateText: @json(__('accounts.show.swal.deactivate_text')),
        deactivateYes: @json(__('accounts.show.swal.deactivate_yes')),
        needApprovalTitle: @json(__('accounts.show.swal.need_approval_title')),
        needApprovalInputLabel: @json(__('accounts.show.swal.need_approval_input_label')),
        needApprovalPlaceholder: @json(__('accounts.show.swal.need_approval_placeholder')),
        needApprovalConfirm: @json(__('accounts.show.swal.need_approval_confirm')),
        sent: @json(__('accounts.show.swal.sent')),
        approvalSent: @json(__('accounts.show.swal.approval_sent')),
        approvalFailed: @json(__('accounts.show.swal.approval_failed')),
        revealFailed: @json(__('accounts.show.swal.reveal_failed')),
        genericError: @json(__('accounts.show.swal.generic_error')),
        cancel: @json(__('common.cancel')),
        emptyResult: @json(__('accounts.show.reauth.result_empty')),
        row: {
          roleLabel: @json(__('accounts.show.secrets.row.role_label')),
          rolePlaceholder: @json(__('accounts.show.secrets.row.role_placeholder')),
          usernameLabel: @json(__('accounts.show.secrets.row.username')),
          usernamePlaceholder: @json(__('accounts.show.secrets.row.username_placeholder')),
          passwordLabel: @json(__('accounts.show.secrets.row.password')),
          passwordRequired: @json(__('accounts.show.secrets.row.password_required')),
          remove: @json(__('accounts.show.secrets.row.remove')),
        }
      };

      const copyText = async (text) => {
        try {
          await navigator.clipboard.writeText(text);
          Swal.fire({ icon: 'success', title: i18n.copied, timer: 1000, showConfirmButton: false });
        } catch (e) {
          Swal.fire({ icon: 'error', title: i18n.copyFailed });
        }
      };

      document.addEventListener('click', function(e) {
        const btnDeactivate = e.target.closest('.btn-deactivate-secret');
        if (btnDeactivate) {
          const form = btnDeactivate.closest('form');
          Swal.fire({
            icon: 'warning',
            title: i18n.deactivateTitle,
            text: i18n.deactivateText,
            showCancelButton: true,
            confirmButtonText: i18n.deactivateYes,
            cancelButtonText: i18n.cancel
          }).then((r) => {
            if (r.isConfirmed) form.submit();
          });
          return;
        }

        const btn = e.target.closest('.btn-copy-username');
        if (!btn) return;
        const secretId = btn.dataset.secretId;
        if (!secretId) return;

        (async () => {
          try {
            const res = await fetch(`{{ url('/admin/accounts/secrets') }}/${secretId}/copy-username`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': @json(csrf_token()),
              },
              body: JSON.stringify({})
            });

            if (!res.ok) {
              const data = await res.json().catch(() => ({}));
              Swal.fire({ icon: 'error', title: i18n.copyFailed, text: data.message || i18n.noAccess });
              return;
            }

            const data = await res.json();
            await copyText((data && data.username) ? data.username : '');
          } catch (err) {
            Swal.fire({ icon: 'error', title: i18n.copyFailed });
          }
        })();
      });

      document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-reveal');
        if (!btn) return;
        document.getElementById('secret-id').value = btn.dataset.secretId;
        document.getElementById('password-confirm').value = '';
        document.getElementById('reveal-result').textContent = i18n.emptyResult;
        document.getElementById('reveal-error').classList.add('d-none');
        if (window.bootstrap && bootstrap.Modal) {
          bootstrap.Modal.getOrCreateInstance(document.getElementById('revealModal')).show();
        }
      });

      document.getElementById('btn-do-reveal')?.addEventListener('click', async function() {
        const secretId = document.getElementById('secret-id').value;
        const pass = document.getElementById('password-confirm').value;
        const $err = document.getElementById('reveal-error');
        $err.classList.add('d-none');

        try {
          const res = await fetch(`{{ url('/admin/accounts/secrets') }}/${secretId}/reveal`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': @json(csrf_token()),
            },
            body: JSON.stringify({ password_confirm: pass })
          });

          if (!res.ok) {
            const data = await res.json().catch(() => ({}));
            if (data && data.requires_approval) {
              const { value: reason } = await Swal.fire({
                icon: 'warning',
                title: i18n.needApprovalTitle,
                input: 'text',
                inputLabel: i18n.needApprovalInputLabel,
                inputPlaceholder: i18n.needApprovalPlaceholder,
                showCancelButton: true,
                confirmButtonText: i18n.needApprovalConfirm,
                cancelButtonText: i18n.cancel
              });

              if (reason !== undefined) {
                const req = await fetch(`{{ url('/admin/accounts/secrets') }}/${secretId}/approval`, {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': @json(csrf_token()),
                  },
                  body: JSON.stringify({ reason: reason || null })
                });

                if (req.ok) {
                  Swal.fire({ icon: 'success', title: i18n.sent, text: i18n.approvalSent });
                } else {
                  const d2 = await req.json().catch(() => ({}));
                  Swal.fire({ icon: 'error', title: @json(__('common.error')), text: d2.message || i18n.approvalFailed });
                }
              }
              return;
            }
            throw new Error(data.message || i18n.revealFailed);
          }

          const data = await res.json();
          document.getElementById('reveal-result').textContent = `username: ${data.username || '-'}\nsecret: ${data.secret}`;
        } catch (e) {
          $err.textContent = e.message || i18n.genericError;
          $err.classList.remove('d-none');
        }
      });

      // Add credentials repeater + bulk paste
      const escapeHtml = (s) => String(s || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');

      const $container = document.getElementById('secrets-container');
      const makeRow = (row) => {
        const label = escapeHtml(row?.label || '');
        const username = escapeHtml(row?.username || '');
        const pw = escapeHtml(row?.password || '');
        return `
          <div class="row g-2 align-items-end secret-row">
            <div class="col-md-3">
              <label class="form-label">${i18n.row.roleLabel}</label>
              <input class="form-control" name="secrets[0][label]" placeholder="${i18n.row.rolePlaceholder}" value="${label}">
            </div>
            <div class="col-md-4">
              <label class="form-label">${i18n.row.usernameLabel}</label>
              <input class="form-control" name="secrets[0][username]" placeholder="${i18n.row.usernamePlaceholder}" value="${username}">
            </div>
            <div class="col-md-4">
              <label class="form-label">${i18n.row.passwordLabel}</label>
              <input class="form-control" name="secrets[0][new_secret]" type="password" autocomplete="new-password" placeholder="${i18n.row.passwordRequired}" value="${pw}" required>
            </div>
            <div class="col-md-1 d-grid">
              <button type="button" class="btn btn-outline-danger btn-remove-secret-row" title="${i18n.row.remove}"><i class="fas fa-trash"></i></button>
            </div>
          </div>
        `;
      };

      const reindexRows = () => {
        if (!$container) return;
        const rows = Array.from($container.querySelectorAll('.secret-row'));
        rows.forEach((row, idx) => {
          row.querySelectorAll('input').forEach((inp) => {
            const name = inp.getAttribute('name') || '';
            inp.setAttribute('name', name
              .replace(/secrets\[\d+\]\[label\]/, `secrets[${idx}][label]`)
              .replace(/secrets\[\d+\]\[username\]/, `secrets[${idx}][username]`)
              .replace(/secrets\[\d+\]\[new_secret\]/, `secrets[${idx}][new_secret]`)
            );
          });
        });
      };

      const ensureOneRow = () => {
        if (!$container) return;
        if ($container.querySelectorAll('.secret-row').length === 0) {
          $container.insertAdjacentHTML('beforeend', makeRow({}));
          reindexRows();
        }
      };

      const parseBulk = (text) => {
        const lines = (text || '').split(/\r?\n/).map(l => l.trim()).filter(Boolean);
        return lines.map(line => {
          let parts = [];
          if (line.includes('|')) parts = line.split('|');
          else if (line.includes(';')) parts = line.split(';');
          else if (line.includes(',')) parts = line.split(',');
          else if (line.includes(':')) parts = line.split(':');
          else parts = [line];

          return {
            username: (parts[0] || '').trim(),
            password: (parts[1] || '').trim(),
            label: (parts[2] || '').trim(),
          };
        }).filter(r => r.username || r.password || r.label);
      };

      document.getElementById('btn-add-secret-row')?.addEventListener('click', () => {
        if (!$container) return;
        $container.insertAdjacentHTML('beforeend', makeRow({}));
        reindexRows();
        ensureOneRow();
      });

      document.getElementById('btn-parse-bulk')?.addEventListener('click', () => {
        if (!$container) return;
        const text = document.getElementById('bulk-secrets')?.value || '';
        const rows = parseBulk(text);
        if (!rows.length) return;
        $container.innerHTML = '';
        rows.forEach(r => $container.insertAdjacentHTML('beforeend', makeRow(r)));
        reindexRows();
        ensureOneRow();
      });

      document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-remove-secret-row');
        if (!btn) return;
        const row = btn.closest('.secret-row');
        if (!row || !$container) return;
        const count = $container.querySelectorAll('.secret-row').length;
        if (count <= 1) {
          row.querySelectorAll('input').forEach(i => i.value = '');
        } else {
          row.remove();
        }
        reindexRows();
        ensureOneRow();
      });

      // initialize
      ensureOneRow();
    })();
  </script>
@endsection
