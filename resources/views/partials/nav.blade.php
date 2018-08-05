<header class="with-background">
    <div class="top-nav container">
        <div class="top-nav-left">
                <div class="logo"><a href="{{ route('landing.index') }}">Lara-Ecommerce</a></div>
                @if(! (request()->is('checkout') || request()->is('guestCheckout')))
                    {{ menu('Main-Top', 'partials.menu.main') }}
                @endif
        </div>
        <div class="top-nav-right">
            @if(! (request()->is('checkout') || request()->is('guestCheckout')))
                @include('partials.menu.main-right')
            @endif
        </div>
    </div> <!-- end top-nav -->
</header>