<?php

namespace App\Traits\Expedition;

use App\Address;
use App\Models\SimpleDelivery;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Order;


trait HasComplexDelivery
{
    public function expeditionRules(){
        return [
            'pickup_address' => ['required'],
            'delivery_address'=>['required']
        ];
    }

    public function setAddressAndApplyDeliveryFee(){
            if($this->request->has('phone')){
                $this->order->phone= $this->request->phone;
            }
            $this->order->pickup_address=$this->request->pickup_address;
            $this->order->delivery_address=$this->request->delivery_address;

            $this->order->delivery_lat=$this->request->delivery_lat;
            $this->order->delivery_lng=$this->request->delivery_lng;

            $this->order->pickup_lat=$this->request->pickup_lat;
            $this->order->pickup_lng=$this->request->pickup_lng;

            //Short link to order
            $random = Str::random(8);
            if(Order::where('md',$random)->first()!=null){
                $random = Str::random(8);
            }
            $this->order->md=$random;
            
            
            $distance=$this->calculateDistance( 
                $this->order->delivery_lat, 
                $this->order->delivery_lng,
                $this->order->pickup_lat,
                $this->order->pickup_lng,
                config('settings.unit','K'));
            $this->order->distance = $distance;

            try {
                //Try to find google calculated distance
                $url='https://maps.googleapis.com/maps/api/distancematrix/json?departure_time=now&destinations='.$this->order->delivery_lat.','.$this->order->delivery_lng.'&origins='.$this->order->pickup_lat.','.$this->order->pickup_lng.'&key='.config('settings.google_maps_api_key');
                $response = Http::get($url);
                $this->order->distance = $response['rows'][0]['elements'][0]['distance']['value']/1000; 
            } catch (\Throwable $th) {
                //throw $th;
            }

            if($this->vendor){
                $this->order->delivery_price = ($this->order->distance*$this->vendor->getConfig('cost_per_kilometer',2))+$this->vendor->getConfig('cost_for_start',5);
            }else{
                $this->order->delivery_price = ($this->order->distance*config('cockpit.cost_per_kilometer',2))+config('cockpit.cost_per_kilometer',5);
            }
            

            $this->order->update();
    }

    public function setTimeSlot(){
        $this->order->delivery_pickup_interval=$this->request->timeslot;
        $this->order->update();
    }
}
