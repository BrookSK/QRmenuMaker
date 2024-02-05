<?php

namespace Modules\Cloner\Models;

use App\Extras;
use Illuminate\Database\Eloquent\Model;
use App\Restorant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Vendorc extends Restorant
{
    public function duplicate($newid){
        
        //Replicate user
       /* $newUser=$this->user->replicate();
        $newUser->email=$email;
        $newUser->name=$name;
        $newUser->password=Hash::make($pass);
        $newUser->api_token=Str::random(80);
        $newUser->plan_id=null;
        $newUser->save();

        //Replicate Company
        $newVendor = $this->replicate();
        $newVendor->name=$this->name." ".time();
        $newVendor->phone=$phone;
        $newVendor->subdomain=$this->subdomain.time();
        $newVendor->user_id=$newUser->id;
        $newVendor->save();*/
        $newVendor=Vendorc::findOrFail($newid);

        //Set same data to the restaurant
        $newVendor->logo=$this->logo;
        $newVendor->cover=$this->cover;
        $newVendor->lat=$this->lat;
        $newVendor->lng=$this->lng;
        $newVendor->address=$this->address;
        $newVendor->minimum=$this->minimum;
        $newVendor->description=$this->description;
        $newVendor->radius=$this->radius;
        $newVendor->city_id=$this->city_id;
        $newVendor->do_covertion=$this->do_covertion;
        $newVendor->fee=$this->fee;
        $newVendor->static_fee=$this->static_fee;
        $newVendor->is_featured=$this->is_featured;
        $newVendor->can_pickup=$this->can_pickup;
        $newVendor->self_deliver=$this->self_deliver;
        $newVendor->free_deliver=$this->free_deliver;
        $newVendor->whatsapp_phone=$this->whatsapp_phone;
        $newVendor->currency=$this->currency;
        $newVendor->payment_info=$this->payment_info;
        $newVendor->update();

        //Copy the configs 
        $newVendor->setMultipleConfig($this->getAllConfigs());



        //Replicate Business Hours
        foreach ($this->hours as $keyH => $hour) {
            $newHour = $hour->replicate();
            $newHour->restorant_id=$newVendor->id;
            $newHour->save();
        }

        //Replicate Local Menus
        foreach ($this->localmenus as $keyL => $localmenu) {
            $newLM = $localmenu->replicate();
            $newLM->restaurant_id=$newVendor->id;
            $newLM->save();
        }

        //areas

        //Replicate Areas
        foreach ($this->areas as $keyAr => $area) {
            $newArea = $area->replicate();
            $newArea->restaurant_id=$newVendor->id;
            $newArea->save();
            
            //tables
            foreach ($area->tables as $keyTB => $table) {
                $newTable = $table->replicate();
                $newTable->restoarea_id=$newArea->id;
                $newTable->restaurant_id=$newVendor->id;
                $newTable->save();
            }
        }

        //Replicate categories and rest
        foreach ($this->categories as $keyC => $category) {
            $newCategory = $category->replicate();
            $newCategory->restorant_id=$newVendor->id;
            $newCategory->save();

            //Replicate items
            foreach ($category->items as $keyI => $item) {
                $newItem = $item->replicate();
                $newItem->category_id=$newCategory->id;
                $newItem->save();

                //extras
                foreach ($item->extras as $keyE => $extra) {
                    $newExtra = $extra->replicate();
                    $newExtra->item_id=$newItem->id;
                    $newExtra->save();
                }
               
                //options
                $optionsChanger=[];
                foreach ($item->options as $keyO => $option) {
                    $newOption = $option->replicate();
                    $newOption->item_id=$newItem->id;
                    $newOption->save();
                    $optionsChanger[$option->id]=$newOption->id;
                }
                
                //variants
                foreach ($item->variants as $keyV => $variant) {
                    $newVariant = $variant->replicate();
                    $newVariant->item_id=$newItem->id;
                    $opt=$newVariant->options;
                   

                    //Change the options
                    foreach ($optionsChanger as $keyOC => $valueOC) {
                        $opt=str_replace("\"".$keyOC."\"","\"".$valueOC."\"",$opt);
                    }
                    $newVariant->options=$opt;
                    $newVariant->save();

                    //Variants with  extras
                    $newExrtasForVariant=[];
                    foreach ($variant->extras as $keyEV => $extra) {

                        //Find the same extra in  the new item
                        $fe=Extras::where('name',$extra->name)->where('price',$extra->price)->where('item_id',$newItem->id)->first();
                        if($fe){
                            array_push($newExrtasForVariant,$fe->id);
                        }
                    }
                    $newVariant->extras()->attach($newExrtasForVariant);
                }
            }
        }
    }
}
