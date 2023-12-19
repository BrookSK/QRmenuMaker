<ul class="navbar-nav">
    @if(config('app.ordering'))
       @if(in_array("poscloud", config('global.modules',[])))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="ni ni-tv-2 text-primary"></i> {{ __('POS') }}
                </a>
            </li>
        @endif
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
            <li class="nav-item">
                <a class="nav-link" href="{{ route($menu['route'],isset($menu['params'])?$menu['params']:[]) }}">
                    <i class="{{ $menu['icon'] }}"></i> {{ __($menu['name']) }}
                </a>
        </li>
    @endforeach

</ul>
