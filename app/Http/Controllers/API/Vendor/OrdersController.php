<?php

namespace App\Http\Controllers\API\Vendor;
use App\Http\Controllers\Controller;
use App\Order;
use Carbon\Carbon;
use App\Status;
use App\Paths;

class OrdersController extends Controller
{
    

    public function earnings()
    {
        //Get the restaurant id 
        $vendor_id=$this->getRestaurant()->id;

        //Today paid orders
        $today=Order::where(['restorant_id'=>$vendor_id])->where('payment_status','paid')->where('created_at', '>=', Carbon::today());
       
        //Week paid orders
        $week=Order::where(['restorant_id'=>$vendor_id])->where('payment_status','paid')->where('created_at', '>=', Carbon::now()->startOfWeek());

        //This month paid orders
        $month=Order::where(['restorant_id'=>$vendor_id])->where('payment_status','paid')->where('created_at', '>=', Carbon::now()->startOfMonth());

        //Previous month paid orders 
        $previousmonth=Order::where(['restorant_id'=>$vendor_id])->where('payment_status','paid')->where('created_at', '>=',  Carbon::now()->subMonth(1)->startOfMonth())->where('created_at', '<',  Carbon::now()->subMonth(1)->endOfMonth());


        return response()->json([
            'data' => [
                'user'=>auth()->user()->name,
                'today'=>[
                    'orders'=>$today->count(),
                    'earning'=>(int)$today->sum('order_price'),
                ],
                'week'=>[
                    'orders'=>$week->count(),
                    'earning'=>(int)$week->sum('order_price'),
                ],
                'month'=>[
                    'orders'=>$month->count(),
                    'earning'=>(int)$month->sum('order_price'),
                ],
                'previous'=>[
                    'orders'=>$previousmonth->count(),
                    'earning'=>(int)$previousmonth->sum('order_price'),
                ]
            ],
            'status' => true,
            'message' => '',
        ]);
    }

    public function index()
    {
        //Get the restaurant id 
        $vendor_id=$this->getRestaurant()->id;

        return response()->json([
            'data' => Order::orderBy('created_at', 'desc')->where(['restorant_id'=>$vendor_id])->where('created_at', '>=', Carbon::today())->with(['items', 'status', 'restorant', 'client', 'address'])->get(),
            'driver_id'=>auth()->user()->id,
            'status' => true,
            'message' => '',
        ]);
    }

    public function order(Order $order){
        return response()->json([
            'status' => true,
            'data' => $order,
        ]);
    }

    public function updateOrderStatus(Order $order, Status $status)
    {
        $order->status()->attach($status->id, ['user_id'=>auth()->user()->id, 'comment'=>isset($_GET['comment']) ? $_GET['comment'] : '']);

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

    public function acceptOrder(Order $order)
    {
        //This restaurant decides to accept the order
        $vendor_id=$this->getRestaurant()->id;

        //Only assigned driver
        if ($vendor_id == $order->restorant_id) {

            //1. Order will be accepted
            $order->status()->attach([3 => ['comment'=>__('Restaurant accepts order'), 'user_id' => auth()->user()->id]]);

            return response()->json([
                'status' => true,
                'message' => __('Order updated.'),
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => __('Order not on this vendor'),
            ]);
        }
    }

    public function rejectOrder(Order $order)
    {
        //This restaurant decides to reject the order
        $vendor_id=$this->getRestaurant()->id;

        //Only assigned driver
        if ($vendor_id == $order->restorant_id) {

            //1. Order will be rejected
            $order->driver_id = null;
            $order->status()->attach([9 => ['comment'=>__('Restaurant reject order'), 'user_id' => auth()->user()->id]]);
            $order->update();

            return response()->json([
                'status' => true,
                'message' => __('Order updated.'),
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => __('Order not on this vendor'),
            ]);
        }
    }
}
