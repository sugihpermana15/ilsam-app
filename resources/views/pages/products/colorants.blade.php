@extends('products')

@section('page_title', __('website.titles.product_colorants'))
@section('breadcrumb_title', __('website.nav.menu.products_items.chemical_colorants'))

@php
  $metaDescription = data_get($product ?? [], 'seoDescription')
    ?: data_get($product ?? [], 'tagline')
    ?: 'Chemical Colorants by PT ILSAM GLOBAL INDONESIA: colorants for PU synthetic leather (SW, SU, SF), PVC synthetic leather (SV, SFV), printing (SP, SG), and water-based systems (SUW).';
  $metaImage = data_get($product ?? [], 'seoImage')
    ?: data_get($product ?? [], 'heroImage')
    ?: asset('assets/img/logo.png');
@endphp
@section('meta_description', $metaDescription)
@section('meta_image', str_starts_with($metaImage, 'http') ? $metaImage : asset($metaImage))
@section('products_content')
  @include('pages.products.partials.detail', ['product' => $product])
@endsection