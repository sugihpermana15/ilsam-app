@extends('products')

@section('page_title', __('website.titles.product_colorants'))
@section('breadcrumb_title', __('website.nav.menu.products_items.chemical_colorants'))

@section('products_content')
  @include('pages.products.partials.detail', ['product' => $product])
@endsection