<?php

namespace Modules\Cloner\Http\Controllers;

use App\Items;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Cloner\Models\Vendorc;

use App\Extras;

class Main extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($newid,$oldid)
    {
       if(auth()->user()->hasRole(['admin','manager'])){

        $resto=Vendorc::findOrFail($oldid);
        $resto->duplicate($newid);
        return redirect()->route('admin.restaurants.index')->withStatus(__('Restaurant cloned.'));
       }else{
           abort(404);
       }
        
    }

    public function cloneItem(Items $item){
        $newItem = $item->replicate();
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

        return redirect()->route('items.index')->withStatus(__('Item cloned.'));


    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('cloner::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('cloner::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('cloner::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
