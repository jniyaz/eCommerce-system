<?php

namespace App\Http\Controllers;

use Cartalyst\Stripe\Stripe;
use Illuminate\Http\Request;
use App\Http\Requests\CheckoutRequest;
use Gloudemans\Shoppingcart\Facades\Cart;
use Cartalyst\Stripe\Exception\CardErrorException;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
        return view('checkout')->with([
            'discount' => $this->getNumbers()->get('discount'),
            'newTax' => $this->getNumbers()->get('newTax'),
            'newSubtotal' => $this->getNumbers()->get('newSubtotal'),
            'newTotal' => $this->getNumbers()->get('newTotal')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CheckoutRequest $request)
    {
        $contents = Cart::content()->map(function ($item){
                        return $item->model->slug .', '.$item->qty;
                    })->values()->toJson();

        try {

            $stripe = Stripe::make(env('STRIPE_SECRET'));

            $charge = $stripe->charges()->create([
                'amount' => $this->getNumbers()->get('newTotal') / 100,
                'currency' => 'CAD',
                'source' => $request->stripeToken,
                'description' => 'Order',
                'receipt_email' => $request->email,
                'metadata' => [
                    // change to Order ID after using DB
                    'Contents' => $contents,
                    'Quantity' => Cart::instance('default')->count(),
                    'Discount' => collect(session()->get('coupon'))->toJson()
                ]
            ]);

             // Successful
            Cart::instance('default')->destroy();

            // redirect to thank you page
             return redirect()->route('confirmation.index')->with('success_message', 'Your payment has been accepted. Confirmation email has been sent.');

        } catch (CardErrorException $e) {
            return back()->withErrors('Error! ' . $e->getMessage());
        }

    }

    private function getNumbers(){
        $tax = config('cart.tax') / 100;
        $discount = session()->get('coupon')['discount'] ?? 0;
        $newSubtotal = (Cart::subtotal() - $discount);
        $newTax = $newSubtotal * $tax;
        $newTotal = $newSubtotal * (1 + $tax);
        
        return collect([
            'tax' => $tax,
            'discount' => $discount,
            'newSubtotal' => $newSubtotal,
            'newTax' => $newTax,
            'newTotal' => $newTotal
        ]);
    }
}
