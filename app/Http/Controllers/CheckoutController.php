<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderProduct;
use App\Mail\OrderPlaced;
use Cartalyst\Stripe\Stripe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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

        if(Cart::instance('default')->count() == 0) {
            return redirect()->route('shop.index');
        }

        if(auth()->user() && request()->is('guestCheckout')){
            return redirect()->route('checkout.index');
        }

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

            $order = $this->addToOrdersTables($request, null);
            
            // send email 
            Mail::send(new OrderPlaced($order));
            
            // Successful
            Cart::instance('default')->destroy();

            // redirect to thank you page
            return redirect()->route('confirmation.index')
                             ->with('success_message', 'Your payment has been accepted. Confirmation email has been sent.');

        } catch (CardErrorException $e) {
            $this->addToOrdersTables($request, $e->getMessage());
            return back()->withErrors('Error! ' . $e->getMessage());
        }

    }

    protected function addToOrdersTables($request, $error) {
        
        // Insert into Orders table

        $order = Order::create([
            'user_id' => auth()->user() ? auth()->user()->id : null,
            'billing_email' => $request->email,
            'billing_name' => $request->name,
            'billing_address' => $request->address,
            'billing_city' => $request->city,
            'billing_province' => $request->province,
            'billing_postalcode' => $request->postalcode,
            'billing_phone' => $request->phone,
            'billing_name_on_card' => $request->name_on_card,
            'billing_discount' => $this->getNumbers()->get('discount'),
            'billing_discount_code' => $this->getNumbers()->get('code'),
            'billing_subtotal' => $this->getNumbers()->get('newSubtotal'),
            'billing_tax' => $this->getNumbers()->get('newTax'),
            'billing_total' => $this->getNumbers()->get('newTotal'),
            'error' => $error,
        ]);


        // Insert into Order_Product Table

        foreach(Cart::content() as $item) {
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $item->model->id,
                'quantity' => $item->qty
            ]);
        }
        
        return $order;
    }

    private function getNumbers(){
        $tax = config('cart.tax') / 100;
        $discount = session()->get('coupon')['discount'] ?? 0;
        $code = session()->get('coupon')['name'] ?? null;
        $newSubtotal = (Cart::subtotal() - $discount);
        $newTax = $newSubtotal * $tax;
        $newTotal = $newSubtotal * (1 + $tax);
        
        return collect([
            'tax' => $tax,
            'discount' => $discount,
            'code' => $code,
            'newSubtotal' => $newSubtotal,
            'newTax' => $newTax,
            'newTotal' => $newTotal
        ]);
    }
}
