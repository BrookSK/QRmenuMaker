<?php

namespace App\Traits\Payments;
use Illuminate\Support\Facades\Validator;
use Laravel\Cashier\Exceptions\PaymentActionRequired;
use Laravel\Cashier\Exceptions\PaymentFailure;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Stripe;

trait HasStripe
{
    public function paymentRules(){
        return ['stripe_token' => ['required']];
    }

    public function payOrder(){
        try {
            $total_price = (int) (($this->order->order_price_with_discount + $this->order->delivery_price) * 100);
            $chargeOptions = [];
            
            //Setup based on vendor
            if(config('settings.stripe_useVendor')){
                
                Stripe::setClientId($this->vendor->getConfig('stripe_key',''));
                Stripe::setApiKey($this->vendor->getConfig('stripe_secret',''));
                config(['settings.stripe_key' => $this->vendor->getConfig('stripe_key','')]);
                config(['settings.stripe_secret' => $this->vendor->getConfig('stripe_secret','')]);
                config(['cashier.key' => $this->vendor->getConfig('stripe_key')]);
                config(['cashier.secret' => $this->vendor->getConfig('stripe_secret')]);
                
            } else if (config('settings.enable_stripe_connect') && $this->vendor->user->stripe_account) {
                $chargeOptions=$this->stripeConnect();
            }

            //User to apply the charge to
            $user=auth()->user()?auth()->user():$this->vendor->user;

            if($this->order->isMobileOrder){
                //Mobile Order
                $srtipe_payment_id=$this->mobileCharge($total_price,$chargeOptions,$user);
            }else {
                //Web order
                $srtipe_payment_id=$this->webCharge($total_price,$chargeOptions,$user);
            }
    
            
    
            $this->order->srtipe_payment_id = $srtipe_payment_id;
            $this->order->payment_status = 'paid';
            $this->order->payment_processor_fee = ($total_price * config('settings.stripe_fee')/10000) + config('settings.stripe_static_fee');
            $this->order->update();
        }catch (PaymentActionRequired $e) {
            //On PaymentActionRequired  3D secure - send the checkout link
            $this->paymentRedirect= route('cashier.payment',[$e->payment->id, 'redirect' => route('handle.order.payment.stripe',['order'=> $this->order->id])]);

            //Set payment link in order
            $this->order->payment_link=$this->paymentRedirect;
            $this->order->update();

        }catch (IncompletePayment $e) {
            //On IncompletePayment - SCA - send the checkout link
            $this->paymentRedirect= route('cashier.payment',[$e->payment->id, 'redirect' => route('handle.order.payment.stripe',['order'=> $this->order->id])]);

            //Set payment link in order
            $this->order->payment_link=$this->paymentRedirect;
            $this->order->update();

        }catch (PaymentFailure $e) {
            //On Fail delete order and inform user
            $this->invalidateOrder();
            return Validator::make(['stripe_payment_failure'=>null], ['stripe_payment_failure'=>'required']);
        }
    
        //In Stripe there is no payment - Return empty validator
        return Validator::make([], []);
    }

    public function webCharge($total_price,$chargeOptions,$user){
        return $user->charge($total_price, $this->request->stripe_token, $chargeOptions)->id;
    }

    public function mobileCharge($total_price,$chargeOptions,$user){
        Stripe::setApiKey(config('settings.stripe_secret'));

        $customer = Customer::create([
            'email' =>$user->email,
            'source'  => $this->request->stripe_token,
        ]);

        $charge = Charge::create([
            'customer' => $customer->id,
            'amount'   => $total_price,
            'currency' => config('settings.cashier_currency'),
        ]);
        return $charge->id;
    }

    public function stripeConnect(){
        
        $application_fee_amount = 0;

        //Delivery fee, only in FoodTiger
        if(config('app.isft',true)){
            $application_fee_amount += (int) (($this->order->delivery_price));
        }
        
        
        //Static fee
        $application_fee_amount += (float) $this->order->static_fee;

        //Percentage fee
        $application_fee_amount += (float) $this->order->fee_value;

        //Make it for stripe
        $application_fee_amount = (int) (float) ($application_fee_amount * 100);

        //Create the charge object
        $chargeOptions = [
            'application_fee_amount' => $application_fee_amount,
            'transfer_data' => [
                'destination' => $this->vendor->user->stripe_account.'',
            ],
        ];
        return $chargeOptions;
    }

    
}
