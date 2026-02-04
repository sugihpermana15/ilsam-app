@extends('products')

@section('page_title', __('website.titles.product_pu_resin'))
@section('breadcrumb_title', __('website.nav.menu.products_items.pu_resin'))

@php
  $metaDescription = data_get($product ?? [], 'seoDescription')
    ?: data_get($product ?? [], 'tagline')
    ?: 'PU Resin by PT ILSAM GLOBAL INDONESIA: skin and adhesive for leather and synthetic leather PU (ISU, ISA, ISW, IWD, IWA, IWS, IEU, IEA, IEW) and polyester for production resin PU (EB, B, DEB).';
  $metaImage = data_get($product ?? [], 'seoImage')
    ?: data_get($product ?? [], 'heroImage')
    ?: asset('assets/img/logo.png');
@endphp
@section('meta_description', $metaDescription)
@section('meta_image', str_starts_with($metaImage, 'http') ? $metaImage : asset($metaImage))
@section('products_content')
  @include('pages.products.partials.detail', ['product' => $product])
@endsection