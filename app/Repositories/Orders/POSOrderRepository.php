<?php

/*
|--------------------------------------------------------------------------
| POS Order
|--------------------------------------------------------------------------
*/

namespace App\Repositories\Orders;

use Illuminate\Support\Facades\Validator;
use Cart;
use Illuminate\Support\Facades\Cookie;

class POSOrderRepository extends BaseOrderRepository implements OrderTypeInterface
{
    private $listOfOrders=null; //The list of order to go in the cookie

    public function validateData(){
        $validator=Validator::make($this->request->all(), array_merge($this->expeditionRules(),$this->paymentRules()));
        if($validator->fails()){$this->status=false;}
        return $validator;
    }

    public function makeOrder(){

        //From Parent - Construct the order
        $this->constructOrder();

        //In POS - currently logged in user is not the client
        $this->order->client_id=null;

        //Payed by default
        $this->order->payment_status='paid';

         //Employee
        if(auth()->user()){
            $this->order->employee_id=auth()->user()->id;
        }

        $this->order->update();



        

        //From trait - set fee and time slot
        if($this->request->delivery_method=="delivery"){
            //In Social, we don't have common, id, instead there, we have a string
            $this->order->whatsapp_address=$this->request->address_id;
            $this->order->delivery_price=$this->request->customFields['deliveryFee'];
            $this->order->update();
        }else{
            $this->setAddressAndApplyDeliveryFee();
        }
        $this->setTimeSlot();

        //From parent - check if order is ok - min price. -- Only for pickup - dine in should not have minimum
        if($this->request->delivery_method=="pickup"){
            $resultFromValidateOrder=$this->validateOrder();
            if($resultFromValidateOrder->fails()){return $resultFromValidateOrder;}
        }
        
        
        //From trait - make attempt to pay order or get payment link
        $resultFromPayOrder=$this->payOrder();
        if($resultFromPayOrder->fails()){return $resultFromPayOrder;}

        //We have a payment link/method
        if(strlen($this->order->payment_link)>3){   
            $this->order->payment_status='unpaid';
            $this->order->update();
        }

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
        //Only if the order is new
        if($this->isNewOrder){
            //Set the just created status
            $this->order->status()->attach(1, ['user_id'=>$this->vendor->user->id, 'comment'=>'POS order']);

            //If automatically approve and FoodTiger, set the approved status also, 
            if (config('app.order_approve_directly')&&config('app.isft')) {
                $this->order->status()->attach(2, ['user_id'=>1, 'comment'=>__('Automatically approved by admin')]);
            }

        }
        
        
    }

    public function redirectOrInform(){
        if($this->status){
            if($this->paymentRedirect==null){
                //We don't have payment redirects
                if($this->isNewOrder){
                    //New order - redirect to success page
                    return redirect()->route('order.success', ['order' => $this->order])->withCookie(cookie('orders', $this->listOfOrders, 360));
                }else{
                    //Updated order - redirect to restaurant page
                    return redirect()
                        ->route('vendor', ['alias'=>$this->vendor->subdomain])
                        ->withCookie(cookie('orders', $this->listOfOrders, 360))
                        ->withStatus(__('Order updated.').' ID #'.$this->order->id);
                }
                
            }else{
                //We have payment redirect
                return redirect()->route('order.success', ['order' => $this->order,'redirectToPayment'=>true])->withCookie(cookie('orders', $this->listOfOrders, 360));
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