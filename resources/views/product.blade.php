@extends('layout')

@section('title', $product->name)

@section('extra-css')
    <link rel="stylesheet" href="{{ asset('css/algolia.css') }}">
@endsection

@section('content')

    @component('components.breadcrumbs')    
            <a href="{{ route('landing.index') }}">Home</a>
            <i class="fa fa-chevron-right breadcrumb-separator"></i>
            <a href="{{ route('shop.index') }}">Shop</a>
            <i class="fa fa-chevron-right breadcrumb-separator"></i>
            <span>Macbook Pro</span>
    @endcomponent

    <div class="container">
        @if(session()->has('success_message'))
            <div class="alert alert-success">{{ session()->get('success_message') }}</div>
        @endif
    
        @if(count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <div class="product-section container">

        @if($product)
        <div>
            <div class="product-section-image">
                <img src="{{  productImage($product->image) }}" class="active" alt="product" id="currentImage">
            </div>
            <div class="product-section-images">
                @if($product->images)
                    @foreach(json_decode($product->images, true) as $image)
                        <div class="product-section-thumbnail selected">
                            <img src="{{  productImage($image) }}" alt="" >
                        </div>
                    @endforeach
                @endif
            </div>
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
@section('extra-js')
    <!-- Include AlgoliaSearch JS Client and autocomplete.js library -->
    <script src="https://cdn.jsdelivr.net/algoliasearch/3/algoliasearch.min.js"></script>
    <script src="https://cdn.jsdelivr.net/autocomplete.js/0/autocomplete.min.js"></script>
    <script src="{{ asset('js/algolia.js') }}"></script>
    <script>
        
        const currentImage = document.querySelector('#currentImage');
        const images = document.querySelectorAll('.product-section-thumbnail');
        
        images.forEach((element) => element.addEventListener('click', thumbnailClick));

        function thumbnailClick(e){
            currentImage.classList.remove('active');

            currentImage.addEventListener('transitionend', () => {
                currentImage.src = this.querySelector('img').src;
                currentImage.classList.add('active');
            });

            images.forEach((element)=>element.classList.remove('selected'));
            
            this.classList.add('selected');
        }
        
    </script>
@endsection
