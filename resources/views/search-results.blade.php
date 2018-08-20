@extends('layout')

@section('title', 'Search Results')

@section('extra-css')
@endsection

@section('content')

@component('components.breadcrumbs')
    <a href="{{ route('landing.index') }}">Home</a>
    <i class="fa fa-chevron-right breadcrumb-separator"></i>
    <span>Search Results</span>
@endcomponent

<div class="search-container container">

    <div>
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

    <div>
        <h2>Search Results</h2>
        <p>{{ $products->total() }} results for '{{ request()->input('query') }}'</p>            
        @if($products->total() > 0)
        <table class="table table-bordered">
            <thead>
                <tr>
                <th>Product Name</th>
                <th>Details</th>
                <th>Description</th>
                <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td><a href="{{ route('shop.show', $product->slug) }}">{{ $product->name }}</a></td>
                        <td>{{ $product->details  }}</td>
                        <td>{!! str_limit($product->description, 75) !!}</td>
                        <td>{{ $product->presentPrice() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $products->appends(request()->input())->links() }}
        @else
        <p class="text-center">No Search Details found. Try again.</p>
        @endif
    

    </div>
   

</div>

@endsection

@section('extra-js')
@endsection