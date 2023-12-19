<?php

namespace App\Http\Controllers\API\KDS;
use App\Http\Controllers\Controller;
use App\Models\CartStorageModel;
use App\Models\Orderitems;
use App\Order;
use Carbon\Carbon;
use App\Status;
use App\Paths;
use App\Restorant;
use App\Tables;
use App\User;
use App\Models\CartStorageModel as DatabaseStorageModel;

class OrdersController extends Controller
{
    /**
     * Converts DB Order to CartModel Style order
     */
    private function orderToCartModel($order){
        $cart_data=[];
        foreach ($order->items as $key => $item) {
            $cartItemName = $item->name." ".$item->pivot->variant_name;
            $extras=json_decode($item->pivot->extras);
            foreach ($extras as $keye => $extra) {
                $cartItemName .= "\n+ ".$extra;
            }
            

            $cart_data[$item->pivot->id]=[
                "id"=>$item->pivot->id,
                "name"=>$cartItemName,
                "kds_finished"=>$item->pivot->kds_finished,
                "quantity"=> $item->pivot->qty,
            ];
        }
        
        return [
            "id"=>$order->id,
            "cart_data"=>$cart_data,
            "created_at"=> $order->created_at,
            "updated_at"=>$order->updated_at,
            "vendor_id"=> $order->restorant_id,
            "user_id"=> $order->employee_id,
            "type"=> $order->delivery_method,
            "kds_finished"=>$order->kds_finished,
            "isFromDbOrder"=>true,
            "expeditionType"=>$order->getExpeditionType(),
            "time_slot"=>$order->time_formated,
            "table_id"=>$order->table_id,
            "comment"=>$order->comment
        ];
    }

    private static function cmpBySort($a, $b) {
        return $b['timePassed'] - $a['timePassed'];
    }

    private static function cmpBySortRev($a, $b) {
        return $a['timePassed'] - $b['timePassed'];
    }

    /**
     * Returns list of orders that are not finished
     */
    public function index($finished="false")
    {
        //Get the restaurant id 
        $vendor=$this->getRestaurant();
        $vendor_id=$vendor->id;

        \App\Services\ConfChanger::switchCurrency($vendor);

        //Orders from Database that are not made
        $dbOrders=Order::orderBy('created_at', 'desc')
                    ->where(['restorant_id'=>$vendor_id])
                    ->where('created_at', '>=', Carbon::today())
                    ->where('kds_finished',($finished=="false"?0:1))
                    ->with(['items', 'status', 'restorant', 'client', 'address'])->get();
    
                    

        //Get all the active orders
        $orders=CartStorageModel::where('vendor_id',$vendor_id)
        ->where('type',3)
        ->where('kds_finished','!=',($finished=="false"?1:0))
        ->get()->toArray();

        //Merge the orders
        foreach ($dbOrders as $key => $orderDB) {
            array_push($orders,$this->orderToCartModel($orderDB));
        }


        //Make good for UI
        foreach ($orders as $key => &$order) {

            //set isFromDbOrder
            if(!isset($order['isFromDbOrder'])){
                $order['isFromDbOrder']=false;
            }
            if(!isset($order['kds_finished'])){
                $order['kds_finished']=0;
            }
            if(!$order['isFromDbOrder']){
                $order['comment']="";
            }
            if(!$order['isFromDbOrder']){
                $order['table_id']=$order['id'];
            }

            //Unset attributes
            foreach ($order['cart_data'] as $keyCD => &$cartData) {
                //Set kds_finished / per item
                if(isset($cartData['kds_finished'])){
                    $cartData['kds_finished']=$cartData['kds_finished'];
                }else if(!$order['isFromDbOrder']){
                    $cartData['kds_finished']=0;
                }
                unset($cartData['attributes']);
                unset($cartData['conditions']); 
                unset($cartData['price']); 
            }

           

            $theTable=$order['type']==3?Tables::findOrFail($order['table_id']):null;

            $tz=$vendor->getConfig('time_zone',config('app.timezone'));
            $diff = Carbon::now()->setTimezone($tz)->diffInSeconds((new Carbon($order['created_at']))->setTimezone($tz));
            $diffInMin=$diff/60;

            //Color codes
            $color="green";
            if($diffInMin>$vendor->getConfig('kds_range_one',0)){
                $color=$vendor->getConfig('kds_range_one_color',"green");
            }

            if($diffInMin>$vendor->getConfig('kds_range_two',10)){
                $color=$vendor->getConfig('kds_range_one_two',"yellow");
            }

            if($diffInMin>$vendor->getConfig('kds_range_three',30)){
                $color=$vendor->getConfig('kds_range_one_three',"orange");
            }

            if($diffInMin>$vendor->getConfig('kds_range_three',60)){
                $color=$vendor->getConfig('kds_range_one_three',"red");
            }

            //Employee
            $employee=__('Online');
            if(strlen($order['user_id'])>0){
                $employee=User::find($order['user_id'])->name;
            }

            $order['header']=[
                'title1'=>$order['type']=="3"?__('Dine In'):$order['expeditionType'],
                'title2'=>$order['type']=="3"?($theTable?$theTable->getFullNameAttribute():""):__("Time slot")." : ".$order['time_slot'],
                'title3'=>gmdate("H:i", $diff)." - ".$employee,
                'color'=>"background-color:".$color
            ];
            $order['timePassed']=$diff;


            //Clear up
            unset($order['created_at']);
            unset($order['updated_at']);
            unset($order['vendor_id']);
            unset($order['user_id']);
            unset($order['type']);
            unset($order['receipt_number']);
            unset($order['expeditionType']);
            unset($order['time_slot']);
        }
        if($finished=="false"){
            usort($orders, array( $this, 'cmpBySort' ));
        }else{
            usort($orders, array( $this, 'cmpBySortRev' ));
        }
        

        return response()->json([
            //'pos'=>CartStorageModel::where('vendor_id',$vendor_id)->where('type',3)->where('kds_finished','!=',($finished=="false"?1:0))->get()->toArray(),
            //'db'=>$dbOrders,
            'data' => $orders,
            'status' => true,
            'message' => '',
        ]);
    }

