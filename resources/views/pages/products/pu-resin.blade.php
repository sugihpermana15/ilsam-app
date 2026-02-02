@extends('products')

@section('page_title', __('website.titles.product_pu_resin'))
@section('breadcrumb_title', __('website.nav.menu.products_items.pu_resin'))

@section('meta_description', 'PU Resin by PT ILSAM GLOBAL INDONESIA: skin and adhesive for leather and synthetic leather PU (ISU, ISA, ISW, IWD, IWA, IWS, IEU, IEA, IEW) and polyester for production resin PU (EB, B, DEB).')
@section('meta_image', data_get($product ?? [], 'heroImage', asset('assets/img/logo.png')))
@section('products_content')
  @include('pages.products.partials.detail', ['product' => $product])
@endsection