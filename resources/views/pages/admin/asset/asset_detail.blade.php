@extends('layouts.master')

@section('title', __('assets.details') . ' | IGI')
@section('title-sub')
    <a href="{{ route('admin.assets.index') }}" class="text-decoration-none text-body">{{ __('assets.management') }}</a>
@endsection
@section('pagetitle', __('assets.details'))

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}">
    <style>
        .kv dt {
            color: #6c757d;
            font-weight: 600;
        }

        .kv dd {
            margin-bottom: .75rem;
        }

        .asset-img {
            max-height: 360px;
            object-fit: contain;
            background: #f8f9fa;
            cursor: pointer;
        }
    </style>
@endsection

@section('content')

    <div class="mx-auto pb-4" style="max-width: 1320px;">

    @php
        $placeholder = 'assets/img/logos/lang-logo/dropbox.png';
        $imgArr = array_values(array_filter([
            $asset->image_1,
            $asset->image_2,
            $asset->image_3,
        ]));
        if (empty($imgArr)) {
            $imgArr = [$placeholder];
        }

        $purchaseDate = $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('d-m-Y') : '-';
        $startUseDate = $asset->start_use_date ? \Carbon\Carbon::parse($asset->start_use_date)->format('d-m-Y') : '-';
        $warrantyEndDate = $asset->warranty_end_date ? \Carbon\Carbon::parse($asset->warranty_end_date)->format('d-m-Y') : '-';
        $inputDate = $asset->input_date ? \Carbon\Carbon::parse($asset->input_date)->format('d-m-Y H:i') : '-';
        $lastUpdated = $asset->last_updated ? \Carbon\Carbon::parse($asset->last_updated)->format('d-m-Y H:i') : '-';

        $price = $asset->price !== null ? 'Rp. ' . number_format($asset->price, 0, ',', '.') : '-';
        $qtyText = ($asset->qty !== null && $asset->qty !== '') ? $asset->qty : '-';
        $qtyUom = $qtyText !== '-' ? ($qtyText . ' ' . ($asset->satuan ?: '')) : '-';
        $qtyUom = trim($qtyUom) ?: '-';

        $statusRaw = (string) ($asset->asset_status ?? '');
        $statusBadge = match ($statusRaw) {
            'Active' => 'bg-success-subtle text-success',
            'Inactive' => 'bg-secondary-subtle text-secondary',
            'Sold' => 'bg-warning-subtle text-warning',
            'Disposed' => 'bg-danger-subtle text-danger',
            default => 'bg-light-subtle text-body',
        };

        $statusKey = strtolower($statusRaw);
        $statusLabel = $statusRaw === ''
            ? '-'
            : (\Illuminate\Support\Facades\Lang::has("assets.options.asset_status.$statusKey")
                ? __("assets.options.asset_status.$statusKey")
                : $statusRaw);

        $conditionRaw = (string) ($asset->asset_condition ?? '');
        $conditionBadge = match ($conditionRaw) {
            'Good' => 'bg-success-subtle text-success',
            'Minor Damage' => 'bg-warning-subtle text-warning',
            'Major Damage' => 'bg-danger-subtle text-danger',
            default => 'bg-light-subtle text-body',
        };

        $conditionKey = strtolower(str_replace(' ', '_', $conditionRaw));
        $conditionLabel = $conditionRaw === ''
            ? '-'
            : (\Illuminate\Support\Facades\Lang::has("assets.options.condition.$conditionKey")
                ? __("assets.options.condition.$conditionKey")
                : $conditionRaw);

        $warrantyRaw = (string) ($asset->warranty_status ?? '');
        $warrantyKey = strtolower($warrantyRaw);
        $warrantyLabel = $warrantyRaw === ''
            ? '-'
            : (\Illuminate\Support\Facades\Lang::has("assets.options.warranty.$warrantyKey")
                ? __("assets.options.warranty.$warrantyKey")
                : $warrantyRaw);

        $ownershipRaw = (string) ($asset->ownership_status ?? '');
        $ownershipKey = strtolower($ownershipRaw);
        $ownershipLabel = $ownershipRaw === ''
            ? '-'
            : (\Illuminate\Support\Facades\Lang::has("assets.options.ownership.$ownershipKey")
                ? __("assets.options.ownership.$ownershipKey")
                : $ownershipRaw);

        $canUpdate = \App\Support\MenuAccess::can(auth()->user(), 'assets_data', 'update');
    @endphp

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h4 class="mb-0">{{ $asset->asset_code }}</h4>
                        <span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span>
                        <span class="badge {{ $conditionBadge }}">{{ $conditionLabel }}</span>
                    </div>
                    <div class="text-muted">{{ $asset->asset_name }}</div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.assets.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ri-arrow-left-line"></i> {{ __('assets.actions.back') }}
                    </a>
                    <a href="{{ $canUpdate ? route('admin.assets.edit', $asset->id) : '#' }}" class="btn btn-warning btn-sm {{ $canUpdate ? '' : 'disabled' }}" aria-disabled="{{ $canUpdate ? 'false' : 'true' }}" title="{{ $canUpdate ? __('assets.actions.edit') : __('assets.actions.no_access_update') }}">
                        <i class="ri-edit-2-line"></i> {{ __('assets.actions.edit') }}
                    </a>
                    <a href="{{ route('admin.assets.printBarcode', $asset->id) }}" target="_blank" class="btn btn-primary btn-sm">
                        <i class="ri-barcode-line"></i> {{ __('assets.actions.print_barcode') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">{{ __('assets.photo.title') }}</h6>
                    <span class="text-muted small">{{ __('assets.photo.count', ['count' => count($imgArr)]) }}</span>
                </div>
                <div class="card-body">
                    <div class="swiper assetGallery">
                        <div class="swiper-wrapper">
                            @foreach($imgArr as $img)
                                @php($imgPath = $img ?: $placeholder)
                                <div class="swiper-slide">
                                    <img class="img-fluid w-100 rounded preview-img asset-img"
                                        src="{{ asset($imgPath) }}" data-img="{{ asset($imgPath) }}" alt="{{ __('assets.photo.image_alt') }}">
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                    <div class="mt-2 small text-muted">{{ __('assets.photo.hint') }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-7">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('assets.summary.title') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">{{ __('assets.summary.category') }}</div>
                            <div class="fw-semibold">{{ $categoryName ?? ($asset->asset_category ?: '-') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">{{ __('assets.summary.location') }}</div>
                            <div class="fw-semibold">{{ $asset->asset_location ?: '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">{{ __('assets.summary.qty') }}</div>
                            <div class="fw-semibold">{{ $qtyUom }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">{{ __('assets.summary.price') }}</div>
                            <div class="fw-semibold">{{ $price }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">{{ __('assets.summary.vendor') }}</div>
                            <div class="fw-semibold">{{ $asset->vendor_supplier ?: '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">{{ __('assets.summary.invoice') }}</div>
                            <div class="fw-semibold">{{ $asset->invoice_number ?: '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">{{ __('assets.summary.department') }}</div>
                            <div class="fw-semibold">{{ $asset->department ?: '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">{{ __('assets.summary.pic') }}</div>
                            <div class="fw-semibold">{{ $asset->person_in_charge ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('assets.detail.title') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <dl class="row mb-0 kv">
                                <dt class="col-5">{{ __('assets.detail.asset_code') }}</dt>
                                <dd class="col-7">{{ $asset->asset_code }}</dd>

                                <dt class="col-5">{{ __('assets.detail.asset_name') }}</dt>
                                <dd class="col-7">{{ $asset->asset_name }}</dd>

                                <dt class="col-5">{{ __('assets.detail.brand_type_model') }}</dt>
                                <dd class="col-7">{{ $asset->brand_type_model ?: '-' }}</dd>

                                <dt class="col-5">{{ __('assets.detail.category') }}</dt>
                                <dd class="col-7">{{ $categoryName ?? ($asset->asset_category ?: '-') }}</dd>

                                <dt class="col-5">{{ __('assets.detail.serial_number') }}</dt>
                                <dd class="col-7">{{ $asset->serial_number ?: '-' }}</dd>

                                <dt class="col-5">{{ __('assets.detail.ownership') }}</dt>
                                <dd class="col-7">{{ $ownershipLabel }}</dd>
                            </dl>
                        </div>
                        <div class="col-lg-6">
                            <dl class="row mb-0 kv">
                                <dt class="col-5">{{ __('assets.detail.purchase_date') }}</dt>
                                <dd class="col-7">{{ $purchaseDate }}</dd>

                                <dt class="col-5">{{ __('assets.detail.start_use_date') }}</dt>
                                <dd class="col-7">{{ $startUseDate }}</dd>

                                <dt class="col-5">{{ __('assets.detail.warranty') }}</dt>
                                <dd class="col-7">
                                    {{ $warrantyLabel }}
                                    @if($warrantyEndDate !== '-')
                                        <span class="text-muted">({{ __('assets.detail.until') }} {{ $warrantyEndDate }})</span>
                                    @endif
                                </dd>

                                <dt class="col-5">{{ __('assets.detail.status') }}</dt>
                                <dd class="col-7"><span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span></dd>

                                <dt class="col-5">{{ __('assets.detail.condition') }}</dt>
                                <dd class="col-7"><span class="badge {{ $conditionBadge }}">{{ $conditionLabel }}</span></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('assets.desc_notes.title') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="text-muted small">{{ __('assets.desc_notes.description') }}</div>
                                <div class="fw-semibold" style="white-space: pre-wrap;">{{ $asset->description ?: '-' }}</div>
                            </div>
                            <div>
                                <div class="text-muted small">{{ __('assets.desc_notes.notes') }}</div>
                                <div class="fw-semibold" style="white-space: pre-wrap;">{{ $asset->notes ?: '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('assets.audit.title') }}</h6>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0 kv">
                                <dt class="col-5">{{ __('assets.audit.input_by') }}</dt>
                                <dd class="col-7">{{ $asset->input_by ?: '-' }}</dd>

                                <dt class="col-5">{{ __('assets.audit.input_date') }}</dt>
                                <dd class="col-7">{{ $inputDate }}</dd>

                                <dt class="col-5">{{ __('assets.audit.last_updated') }}</dt>
                                <dd class="col-7">{{ $lastUpdated }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="imgPreviewModal" tabindex="-1" aria-labelledby="imgPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imgPreviewModalLabel">{{ __('assets.photo.preview_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('common.close') }}"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="imgPreviewModalImg" src="" alt="{{ __('assets.photo.preview_alt') }}" class="img-fluid rounded" style="max-height:70vh;">
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection

@section('js')
    <script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Swiper gallery
            if (typeof Swiper !== 'undefined') {
                new Swiper('.assetGallery', {
                    loop: true,
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                });
            }

            // Image click preview
            document.querySelectorAll('.preview-img').forEach(function (img) {
                img.addEventListener('click', function () {
                    var src = this.getAttribute('data-img');
                    document.getElementById('imgPreviewModalImg').src = src;
                    if (window.bootstrap && bootstrap.Modal) {
                        var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('imgPreviewModal'));
                        modal.show();
                    }
                });
            });
        });
    </script>

    <!-- App js -->
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection