@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Product</h1>
    </div>
    <div id="app">
        <create-product :variants="{{ $variants }}" v-bind:product-values="{{ json_encode($productData) }}">Loading</create-product>
    </div>
@endsection
