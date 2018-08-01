<ul>
    @foreach($items as $menu_item)
        <li>
            <a href="{{ $menu_item->link() }}">{{ $menu_item->title }}
                @if($menu_item->title == 'Cart')
                    @if(Cart::instance('default')->count() > 0)
                        <span class="badge badge-warning">{{ Cart::instance('default')->count() }}</span>
                    @endif
                @endif    
        </a></li>
    @endforeach
</ul>

{{-- <ul>
    <li><a href="{{ route('shop.index') }}">Shop</a></li>
    <li><a href="#">About</a></li>
    <li><a href="https://niyazahamed.wordpress.com/" target="_blank">Blog</a></li>
    <li><a href="{{ route('cart.index') }}">Cart 
    @if(Cart::instance('default')->count() > 0)<span class="badge badge-warning">{{ Cart::instance('default')->count() }}</span>@endif
    </a></li>
</ul> --}}