@extends('products')

@section('page_title', 'Ilsam - Chemical Colorants')
@section('breadcrumb_title', 'Chemical Colorants')

@section('products_content')
  @include('pages.products.partials.detail', ['product' => $product])
@endsection