<?php

/*
|--------------------------------------------------------------------------
| QRSaaS Web Order
|--------------------------------------------------------------------------
*/

namespace App\Repositories\Orders;

use App\Models\SimpleDelivery;
use Illuminate\Support\Facades\Validator;
use Cart;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use App\Notifications\OrderNotification;
use App\Events\OrderAcceptedByVendor;

class SocialDriveOrderRepository extends BaseOrderRepository implements OrderTypeInterface
{

    private $orderMessage=""; //The message we will send

    public function validateData(){
        $validator=Validator::make($this->request->all(), array_merge($this->expeditionRules(),$this->paymentRules()));
        if($validator->fails()){$this->status=false;}
        return $validator;
    }

    public function makeOrder(){

        

        //From Parent - Construct the order
        $this->constructOrder();

     
        //From trait - set fee and time slot
        $this->setAddressAndApplyDeliveryFee();

       

        //From parent - check if order is ok - min price. -- Only for pickup - dine in should not have minimum
        //Don't validate minimum order price
        //$resultFromValidateOrder=$this->validateOrder();
        //if($resultFromValidateOrder->fails()){return $resultFromValidateOrder;}

         //From trait - make attempt to pay order or get payment link
         $resultFromPayOrder=$this->payOrder();
         if($resultFromPayOrder->fails()){return $resultFromPayOrder;}

        //Assign to driver
        $this->order->driver_id=$this->request->driver_id;
        $this->order->update();

       

        //Local - set Initial Status
        $this->setInitialStatus();
        
       

         //Local - Notify
         $this->notify();

        //At the end, return that all went ok
        return Validator::make([], []);
    }


    
    public function setInitialStatus(){
        //Set the just created status
        if($this->vendor){
            $this->order->status()->attach(1, ['user_id'=>$this->vendor->user->id, 'comment'=>'Website whatsapp ride order']);
        }

        //Assign to driver
        $this->order->status()->attach(4, ['user_id'=>$this->request->driver_id, 'comment'=>'Assigned to driver']);

     }

    public function redirectOrInform(){
        if($this->status){
            if($this->order->driver->phone=="000000000"){
                //New order - redirect to success page
                return redirect()->route('order.success', ['order' => $this->order])->withCookie(cookie('orders', $this->listOfOrders, 360));
            }else{
                $message=$this->order->getSocialMessageAttribute(true);
                $url = 'https://api.whatsapp.com/send?phone='.$this->order->driver->phone.'&text='.$message;
                return Redirect::to($url);
            }
        }else{
            //There was some error, return back to the order page
            return redirect()->route('cart.checkout')->withInput();
        }
    }

    private function notify(){
       //Notify driver
        //Inform driver - via email, sms or db, push
        $this->order->driver->notify((new OrderNotification($this->order,4,$this->order->driver))->locale(strtolower(config('settings.app_locale'))));

        if($this->vendor){
            //Dispatch Approved by admin event
            OrderAcceptedByVendor::dispatch($this->order);
        }
        
    }
}