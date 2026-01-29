@extends('layouts.master')

@section('title', __('documents.dash.title') . ' | IGI')
@section('title-sub', ' Dashboard Archived Berkas ')
@section('pagetitle', __('documents.dash.title'))

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

        /** @var \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $expiring */
        /** @var \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $latest */
        /** @var \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $activeByMonth */
        $expiring = $expiring ?? collect();
        $latest = $latest ?? collect();
        $activeByMonth = $activeByMonth ?? collect();
    @endphp

    <div class="container-fluid doc-page">
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-start">
                    <div>
                        <h4 class="mb-1">{{ __('documents.dash.title') }}</h4>
                        <div class="text-muted">{{ __('documents.dash.subtitle') }}</div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 page-actions">
                        <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary">{{ __('documents.list') }}</a>
                        @if($canCreate)
                            <a href="{{ route('admin.documents.create') }}" class="btn btn-primary">{{ __('documents.upload') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-xl-6">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ __('documents.dash.expiring_title') }}</h5>
                        <a href="{{ route('admin.documents.index', ['status' => 'Active']) }}" class="btn btn-sm btn-outline-primary">{{ __('documents.dash.view_all') }}</a>
                    </div>
                    <div class="card-body">
                        @if($expiring->isEmpty())
                            <div class="empty-state text-muted">{{ __('documents.dash.none_expiring') }}</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>{{ __('documents.dash.doc_title') }}</th>
                                            <th>{{ __('documents.dash.vendor') }}</th>
                                            <th>{{ __('documents.dash.end_date') }}</th>
                                            <th>{{ __('documents.dash.status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expiring as $d)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.documents.show', $d->document_id) }}">{{ $d->document_title }}</a>
                                                    <div class="text-muted small">{{ $d->document_type }} • {{ $d->document_number }}</div>
                                                </td>
                                                <td>{{ $d->vendor?->name }}</td>
                                                <td>{{ optional($d->contractTerms?->end_date)->format('Y-m-d') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $d->status === 'Active' ? 'success' : ($d->status === 'Draft' ? 'secondary' : 'warning') }}">{{ $d->status }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ __('documents.dash.latest_title') }}</h5>
                        <a href="{{ route('admin.documents.index') }}" class="btn btn-sm btn-outline-primary">{{ __('documents.list') }}</a>
                    </div>
                    <div class="card-body">
                        @if($latest->isEmpty())
                            <div class="empty-state text-muted">{{ __('documents.dash.none_yet') }}</div>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach($latest as $d)
                                    <li class="list-group-item d-flex align-items-center justify-content-between">
                                        <div>
                                            <a href="{{ route('admin.documents.show', $d->document_id) }}">{{ $d->document_title }}</a>
                                            <div class="text-muted small">{{ $d->vendor?->name }} • {{ $d->document_type }} • {{ $d->document_number }}</div>
                                        </div>
                                        <div class="text-muted small">{{ $d->updated_at?->format('Y-m-d H:i') }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('documents.dash.active_by_month') }}</h5>
                    </div>
                    <div class="card-body">
                        @if($activeByMonth->isEmpty())
                            <div class="empty-state text-muted">{{ __('documents.dash.no_data') }}</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('documents.dash.month') }}</th>
                                            <th class="text-end">{{ __('documents.dash.total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activeByMonth as $row)
                                            <tr>
                                                <td>{{ $row->ym }}</td>
                                                <td class="text-end">{{ $row->total }}</td>
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
@endsection
