<?php

namespace Modules\Paypal\Http\Controllers;

//PayPal

use App\Restorant;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class App 
{
    private $order;
    private $vendor;

    public function __construct($order, $vendor) {
        
        $this->order=$order;
        $this->vendor=$vendor;

    }

    public function execute(){
         //System setup 
         $client_id=config('paypal.client_id');
         $secret=config('paypal.secret');
         $mode=config('paypal.mode');
 
         //Setup based on vendor
         if(config('paypal.useVendor')){
             $client_id=$this->vendor->getConfig('paypal_client_id','');
             $secret=$this->vendor->getConfig('paypal_secret','');
             $mode=$this->vendor->getConfig('paypal_mode','sandbox');
         }

        
        \App\Services\ConfChanger::switchCurrency($this->order->restorant);
 
         $apiContext = new ApiContext(
             new OAuthTokenCredential(
                 $client_id,
                 $secret
             )
         );
 
         $apiContext->setConfig(array('mode' => $mode));
 
         $payer = new Payer();
         $payer->setPaymentMethod('paypal');
 
         $itemsArr = [];
         foreach ($this->order->items as $key => $item) {
             $itemObj = new Item();
             $itemObj->setName($item->name." ".$item->variant_name)
                 ->setCurrency(strtoupper(config('settings.cashier_currency')))
                 ->setQuantity(intval($item->pivot->qty))
                 ->setSku(strval($item->id)) // Similar to `item_number` in Classic API
                 ->setPrice($item->pivot->variant_price);
 
             array_push($itemsArr, $itemObj);
         }
 
         //Add delivery
         if($this->order->delivery_price>0){
             $itemObj = new Item();
             $itemObj->setName(__('Delivery'))
                 ->setCurrency(strtoupper(config('settings.cashier_currency')))
                 ->setQuantity(1)
                 ->setSku(0) // Similar to `item_number` in Classic API
                 ->setPrice($this->order->delivery_price);
             array_push($itemsArr, $itemObj);
         }
 
 
         $itemList = new ItemList();
         $itemList->setItems($itemsArr);
 
         $amount = new Amount();
         $amount->setCurrency(strtoupper(config('settings.cashier_currency')))->setTotal($this->order->order_price_with_discount+$this->order->delivery_price);
 
         $transaction = new Transaction();
         $transaction->setAmount($amount)
             ->setItemList($itemList)
             ->setDescription(__('Payment for order on ').config('settings.app_name'))
             ->setInvoiceNumber($this->order->id);
 
         $redirectUrls = new RedirectUrls();
         $redirectUrls
            ->setReturnUrl(route('paypal.execute').'?success=true&vendor_id='.$this->vendor->id)
            ->setCancelUrl(route('paypal.cancel'));
 
         $payment = new Payment();
         $payment->setIntent('sale')
             ->setPayer($payer)
             ->setRedirectUrls($redirectUrls)
             ->setTransactions([$transaction]);
         
         try {
             $payment->create($apiContext);
 
             if (isset($redirectUrls)) {
                
 
                 //Set payment link in order
                 $this->order->payment_link=$payment->getApprovalLink();
                 $this->order->update();
 
                 //All ok
                 return Validator::make([], []);
 
             }else{
                 
                 return Validator::make(['paypal_payment_approval_missing'=>null], ['paypal_payment_approval_missing'=>'required']);
             }
         } catch (\PayPal\Exception\PayPalConnectionException $ex) {
             //On Fail delete order
             dd($ex);
             return Validator::make(['paypal_payment_error_action'=>null], ['paypal_payment_error_action'=>'required']);
         }
    }

}