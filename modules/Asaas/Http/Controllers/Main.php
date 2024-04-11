<?php

namespace Modules\Asaas\Http\Controllers;

require __DIR__.'/../../vendor/autoload.php';

use App\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Asaas\Preference;
use Asaas\Item;
use Asaas\SDK;

class Main extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function pay($order)
    {
        $order=Order::findOrFail($order);
        $totalValue=$order->order_price_with_discount+$order->delivery_price;

        //GET THE KEY
        //System setup 
        $access_token=config('asaas.access_token',"");

        //Setup based on vendor
        if(config('asaas.useVendor')){
            $access_token=$order->restorant->getConfig('asaas_access_token','');
        }

        // Agrega credenciales
        SDK::setAccessToken($access_token);

        
       
        // Crea un objeto de preferencia
        $preference = new Preference();

        // Crea un ítem en la preferencia
        $item = new Item();
        $item->title = 'Pedido #'.$order->id;
        $item->quantity = 1;
        $item->unit_price = $totalValue;
        $preference->items = array($item);
        $preference->external_reference=$order->id;

        $preference->back_urls = array(
            "success" => route('asaas.execute',['status'=>'success','order'=>$order->id]),
            "failure" => route('asaas.execute',['status'=>'failure','order'=>$order->id]),
            "pending" => route('asaas.execute',['status'=>'pending','order'=>$order->id])
        );
        $preference->auto_return = "approved";


        $preference->save();

        //OR
        return redirect($preference->init_point);

        //OR
       // return view('asaas::pay',['preference_id'=>$preference->id,'order'=>$order]);
    }

    public function executePayment($status,$order){
        
        if(isset($_GET['external_reference'])){
            $order=Order::findOrFail($_GET['external_reference']);
        }else{
            $order=Order::findOrFail($order);
        }

        if($status=="success"){
            $order->payment_status = 'paid';
            $order->update();
            return redirect()->route('order.success', ['order' => $order]);
        }else{
            \Session::put('error',__('Payment status:')." ".$status);
            return redirect()->route('asaas.pay', ['order' => $order->id]);
        }

        

        
        
    }

  
}
