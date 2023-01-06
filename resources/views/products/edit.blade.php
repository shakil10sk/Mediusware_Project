@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Product</h1>
    </div>
    <div id="app">
        <edit-product :variants="{{$variants}}" :product-info="{{$product_info}}" :product-variant="{{$productVariant}}" :exist-variant="{{json_encode($existVariant)}}">Loading</edit-product>
    </div>
@endsection
