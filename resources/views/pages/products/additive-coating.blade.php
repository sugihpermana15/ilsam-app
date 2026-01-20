@extends('products')

@section('page_title', 'Ilsam - Additive Coating')
@section('breadcrumb_title', 'Additive Coating')

@section('products_content')
  @include('pages.products.partials.detail', ['product' => $product])
@endsection