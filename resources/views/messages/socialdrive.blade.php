<?php
    $dnl="\n\n";
    $nl="\n\n";
    $tabSpace="      ";
?>
{{ __("Hi, I'd like to request a ride")." 👇"}} 

📍 {{ __('Pickup location') }}
{{ $order->pickup_address }}
{{ config('app.url')."/p/".$order->md }}

🏁 {{ __('Destination') }}
{{ $order->delivery_address }}

🚕 {{ __('Route') }}
{{ config('app.url')."/d/".$order->md }}

📱 {{ __('My phone') }}
{{ $order->phone }}


🧾 {{__('Estimated cost: ').money(($order->order_price_with_discount+$order->delivery_price), config('settings.cashier_currency'), config('settings.do_convertion')) }}
@if (strlen($order->comment)>1)   
🗒 {{ __('Comment') }}
{{ $order->comment }}  
@endif

🚕 {{ __('Vehicle info') }}
{{ $order->driver->name }}
{{ $order->driver->getConfig('plate_number','') }}