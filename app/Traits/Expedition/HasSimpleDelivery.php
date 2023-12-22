<?php

namespace App\Traits\Expedition;

use App\Address;
use App\Models\SimpleDelivery;

trait HasSimpleDelivery
{
    public function expeditionRules(){
        return [
            'deliveryAreaId' => ['required','not_in:0']
            //,'address_id' => ['required','string'],
            //'customFields.client_name' =>['required'],
            //'customFields.client_phone' =>['required']
        ];
    }

    public function setAddressAndApplyDeliveryFee(){
            if($this->request->has('phone')){
                $this->order->phone= $this->request->phone;
            }
            $this->order->whatsapp_address=$this->request->address_id;
            if($this->request->deliveryAreaId){
                $this->order->delivery_price = SimpleDelivery::findOrFail($this->request->deliveryAreaId)->cost;
            }else{
                $this->order->delivery_price = 0;
            }

            $this->order->update();
    }

    public function setTimeSlot(){
        $this->order->delivery_pickup_interval=$this->request->timeslot;
        $this->order->update();
    }
}
