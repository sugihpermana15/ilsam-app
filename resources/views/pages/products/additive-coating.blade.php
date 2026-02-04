@extends('products')

@section('page_title', __('website.titles.product_additive_coating'))
@section('breadcrumb_title', __('website.nav.menu.products_items.additive_coating'))

@php
  $metaDescription = data_get($product ?? [], 'seoDescription')
    ?: data_get($product ?? [], 'tagline')
    ?: 'Additive Coating by PT ILSAM GLOBAL INDONESIA: supplementary agents to promote quality and curing for PU and PVC (SC, SS, SI).';
  $metaImage = data_get($product ?? [], 'seoImage')
    ?: data_get($product ?? [], 'heroImage')
    ?: asset('assets/img/logo.png');
@endphp
@section('meta_description', $metaDescription)
@section('meta_image', str_starts_with($metaImage, 'http') ? $metaImage : asset($metaImage))
@section('products_content')
  @include('pages.products.partials.detail', ['product' => $product])
@endsection