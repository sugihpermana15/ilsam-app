@extends('products')

@section('page_title', __('website.titles.product_colorants'))
@section('breadcrumb_title', __('website.nav.menu.products_items.chemical_colorants'))

@section('meta_description', 'Chemical Colorants by PT ILSAM GLOBAL INDONESIA: colorants for PU synthetic leather (SW, SU, SF), PVC synthetic leather (SV, SFV), printing (SP, SG), and water-based systems (SUW).')
@section('meta_image', data_get($product ?? [], 'heroImage', asset('assets/img/logo.png')))
@section('products_content')
  @include('pages.products.partials.detail', ['product' => $product])
@endsection