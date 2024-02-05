<?php

namespace Modules\Paypal\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;


use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use App\Order;
use App\Restorant;

class Main extends Controller
{

    //callback after payment
    public function executePayment(Request $request)
    {
        $vendor = Restorant::findOrFail($_GET['vendor_id']);

        //System setup 
        $client_id=config('paypal.client_id');
        $secret=config('paypal.secret');
        $mode=config('paypal.mode');

        //Setup based on vendor
        if(config('paypal.useVendor')){
            $client_id=$vendor->getConfig('paypal_client_id','');
            $secret=$vendor->getConfig('paypal_secret','');
            $mode=$vendor->getConfig('paypal_mode','sandbox');
        }


        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $client_id,     // ClientID
                $secret     // ClientID
            )
        );
        $apiContext->setConfig(array('mode' => $mode));

        try {
            $dataArray = $request->all();

            /* If the request success is true process with payment execution*/
            if (isset($_GET['success']) && $_GET['success'] == 'true' && $dataArray['success'] == 'true') {

                /* If the payer ID or token aren't set, there was a corrupt response */
                if (empty($dataArray['PayerID']) || empty($dataArray['token'])) {
                    return redirect()->route('cart.checkout')->withMesswithErrorage('The payment attempt failed')->withInput();
                }

                $paymentId = $_GET['paymentId'];
                $payment = Payment::get($paymentId, $apiContext);

                $execution = new PaymentExecution();
                $execution->setPayerId($_GET['PayerID']);

                $result = $payment->execute($execution, $apiContext);

                if ($result->getState() == 'approved') {
                    $order_id = intval($result->transactions[0]->invoice_number);

                    $order = Order::findOrFail($order_id);
                    //$restorant = Restorant::findOrFail($order->restorant->id);

                    $order->payment_status = 'paid';

                    $order->update();

                    return redirect()->route('order.success', ['order' => $order]);
                }
            }
        } catch (Exception $ex) {
            
            return redirect()->route('cart.checkout')->withMesswithErrorage('The payment attempt failed because additional action is required before it can be completed.')->withInput();
        }
    }

    //Canceled payment
    public function cancelPayment(Request $request){
        return redirect()->route('cart.checkout')->withMesswithErrorage('The payment attempt was canceled')->withInput();
    }
    
}
