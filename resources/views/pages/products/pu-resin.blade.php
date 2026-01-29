@extends('products')

@section('page_title', __('website.titles.product_pu_resin'))
@section('breadcrumb_title', __('website.nav.menu.products_items.pu_resin'))

@section('products_content')
  @include('pages.products.partials.detail', ['product' => $product])
@endsection