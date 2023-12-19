<ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('orders.index') }}">
            <i class="ni ni-basket text-orange"></i> {{ __('My Orders') }}
        </a>
    </li>
    @if (config('app.isft'))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('addresses.index') }}">
                <i class="ni ni-map-big text-green"></i> {{ __('My Addresses') }}
            </a>
        </li> 
    @endif
    
</ul>
