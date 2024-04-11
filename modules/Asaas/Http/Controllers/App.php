<?php

namespace Modules\Asaas\Http\Controllers;

require __DIR__.'/../../vendor/autoload.php';


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
        // //System setup 
        // $secret=config('asaas.secret');

        // //Setup based on vendor
        // if(config('asaas.useVendor')){
        //     $secret=$this->vendor->getConfig('asaas','');
        // }

        // \App\Services\ConfChanger::switchCurrency($this->order->restorant);





        //Set payment link in order
        $this->order->payment_link=route('asaas.pay',['order'=>$this->order->id]);
        $this->order->update();

        //All ok
        return Validator::make([], []);
   }
}
