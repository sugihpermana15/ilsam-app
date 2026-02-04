@extends('products')

@section('page_title', __('website.titles.product_surface_coating_agents'))
@section('breadcrumb_title', __('website.nav.menu.products_items.surface_coating_agents'))

@php
  $metaDescription = data_get($product ?? [], 'seoDescription')
    ?: data_get($product ?? [], 'tagline')
    ?: 'Surface Coating Agents by PT ILSAM GLOBAL INDONESIA: solution-type surface coating agent for leather and synthetic leather PU and PVC (SUS).';
  $metaImage = data_get($product ?? [], 'seoImage')
    ?: data_get($product ?? [], 'heroImage')
    ?: asset('assets/img/logo.png');
@endphp
@section('meta_description', $metaDescription)
@section('meta_image', str_starts_with($metaImage, 'http') ? $metaImage : asset($metaImage))
@section('products_content')
  @include('pages.products.partials.detail', ['product' => $product])
@endsection