<ul>
    @guest
    <li><a href="{{ route('register') }}">Register</a></li>
    <li><a href="{{ route('login') }}">Login</a></li>
    @else
    <li>
        <a class="dropdown-item" href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            {{ __('Logout') }}
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </li>
    @endguest
    <li>
        <a href="{{ route('cart.index') }}">Cart 
            @if(Cart::instance('default')->count() > 0)<span class="badge badge-warning">{{ Cart::instance('default')->count() }}</span>@endif
        </a>
    </li>
</ul>