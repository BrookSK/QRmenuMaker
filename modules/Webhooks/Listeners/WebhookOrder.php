<?php

namespace Modules\Webhooks\Listeners;

use App\Models\Company;
use App\Order;

class WebhookOrder
{

    private function notify($order,$webhook){
        $client = new \GuzzleHttp\Client();

        $dataToSend=$order->toArray();
        $dataToSend['custom_fields']=$order->getAllConfigs();
        $dataToSend['items']=$order->items->toArray();
        $dataToSend['last_status']=$order->laststatus();

        $payload = [
            'form_params' => $dataToSend,
        ];

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $webhook, $payload);
    }


    public function handleOrderAcceptedByAdmin($event){

       

        $order=$event->order;
        $vendor=$order->restorant;

        //Vendor setup
        $webhook_for_vendor=$vendor->getConfig('webhook_url_by_admin','');

        //Admin setup
        $webhook_for_admin=config('webhook.webhook_by_admin');

        //Notify Admin
        if(strlen($webhook_for_admin)>5){
            $this->notify($order,$webhook_for_admin);
        }
        

        //Notify Owner
        if(strlen($webhook_for_vendor)>5){
            $this->notify($order,$webhook_for_vendor);
        }
        
    }

    public function handleOrderAcceptedByVendor($event){
        
      
        $order=$event->order;
        $vendor=$order->restorant;

        //Vendor setup
        $webhook_for_vendor=$vendor->getConfig('webhook_url_by_vendor','');

        //Admin setup
        $webhook_for_admin=config('webhook.webhook_by_vendor');

        //Notify Admin
        if(strlen($webhook_for_admin)>5){
            $this->notify($order,$webhook_for_admin);
        }
        

        //Notify Owner
        if(strlen($webhook_for_vendor)>5){
            $this->notify($order,$webhook_for_vendor);
        }
    }

    public function handleUpdateOrder($event){
        $order=Order::where('id',$event->order["id"])->first();
        if($order){
            $vendor=$order->restorant;

            //Vendor setup
            $webhook_for_vendor=$vendor->getConfig('webhook_order_status','');

            //Admin setup
            $webhook_for_admin=config('webhook.webhook_status_by_admin');

            //Notify Admin
            if(strlen($webhook_for_admin)>5){
                $this->notify($order,$webhook_for_admin);
            }
            

            //Notify Owner
            if(strlen($webhook_for_vendor)>5){
                $this->notify($order,$webhook_for_vendor);
            }
        }
        
 
    }

    public function handleNewVendor($event){
        $webhook_for_admin=config('webhooks.webhook_new_vendor',"");
        if(strlen( $webhook_for_admin)>3){
            $client = new \GuzzleHttp\Client();
            
            
            $dataToSend=$event->vendor->toArray();
        
            $payload = [
                'form_params' => $dataToSend,
            ];
    
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', $webhook_for_admin, $payload);
        }
       
       
    }


    public function subscribe($events)
        {
            $events->listen(
                'App\Events\OrderAcceptedByAdmin',
                [WebhookOrder::class, 'handleOrderAcceptedByAdmin']
            );

            $events->listen(
                'App\Events\OrderAcceptedByVendor',
                [WebhookOrder::class, 'handleOrderAcceptedByVendor']
            );

            $events->listen(
                'App\Events\NewVendor',
                [WebhookOrder::class, 'handleNewVendor']
            );

            $events->listen(
                'App\Events\UpdateOrder',
                [WebhookOrder::class, 'handleUpdateOrder']
            );
        }
}

?>