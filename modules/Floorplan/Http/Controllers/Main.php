<?php

namespace Modules\Floorplan\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\RestoArea;
use App\Tables;

class Main extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function edit(RestoArea $restoarea)
    {

        if(auth()->user()->hasRole('owner')&&$restoarea->restorant->user_id==auth()->user()->id){
            //Ok
            return view('floorplan::edit',['restoarea'=>$restoarea,'title'=>__('Floor manager for').$restoarea->name]);
        }else{
            abort(404);
        }
        
    }

    public function saveFloorPlan(Request $request,RestoArea $restoarea){
        foreach ($request->items as $key => $item) {

            if(isset($item['table_id'])){
                $table=Tables::findOrFail($item['table_id']);
            }else{
                $table=Tables::create([
                    'name'=>"",
                    'size'=>4,
                    'restoarea_id'=>$restoarea->id,
                    'restaurant_id'=>$restoarea->restaurant_id
                ]);
                $table->save();
            }

            if(isset($item['name'])){
                $table->name=$item['name'];
            }
            if(isset($item['size'])){
                $table->size=$item['size'];
            }
            if(isset($item['w'])){
                $table->w=$item['w'];
            }
            if(isset($item['h'])){
                $table->h=$item['h'];
            }
            if(isset($item['x'])){
                $table->x=$item['x'];
            }
            if(isset($item['y'])){
                $table->y=$item['y'];
            }
            if(isset($item['rounded'])){
                $table->rounded=$item['rounded'];
            }
            $table->update();

            //Or remove
            if(isset($item['deleted'])){
                $table->delete();
            }
        }
        return response()->json([
            'data' => [],
            'status' => true,
            'errMsg' => '',
        ]);
    }
}
