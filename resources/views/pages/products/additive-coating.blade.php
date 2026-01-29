@extends('products')

@section('page_title', __('website.titles.product_additive_coating'))
@section('breadcrumb_title', __('website.nav.menu.products_items.additive_coating'))

@section('products_content')
  @include('pages.products.partials.detail', ['product' => $product])
@endsection