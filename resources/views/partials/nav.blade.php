<header class="with-background">
    <div class="top-nav container">
        <div class="logo"><a href="{{ route('landing.index') }}">Ecommerce</a></div>
        <ul>
            <li><a href="{{ route('shop.index') }}">Shop</a></li>
            <li><a href="#">About</a></li>
            <li><a href="https://niyazahamed.wordpress.com/" target="_blank">Blog</a></li>
            <li><a href="{{ route('cart.index') }}">Cart 
            @if(Cart::instance('default')->count() > 0)<span class="badge badge-warning">{{ Cart::instance('default')->count() }}</span>@endif
            </a></li>
        </ul>
    </div> <!-- end top-nav -->
</header>