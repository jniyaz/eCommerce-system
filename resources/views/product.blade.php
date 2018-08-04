@extends('layout')

@section('title', $product->name)

@section('extra-css')

@endsection

@section('content')

    <div class="breadcrumbs">
        <div class="container">
            <a href="{{ route('landing.index') }}">Home</a>
            <i class="fa fa-chevron-right breadcrumb-separator"></i>
            <a href="{{ route('shop.index') }}">Shop</a>
            <i class="fa fa-chevron-right breadcrumb-separator"></i>
            <span>Macbook Pro</span>
        </div>
    </div> <!-- end breadcrumbs -->

    <div class="product-section container">
        @if($product)
        <div class="product-section-image">
            <img src="{{  productImage($product->image) }}" class="active" alt="product">
        </div>
        <div class="product-section-information">
            <h1 class="product-section-title">{{ $product->name }}</h1>
            
            <div class="product-section-subtitle">{{ $product->details }}</div>

            <h2>{{ $product->presentPrice() }}</h2>

            <p>
                {!! $product->description !!}
            </p>

            <p>&nbsp;</p>

            <form action="{{ route('cart.store') }}" method="POST">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ $product->id }}">
                <input type="hidden" name="name" value="{{ $product->name }}">
                <input type="hidden" name="price" value="{{ $product->price }}">
                <button class="button button-plain">Add To Cart</button>
            </form>

        </div>
        @else
        <p class="text-center">Not available.</p>
        @endif
    </div> <!-- end product-section -->

    @include('partials.might-like')


@endsection