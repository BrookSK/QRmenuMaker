<ul class="navbar-nav">
    @if(config('settings.makePureSaaS',false))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('home') }}">
                <i class="ni ni-tv-2 text-primary"></i> {{ __('Dashboard') }}
            </a>
        </li>
    @endif
    @if(config('app.ordering')&&!config('settings.makePureSaaS',false))
        <li class="nav-item">
            <a class="nav-link" href="/live">
                <i class="ni ni-basket text-success"></i> {{ __('Live Orders') }}<div class="blob red"></div>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('orders.index') }}">
                <i class="ni ni-basket text-orangse"></i> {{ __('Orders') }}
            </a>
        </li>
    @endif
    @foreach (auth()->user()->getExtraMenus() as $menu)
    @if ($menu['route']!="staff.index")
        <li class="nav-item">
            <a class="nav-link" href="{{ route($menu['route'],isset($menu['params'])?$menu['params']:[]) }}">
                <i class="{{ $menu['icon'] }}"></i> {{ __($menu['name']) }}
            </a>
        </li>
    @endif
            
    @endforeach

</ul>
