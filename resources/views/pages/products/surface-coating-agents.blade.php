@extends('products')

@section('page_title', __('website.titles.product_surface_coating_agents'))
@section('breadcrumb_title', __('website.nav.menu.products_items.surface_coating_agents'))

@section('meta_description', 'Surface Coating Agents by PT ILSAM GLOBAL INDONESIA: solution-type surface coating agent for leather and synthetic leather PU and PVC (SUS).')
@section('meta_image', data_get($product ?? [], 'heroImage', asset('assets/img/logo.png')))
@section('products_content')
  @include('pages.products.partials.detail', ['product' => $product])
@endsection