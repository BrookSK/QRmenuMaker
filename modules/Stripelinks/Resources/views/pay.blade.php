@extends('layouts.app', ['title' => ''])
@section('head')
<style>
    .razorpay-payment-button {
        background: #6c5ce7;
        color: whitesmoke;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1;
        width: 15vw;
        height: 8vh;
        border: none;
        padding: 0.3rem 0.3rem;
    }
</style>

@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="card shadow border-0 mt-8">
            <div class="card-body text-center">
                
                <h1 class="mb-4">
                    <span class="badge badge-primary">{{ __('Order')." #".$order->id_formated }}</span>
                </h1>
                @if($message = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <strong>Error!</strong> {{ $message }}
                    </div>
                @endif
                <div class="d-flex justify-content-center">
                    <div class="col-8">
                       
                        <div class="font-weight-300 mb-5">
                            {{ __("Thanks for your purchase") }}, 
                        <span class="h3">{{ $order->restorant->name }}</span></div>
                        @if (config('settings.wildcard_domain_ready'))
                            <a href="{{ $order->restorant->getLinkAttribute() }}" class="btn btn-sm">{{ __('Go back to restaurant') }}</a>
                        @else
                            <a href="{{ route('vendor',$order->restorant->subdomain) }}" class="btn s btn-sm">{{ __('Go back to restaurant') }}</a>
                        @endif

                       
                        
                    </div>
                </div>
                <br />
                <form action="{!!route('razorpay.execute')!!}" method="POST" >                        
                    <script src="https://checkout.razorpay.com/v1/checkout.js"
                            data-key="{{ $key }}"
                            data-amount="{{ $amount }}"
                            data-buttontext="{{ $button }}"
                            data-name="{{ $name }}"
                            data-description="{{ __("Order") }} # {{ $order->id}}">
                    </script>

                    <input type="hidden" name="order_id" value="{{ $order->id_formated }}">
                    <input type="hidden" name="_token" value="{!!csrf_token()!!}">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection





