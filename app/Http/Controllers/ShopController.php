<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pagination = 9;
        $categories = Category::all();

        if(request()->category){
            $products = Product::with('categories')->whereHas('categories', function($query){
                $query->where('slug', request()->category);
            });
            $categoryName = $categories->where('slug', request()->category)->first()->name;
        } else {
            $products = Product::where('featured', true);
            $categoryName = 'Featured';
        }

        if (request()->sort == 'low_high'){
            $products = $products->orderBy('price')->paginate($pagination);
        } elseif (request()->sort == 'high_low') {
            $products = $products->orderBy('price', 'desc')->paginate($pagination);
        } else {
            $products = $products->paginate($pagination);
        }

        return view('shop')->with([
            'products' => $products,
            'categories' => $categories,
            'categoryName' => $categoryName
        ]);
    }

    
    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->first();
        $might_also_like_products = Product::where('slug', '!=', $slug)->MightAlsoLike();
        return view('product')->with([
                'product' => $product,
                'mightLikeProducts' => $might_also_like_products
            ]);
    }

    public function search(Request $request) {

        $request->validate([
            'query' => 'required|min:3'
        ]); 

        $query = $request->input('query');

        // tried in manual way

        // $products = Product::where('name', 'like', "%$query%")
        //                     ->orWhere('details', 'like', "%$query%")
        //                     ->orWhere('description', 'like', "%$query%")
        //                     ->paginate(10);

        // using Nicolaslopezj package for searchable full text
        $products = Product::search($query)->paginate(10);

        return view('search-results')->with('products', $products);
    }
}
