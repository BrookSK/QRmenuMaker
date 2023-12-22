<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
        </div>
    </div>
</div>
<div class="container-fluid mt--6">
    <div class="row">
    @foreach($earnings as $key => $value)
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                  <div class="row">
                    <div class="col">
                      <h5 class="card-title text-uppercase text-muted mb-0">{{ __($key) }}</h5>
                      <span class="h3 font-weight-bold mb-0">{{ __('Orders').": ".$value['orders'] }}</span>
                    </div>
                    <div class="col-auto">
                      <div class="{{ 'icon icon-shape text-white rounded-circle shadow '.$value['icon'] }}">
                        <i class="ni ni-active-40"></i>
                      </div>
                    </div>
                  </div>
                  <p class="mt-3 mb-0 text-sm">
                    <!--<span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>-->
                    <span class="h4 mb-0 text-nowrap">{{ __('Earnings').": "}}@money($value['earning'], config('settings.cashier_currency'),config('settings.do_convertion'))</span>
                  </p>
                </div>
            </div>
        </div>
    @endforeach  
    </div>
</div>