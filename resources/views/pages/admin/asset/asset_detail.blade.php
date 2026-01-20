@extends('layouts.master')

@section('title', 'Asset Detail | IGI')
@section('title-sub')
    <a href="{{ route('admin.assets.index') }}" style="text-decoration:none; color:inherit; cursor:pointer;">
        Asset Management
    </a>
@endsection
@section('pagetitle', 'Asset Details')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}">
@endsection

@section('content')


    <!-- begin::App -->
    <div id="layout-wrapper">

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
            $uomText = $asset->satuan ?: '-';
            $qtyUom = $qtyText !== '-' ? ($qtyText . ' ' . ($asset->satuan ?: '')) : '-';
            $qtyUom = trim($qtyUom) ?: '-';

            $status = $asset->asset_status ?: '-';
            $statusBadge = match($status) {
                'Active' => 'bg-success-subtle text-success',
                'Inactive' => 'bg-secondary-subtle text-secondary',
                'Sold' => 'bg-warning-subtle text-warning',
                'Disposed' => 'bg-danger-subtle text-danger',
                default => 'bg-light-subtle text-body',
            };

            $condition = $asset->asset_condition ?: '-';
            $conditionBadge = match($condition) {
                'Good' => 'bg-success-subtle text-success',
                'Minor Damage' => 'bg-warning-subtle text-warning',
                'Major Damage' => 'bg-danger-subtle text-danger',
                default => 'bg-light-subtle text-body',
            };
        @endphp

        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h4 class="mb-0">{{ $asset->asset_code }}</h4>
                            <span class="badge {{ $statusBadge }}">{{ $status }}</span>
                            <span class="badge {{ $conditionBadge }}">{{ $condition }}</span>
                        </div>
                        <div class="text-muted">{{ $asset->asset_name }}</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.assets.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="ri-arrow-left-line"></i> Kembali
                        </a>
                        <a href="{{ route('admin.assets.edit', $asset->id) }}" class="btn btn-warning btn-sm">
                            <i class="ri-edit-2-line"></i> Edit
                        </a>
                        <a href="{{ route('admin.assets.printBarcode', $asset->id) }}" target="_blank" class="btn btn-primary btn-sm">
                            <i class="ri-barcode-line"></i> Print Barcode
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-5">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0">Foto Asset</h6>
                    </div>
                    <div class="card-body">
                        <div class="swiper assetGallery">
                            <div class="swiper-wrapper">
                                @foreach($imgArr as $img)
                                    @php
                                        $imgPath = $img ?: $placeholder;
                                    @endphp
                                    <div class="swiper-slide">
                                        <img class="img-fluid w-100 rounded preview-img"
                                            style="max-height:360px; object-fit:contain; background:#f8f9fa; cursor:pointer;"
                                            src="{{ asset($imgPath) }}" data-img="{{ asset($imgPath) }}" alt="Asset Image">
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                        <div class="mt-2 small text-muted">Klik gambar untuk preview.</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0">Ringkasan</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Kategori</div>
                                <div class="fw-semibold">{{ $asset->asset_category ?: '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Lokasi</div>
                                <div class="fw-semibold">{{ $asset->asset_location ?: '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Qty</div>
                                <div class="fw-semibold">{{ $qtyUom }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Harga</div>
                                <div class="fw-semibold">{{ $price }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Vendor</div>
                                <div class="fw-semibold">{{ $asset->vendor_supplier ?: '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Invoice</div>
                                <div class="fw-semibold">{{ $asset->invoice_number ?: '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Department</div>
                                <div class="fw-semibold">{{ $asset->department ?: '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">PIC</div>
                                <div class="fw-semibold">{{ $asset->person_in_charge ?: '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Detail</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="width: 220px;">Asset Code</th>
                                        <td>{{ $asset->asset_code }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Asset Name</th>
                                        <td>{{ $asset->asset_name }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Brand / Type / Model</th>
                                        <td>{{ $asset->brand_type_model ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Serial Number</th>
                                        <td>{{ $asset->serial_number ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Purchase Date</th>
                                        <td>{{ $purchaseDate }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Start Use Date</th>
                                        <td>{{ $startUseDate }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Ownership Status</th>
                                        <td>{{ $asset->ownership_status ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Warranty</th>
                                        <td>
                                            {{ $asset->warranty_status ?: '-' }}
                                            @if($warrantyEndDate !== '-')
                                                <span class="text-muted">(s/d {{ $warrantyEndDate }})</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Deskripsi</th>
                                        <td>{{ $asset->description ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Catatan</th>
                                        <td>{{ $asset->notes ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Input By</th>
                                        <td>{{ $asset->input_by ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Input Date</th>
                                        <td>{{ $inputDate }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Last Updated</th>
                                        <td>{{ $lastUpdated }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for image preview -->
        <div class="modal fade" id="imgPreviewModal" tabindex="-1" aria-labelledby="imgPreviewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imgPreviewModalLabel">Preview Foto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img id="imgPreviewModalImg" src="" alt="Preview" class="img-fluid rounded" style="max-height:70vh;">
                    </div>
                </div>
            </div>
        </div>
    </div><!--End container-fluid-->
    </main><!--End app-wrapper-->

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