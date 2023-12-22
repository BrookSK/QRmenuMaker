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

class SocialOrderRepository extends BaseOrderRepository implements OrderTypeInterface
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
        $this->setTimeSlot();

        //From parent - check if order is ok - min price. -- Only for pickup - dine in should not have minimum
        $resultFromValidateOrder=$this->validateOrder();
        if($resultFromValidateOrder->fails()){return $resultFromValidateOrder;}

         //From trait - make attempt to pay order or get payment link
         $resultFromPayOrder=$this->payOrder();
         if($resultFromPayOrder->fails()){return $resultFromPayOrder;}

        //Local - set Initial Status
        $this->setInitialStatus();

         //Local - clear cart
         $this->clearCart();

         //Local - Notify
         $this->notify();

        //At the end, return that all went ok
        return Validator::make([], []);
    }


    
    public function setInitialStatus(){
        //Set the just created status
        $this->order->status()->attach(1, ['user_id'=>$this->vendor->user->id, 'comment'=>'Social Network order']);

     }

    public function redirectOrInform(){
        if($this->status){
            if($this->request->deliveryAreaId){
                $this->order->delivery_price = SimpleDelivery::findOrFail($this->request->deliveryAreaId)->cost;
            }
            if($this->vendor->whatsapp_phone=="000000000"){
                //New order - redirect to success page
                return redirect()->route('order.success', ['order' => $this->order]);
            }else{
                $phoneToSend=$this->vendor->whatsapp_phone;
                if($this->request->deliveryAreaId){
                    $areaPhone = SimpleDelivery::findOrFail($this->request->deliveryAreaId)->phone;
                    if(strlen($areaPhone)>5){
                        $phoneToSend=$areaPhone;
                    }
                }
                $message=$this->order->getSocialMessageAttribute(true);
                $url = 'https://api.whatsapp.com/send?phone='.$phoneToSend.'&text='.$message;
                return Redirect::to($url);
            }
        }else{
            //There was some error, return back to the order page
            return redirect()->route('cart.checkout')->withInput();
        }
    }

    private function clearCart(){
        Cart::clear();
    }

    private function notify(){
        $this->notifyOwner();
    }
}