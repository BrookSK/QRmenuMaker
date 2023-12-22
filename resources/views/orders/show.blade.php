@extends('layouts.app', ['title' => __('Orders')])

@section('content')
    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
    </div>
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-7 ">
                <br/>
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ "#".$order->id." - ".$order->created_at->locale(Config::get('app.locale'))->isoFormat('LLLL') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-primary"><i class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
                                @if ($pdFInvoice)
                                <a target="_blank" href="/pdfinvoice/{{$order->id}}" class="btn btn-sm btn-success"><i class="fas fa-print"></i> {{ __('Print bill') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                   @include('orders.partials.orderinfo')
                   @include('orders.partials.actions.buttons',['order'=>$order])
                </div>
            </div>
            <div class="col-xl-5  mb-5 mb-xl-0">
                @if(config('app.isft')&&$order->restorant)
                <br/>
                <div class="card card-profile shadow">
                    <div class="card-header">
                        <h5 class="h3 mb-0">{{ __("Order tracking")}}</h5>
                    </div>
                    @if ($order->restorant)
                        <div class="card-body">
                            @include('orders.partials.map',['order'=>$order])
                        </div>
                    @endif
                    
                </div>
                @endif
                <br/>
                @if ($order->client_id!=null&&$order->client_id!=$order->restorant->id)
                <div class="card card-profile shadow mb-3">
                    @include('clients.profile',['client'=>$order->client])
                </div>
                @endif
                <div class="card card-profile shadow">
                    <div class="card-header">
                        <h5 class="h3 mb-0">{{ __("Status History")}}</h5>
                    </div>
                    @include('orders.partials.orderstatus')
                    
                </div>
                @if(auth()->user()->hasAnyRole(['admin','owner','staff','driver']))
                @foreach ($orderModules as $orderModule)
                    @include($orderModule.'::card')
                @endforeach
                @endif

                @if(auth()->user()->hasRole('client'))
                @if($order->status->pluck('alias')->last() == "delivered")
                    <br/>
                    @include('orders.partials.rating',['order'=>$order])
                @endif
                @endif
            </div>
        </div>
        @include('layouts.footers.auth')
        @include('orders.partials.modals',['order'=>$order])
    </div>
@endsection

@section('head')
    <link type="text/css" href="{{ asset('custom') }}/css/rating.css" rel="stylesheet">
@endsection

@section('js')
<!-- Google Map -->
<script async defer src= "https://maps.googleapis.com/maps/api/js?libraries=geometry,drawing&key=<?php echo config('settings.google_maps_api_key'); ?>"> </script>
  

    <script src="{{ asset('custom') }}/js/ratings.js"></script>
@endsection

