@extends('products')

@section('page_title', 'Ilsam - PU Resin')
@section('breadcrumb_title', 'PU Resin')

@section('products_content')
  @include('pages.products.partials.detail', ['product' => $product])
@endsection