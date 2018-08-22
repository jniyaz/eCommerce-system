@extends('layout')

@section('title', 'Checkout')

@section('extra-css')
<script src="https://js.stripe.com/v3/"></script>
@endsection

@section('content')
    <div class="breadcrumbs">
        <div class="container">
            <a href="{{ route('landing.index') }}">Home</a>
            <i class="fa fa-chevron-right breadcrumb-separator"></i>
            <a href="{{ route('checkout.index') }}">Checkout</a>
        </div>
    </div> <!-- end breadcrumbs -->
    <div class="container">
        @if(session()->has('success_message'))
            <div class="alert alert-success">{{ session()->get('success_message') }}</div>
        @endif

        @if(count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{!! $error !!}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="checkout-section">
            <div>
                <form action="{{ route('checkout.store') }}" id="payment-form" method="POST">

                    {{ csrf_field() }}

                    <h2>Billing Details</h2>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        @if(auth()->user())
                            <input type="email" class="form-control" readonly id="email" name="email" value="{{ auth()->user()->email }}">
                        @else
                            <input type="email" class="form-control" required id="email" name="email" value="{{ old('email') }}">
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" required>
                    </div>

                    <div class="half-form">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="province">Province</label>
                            <input type="text" class="form-control" id="province" name="province" value="{{ old('province') }}" required>
                        </div>
                    </div> <!-- end half-form -->

                    <div class="half-form">
                        <div class="form-group">
                            <label for="postalcode">Postal Code</label>
                            <input type="text" class="form-control" id="postalcode" name="postalcode" value="{{ old('postalcode') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                        </div>
                    </div> <!-- end half-form -->

                    <div class="spacer"></div>

                    <h2>Payment Details</h2>

                    <div class="form-group">
                        <label for="name">Name On Card</label>
                        <input type="text" class="form-control" id="name_on_card" name="name_on_card" value="{{ old('name_on_card') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="card-element">
                        Credit or debit card
                        </label>
                        <div id="card-element">
                        <!-- A Stripe Element will be inserted here. -->
                        </div>
                    
                        <!-- Used to display form errors. -->
                        <div id="card-errors" role="alert"></div>
                    </div>

                    <div class="spacer"></div>

                    <input type="submit" id="complete-order" class="button-primary full-width" value="Complete Order">


                </form>
            </div>



            <div class="checkout-table-container">
                <h2>Your Order</h2>

                <div class="checkout-table">
                    
                    @foreach(Cart::content() as $item)
                    <div class="checkout-table-row">
                        <div class="checkout-table-row-left">
                            <img src="{{  productImage($item->model->image) }}" alt="item" class="checkout-table-img">
                            <div class="checkout-item-details">
                                <div class="checkout-table-item">{{ $item->model->name }}</div>
                                <div class="checkout-table-description">{{ str_limit($item->model->details, 25) }}</div>
                                <div class="checkout-table-price">{{ $item->model->presentPrice() }}</div>
                            </div>
                        </div> <!-- end checkout-table -->

                        <div class="checkout-table-row-right">
                            <div class="checkout-table-quantity">{{ $item->qty }}</div>
                        </div>
                    </div> <!-- end checkout-table-row -->
                    @endforeach

                </div> <!-- end checkout-table -->

                <div class="checkout-totals">
                    <div class="checkout-totals-left">
                        Subtotal <br>
                        @if(session()->has('coupon'))
                        Discount {{ session()->get('coupon')['name'] }} 
                        <form action="{{ route('coupon.destroy') }}" method="POST" style="display:inline;">
                            {{ csrf_field() }}
                            {{ method_field('delete') }}
                            <button type="submit">Remove</button>
                        </form>
                        <br>
                        <hr>
                        New Subtotal <br>
                        @endif
                        Tax(6%) <br>
                        <span class="checkout-totals-total">Total</span>

                    </div>

                    <div class="checkout-totals-right">
                        {{ presentPrice(Cart::subtotal()) }} <br>
                        @if(session()->has('coupon'))
                        -{{ presentPrice($discount) }} <hr>
                        {{ presentPrice($newSubtotal) }} <br> 
                        @endif
                        {{ presentPrice($newTax) }} <br>
                        <span class="checkout-totals-total">{{ presentPrice($newTotal) }}</span>

                    </div>
                </div> <!-- end checkout-totals -->

                <div class="have-code">Have a Code?</div>

                <div class="have-code-container">
                    <form action="{{ route('coupon.store') }}" method="POST">
                        {{ csrf_field() }}
                        <input type="text" name="coupon_code" id="coupon_code" value="">
                        <button type="submit" class="button button-plain" >Apply</button>
                    </form>
                </div>
            </div>

        </div> <!-- end checkout-section -->
    </div>

@endsection

@section('extra-js')
<script>
    // Create a Stripe client.
    var stripe = Stripe('pk_test_xlBC9Ssln6Oo4bhQro7Vk5X1');
    // Create an instance of Elements.
    var elements = stripe.elements();
    // Custom styling can be passed to options when creating an Element.
    // (Note that this demo uses a wider set of styles than the guide below.)
    var style = {
    base: {
        color: '#32325d',
        lineHeight: '18px',
        fontFamily: '"Roboto, Helvetica Neue", Helvetica, sans-serif',
        fontSmoothing: 'antialiased',
        fontSize: '16px',
        '::placeholder': {
        color: '#aab7c4'
        }
    },
    invalid: {
        color: '#fa755a',
        iconColor: '#fa755a'
    }
    };
    // Create an instance of the card Element.
    var card = elements.create('card', {
        style: style,
        hidePostalCode: true    
    });
    // Add an instance of the card Element into the `card-element` <div>.
    card.mount('#card-element');
    // Handle real-time validation errors from the card Element.
    card.addEventListener('change', function(event) {
    var displayError = document.getElementById('card-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }
    });
    // Handle form submission.
    var form = document.getElementById('payment-form');
    form.addEventListener('submit', function(event) {
    event.preventDefault();
    // disable submit button 
    document.getElementById('complete-order').disabled = true;
    var options = {
        name: document.getElementById('name_on_card').value,
        address_line1: document.getElementById('address').value,
        address_city: document.getElementById('city').value,
        address_state: document.getElementById('province').value,
        address_zip: document.getElementById('postalcode').value,
    }
    stripe.createToken(card, options).then(function(result) {
        if (result.error) {
        // Inform the user if there was an error.
        var errorElement = document.getElementById('card-errors');
        errorElement.textContent = result.error.message;
        // enable submit button 
        document.getElementById('complete-order').disabled = false;
        } else {
        // Send the token to your server.
        stripeTokenHandler(result.token);
        }
    });
    });
    function stripeTokenHandler(token) {
        // Insert the token ID into the form so it gets submitted to the server
        var form = document.getElementById('payment-form');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'stripeToken');
        hiddenInput.setAttribute('value', token.id);
        form.appendChild(hiddenInput);
        // Submit the form
        form.submit();
    }
</script>
@endsection 