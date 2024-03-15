  <!-- Navbar Dark -->
  <nav
  class="navbar navbar-expand-lg navbar-dark bg-gradient-dark z-index-4 py-3">
  <div class="container">
    <a class="navbar-brand m-0" >
      @if(!config('settings.hide_project_branding'))
        <!-- Restaruant Branding -->
        <img src="{{ $vendor->logowidedark }}" class="navbar-brand-img h-80 w-30"  style="max-height: 80px" alt="...">
      @else
        <!-- Admin branding -->
        <img src="{{ config('global.site_logo_dark') }}" class="navbar-brand-img h-80 w-30"  style="max-height: 80px" alt="...">
      @endif
    </a>

    <div class="col-auto justify-content-right">
      <button id="finishedOrders" onclick="showFinished()" type="button" class="btn bg-gradient-primary my-1 me-1">
        <span>{{ __('Show Finished') }}</span>
      </button>
    
      <button id="activeOrders" style="display: none" onclick="showActive()" type="button" class="btn bg-gradient-info my-1 mx-2 me-1">{{ __('Show Active') }}</button>
    
    </div>
    
    

    <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon mt-2">
        <span class="navbar-toggler-bar bar1"></span>
        <span class="navbar-toggler-bar bar2"></span>
        <span class="navbar-toggler-bar bar3"></span>
      </span>
    </button>
    <div class="collapse navbar-collapse" id="navigation">
      <ul class="navbar-nav navbar-nav-hover ms-auto">
        <div class="row">
          
          <div class="col-auto m-auto">
            
            <div class="dropdown" >
              <a class="text-white cursor-pointer" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">{{ auth()->user()->name }}</a>
              <ul class="dropdown-menu  px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton" data-bs-popper="none">
                <li class="mb-2">
                  <a class="dropdown-item border-radius-md"  href="{{ route('profile.edit') }}">
                    <div class="d-flex py-1">
                      
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="text-sm font-weight-normal mb-1">
                          <span class="font-weight-bold">{{ __('Profile') }}</span>
                        </h6>
                        <p class="text-xs text-secondary mb-0">
                          <i class="fa fa-clock me-1" aria-hidden="true"></i>
                          {{ __('Manage your profile') }}
                        </p>
                      </div>
                    </div>
                  </a>
                </li>
                <li class="mb-2">
                  <a class="dropdown-item border-radius-md" href="{{ route('orders.index') }}">
                    <div class="d-flex py-1">
                      
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="text-sm font-weight-normal mb-1">
                          <span class="font-weight-bold">{{ __('All orders') }}</span>
                        </h6>
                        <p class="text-xs text-secondary mb-0">
                          <i class="fa fa-clock me-1" aria-hidden="true"></i>
                          {{ __('View all finished orders') }}
                        </p>
                      </div>
                    </div>
                  </a>
                </li>
                <li class="mb-2 d-xl-none">
                  <a class="dropdown-item border-radius-md" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <div class="d-flex py-1">
                      
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="text-sm font-weight-normal mb-1">
                          <span class="font-weight-bold">{{ __('Logout') }}</span>
                        </h6>
                      </div>
                    </div>
                  </a>
                </li>
              </ul>

            
            </div>
          </div>
          <div class="col-auto m-auto">
            
            <div class="dropdown">
              
            </div>
          </div>


          <div class="col-auto">
            @auth()
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  @csrf
              </form>
            
          @endauth

            

            <a class="btn btn-outline-danger my-1 me-1 d-none d-xl-inline-block" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <span class="my-2 btn-inner--icon"><i class="ni ni-button-power"></i></span>
              <span class="mx-1 btn-inner--text">{{__('Close')}}</span>
            </a>

          

          
            
          </div>
        </div>
      </ul>
    </div>
  </div>
  </nav>
<!-- End Navbar -->