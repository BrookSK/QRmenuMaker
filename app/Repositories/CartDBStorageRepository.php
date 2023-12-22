<?php

namespace App\Repositories;

use App\Items;
use App\Models\CartStorageModel as DatabaseStorageModel;
use App\Restorant;
use Darryldecode\Cart\CartCollection;
use Illuminate\Support\Facades\Auth;

class CartDBStorageRepository {

    public function has($key)
    {
        return DatabaseStorageModel::find($key);
    }

    public function get($key)
    {
        if($this->has($key))
        {
            return new CartCollection(DatabaseStorageModel::find($key)->cart_data);
        }
        else
        {
            return [];
        }
    }

    public function put($key, $value)
    {
        
        if($row = DatabaseStorageModel::find($key))
        {
            // update
            $row->cart_data = $value;
            $row->save();
        }
        else
        {
            if(Auth::check()){
                $vendor_id=auth()->user()->restaurant_id;
                $user_id=auth()->user()->id;
            }else{
                $vendor_id=0;
                //dd($value->toArray());
                foreach ($value->toArray() as $key => $value) {
                    $vendor_id=$value['attributes']['id'];
                }
                //We will find the vendor via the item
                
                $vendor=Restorant::findOrFail($vendor_id);
                $user_id=$vendor->user_id;

            }
            DatabaseStorageModel::create([
                'id' => $key,
                'cart_data' => $value,
                'vendor_id' => $vendor_id,
                'user_id'=>$user_id,
                'type'=>strlen($key)<15?'3':(strlen($key)<19?"2":"1"),
                'receipt_number'=>now()->timestamp."-".sprintf('%03d', $user_id)."-".rand(1000,9999),
                'kds_finished'=>0
            ]);
        }
    }
}