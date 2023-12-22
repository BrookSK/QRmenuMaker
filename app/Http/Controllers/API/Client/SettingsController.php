<?php

namespace App\Http\Controllers\API\Client;
use Akaunting\Module\Facade as Module;

class SettingsController
{
    public function index()
    {
            //Fill Payment options
            $paymentOptions=[];
            foreach (Module::all() as $key => $module) {
                    //If Module is payment method, check if admin has approved usage
                    if($module->get('isPaymentModule')){
                        //check if admin has approved usage
                        array_push($paymentOptions,[
                            'name'=>__($module->get('alias')),
                            'alias'=>$module->get('alias')
                        ]);
                    }
            }

            return response()->json([
                'data' => [
                    'app_name'=>config('app.name'),
                    'single_mode'=>config('settings.single_mode'),
                    'single_mode_id'=>config('settings.single_mode_id'),
                    'single_mode_name'=>config('app.name'),
                    'multi_city'=>config('settings.multi_city'),
                    'currency'=>config('settings.cashier_currency'),
                    'currency_sign'=>currency(config('settings.cashier_currency')),
                    'enable_cod'=>!config('settings.hide_cod'),
                    'enable_stripe'=>config('settings.enable_stripe'),
                    'stripe_publish_key'=>config('settings.stripe_key'),
                    'onesignal_android_app_id'=>config('settings.onesignal_android_app_id'),
                    'onesignal_ios_app_id'=>config('settings.onesignal_ios_app_id'),
                    'google_api_key'=>config('settings.google_maps_api_key'),
                    'driver_percent_from_deliver'=>config('driver_percent_from_deliver',100),
                    'payment_methods'=>$paymentOptions,
                    'issd'=>config('app.issd',false),
                    'isft'=>config('app.isft',false),
                    'units'=>config('settings.units',"K"),
                ],
                'status' => true,
                'errMsg' => '',
            ]);
    }
    
}
