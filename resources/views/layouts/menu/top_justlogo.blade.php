<nav id="navbar-main" class="navbar navbar-light navbar-expand-lg fixed-top">


    <div class="container-fluid">
        @if(!config('settings.hide_project_branding')||(!isset($restorant)))
          <a class="navbar-brand mr-lg-5" href="/">
            <img class="theProjectLogo" src="{{ config('global.site_logo') }}">
          </a>
        @else
          <a class="navbar-brand mr-lg-5" id="topLightLogo" href="#">
            <img class="theProjectLogo" src="{{$restorant->logowide }}">
          </a>
          <a class="navbar-brand mr-lg-5" id="topDarkLogo" style="display: none" href="#">
            <img class="theProjectLogo" src="{{$restorant->logowidedark }}">
          </a>
        @endif

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar_global" aria-controls="navbar_global" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>


        <div class="navbar-collapse collapse" id="navbar_global">
          <div class="navbar-collapse-header">
            <div class="row">
              @if(!config('settings.hide_project_branding')||(!isset($restorant)))
              <div class="col-6 collapse-brand">
                <a href="/">
                  <img src="{{ config('global.site_logo') }}">
                </a>
              </div>
              @else
              <div class="col-6 collapse-brand">
                <a href="#">
                  <img src="{{$restorant->logowidedark }}">
                </a>
              </div>
              @endif
              <div class="col-6 collapse-close">
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar_global" aria-controls="navbar_global" aria-expanded="false" aria-label="Toggle navigation">
                  <span></span>
                  <span></span>
                </button>
              </div>
            </div>
          </div>

          <ul class="navbar-nav align-items-lg-center ml-lg-auto">
            @isset($restorant)
                @yield('addiitional_button_1')
                @yield('addiitional_button_2')
                @yield('addiitional_button_3')
                @if(config('app.isqrsaas'))
                  
                  @if(\Request::route()->getName() != "cart.checkout"&&!config('settings.is_whatsapp_ordering_mode') && !$restorant->getConfig('disable_callwaiter', 0) && strlen(config('broadcasting.connections.pusher.app_id')) > 2 && strlen(config('broadcasting.connections.pusher.key')) > 2 && strlen(config('broadcasting.connections.pusher.secret')) > 2)
                    <li class="web-menu mr-1">
                      <button type="button" class="btn btn-neutral btn-icon btn-cart" data-toggle="modal" data-target="#modal-form">
                        <span class="btn-inner--icon">
                          <i class="fa fa-bell"></i>
                        </span>
                        <span class="nav-link-inner--text">{{ __('Call Waiter') }}</span>
                      </button>
                    </li>
                  @endif
                  
                  @if(config('settings.enable_guest_log'))
                    <li class="web-menu mr-1">
                      <a  href="{{ route('register.visit',['restaurant_id'=>$restorant->id])}}" class="btn btn-neutral btn-icon btn-cart" style="cursor:pointer;">
                            <span class="btn-inner--icon">
                              <i class="fa fa-calendar-plus-o"></i>
                            </span>
                            <span class="nav-link-inner--text">{{ __('Register visit') }}</span>
                        </a>
                    </li>
                  @endif

                  @if(isset($hasGuestOrders)&&$hasGuestOrders)
                    <li class="web-menu mr-1">
                      <a  href="{{ route('guest.orders')}}" class="btn btn-neutral btn-icon btn-cart" style="cursor:pointer;">
                        <span class="btn-inner--icon">
                          <i class="fa fa-list-alt"></i>
                        </span>
                        <span class="nav-link-inner--text">{{ __('My Orders') }}</span>
                      </a>
                    </li>
                  @endif

                  @if ($restorant->getConfig('clients_enable','false')!='false')
                    <li class="nav-item dropdown">
                      @auth()
                          @include('layouts.menu.partials.auth')
                      @endauth
                      @guest()
                          @include('layouts.menu.partials.guest')
                      @endguest
                  </li>
                  @endif

                @endif

            @endisset


            @if(
            \Request::route()->getName() != "newrestaurant.register" && 
            config('app.ordering')&&
            !config('settings.is_pos_cloud_mode') && 
            !config('app.issd') && 
            (isset($canDoOrdering)?$canDoOrdering:true)
            )
            <li class="web-menu" id="cartButtonHolder">

              @if(\Request::route()->getName() != "blog"&&
              \Request::route()->getName() != "pages.show"&&
              \Request::route()->getName() != "cart.checkout")
                <a  id="desCartLink" onclick="openNav()" class="btn btn-neutral btn-icon btn-cart" style="cursor:pointer;">
                  <span class="btn-inner--icon">
                    <i class="fa fa-shopping-cart"></i>
                  </span>
                  <span class="nav-link-inner--text">{{ __('Cart') }}</span>
                  <span v-if="counter>0" class="badge badge-primary">@{{ counter }}</span>
              </a>
              @endif

            </li>
            @endif
            <li class="mobile-menu">

              @yield('addiitional_button_1_mobile')
              @yield('addiitional_button_2_mobile')

              @isset($restorant)
                
                @if(config('app.isqrsaas'))
                  @if(\Request::route()->getName() != "cart.checkout" && !$restorant->getConfig('disable_callwaiter', 0) && strlen(config('broadcasting.connections.pusher.app_id')) > 2 && strlen(config('broadcasting.connections.pusher.key')) > 2 && strlen(config('broadcasting.connections.pusher.secret')) > 2&&!config('settings.is_pos_cloud_mode'))
                    <a type="button" class="nav-link" data-toggle="modal" data-target="#modal-form">
                      <span class="btn-inner--icon">
                        <i class="fa fa-bell"></i>
                      </span>
                      <span class="nav-link-inner--text">{{ __('Call Waiter') }}</span>
                    </a>
                  @endif


                  @if(config('settings.enable_guest_log'))
                    <a href="{{ route('register.visit',['restaurant_id'=>$restorant->id])}}" class="nav-link" style="cursor:pointer;">
                        <i class="fa fa-calendar-plus-o"></i>
                        <span class="nav-link-inner--text">{{ __('Register visit') }}</span>
                    </a>
                  @endif

                  @if(isset($hasGuestOrders)&&$hasGuestOrders)

                    <a  href="{{ route('guest.orders')}}" class="nav-link" style="cursor:pointer;">

                        <i class="fa fa-list-alt"></i>

                      <span class="nav-link-inner--text">{{ __('My Orders') }}</span>
                    </a>
                  @endif
                @endif

                @if(
                  \Request::route()->getName() != "blog"&&
                  \Request::route()->getName() != "pages.show"&&
                \Request::route()->getName() != "newrestaurant.register" 
                && config('app.ordering') &&
                !config('app.issd') && 
                 (isset($canDoOrdering)?$canDoOrdering:true)
                )
                <a id="mobileCartLink" onclick="openNav()" class="nav-link" style="cursor:pointer;">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="nav-link-inner--text">{{ __('Cart') }}</span>
                </a>
                @endif
                
              @endisset


            </li>
          </ul>
        </div>


      </div>

    </nav>
