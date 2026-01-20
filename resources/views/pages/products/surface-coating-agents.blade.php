@extends('products')

@section('page_title', 'Ilsam - Surface Coating Agents')
@section('breadcrumb_title', 'Surface Coating Agents')

@section('products_content')
  @include('pages.products.partials.detail', ['product' => $product])
@endsection