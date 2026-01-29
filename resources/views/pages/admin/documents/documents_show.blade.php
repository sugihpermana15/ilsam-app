@extends('layouts.master')

@section('title', __('documents.detail') . ' | IGI')
@section('title-sub', ' Archived Berkas ')
@section('pagetitle', __('documents.detail'))

@section('css')
    <style>
        .doc-page .page-actions .btn { white-space: nowrap; }
        .doc-page .card { border-radius: .75rem; }
        .doc-page .card-header { background: transparent; }
        .doc-page .doc-title { letter-spacing: .2px; }
        .doc-page .kv dt { color: var(--bs-secondary-color); font-weight: 600; }
        .doc-page .kv dd { margin-bottom: .6rem; }
        .doc-page .badge-soft { background: rgba(13,110,253,.10); color: #0d6efd; }
        .doc-page .empty-state { border: 1px dashed rgba(0,0,0,.15); border-radius: .75rem; padding: 1rem; }
        .doc-page .table thead th { font-weight: 600; color: var(--bs-secondary-color); }
        .doc-page .modal .form-label { font-weight: 600; }
        .doc-page .select-multiple { min-height: 170px; }
    </style>
@endsection

@section('content')
    @php
        $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'documents_archive', 'update');
        $canDelete = \App\Support\MenuAccess::can(auth()->user(), 'documents_archive', 'delete');
        $canOpenMainDashboard = \App\Support\MenuAccess::can(auth()->user(), 'admin_dashboard', 'read');
        $isTrashed = method_exists($doc, 'trashed') && $doc->trashed();
    @endphp

    <div class="container-fluid doc-page">
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-start">
                    <div>
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-1">
                            <h4 class="mb-0 doc-title">{{ $doc->document_title }}</h4>
                            <span class="badge bg-light text-dark">{{ $doc->document_type }}</span>
                            <span class="badge bg-{{ $doc->status === 'Active' ? 'success' : ($doc->status === 'Expired' ? 'danger' : ($doc->status === 'Archived' ? 'secondary' : 'warning')) }}">{{ $doc->status }}</span>
                            <span class="badge badge-soft">{{ $doc->confidentiality_level }}</span>
                            @if($isTrashed)
                                <span class="badge bg-danger">{{ __('documents.show.deleted') }}</span>
                            @endif
                        </div>
                        <div class="text-muted">
                            <span class="me-2">{{ $doc->document_number ?: '-' }}</span>
                            <span class="text-muted">•</span>
                            <span class="ms-2">{{ __('documents.show.vendor') }}: {{ $doc->vendor?->name ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 page-actions">
                        <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary">{{ __('common.back') }}</a>
                        @if($canOpenMainDashboard)
                            <a href="{{ route('admin.dashboard', ['tab' => 'documents']) }}" class="btn btn-outline-secondary">{{ __('documents.dashboard') }}</a>
                        @endif
                        @if($isTrashed && $canUpdate)
                            <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#restoreDocModal">{{ __('documents.show.restore') }}</button>
                        @endif
                        @if($canUpdate && !$isTrashed)
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editDocModal">{{ __('documents.show.edit_metadata') }}</button>
                        @endif
                        @if($canDelete && !$isTrashed)
                            <button class="btn btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#deleteDocModal">{{ __('documents.show.delete') }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($isTrashed)
            <div class="alert alert-warning">
                {{ __('documents.show.trashed_banner') }}
            </div>
        @endif

        <div class="row g-3">
            <div class="col-12 col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <strong>{{ __('documents.show.metadata') }}</strong>
                    </div>
                    <div class="card-body">
                        @php
                            $tags = is_array($doc->tags) ? $doc->tags : [];
                            $sitesText = $doc->sites->isEmpty() ? '-' : $doc->sites->map(fn($l) => $l->plant_site . ($l->name ? (' - ' . $l->name) : ''))->join(', ');
                            $assetsText = $doc->assets->isEmpty() ? '-' : $doc->assets->map(fn($a) => ($a->asset_code ? ($a->asset_code . ' - ') : '') . $a->asset_name)->join(', ');
                        @endphp

                        <dl class="row kv mb-0">
                            <dt class="col-5 col-md-4">{{ __('documents.show.vendor') }}</dt>
                            <dd class="col-7 col-md-8">{{ $doc->vendor?->name ?? '-' }}</dd>

                            <dt class="col-5 col-md-4">{{ __('documents.show.plant_site') }}</dt>
                            <dd class="col-7 col-md-8">{{ $sitesText }}</dd>

                            <dt class="col-5 col-md-4">{{ __('documents.show.asset_service') }}</dt>
                            <dd class="col-7 col-md-8">{{ $assetsText }}</dd>

                            <dt class="col-5 col-md-4">{{ __('documents.show.department_owner') }}</dt>
                            <dd class="col-7 col-md-8">{{ $doc->departmentOwner?->name ?? '-' }}</dd>

                            <dt class="col-5 col-md-4">{{ __('documents.show.pic') }}</dt>
                            <dd class="col-7 col-md-8">{{ $doc->picUser?->name ?? '-' }}</dd>

                            <dt class="col-5 col-md-4">{{ __('documents.show.tags') }}</dt>
                            <dd class="col-7 col-md-8">
                                @if(empty($tags))
                                    <span class="text-muted">-</span>
                                @else
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($tags as $t)
                                            <span class="badge rounded-pill bg-light text-dark">{{ $t }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </dd>

                            <dt class="col-5 col-md-4">{{ __('documents.show.note') }}</dt>
                            <dd class="col-7 col-md-8">
                                @if(blank($doc->description))
                                    <span class="text-muted">-</span>
                                @else
                                    <div style="white-space: pre-wrap">{{ $doc->description }}</div>
                                @endif
                            </dd>

                            <dt class="col-5 col-md-4">{{ __('documents.show.created') }}</dt>
                            <dd class="col-7 col-md-8">{{ $doc->created_at?->format('Y-m-d H:i') ?? '-' }} <span class="text-muted">({{ $doc->creator?->name ?? '-' }})</span></dd>

                            <dt class="col-5 col-md-4 mb-0">{{ __('documents.show.updated') }}</dt>
                            <dd class="col-7 col-md-8 mb-0">{{ $doc->updated_at?->format('Y-m-d H:i') ?? '-' }} <span class="text-muted">({{ $doc->updater?->name ?? '-' }})</span></dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <strong>{{ __('documents.show.terms') }}</strong>
                    </div>
                    <div class="card-body">
                        @php $t = $doc->contractTerms; @endphp
                        @if(!$t)
                            <div class="empty-state text-muted">{{ __('documents.show.no_terms') }}</div>
                        @else
                            <dl class="row kv mb-0">
                                <dt class="col-5 col-md-4">{{ __('documents.create.start_date') }}</dt>
                                <dd class="col-7 col-md-8">{{ $t->start_date?->format('Y-m-d') ?? '-' }}</dd>

                                <dt class="col-5 col-md-4">{{ __('documents.create.end_date') }}</dt>
                                <dd class="col-7 col-md-8">{{ $t->end_date?->format('Y-m-d') ?? '-' }}</dd>

                                <dt class="col-5 col-md-4">{{ __('documents.create.renewal_type') }}</dt>
                                <dd class="col-7 col-md-8">{{ $t->renewal_type ?? '-' }}</dd>

                                <dt class="col-5 col-md-4">{{ __('documents.show.notice_period') }}</dt>
                                <dd class="col-7 col-md-8">{{ $t->notice_period_days !== null ? ($t->notice_period_days . ' ' . __('documents.show.days')) : '-' }}</dd>

                                <dt class="col-5 col-md-4">{{ __('documents.create.billing_cycle') }}</dt>
                                <dd class="col-7 col-md-8">{{ $t->billing_cycle ?? '-' }}</dd>

                                <dt class="col-5 col-md-4">{{ __('documents.show.value') }}</dt>
                                <dd class="col-7 col-md-8">{{ $t->contract_value !== null ? number_format((float)$t->contract_value, 2) . ' ' . ($t->currency ?? '') : '-' }}</dd>

                                <dt class="col-5 col-md-4">{{ __('documents.create.payment_terms') }}</dt>
                                <dd class="col-7 col-md-8">{{ $t->payment_terms ?? '-' }}</dd>

                                <dt class="col-5 col-md-4">{{ __('documents.show.scope') }}</dt>
                                <dd class="col-7 col-md-8">
                                    @if(blank($t->scope_service))
                                        <span class="text-muted">-</span>
                                    @else
                                        <div style="white-space: pre-wrap">{{ $t->scope_service }}</div>
                                    @endif
                                </dd>

                                <dt class="col-5 col-md-4 mb-0">{{ __('documents.create.remarks') }}</dt>
                                <dd class="col-7 col-md-8 mb-0">
                                    @if(blank($t->remarks))
                                        <span class="text-muted">-</span>
                                    @else
                                        <div style="white-space: pre-wrap">{{ $t->remarks }}</div>
                                    @endif
                                </dd>
                            </dl>
                        @endif

                        <div class="mt-3">
                            <div class="text-muted small">{{ __('documents.show.reminder') }}</div>
                            @if($t?->end_date)
                                <div class="small">{{ __('documents.show.reminder_will') }} <strong>{{ $t->end_date->format('Y-m-d') }}</strong> (90/60/30/14/7 {{ __('documents.show.days') }}).</div>
                            @else
                                <div class="small text-muted">{{ __('documents.show.reminder_inactive') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>{{ __('documents.show.files_versions') }}</strong>
                        @if($canUpdate && !$isTrashed)
                            <form method="POST" action="{{ route('admin.documents.files.upload', $doc->document_id) }}" enctype="multipart/form-data" class="d-flex flex-wrap gap-2 align-items-center">
                                @csrf
                                <input type="file" class="form-control form-control-sm" name="file" required>
                                <button class="btn btn-sm btn-primary" type="submit">{{ __('documents.show.upload_new_version') }}</button>
                            </form>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($doc->files->isEmpty())
                            <div class="empty-state text-muted">{{ __('documents.show.no_files') }}</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>{{ __('documents.show.version') }}</th>
                                            <th>{{ __('documents.show.file_name') }}</th>
                                            <th>{{ __('documents.show.type') }}</th>
                                            <th>{{ __('documents.show.size') }}</th>
                                            <th>{{ __('documents.show.uploaded') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($doc->files as $f)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-{{ $f->is_latest ? 'primary' : 'secondary' }}">v{{ $f->version_number }}</span>
                                                    @if($f->is_latest)
                                                        <span class="badge bg-success">{{ __('documents.show.latest') }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $f->file_name }}</td>
                                                <td class="small">{{ $f->file_type }}</td>
                                                <td class="small">
                                                    @php
                                                        $bytes = (int)($f->file_size ?? 0);
                                                        $kb = $bytes / 1024;
                                                        $mb = $kb / 1024;
                                                    @endphp
                                                    {{ $mb >= 1 ? number_format($mb, 2) . ' MB' : number_format($kb, 1) . ' KB' }}
                                                </td>
                                                <td class="small">{{ $f->uploaded_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                                <td class="text-end">
                                                    @php $link = $downloadLinks[$f->file_id] ?? null; @endphp
                                                    <a class="btn btn-sm btn-outline-primary {{ $link ? '' : 'disabled' }}" href="{{ $link ?? '#' }}" {{ $link ? '' : 'aria-disabled=true' }}>{{ __('documents.show.download') }}</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-muted small">{{ __('documents.show.download_hint') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <strong>{{ __('documents.show.relations_title') }}</strong>
                    </div>
                    <div class="card-body">
                        @if(empty($doc->contract_group_id))
                            <div class="empty-state text-muted">{{ __('documents.show.no_contract_group') }}</div>
                        @else
                            <div class="text-muted small mb-2">{{ __('documents.show.group') }}: <span class="badge bg-light text-dark">{{ $doc->contract_group_id }}</span></div>
                            @if($related->isEmpty())
                                <div class="empty-state text-muted">{{ __('documents.show.no_related_in_group') }}</div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped align-middle">
                                        <thead>
                                            <tr>
                                                <th>{{ __('documents.show.col_title') }}</th>
                                                <th>{{ __('documents.show.col_type') }}</th>
                                                <th>{{ __('documents.show.col_status') }}</th>
                                                <th>{{ __('documents.show.col_end_date') }}</th>
                                                <th>{{ __('documents.show.col_updated') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($related as $r)
                                                <tr>
                                                    <td><a href="{{ route('admin.documents.show', $r->document_id) }}">{{ $r->document_title }}</a></td>
                                                    <td><span class="badge bg-light text-dark">{{ $r->document_type }}</span></td>
                                                    <td><span class="badge bg-secondary">{{ $r->status }}</span></td>
                                                    <td class="small">{{ $r->contractTerms?->end_date?->format('Y-m-d') ?? '-' }}</td>
                                                    <td class="small">{{ $r->updated_at?->format('Y-m-d H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

        </div>

        @if($canUpdate && !$isTrashed)
            <div class="modal fade" id="editDocModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('documents.show.edit_metadata') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('admin.documents.update', $doc->document_id) }}" class="modal-body">
                            @csrf
                            @method('PATCH')

                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label">{{ __('documents.show.edit_form.title') }}</label>
                                    <input type="text" class="form-control" name="document_title" value="{{ old('document_title', $doc->document_title) }}" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">{{ __('documents.show.edit_form.number') }}</label>
                                    <input type="text" class="form-control" name="document_number" value="{{ old('document_number', $doc->document_number) }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">{{ __('documents.show.edit_form.type') }}</label>
                                    <select class="form-select" name="document_type" required>
                                        @foreach(['Contract','Quotation','PO','Invoice','Payment','Subscription','Renewal','Addendum','NDA','Other'] as $t)
                                            <option value="{{ $t }}" @selected(old('document_type', $doc->document_type)===$t)>{{ $t }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">{{ __('documents.show.edit_form.status') }}</label>
                                    <select class="form-select" name="status" required>
                                        @foreach(['Draft','Active','Expired','Terminated','Archived'] as $s)
                                            <option value="{{ $s }}" @selected(old('status', $doc->status)===$s)>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">{{ __('documents.show.edit_form.confidentiality') }}</label>
                                    <select class="form-select" name="confidentiality_level" required>
                                        @foreach(['Internal','Confidential','Restricted'] as $c)
                                            <option value="{{ $c }}" @selected(old('confidentiality_level', $doc->confidentiality_level)===$c)>{{ $c }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">{{ __('documents.show.edit_form.tags_comma') }}</label>
                                    <input type="text" class="form-control" name="tags" value="{{ old('tags', is_array($doc->tags) ? implode(', ', $doc->tags) : '') }}">
                                    <div class="form-text">{{ __('documents.show.edit_form.tags_example') }}</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">{{ __('documents.show.note') }}</label>
                                    <textarea class="form-control" name="description" rows="3">{{ old('description', $doc->description) }}</textarea>
                                </div>

                                <div class="col-12"><hr><h6 class="mb-0">{{ __('documents.show.edit_form.terms_heading') }}</h6></div>
                                @php $t = $doc->contractTerms; @endphp
                                <div class="col-6 col-md-3">
                                    <label class="form-label">{{ __('documents.show.edit_form.start') }}</label>
                                    <input type="date" class="form-control" name="start_date" value="{{ old('start_date', $t?->start_date?->format('Y-m-d')) }}">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label">{{ __('documents.show.edit_form.end') }}</label>
                                    <input type="date" class="form-control" name="end_date" value="{{ old('end_date', $t?->end_date?->format('Y-m-d')) }}">
                                    <div class="form-text">{{ __('documents.show.edit_form.end_hint') }}</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label">{{ __('documents.show.edit_form.renewal') }}</label>
                                    <input type="text" class="form-control" name="renewal_type" value="{{ old('renewal_type', $t?->renewal_type) }}">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label">{{ __('documents.show.edit_form.notice_days') }}</label>
                                    <input type="number" class="form-control" name="notice_period_days" value="{{ old('notice_period_days', $t?->notice_period_days) }}" min="0" max="3650">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label">{{ __('documents.show.edit_form.billing') }}</label>
                                    <input type="text" class="form-control" name="billing_cycle" value="{{ old('billing_cycle', $t?->billing_cycle) }}">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label">{{ __('documents.show.value') }}</label>
                                    <input type="number" step="0.01" class="form-control" name="contract_value" value="{{ old('contract_value', $t?->contract_value) }}">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label">{{ __('documents.create.currency') }}</label>
                                    <input type="text" class="form-control" name="currency" value="{{ old('currency', $t?->currency) }}">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label">{{ __('documents.create.payment_terms') }}</label>
                                    <input type="text" class="form-control" name="payment_terms" value="{{ old('payment_terms', $t?->payment_terms) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('documents.show.scope') }}</label>
                                    <textarea class="form-control" name="scope_service" rows="2">{{ old('scope_service', $t?->scope_service) }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('documents.create.remarks') }}</label>
                                    <textarea class="form-control" name="remarks" rows="2">{{ old('remarks', $t?->remarks) }}</textarea>
                                </div>

                                <div class="col-12"><hr><h6 class="mb-0">{{ __('documents.show.edit_form.relations_heading') }}</h6></div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('documents.show.plant_site') }}</label>
                                    <select class="form-select select-multiple" name="location_ids[]" multiple size="8">
                                        @foreach(($locations ?? collect()) as $l)
                                            <option value="{{ $l->id }}" @selected($doc->sites->pluck('id')->contains($l->id))>{{ $l->plant_site }}{{ $l->name ? (' - ' . $l->name) : '' }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">{{ __('documents.create.plant_site_tips') }}</div>
                                    <div class="mt-2">
                                        <label class="form-label mb-1">{{ __('documents.create.add_new_plant_site_optional') }}</label>
                                        <input type="text" class="form-control" name="new_locations" value="{{ old('new_locations') }}" placeholder="{{ __('documents.create.new_plant_site_placeholder') }}">
                                        <div class="form-text">{{ __('documents.create.new_plant_site_hint') }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('documents.show.edit_form.asset_service') }}</label>
                                    <select class="form-select select-multiple" name="asset_ids[]" multiple size="8">
                                        @foreach(\App\Models\Asset::query()->orderByDesc('id')->limit(500)->get() as $a)
                                            <option value="{{ $a->id }}" @selected($doc->assets->pluck('id')->contains($a->id))>{{ $a->asset_code }} - {{ $a->asset_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">{{ __('documents.show.edit_form.asset_tips') }}</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">{{ __('documents.show.vendor') }}</label>
                                    <select class="form-select" name="vendor_id" required>
                                        @foreach(\App\Models\AssetVendor::query()->where('is_active',true)->orderBy('name')->get() as $v)
                                            <option value="{{ $v->id }}" @selected((int)$doc->vendor_id === (int)$v->id)>{{ $v->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('documents.show.department_owner') }}</label>
                                    <select class="form-select" name="department_owner_id">
                                        <option value="">-</option>
                                        @foreach(\App\Models\Department::query()->orderBy('name')->get() as $d)
                                            <option value="{{ $d->id }}" @selected((int)($doc->department_owner_id ?? 0) === (int)$d->id)>{{ $d->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                                <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if($isTrashed && $canUpdate)
            <div class="modal fade" id="restoreDocModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('documents.show.confirm_restore') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success mb-0">
                                {{ __('documents.show.restore_question') }}
                                <div class="mt-2"><strong>{{ $doc->document_title }}</strong></div>
                                <div class="text-muted small">{{ $doc->document_number ?: '-' }} • {{ $doc->document_type }} • {{ $doc->vendor?->name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                            <form method="POST" action="{{ route('admin.documents.restore', $doc->document_id) }}">
                                @csrf
                                <button class="btn btn-success" type="submit">{{ __('documents.show.yes_restore') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($canDelete)
            <div class="modal fade" id="deleteDocModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('documents.show.confirm_delete') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning mb-0">
                                {{ __('documents.show.delete_warning') }}
                                <div class="mt-2"><strong>{{ $doc->document_title }}</strong></div>
                                <div class="text-muted small">{{ $doc->document_number ?: '-' }} • {{ $doc->document_type }} • {{ $doc->vendor?->name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                            <form method="POST" action="{{ route('admin.documents.destroy', $doc->document_id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">{{ __('documents.show.yes_delete') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection
