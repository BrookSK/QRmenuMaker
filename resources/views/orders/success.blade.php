@extends('layouts.app', ['title' => ''])
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="card shadow border-0 mt-8">
            <div class="card-body text-center">
                <div class="justify-content-center text-center">
                    <lottie-player src="https://assets5.lottiefiles.com/packages/lf20_y2hxPc.json"  background="transparent"  speed="1"  style=" height: 200px;"    autoplay></lottie-player>
                </div>
                <h2 class="display-2">{{ __("You're all set!") }}</h2>
                <h1 class="mb-4">
                    <span class="badge badge-primary">{{ __('Order')." #".$order->id }}</span>
                </h1>
                <div class="d-flex justify-content-center">
                    <div class="col-8">
                        <h5 class="mt-0 mb-5 heading-small text-muted">
                            {{ __("Your order is created. You will be notified for further information.") }}
                        </h5>
                        <div class="font-weight-300 mb-5">
                            {{ __("Thanks for your purchase") }}, 
                        <span class="h3">{{ $order->restorant->name }}</span></div>
                        @if (config('settings.wildcard_domain_ready'))
                            <a href="{{ $order->restorant->getLinkAttribute() }}" class="btn btn-outline-primary btn-sm">{{ __('Go back to restaurant') }}</a>
                        @else
                            <a href="{{ route('vendor',$order->restorant->subdomain) }}" class="btn btn-outline-primary btn-sm">{{ __('Go back to restaurant') }}</a>
                        @endif

                        <!-- My Order Buttton -->
                        @if (config('app.isqrsaas'))
                        <br/><br/><br/>
                         <a href="{{ route('guest.orders')}}"  class="btn  btn-lg btn-outline-primary btn btn-neutral btn-icon btn-cart">
                            <span class="btn-inner--icon">
                                <i class="fa fa-list-alt"></i>
                              </span>
                             <span  class="btn-inner--text">{{ __('My Orders') }}</span>
                         </a>
                         @endif
                        
                        <!-- End  My Order Button -->

                        <!-- WHATS APP Buttton -->
                        @if ($showWhatsApp)
                            <a target="_blank" href="?order={{$_GET['order']}}&whatsapp=yes"  class="btn btn-lg btn-icon btn btn-success mt-4 paymentbutton">
                                <span style="color:white" class="btn-inner--icon lg"><i class="fa fa-whatsapp" aria-hidden="true"></i></span>
                                <span style="color:white" class="btn-inner--text">{{ __('Send order on WhatsApp') }}</span>
                            </a>
                        @endif
                        <!-- End WhattsApp Button -->


                        <!-- Whats App  Redirect -->
                       @isset($whatsappurl)
                        <script type="text/javascript">

                                var redirectDone=false;
                                if(!redirectDone){
                                    redirectDone=true;

                                    var redirectWindow = window.open('{{ $whatsappurl }}', '_blank');
                                    redirectWindow.location;
                                }
                            </script>
                       @endisset
                            
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection





