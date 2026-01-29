@extends('layouts.master')

@section('title', __('uniforms.distribution.page_title'))
@section('title-sub', __('uniforms.distribution.title_sub'))
@section('pagetitle', __('uniforms.distribution.pagetitle'))

@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
  <style>
    .igi-actions {
      display: flex;
      gap: .5rem;
      flex-wrap: wrap;
    }
    .igi-actions .btn {
      white-space: nowrap;
    }
    @media (max-width: 575.98px) {
      .igi-actions {
        flex-direction: column;
        width: 100%;
      }
      .igi-actions .btn {
        width: 100%;
      }
    }
  </style>
@endsection

@section('content')
  @php
    $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'uniforms_distribution', 'create');
    $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'uniforms_distribution', 'update');
  @endphp
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
        <div
          class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
          <h5 class="card-title mb-0">{{ __('uniforms.distribution.card_title') }}</h5>
          <div class="igi-actions">
            <a href="{{ route('admin.uniforms.master') }}" class="btn btn-secondary btn-sm"><i
                class="fas fa-list"></i>
              {{ __('uniforms.nav.master') }}</a>
            <a href="{{ route('admin.uniforms.stock') }}" class="btn btn-outline-secondary btn-sm"><i
                class="fas fa-warehouse"></i> {{ __('uniforms.nav.stock') }}</a>
            <a href="{{ route('admin.uniforms.lots') }}" class="btn btn-outline-primary btn-sm"><i
              class="fas fa-layer-group"></i> {{ __('uniforms.nav.lots') }}</a>
            <a href="{{ route('admin.uniforms.reconcile') }}" class="btn btn-outline-primary btn-sm"><i
              class="fas fa-scale-balanced"></i> {{ __('uniforms.nav.reconcile') }}</a>
            <a href="{{ route('admin.uniforms.adjustments') }}" class="btn btn-outline-primary btn-sm"><i
              class="fas fa-sliders"></i> {{ __('uniforms.nav.adjustments') }}</a>
            <a href="{{ route('admin.uniforms.writeoffs') }}" class="btn btn-outline-danger btn-sm"><i
              class="fas fa-trash"></i> {{ __('uniforms.nav.writeoffs') }}</a>
          </div>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.uniforms.distribution.issue') }}" class="row g-3">
            @csrf
            <div class="col-md-6">
              <label class="form-label">{{ __('uniforms.distribution.form.item') }}</label>
              <select name="uniform_item_id" class="form-select js-item-select" required>
                <option value="">{{ __('uniforms.distribution.form.select_item') }}</option>
                @foreach($items as $item)
                  <option value="{{ $item->id }}">{{ $item->item_code }} - {{ $item->item_name }} - {{ $item->sizeMaster?->code ?? $item->size ?? '-' }} | Stock:
                    {{ $item->current_stock }} {{ $item->uom }}
                  </option>
                @endforeach
              </select>
              <small class="text-muted">{{ __('uniforms.distribution.form.search_item_hint') }}</small>
            </div>
            <div class="col-md-4">
              <label class="form-label">{{ __('uniforms.distribution.form.employee') }}</label>
              <select name="issued_to_employee_id" class="form-select" required>
                <option value="">{{ __('uniforms.distribution.form.select_employee') }}</option>
                @foreach($employees as $e)
                  <option value="{{ $e->id }}">
                    {{ $e->name }} ({{ $e->no_id }})
                    @if($e->department || $e->position)
                      - {{ $e->department?->name ?? '-' }} / {{ $e->position?->name ?? '-' }}
                    @endif
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">{{ __('uniforms.distribution.form.qty') }}</label>
              <input type="number" name="qty" class="form-control" min="1" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">{{ __('uniforms.distribution.form.date_optional') }}</label>
              <input type="datetime-local" name="issued_at" class="form-control">
            </div>
            <div class="col-md-8">
              <label class="form-label">{{ __('uniforms.distribution.form.notes') }}</label>
              <input type="text" name="notes" class="form-control"
                placeholder="{{ __('uniforms.distribution.form.notes_placeholder') }}">
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary" {{ $canCreate ? '' : 'disabled' }} title="{{ $canCreate ? '' : __('common.no_access_create') }}"><i class="fas fa-paper-plane"></i> {{ __('uniforms.distribution.form.submit') }}</button>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">{{ __('uniforms.distribution.recent_title') }}</h5>
          <a href="{{ route('admin.uniforms.history') }}" class="btn btn-outline-secondary btn-sm">{{ __('uniforms.nav.open_history') }}</a>
        </div>
        <div class="card-body">
            @php
              $uniformIssueStatusLabels = [
                'ISSUED' => __('uniforms.distribution.issue_status.ISSUED'),
                'RETURNED' => __('uniforms.distribution.issue_status.RETURNED'),
                'REPLACED' => __('uniforms.distribution.issue_status.REPLACED'),
                'DAMAGED' => __('uniforms.distribution.issue_status.DAMAGED'),
              ];

              $uniformIssueStatusBadge = function (?string $status): string {
                $status = (string) ($status ?? '');

                return match ($status) {
                  'ISSUED' => 'bg-info-subtle text-info',
                  'RETURNED' => 'bg-success-subtle text-success',
                  'REPLACED' => 'bg-warning-subtle text-warning',
                  'DAMAGED' => 'bg-danger-subtle text-danger',
                  default => 'bg-secondary-subtle text-secondary',
                };
              };
            @endphp

            <table id="alternative-pagination" class="table table-nowrap table-striped table-bordered w-100">
              <thead>
                <tr>
                  <th>{{ __('common.no') }}</th>
                  <th>{{ __('uniforms.distribution.table.issue_code') }}</th>
                  <th>{{ __('common.date') }}</th>
                  <th>{{ __('common.item') }}</th>
                  <th>{{ __('common.size') }}</th>
                  <th>{{ __('uniforms.distribution.table.target') }}</th>
                  <th>{{ __('common.qty') }}</th>
                  <th>{{ __('common.status') }}</th>
                  <th>{{ __('common.by') }}</th>
                  <th>{{ __('common.notes') }}</th>
                  <th>{{ __('common.action') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($recentIssues as $iss)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $iss->issue_code }}</td>
                    <td>{{ $iss->issued_at ? \Carbon\Carbon::parse($iss->issued_at)->format('d-m-Y H:i') : '-' }}</td>
                    <td>{{ $iss->item?->item_code }} - {{ $iss->item?->item_name }}</td>
                    <td>{{ $iss->item?->sizeMaster?->code ?? $iss->item?->size ?? '-' }}</td>
                    <td>{{ $iss->issuedToEmployee?->name ?? $iss->issuedTo?->name ?? '-' }}</td>
                    <td>{{ $iss->qty }}</td>
                    <td>
                      <span class="badge {{ $uniformIssueStatusBadge((string) $iss->status) }}">
                        {{ $uniformIssueStatusLabels[$iss->status] ?? $iss->status }}
                      </span>
                    </td>
                    <td>{{ $iss->issuedBy?->name ?? '-' }}</td>
                    <td>{{ $iss->notes ?? '-' }}</td>
                    <td>
                      @if($iss->status === 'ISSUED')
                        <form action="{{ route('admin.uniforms.issues.return', $iss->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm(@json(__('uniforms.distribution.actions.confirm_return')))" {{ $canUpdate ? '' : 'disabled' }} title="{{ $canUpdate ? '' : __('common.no_access_update') }}">{{ __('uniforms.distribution.actions.return') }}</button>
                        </form>

                        <button
                          type="button"
                          class="btn btn-sm btn-outline-warning js-open-replace"
                          data-action="{{ route('admin.uniforms.issues.replace', $iss->id) }}"
                          data-issue-code="{{ $iss->issue_code }}"
                          data-qty="{{ $iss->qty }}"
                          data-bs-toggle="modal"
                          data-bs-target="#replaceModal"
                          {{ $canUpdate ? '' : 'disabled' }} title="{{ $canUpdate ? '' : __('common.no_access_update') }}"
                        >{{ __('uniforms.distribution.actions.replacement') }}</button>
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
        </div>
      </div>

      <div class="modal fade" id="replaceModal" tabindex="-1" aria-labelledby="replaceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <form method="POST" id="replaceForm" action="#">
              @csrf
              <div class="modal-header">
                <h5 class="modal-title" id="replaceModalLabel">{{ __('uniforms.distribution.actions.replacement_modal_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="alert alert-warning">
                  <div class="fw-semibold">{{ __('uniforms.distribution.replacement_modal.audit_note_title') }}</div>
                  <div>{{ __('uniforms.distribution.replacement_modal.audit_note_text') }}</div>
                </div>

                <div class="mb-2">
                  <small class="text-muted">{{ __('uniforms.distribution.replacement_modal.issue_code_label') }} <span class="fw-semibold" id="replaceIssueCode">-</span></small>
                </div>

                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label">{{ __('uniforms.distribution.replacement_modal.qty_label') }}</label>
                    <input type="number" name="qty" id="replaceQty" class="form-control" min="1" required>
                  </div>

                  <div class="col-md-8">
                    <label class="form-label">{{ __('uniforms.distribution.replacement_modal.date_optional') }}</label>
                    <input type="datetime-local" name="issued_at" class="form-control">
                  </div>

                  <div class="col-12">
                    <label class="form-label">{{ __('uniforms.distribution.replacement_modal.reason_required') }}</label>
                    <input type="text" name="reason" class="form-control" maxlength="255" required placeholder="{{ __('uniforms.distribution.replacement_modal.reason_placeholder') }}">
                  </div>

                  <div class="col-12">
                    <label class="form-label">{{ __('uniforms.distribution.replacement_modal.notes_optional') }}</label>
                    <input type="text" name="notes" class="form-control" placeholder="{{ __('uniforms.distribution.replacement_modal.notes_placeholder') }}">
                  </div>

                  <div class="col-12">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="1" id="replaceReturnOld" name="return_old">
                      <label class="form-check-label" for="replaceReturnOld">{{ __('uniforms.distribution.replacement_modal.exchange_label') }}</label>
                    </div>
                    <small class="text-muted">{{ __('uniforms.distribution.replacement_modal.exchange_hint') }}</small>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="submit" class="btn btn-warning" onclick="return confirm(@json(__('uniforms.distribution.actions.confirm_replacement')))" {{ $canUpdate ? '' : 'disabled' }} title="{{ $canUpdate ? '' : __('common.no_access_update') }}">{{ __('uniforms.distribution.actions.process_replacement') }}</button>
              </div>
            </form>
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
    $(function () {
      const $select = $('.js-item-select');
      if ($select.length && $.fn.select2) {
        $select.select2({
          theme: 'bootstrap-5',
          width: '100%',
          placeholder: @json(__('uniforms.distribution.form.select_item')),
          allowClear: true,
        });
      }

      $(document).on('click', '.js-open-replace', function () {
        const action = $(this).data('action');
        const issueCode = $(this).data('issue-code');
        const qty = $(this).data('qty');

        $('#replaceForm').attr('action', action);
        $('#replaceIssueCode').text(issueCode || '-');
        $('#replaceQty').val(qty || 1);
        $('#replaceReturnOld').prop('checked', false);
      });
    });
  </script>
@endsection