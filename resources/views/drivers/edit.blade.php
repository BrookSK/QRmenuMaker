@extends('layouts.app', ['title' => __('Drivers Management')])

@section('content')
@include('drivers.partials.header', ['title' => __('Edit Driver')])

<div class="container-fluid mt--7">
    <div class="row">
        <!--<div class="col-xl-12 order-xl-1">-->
        <div class="col-xl-8">
            <div class="col-12">
                @include('partials.flash')
            </div>
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">{{ __('Driver Management') }}</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('drivers.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="pl-lg-4">
                        <form method="post" action="{{ route('drivers.update', $driver) }}" autocomplete="off">
                            @csrf
                            @method('put')
                        </form>
                    </div>
                    <hr />
                    <h6 class="heading-small text-muted mb-4">{{ __('Driver information') }}</h6>
                    <div class="pl-lg-4">
                        <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                            <label class="form-control-label" for="input-name">{{ __('Driver Name') }}</label>
                            <input type="text" name="name_driver" id="input-name" class="form-control form-control-alternative" placeholder="{{ __('Driver Name') }}" value="{{ old('name', $driver->name) }}" readonly>
                        </div>
                        <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                            <label class="form-control-label" for="input-name">{{ __('Driver Email') }}</label>
                            <input type="text" name="email_driver" id="input-name" class="form-control form-control-alternative" placeholder="{{ __('Driver Email') }}" value="{{ old('name', $driver->email) }}" readonly>
                        </div>
                        <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                            <label class="form-control-label" for="input-name">{{ __('Driver Phone') }}</label>
                            <input type="text" name="phone_driver" id="input-name" class="form-control form-control-alternative" placeholder="{{ __('Driver Phone') }}" value="{{ old('name', $driver->phone) }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <div class="col-xl-4 mb-5 mb-xl-0">
            <div class="row">
                <div class="col-xl-12 col-md-6">
                    @foreach($earnings as $key => $value)
                        <div class="card card-stats">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">{{ __($key) }}</h5>
                                        <span class="h4 font-weight-bold mb-0">{{ __('Orders').": ".$value['orders'] }}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="{{ 'icon icon-shape text-white rounded-circle shadow '.$value['icon'] }}">
                                            <i class="ni ni-chart-bar-32"></i>
                                        </div>
                                    </div>
                                </div>
                                <p class="mb-0 text-sm">
                                    <!--<span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>
                                    <span class="text-nowrap">Since last month</span>-->
                                    <span class="h4 mb-0 text-nowrap">{{ __('Earnings').": "}}@money($value['earning'], config('settings.cashier_currency'),config('settings.do_convertion'))</span>
                                </p>
                            </div>
                        </div>
                        <br/>
                    @endforeach

                    @if(isset($extraViews))
                        @foreach ($extraViews as $extraView )
                            @include($extraView['route'])
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="row">
                 <!-- Included views -->
                 
                </div>   
        </div>
    </div>
    @include('layouts.footers.auth')
</div>
@endsection
