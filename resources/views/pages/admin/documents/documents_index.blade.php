@extends('layouts.master')

@section('title', __('documents.archived') . ' | IGI')
@section('title-sub', ' ' . __('documents.dashboard') . ' ' . __('documents.archived') . ' ')
@section('pagetitle', __('documents.archived'))

@section('css')
    <style>
        .doc-page .page-actions .btn { white-space: nowrap; }
        .doc-page .card { border-radius: .75rem; }
        .doc-page .card-header { background: transparent; }
        .doc-page .empty-state { border: 1px dashed rgba(0,0,0,.15); border-radius: .75rem; padding: 1rem; }
        .doc-page .table thead th { font-weight: 600; color: var(--bs-secondary-color); }
    </style>
@endsection

@section('content')
    @php
        $canCreate = \App\Support\MenuAccess::can(auth()->user(), 'documents_archive', 'create');
        $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'documents_archive', 'update');
        $isTrash = request('trashed') === 'only';
    @endphp

    <div class="container-fluid doc-page">
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-start">
                    <div>
                        <h4 class="mb-1">{{ __('documents.archived') }}</h4>
                        <div class="text-muted">{{ __('documents.subtitle') }}</div>
                        @if($isTrash)
                            <div class="mt-2"><span class="badge bg-danger">{{ __('documents.trash.label') }}</span> <span class="text-muted small">{{ __('documents.trash.hint') }}</span></div>
                        @endif
                    </div>
                    <div class="d-flex flex-wrap gap-2 page-actions">
                        <a href="{{ route('admin.documents.dashboard') }}" class="btn btn-outline-secondary">{{ __('documents.dashboard') }}</a>
                        @if($canUpdate)
                            @if($isTrash)
                                <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary">{{ __('documents.trash.view_active') }}</a>
                            @else
                                <a href="{{ route('admin.documents.index', array_merge(request()->except('page'), ['trashed' => 'only'])) }}" class="btn btn-outline-danger">{{ __('documents.trash.open_trash') }}</a>
                            @endif
                        @endif
                        @if($canCreate)
                            <a href="{{ route('admin.documents.create') }}" class="btn btn-primary">{{ __('documents.actions.upload') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <strong>{{ __('documents.filter.title') }}</strong>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.documents.index') }}" class="row g-2">
                    @if($isTrash)
                        <input type="hidden" name="trashed" value="only">
                    @endif
                    <div class="col-12 col-md-3">
                        <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ __('documents.filter.search_placeholder') }}">
                    </div>
                    <div class="col-12 col-md-2">
                        <select class="form-select" name="vendor_id">
                            <option value="">{{ __('documents.filter.vendor_all') }}</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}" @selected((string)request('vendor_id')===(string)$v->id)>{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <select class="form-select" name="document_type">
                            <option value="">{{ __('documents.filter.type_all') }}</option>
                            @foreach($documentTypes as $t)
                                <option value="{{ $t }}" @selected(request('document_type')===$t)>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <select class="form-select" name="status">
                            <option value="">{{ __('documents.filter.status_all') }}</option>
                            @foreach($statuses as $s)
                                <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <select class="form-select" name="location_id">
                            <option value="">{{ __('documents.filter.plant_site_all') }}</option>
                            @foreach($locations as $l)
                                <option value="{{ $l->id }}" @selected((string)request('location_id')===(string)$l->id)>
                                    {{ $l->plant_site }}{{ $l->name ? (' - ' . $l->name) : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-2">
                        <input type="date" class="form-control" name="contract_end_from" value="{{ request('contract_end_from') }}" placeholder="{{ __('documents.filter.end_from') }}">
                    </div>
                    <div class="col-12 col-md-2">
                        <input type="date" class="form-control" name="contract_end_to" value="{{ request('contract_end_to') }}" placeholder="{{ __('documents.filter.end_to') }}">
                    </div>
                    <div class="col-6 col-md-2">
                        <input type="number" step="0.01" class="form-control" name="min_value" value="{{ request('min_value') }}" placeholder="{{ __('documents.filter.min_value') }}">
                    </div>
                    <div class="col-6 col-md-2">
                        <input type="number" step="0.01" class="form-control" name="max_value" value="{{ request('max_value') }}" placeholder="{{ __('documents.filter.max_value') }}">
                    </div>
                    <div class="col-12 col-md-2">
                        <input type="text" class="form-control" name="tag" value="{{ request('tag') }}" placeholder="{{ __('documents.filter.tag_exact') }}">
                    </div>

                    <div class="col-12 col-md-2">
                        <button class="btn btn-primary w-100" type="submit">{{ __('documents.filter.apply') }}</button>
                    </div>
                    <div class="col-12 col-md-2">
                        <a class="btn btn-outline-secondary w-100" href="{{ route('admin.documents.index') }}">{{ __('documents.filter.reset') }}</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if($documents->count() === 0)
                    <div class="empty-state text-muted">{{ __('documents.index.no_results') }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle" id="documentsTable">
                            <thead>
                                <tr>
                                    <th>{{ __('documents.index.table.document_title') }}</th>
                                    <th>{{ __('documents.index.table.vendor') }}</th>
                                    <th>{{ __('documents.index.table.type') }}</th>
                                    <th>{{ __('documents.index.table.plant') }}</th>
                                    <th>{{ __('documents.index.table.status') }}</th>
                                    <th>{{ __('documents.index.table.start_end') }}</th>
                                    <th>{{ __('documents.index.table.value') }}</th>
                                    <th>{{ __('documents.index.table.last_update') }}</th>
                                    @if($isTrash && $canUpdate)
                                        <th class="text-end">{{ __('documents.index.table.actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $d)
                                    <tr>
                                        <td>
                                            <a class="fw-semibold" href="{{ route('admin.documents.show', $d->document_id) }}">{{ $d->document_title }}</a>
                                            <div class="text-muted small">{{ $d->document_number ?: '-' }} • {{ $d->confidentiality_level }}</div>
                                            @if(method_exists($d, 'trashed') && $d->trashed())
                                                <div class="mt-1"><span class="badge bg-danger">{{ __('documents.show.deleted') }}</span></div>
                                            @endif
                                        </td>
                                        <td>{{ $d->vendor?->name ?? '-' }}</td>
                                        <td><span class="badge bg-light text-dark">{{ $d->document_type }}</span></td>
                                        <td class="small">
                                            @php $plants = $d->sites->pluck('plant_site')->filter()->unique()->values(); @endphp
                                            {{ $plants->isEmpty() ? '-' : $plants->join(', ') }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $d->status === 'Active' ? 'success' : ($d->status === 'Expired' ? 'danger' : ($d->status === 'Archived' ? 'secondary' : 'warning')) }}">{{ $d->status }}</span>
                                        </td>
                                        <td class="small">
                                            @php $t = $d->contractTerms; @endphp
                                            {{ $t?->start_date?->format('Y-m-d') ?? '-' }} / {{ $t?->end_date?->format('Y-m-d') ?? '-' }}
                                        </td>
                                        <td class="small">
                                            @php $v = $d->contractTerms?->contract_value; @endphp
                                            {{ $v !== null ? number_format((float)$v, 2) . ' ' . ($d->contractTerms?->currency ?? '') : '-' }}
                                        </td>
                                        <td class="small">{{ $d->updated_at?->format('Y-m-d H:i') }}</td>
                                        @if($isTrash && $canUpdate)
                                            <td class="text-end">
                                                <form method="POST" action="{{ route('admin.documents.restore', $d->document_id) }}" onsubmit="return confirm(@js(__('documents.show.restore_question')))" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-success" type="submit">{{ __('documents.show.restore') }}</button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection

@section('js')
    <script>
        (function() {
            if (!window.jQuery) return;
            var $ = window.jQuery;

            $(function() {
                if (!document.getElementById('documentsTable')) return;
                if (!$.fn || !$.fn.DataTable) return;

                $('#documentsTable').DataTable({
                    pageLength: 25,
                    order: [[7, 'desc']],
                });
            });
        })();
    </script>
@endsection
