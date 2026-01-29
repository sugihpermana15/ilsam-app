@extends('products')

@section('page_title', __('website.titles.product_surface_coating_agents'))
@section('breadcrumb_title', __('website.nav.menu.products_items.surface_coating_agents'))

@section('products_content')
  @include('pages.products.partials.detail', ['product' => $product])
@endsection