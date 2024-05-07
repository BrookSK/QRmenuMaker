<?php

namespace App\Http\Controllers;

use Akaunting\Module\Facade as Module;
use App\Order;

use Illuminate\Support\Facades\DB;
//Use \DB;

use Cart;
use Illuminate\Http\Request;




class PaymentController extends Controller
{
    public function statusPix(Request $request){
        $BaseUri = str_replace('www.', '', $_SERVER['HTTP_HOST']);
        $url = array_filter(explode('/', $request->url()));
        $orderID = Order::where('id', $url[4])->firstOrFail();

        if($orderID->status_transacao == 'aprovado'){
            echo '<script>window.location.href = "https://'.$BaseUri.'/pdfinvoice/'.$url[4].'";</script>';
        }
    }

    public function notificationPix(Request $request){
        $json       = file_get_contents('php://input');
        $resultadoJson  = json_decode($json, true);
        if(isset($resultadoJson['payment']['pixQrCodeId'])){
            
            $codigo_transacao = $resultadoJson['payment']['pixQrCodeId'];
            $orderID   = DB::table('orders')
            ->where([
                ['codigo_transacao', $codigo_transacao]
            ])
            ->first();
            //var_dump($resultadoJson);
            if($resultadoJson['payment']['status'] == "RECEIVED"){
                if($orderID->status_transacao <> 'aprovado'){
                    DB::table('orders')
                        ->where('codigo_transacao', $orderID->codigo_transacao)
                        ->update(['status_transacao' => 'aprovado']);
                }
            }
        }
    }

    public function pagamentoPix(Request $request)
    {
        $BaseUri = str_replace('www.', '', $_SERVER['HTTP_HOST']);
        $url = array_filter(explode('/', $request->url()));
        $orderID = Order::where('id', $url[4])->firstOrFail();
       
        $configID   = DB::table('configs')
            ->where([
                ['model_id', $orderID->restorant_id],
                ['key', 'stripe_secret']
            ])
            ->first();
        $companiesID   = DB::table('companies')
            ->where([
                ['id', $orderID->restorant_id]
            ])
            ->first();

        // RECUPERA O PRECO DO PEDIDO
        //echo $orderID->order_price;
        //echo 'ID do restaurante dentro ORDER:'.$orderID->restorant_id;
        $access_token           = $configID->value;
        $dados['addressKey']    = '';
        $dados['description']               = 'Compra de um produto';
        $dados['value']                     = $orderID->order_price;
        $dados['format']                    = 'ALL';
        $dados['expirationDate']            = null;
        $dados['expirationSeconds']         = null;
        $dados['allowsMultiplePayments']    = true;

        $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://asaas.com/api/v3/pix/qrCodes/static',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($dados),
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                'content-type: application/json',
                'access_token: '.$access_token
            ),
            ));
            $response = curl_exec($curl);
            $resultado = json_decode($response);
            //var_dump($resultado);
            
        curl_close($curl);
        if(isset($resultado->payload)){
            $id_transacao   = $resultado->id;
            $CopiaCola      = $resultado->payload;
            $Qrcode         = $resultado->encodedImage;
            DB::table('orders')
                ->where('id', $url[4])
                ->update(['codigo_transacao' => $id_transacao]);
        }
        // Variável na view
        // Onde está a visualização
        return view('pagamento.pagamento', [
            // Variavel sendo enviada para a view
            'CopiaCola' => $CopiaCola,
            'Qrcode'    => $Qrcode,
            'BaseUri'   => $BaseUri,
            'Subdomain' => $companiesID->subdomain,
            'idPedido'  => $url[4],
            'preco'     => $orderID->order_price
        ]);
    }




    public function view(Request $request)
    {
        $total = Money(Cart::getSubTotal(), config('settings.cashier_currency'), config('settings.do_convertion'))->format();

        //Clear cart
        Cart::clear();

        return view('payment.payment', [
            'total' => $total,
        ]);
    }

    public function handleOrderPaymentStripe(Request $request,Order $order){
        if($request->success.""=="true"){
            $order->payment_status = 'paid';
            $order->update();
            return redirect()->route('order.success', ['order' => $order]);
        }else{
            return redirect()->route('vendor',$order->restorant->subdomain)->withMesswithErrorage($request->message)->withInput();
        }
    }

    public function selectPaymentGateway(Order $order){
        $paymentMethods=[];

        $vendor=$order->restorant;

            //Payment methods
            foreach (Module::all() as $key => $module) {
                if($module->get('isPaymentModule')){
                    if($vendor->getConfig($module->get('alias')."_enable","false")=="true"){
                        $vendorHasOwnPayment=$module->get('alias');
                        $paymentMethods[$module->get('alias')]=ucfirst($module->get('alias'));
                    }
                }
            }
        return view('orders.multypay', 
        [
            'paymentMethods'=>$paymentMethods,
            'order' => $order,
            'showWhatsApp'=>false,
            'whatsappurl'=>''
        ]);
    }

    public function selectedPaymentGateway(Order $order,$paymentMethod){
        $order->payment_method=$paymentMethod;
        $className = '\Modules\\'.ucfirst($paymentMethod).'\Http\Controllers\App';
        $ref = new \ReflectionClass($className);
        $ref->newInstanceArgs(array($order,$order->restorant))->execute();
        return redirect()->route('order.success', ['order' => $order,'redirectToPayment'=>true]);
    }

    
}