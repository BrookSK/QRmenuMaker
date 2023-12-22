<?php

namespace App\Traits\Payments;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Akaunting\Module\Facade as Module;

trait HasLinkPayment
{
    public function paymentRules(){
        return [];
    }


    public function payOrder(){

        //Since v3, we have option to show all  the payment networks
        if($this->request->payment_method=="all"){

            //Payment methods
            $paymentMethodsCount=0;
            foreach (Module::all() as $key => $module) {
                if($module->get('isPaymentModule')){
                    if($this->vendor->getConfig($module->get('alias')."_enable","false")=="true"){
                        $vendorHasOwnPayment=$module->get('alias');
                        $paymentMethodsCount++;
                    }
                }
            }

            if($paymentMethodsCount==1){
                //Set to the only avaiable  method and move on
                $this->request->payment_method=$vendorHasOwnPayment;
            }else{

                //In this case, we have more than 1 available payment option, user will be redirected
                //to screen where she can choose the way he wants to pay
                $this->order->payment_link=route('selectpay',['order'=>$this->order->id]);
                $this->order->update();
                $this->paymentRedirect= $this->order->payment_link;
                return Validator::make([], []);
            }

           
        }

        $className = '\Modules\\'.ucfirst($this->request->payment_method).'\Http\Controllers\App';
        $ref = new \ReflectionClass($className);
        $theValidator = $ref->newInstanceArgs(array($this->order,$this->vendor))->execute();

        if($theValidator->fails()){
            $this->invalidateOrder();
        }else{
            //It is ok, use the link
            $this->paymentRedirect= $this->order->payment_link;
        }
        return $theValidator;
    }
}
