<ul class="navbar-nav">
    <li class="nav-item">
            <a class="nav-link" href="{{ route('home') }}">
                <i class="ni ni-tv-2 text-primary"></i> {{ __('Dashboard') }}
            </a>
        </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('orders.index') }}">
            <i class="ni ni-basket text-orange"></i> {{ __('Orders') }}
        </a>
    </li>
</ul>