    public function finished(){
        return $this->index("true");
    }


    /**
     * ITEMS
     */
    public function finishItem($orderId, $itemId, $isDBtypeOrder="true"){
     if($isDBtypeOrder=="true"){
        return $this->finishDBItem($orderId, $itemId);
     }else{
        return $this->finishPOSItem($orderId, $itemId);
     }
    }

    private function finishDBItem($orderId, $itemId){
        $oi=Orderitems::findOrFail($itemId);
        $oi->kds_finished=1;
        $oi->update();
        return response()->json([
            'status' => true,
            'message' => __('Order updated.'),
            'data'=> null,
        ]);
    }

    private function finishPOSItem($orderId, $itemId){
        if($row = DatabaseStorageModel::find($orderId."_cart_items"))
        {
            $row->updateItemAttribute($row->cart_data,$itemId,'kds_finished',1);
            $row->save();
            return response()->json([
                'status' => true,
                'message' => __('Order updated.'.$itemId),
                'data'=> $row->cart_data,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => __('Order not updated.'),
            'data'=> null,
        ]);
    }

    public function unfinishItem($orderId, $itemId, $isDBtypeOrder="true"){
        if($isDBtypeOrder=="true"){
           return $this->unfinishDBItem($orderId, $itemId);
        }else{
           return $this->unfinishPOSItem($orderId, $itemId);
        }
       }
   
   
    private function unfinishDBItem($orderId, $itemId){
        $oi=Orderitems::findOrFail($itemId);
        $oi->kds_finished=0;
        $oi->update();
        return response()->json([
            'status' => true,
            'message' => __('Order updated.'),
            'data'=> null,
        ]);
    }
   
    private function unfinishPOSItem($orderId, $itemId){
        if($row = DatabaseStorageModel::find($orderId."_cart_items"))
        {
            $row->updateItemAttribute($row->cart_data,$itemId,'kds_finished',0);
            $row->save();
            return response()->json([
                'status' => true,
                'message' => __('Order updated.'),
                'data'=>null,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => __('Order not updated.'),
            'data'=> null,
        ]);
    }



    /***
     * ORDERS
     */
    public function finishOrder($orderId, $isDBtypeOrder="true"){
        if($isDBtypeOrder=="true"){
            return $this->finishDBOrder($orderId);
         }else{
            return $this->finishPOSOrder($orderId);
         }
    }

    private function finishDBOrder($orderId){
        $order=Order::findOrFail($orderId);
        $order->kds_finished=1;
        $order->update();
        return response()->json([
            'status' => true,
            'message' => __('Order updated.'),
            'data'=> null,
        ]);
    }

    private function finishPOSOrder($orderId){
        if($row = DatabaseStorageModel::find($orderId."_cart_items"))
        {
            $row->updateAttribute('kds_finished',1);
            $row->save();
            return response()->json([
                'status' => true,
                'message' => __('Order updated.'),
                'data'=> null,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => __('Order not updated.'),
            'data'=> null,
        ]);
    }


    public function unfinishOrder($orderId, $isDBtypeOrder="true"){
        if($isDBtypeOrder=="true"){
            return $this->unfinishDBOrder($orderId);
         }else{
            return $this->unfinishPOSOrder($orderId);
         }
    }

    private function unfinishDBOrder($orderId){
        $order=Order::findOrFail($orderId);
        $order->kds_finished=0;
        $order->update();
        return response()->json([
            'status' => true,
            'message' => __('Order updated.'),
            'data'=> null,
        ]);
    }

    private function unfinishPOSOrder($orderId){
        if($row = DatabaseStorageModel::find($orderId."_cart_items"))
        {
            $row->updateAttribute('kds_finished',0);
            $row->save();
            return response()->json([
                'status' => true,
                'message' => __('Order updated.'),
                'data'=> null,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => __('Order not updated.'),
            'data'=> null,
        ]);
    }



    
}
