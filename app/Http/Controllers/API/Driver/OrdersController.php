<?php

namespace App\Http\Controllers\API\Driver;
use App\Http\Controllers\Controller;
use App\Order;
use Carbon\Carbon;
use App\Status;
use App\Paths;

class OrdersController extends Controller
{

    public function earnings()
    {
        //Today paid orders
        $today=Order::where(['driver_id'=>auth()->user()->id])->where('payment_status','paid')->where('created_at', '>=', Carbon::today());
       
        //Week paid orders
        $week=Order::where(['driver_id'=>auth()->user()->id])->where('payment_status','paid')->where('created_at', '>=', Carbon::now()->startOfWeek());

        //This month paid orders
        $month=Order::where(['driver_id'=>auth()->user()->id])->where('payment_status','paid')->where('created_at', '>=', Carbon::now()->startOfMonth());

        //Previous month paid orders 
        $previousmonth=Order::where(['driver_id'=>auth()->user()->id])->where('payment_status','paid')->where('created_at', '>=',  Carbon::now()->subMonth(1)->startOfMonth())->where('created_at', '<',  Carbon::now()->subMonth(1)->endOfMonth());


        //This user driver_percent_from_deliver
        $driver_percent_from_deliver=intval(auth()->user()->getConfig('driver_percent_from_deliver',config('settings.driver_percent_from_deliver')))/100;

        return response()->json([
            'data' => [
                'user'=>auth()->user()->name,
                'today'=>[
                    'orders'=>$today->count(),
                    'earning'=>$today->sum('delivery_price')*$driver_percent_from_deliver,
                ],
                'week'=>[
                    'orders'=>$week->count(),
                    'earning'=>$week->sum('delivery_price')*$driver_percent_from_deliver,
                ],
                'month'=>[
                    'orders'=>$month->count(),
                    'earning'=>$month->sum('delivery_price')*$driver_percent_from_deliver,
                ],
                'previous'=>[
                    'orders'=>$previousmonth->count(),
                    'earning'=>$previousmonth->sum('delivery_price')*$driver_percent_from_deliver,
                ]
            ],
            'status' => true,
            'message' => '',
        ]);
    }

    public function index()
    {
        return response()->json([
            'data' => Order::orderBy('created_at', 'desc')->where(['driver_id'=>auth()->user()->id])->where('created_at', '>=', Carbon::today())->with(['items', 'status', 'restorant', 'client', 'address'])->get(),
            'driver_id'=>auth()->user()->id,
            'status' => true,
            'message' => '',
        ]);
    }

    public function odersWithLatLng($lat,$lng)
    {

        auth()->user()->lat = $lat;
        auth()->user()->lng = $lng;
        auth()->user()->update();

        return response()->json([
            'data' => Order::orderBy('created_at', 'desc')->where(['driver_id'=>auth()->user()->id])->where('created_at', '>=', Carbon::today())->with(['items', 'status', 'restorant', 'client', 'address'])->get(),
            'driver_id'=>auth()->user()->id,
            'status' => true,
            'message' => 'odersWithLatLng ',
        ]);
    }

    public function order(Order $order){
        return response()->json([
            'status' => true,
            'data' => $order,
        ]);
    }

    //updateOrderDeliveryPrice
    public function updateOrderDeliveryPrice(Order $order, $delivery_price)
    {
        
        $order->delivery_price = $delivery_price;
         $order->update();
        

        return response()->json([
            'status' => true,
            'message' => __('Order updated.'),
            'data'=> Order::where(['id'=>$order->id])->with(['items', 'status', 'restorant', 'client', 'address'])->get(),
        ]);
    }


    public function updateOrderStatus(Order $order, Status $status)
    {
        $order->status()->attach($status->id, ['user_id'=>auth()->user()->id, 'comment'=>isset($_GET['comment']) ? $_GET['comment'] : '']);
        if ($status->id == 6) {
            $order->lat = $order->restorant->lat;
            $order->lng = $order->restorant->lng;
            $order->update();
        }

        if ($status->alias.'' == 'delivered') {
            $order->payment_status = 'paid';
            $order->update();
        }

        return response()->json([
            'status' => true,
            'message' => __('Order updated.'),
            'data'=> Order::where(['id'=>$order->id])->with(['items', 'status', 'restorant', 'client', 'address'])->get(),
        ]);
    }

    public function orderTracking(Order $order, $lat, $lng)
    {
        
        $order->lat = $lat;
        $order->lng = $lng;
        $order->update();

        $order->driver->lat = $lat;
        $order->driver->lng = $lng;
        $order->driver->update();

        $lastPath=$order->driver->paths()->orderByDesc('id')->first();
        
        $shouldInsertPath=!($lastPath&&$lastPath->lat==$lat&&$lastPath->lng==$lng);


       if($shouldInsertPath){
        $path = new Paths;

        $path->lat =$lat;
        $path->lng =$lng;
        $path->user_id=$order->driver->id;

        $path->save();
       }

        

       

        return response()->json([
            'status' => true,
            'message' => __('Order updated.'),
        ]);
    }


    public function acceptOrder(Order $order)
    {
        //This driver decides to accept the order

        //Only assigned driver
        if (auth()->user()->id == $order->driver_id) {

            //1. Order will be accepted
            $order->status()->attach([13 => ['comment'=>__('Driver accepts order'), 'user_id' => auth()->user()->id]]);

            return response()->json([
                'status' => true,
                'message' => __('Order updated.'),
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => __('Order not on this driver'),
            ]);
        }
    }

    public function rejectOrder(Order $order)
    {
        //This driver decides to reject the order

        //Only assigned driver
        if (auth()->user()->id == $order->driver_id) {

            //1. Order will be rejected
            $order->driver_id = null;
            $order->status()->attach([12 => ['comment'=>__('Driver reject order'), 'user_id' => auth()->user()->id]]);
            $order->update();

            //2. TODO Look for new driver

            //3. Update driver acceptence status
            auth()->user()->rejectedorders = auth()->user()->rejectedorders + 1;
            auth()->user()->update();

            return response()->json([
                'status' => true,
                'message' => __('Order updated.'),
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => __('Order not on this driver'),
            ]);
        }
    }
}
