<ul class="navbar-nav">
    @if (!config('app.isloyalty',false))
    <li class="nav-item">
        <a class="nav-link" href="{{ route('orders.index') }}">
            <i class="ni ni-basket text-orange"></i> {{ __('My Orders') }}
        </a>
    </li>
    @endif
    @if (config('app.isft'))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('addresses.index') }}">
                <i class="ni ni-map-big text-green"></i> {{ __('My Addresses') }}
            </a>
        </li> 
    @endif
    <!-- Exrta menus -->
    @foreach (auth()->user()->getExtraMenus() as $menu)
            <li class="nav-item">
                <a class="nav-link" href="{{ route($menu['route'],isset($menu['params'])?$menu['params']:[]) }}">
                    <i class="{{ $menu['icon'] }}"></i> {{ __($menu['name']) }}
                </a>
        </li>
    @endforeach
    
</ul>
