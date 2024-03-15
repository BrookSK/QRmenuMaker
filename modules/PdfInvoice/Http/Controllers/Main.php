<?php

namespace Modules\PdfInvoice\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Order;

class Main extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Order $order)
    {
        $dataToSend=['order'=>$order,'custom_data'=>$order->getAllConfigs()];
        \App\Services\ConfChanger::switchCurrency($order->restorant);
        
        if(isset($_GET['site_token'])&&$_GET['site_token']=="89324nkjdcs8c9234234"){
            return view('pdf-invoice::index',$dataToSend);
        }
        
        if(auth()->user()->hasRole('admin')){
            return view('pdf-invoice::index',$dataToSend);
        }else if(auth()->user()->hasRole('owner')&&$order->restorant->user_id==auth()->user()->id){
            return view('pdf-invoice::index',$dataToSend);
        }else if(auth()->user()->hasRole('staff')&&$order->restorant->id==auth()->user()->restaurant_id){
            return view('pdf-invoice::index',$dataToSend);
        }else if(auth()->user()->hasRole('client')&&$order->client_id==auth()->user()->id){
            return view('pdf-invoice::index',$dataToSend);
        }else if(auth()->user()->hasRole('driver')&&$order->driver_id==auth()->user()->id){
            return view('pdf-invoice::index',$dataToSend);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('pdf-invoice::create');
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
        return view('pdf-invoice::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('pdf-invoice::edit');
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
