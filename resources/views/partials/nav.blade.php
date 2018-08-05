<header class="with-background">
    <div class="top-nav container">
        <div class="top-nav-left">
                <div class="logo"><a href="{{ route('landing.index') }}">Lara-Ecommerce</a></div>
                {{ menu('Main-Top', 'partials.menu.main') }}
        </div>
        <div class="top-nav-right">
            @include('partials.menu.main-right')
        </div>
    </div> <!-- end top-nav -->
</header>