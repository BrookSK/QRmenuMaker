@extends('layouts.app', ['title' => ''])
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="card shadow border-0 mt-8">
            <div class="card-body text-center">
                <div class="justify-content-center text-center">
                    <lottie-player src="https://assets3.lottiefiles.com/packages/lf20_ltbqacam.json"  background="transparent"  speed="1"  style=" height: 200px;"  loop  autoplay></lottie-player>
                </div>
               
                <h3 class="display-2">{{ __("Let's pay") }}</h3>
                <h1 class="mb-4">
                    <?php 
                        $currency=config('settings.cashier_currency');
                        $convert=config('settings.do_convertion');
                    ?>
                    <span class="badge badge-primary">{{ __('Order')." #".$order->id }}</span>
                    <span class="badge badge-primary">@money( $order->delivery_price+$order->order_price_with_discount, $currency,true)</span>  
                    
                </h1>
                <div class="d-flex justify-content-center">
                    <div class="col-8">
                        <br >
                        @foreach ($paymentMethods as $key => $name)
                        <a type="button"  href="{{ route("selectedpaymentt",["order"=>$order->id,'payment'=>$key])}}" role="button" class="btn btn-primary text-white">{{ __('Pay with')}} {{ __($name)}}</a><br ><br >
                            
                        @endforeach
                        <br ><br >
                        <div class="font-weight-300 mb-5">
                            {{ __("Thanks for your purchase") }}, 
                        <span class="h3">{{ $order->restorant->name }}</span></div>
                        @if (config('settings.wildcard_domain_ready'))
                            <a href="{{ $order->restorant->getLinkAttribute() }}" class="btn btn-outline-primary btn-sm">{{ __('Go back to restaurant') }}</a>
                        @else
                            <a href="{{ route('vendor',$order->restorant->subdomain) }}" class="btn btn-outline-primary btn-sm">{{ __('Go back to restaurant') }}</a>
                        @endif

                       
                            
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection





