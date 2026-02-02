@extends('products')

@section('page_title', __('website.titles.product_additive_coating'))
@section('breadcrumb_title', __('website.nav.menu.products_items.additive_coating'))

@section('meta_description', 'Additive Coating by PT ILSAM GLOBAL INDONESIA: supplementary agents to promote quality and curing for PU and PVC (SC, SS, SI).')
@section('meta_image', data_get($product ?? [], 'heroImage', asset('assets/img/logo.png')))
@section('products_content')
  @include('pages.products.partials.detail', ['product' => $product])
@endsection