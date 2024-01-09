<?php

namespace App\Traits\Expedition;
use App\Order;

trait HasDineIn
{
    public function expeditionRules(){
        return [
            'dinein_table_id' => ['required']
        ];
    }

    public function setAddressAndApplyDeliveryFee(){
            $this->order->table_id= $this->request->dinein_table_id;
            $this->order->delivery_price= 0;
            $this->order->update();
    }

    public function setTimeSlot(){
    }

    public function findMePreviousOrder(){
        //1. Get the last order for this table
        $lastOrdereForTable = Order::where('table_id', $this->request->dinein_table_id)
        ->where('payment_status', 'unpaid')
        ->where('payment_method', 'cod')
        ->latest('id')
        ->first();


        //2. Check if the status is 1 - jc, 3 -accepted, 5-prepared, 7- delivered, 10 - updated, and unpaid
        if ($lastOrdereForTable != null && in_array($lastOrdereForTable->status->pluck('id')->last(), [1, 2, 3, 5, 7, 10])) {
            //Yes, we can extend this order

            //But make sure vendor supports continues ordering
            if(!$this->vendor->getConfig('disable_continues_ordering',0)){
                //Set this order as the order
                $this->order=$lastOrdereForTable;

                //Set that it is not KDS finished
                $this->order->kds_finished=0;
                $this->order->update();

                //Apply new status
                $this->order->status()->attach(10, ['user_id'=>$this->vendor->user_id, 'comment'=>'Local ordering updated']);
            }
        }

        
    }
}
