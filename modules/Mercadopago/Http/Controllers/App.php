<?php

namespace Modules\Mercadopago\Http\Controllers;

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
        //Set payment link in order
        $this->order->payment_link=route('mercadopago.pay',['order'=>$this->order->id]);
        $this->order->update();

        //All ok
        return Validator::make([], []);
   }
}
