@extends('layouts.master')

@section('title', __('documents.create.title') . ' | IGI')
@section('title-sub', ' Upload Archived Berkas ')
@section('pagetitle', __('documents.create.title'))

@section('css')
    <style>
        .doc-page .page-actions .btn { white-space: nowrap; }
        .doc-page .card { border-radius: .75rem; }
        .doc-page .card-header { background: transparent; }
        .doc-page .select-multiple { min-height: 170px; }
    </style>
@endsection

@section('content')
    <div class="container-fluid doc-page">
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-start">
                    <div>
                        <h4 class="mb-1">{{ __('documents.create.title') }}</h4>
                        <div class="text-muted">{{ __('documents.create.subtitle') }}</div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 page-actions">
                        <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary">{{ __('common.back') }}</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <strong>{{ __('documents.create.wizard') }}</strong>
                <div class="text-muted small">{{ __('documents.create.wizard_hint') }}</div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data" class="row g-3">
                    @csrf

                    <div class="col-12 col-xl-6">
                        <label class="form-label">{{ __('documents.create.vendor') }} <span class="text-danger">*</span></label>
                        <select class="form-select" name="vendor_id" required>
                            <option value="">{{ __('documents.create.choose_vendor') }}</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}" @selected(old('vendor_id')==$v->id)>{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-xl-3">
                        <label class="form-label">{{ __('documents.create.type') }} <span class="text-danger">*</span></label>
                        <select class="form-select" name="document_type" required>
                            @foreach($documentTypes as $t)
                                <option value="{{ $t }}" @selected(old('document_type','Contract')===$t)>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-xl-3">
                        <label class="form-label">{{ __('documents.create.status') }} <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" required>
                            @foreach(['Draft','Active','Expired','Terminated','Archived'] as $s)
                                <option value="{{ $s }}" @selected(old('status','Draft')===$s)>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('documents.create.title_label') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="document_title" value="{{ old('document_title') }}" required>
                    </div>

                    <div class="col-12 col-xl-4">
                        <label class="form-label">{{ __('documents.create.number_optional') }}</label>
                        <input type="text" class="form-control" name="document_number" value="{{ old('document_number') }}" placeholder="{{ __('documents.create.number_placeholder') }}">
                    </div>

                    <div class="col-12 col-xl-4">
                        <label class="form-label">{{ __('documents.create.confidentiality') }}</label>
                        <select class="form-select" name="confidentiality_level" required>
                            @foreach($confidentialities as $c)
                                <option value="{{ $c }}" @selected(old('confidentiality_level','Internal')===$c)>{{ $c }}</option>
                            @endforeach
                        </select>
                        <div class="text-muted small">{{ __('documents.create.confidentiality_hint') }}</div>
                    </div>

                    <div class="col-12 col-xl-4">
                        <label class="form-label">{{ __('documents.create.department_owner') }}</label>
                        <select class="form-select" name="department_owner_id">
                            <option value="">-</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}" @selected(old('department_owner_id')==$d->id)>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('documents.create.plant_site_multi') }}</label>
                        <select class="form-select select-multiple" name="location_ids[]" multiple size="8">
                            @foreach($locations as $l)
                                <option value="{{ $l->id }}" @selected(collect(old('location_ids',[]))->contains($l->id))>{{ $l->plant_site }}{{ $l->name ? (' - ' . $l->name) : '' }}</option>
                            @endforeach
                        </select>
                        <div class="text-muted small">{{ __('documents.create.plant_site_tips') }}</div>
                        <div class="mt-2">
                            <label class="form-label mb-1">{{ __('documents.create.add_new_plant_site_optional') }}</label>
                            <input type="text" class="form-control" name="new_locations" value="{{ old('new_locations') }}" placeholder="{{ __('documents.create.new_plant_site_placeholder') }}">
                            <div class="text-muted small">{{ __('documents.create.new_plant_site_hint') }}</div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('documents.create.asset_service_multi') }}</label>
                        <select class="form-select select-multiple" name="asset_ids[]" multiple size="8">
                            @foreach($assets as $a)
                                <option value="{{ $a->id }}" @selected(collect(old('asset_ids',[]))->contains($a->id))>{{ $a->asset_code }} - {{ $a->asset_name }}</option>
                            @endforeach
                        </select>
                        <div class="text-muted small">{{ __('documents.create.asset_service_hint') }}</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('documents.create.tags') }}</label>
                        <input type="text" class="form-control" name="tags" value="{{ old('tags') }}" placeholder="{{ __('documents.create.tags_placeholder') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('documents.create.note') }}</label>
                        <textarea class="form-control" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>

                    <div class="col-12">
                        <hr>
                        <h6>{{ __('documents.create.contract_terms') }}</h6>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label">{{ __('documents.create.start_date') }}</label>
                        <input type="date" class="form-control" name="start_date" value="{{ old('start_date') }}">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">{{ __('documents.create.end_date') }}</label>
                        <input type="date" class="form-control" name="end_date" value="{{ old('end_date') }}">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">{{ __('documents.create.renewal_type') }}</label>
                        <select class="form-select" name="renewal_type">
                            <option value="">-</option>
                            @foreach(['Auto','Manual','One-time'] as $rt)
                                <option value="{{ $rt }}" @selected(old('renewal_type')===$rt)>{{ $rt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">{{ __('documents.create.notice_period_days') }}</label>
                        <input type="number" class="form-control" name="notice_period_days" value="{{ old('notice_period_days') }}" min="0" max="3650">
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label">{{ __('documents.create.billing_cycle') }}</label>
                        <select class="form-select" name="billing_cycle">
                            <option value="">-</option>
                            @foreach(['Monthly','Quarterly','Yearly','One-time'] as $bc)
                                <option value="{{ $bc }}" @selected(old('billing_cycle')===$bc)>{{ $bc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">{{ __('documents.create.contract_value') }}</label>
                        <input type="number" step="0.01" class="form-control" name="contract_value" value="{{ old('contract_value') }}">
                    </div>
                    <div class="col-12 col-md-2">
                        <label class="form-label">{{ __('documents.create.currency') }}</label>
                        <input type="text" class="form-control" name="currency" value="{{ old('currency','IDR') }}">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">{{ __('documents.create.payment_terms') }}</label>
                        <input type="text" class="form-control" name="payment_terms" value="{{ old('payment_terms') }}" placeholder="{{ __('documents.create.payment_terms_placeholder') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('documents.create.scope_service') }}</label>
                        <textarea class="form-control" name="scope_service" rows="2">{{ old('scope_service') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('documents.create.remarks') }}</label>
                        <textarea class="form-control" name="remarks" rows="2">{{ old('remarks') }}</textarea>
                    </div>

                    <div class="col-12">
                        <hr>
                        <h6>{{ __('documents.create.file_upload') }}</h6>
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('documents.create.file') }} <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="file" required>
                        <div class="text-muted small">{{ __('documents.create.file_hint') }}</div>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="create_contract_group" name="create_contract_group" value="1" checked>
                            <label class="form-check-label" for="create_contract_group">{{ __('documents.create.create_contract_group') }}</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">{{ __('documents.create.save_archive') }}</button>
                        <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
