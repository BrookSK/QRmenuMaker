<ul class="navbar-nav">
    @if(config('app.ordering'))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('home') }}">
                <i class="ni ni-tv-2 text-primary"></i> {{ __('Dashboard') }}
            </a>
        </li>
        @if(config('app.isft'))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('orders.index') }}">
                    <i class="ni ni-basket text-orange"></i> {{ __('Orders') }}
                </a>
            </li>
        @endif
    @endif

    @if (config('app.isft'))
        <li class="nav-item">
            <a class="nav-link" href="/live">
                <i class="ni ni-basket text-success"></i> {{ __('Live Orders') }}<div class="blob red"></div>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('drivers.index') }}">
                <i class="ni ni-delivery-fast text-pink"></i> {{ __('Drivers') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('clients.index') }}">
                <i class="ni ni-single-02 text-blue"></i> {{ __('Clients') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('reviews.index') }}">
                <i class="ni ni-diamond text-info"></i> {{ __('Reviews') }}
            </a>
        </li>
    @endif
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.restaurants.index') }}">
            <i class="ni ni-shop text-info"></i> {{ __('Restaurants') }}
        </a>
    </li>
    @if(config('settings.multi_city'))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('cities.index') }}">
                <i class="ni ni-building text-orange"></i> {{ __('Cities') }}
            </a>
        </li>
    @endif
    @if(config('app.ordering')&&config('settings.enable_finances_admin'))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('finances.admin') }}">
                <i class="ni ni-money-coins text-blue"></i> {{ __('Finances') }}
            </a>
        </li>
    @endif
        
    @if(config('settings.app_dev'))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.restaurant.banners.index') }}">
                <i class="ni ni-album-2 text-green"></i> {{ __('Banners') }}
            </a>
         </li>
     @endif
   

    @if(!config('settings.hideApps'))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('apps.index') }}">
                <i class="ni ni-spaceship text-red"></i> {{ __('Apps') }}
            </a>
        </li>
    @endif
        
    <li class="nav-item">
        <a class="nav-link" href="#navbar-resources" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="navbar-maps">
            <i class="ni ni-single-copy-04 text-primary"></i>
            <span class="nav-link-text">{{ __('Resources') }}</span>
        </a>
        <div class="collapse" id="navbar-resources" style="">
            <ul class="nav nav-sm flex-column">

                @foreach (auth()->user()->getExtraMenus() as $menu)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($menu['route'],isset($menu['params'])?$menu['params']:[]) }}">
                                    <i class="{{ $menu['icon'] }}"></i> {{ __($menu['name']) }}
                                </a>
                        </li>
                @endforeach
            </ul>
        </div>
    </li>

        
    

        <li class="nav-item">
            <a class="nav-link" href="#navbar-system" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="navbar-maps">
                <i class="ni ni-settings text-primary"></i>
                <span class="nav-link-text">{{ __('System') }}</span>
            </a>
            <div class="collapse" id="navbar-system" style="">
                <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('settings.index') }}">
                            <i class="ni ni-settings text-black"></i> {{ __('Site Settings') }}
                        </a>
                    </li>
                    @if(config('app.isqrsaas'))
                        @if(config('settings.showlandingmanagment',false)||!config('app.isqrexact'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.landing') }}">
                                    <i class="ni ni-html5 text-green"></i> {{ __('Landing Page') }}
                                </a>
                            </li>
                        @endif
                    @endif
                    @if(config('settings.enable_pricing'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('plans.index') }}">
                                <i class="ni ni-credit-card text-orange"></i> {{ __('Pricing plans') }}
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pages.index') }}">
                            <i class="ni ni-ungroup text-info"></i> {{ __('Pages') }}
                        </a>
                    </li>
                    @if(config('app.isqrsaas'))
                        <li class="nav-item">
                            <?php
                                $theLocaleToOpen=strtolower(config('settings.app_locale'));
                                if( strtolower(session('applocale_change')).""!=""){
                                    $theLocaleToOpen=strtolower(session('applocale_change'));
                                }
                            ?>
                            <a class="nav-link" target="_blank" href="{{ url('/admin/languages')."/".$theLocaleToOpen."/translations" }}">
                                <i class="ni ni-world text-orange"></i>{{ __('Translations') }}
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" target="_blank" href="{{ url('/admin/languages')."/".strtolower(config('settings.app_locale'))."/translations" }}">
                                <i class="ni ni-world text-orange"></i> {{ __('Translations') }}
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('settings.cloudupdate') }}">
                                <i class="ni ni-cloud-download-95 text-blue"></i> {{ __('Updates') }}
                            </a>
                        </li>
                    </li>
                    
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#navbar-support" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="navbar-maps">
                <i class="ni ni-support-16 text-success"></i>
                <span class="nav-link-text">{{ __('Support') }}</span>
            </a>
            <div class="collapse" id="navbar-support" style="">
                <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                        <a class="nav-link" target="_blank" href="https://mobidonia.gitbook.io/qr-menu-maker/faq/faq">
                            {{ __('FAQ') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" target="_blank" href="https://help.mobidonia.com/">
                            {{ __('Web project docs') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" target="_blank" href="https://mobidonia.gitbook.io/mobile-apps/">
                            {{ __('Mobile app docs') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" target="_blank" href="https://mobidonia.helpviser.com/">
                            {{ __('Ticket support') }}
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        
</ul>
