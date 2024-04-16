<?php

namespace Modules\PrintNode\Listeners;

require __DIR__.'/../vendor/autoload.php';

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;



use \ConvertApi\ConvertApi;


class PrintOnOrder
{

    public function handleOrderAcceptedByAdmin($event){
        $order=$event->order;
        $vendor=$order->restorant;
        $key=$vendor->getConfig('print_node_key','');

         //Check if api key is set
         if(strlen($key)>3){
             
             //Kitchen
            if($vendor->getConfig('printnode_print_kitchen_thermal_when',"restaurant_approve")=="order_received"){
                $this->printKitchenThermal($order,$key,$vendor->getConfig('print_node_kitchen_thermal_id',''));
            }

            //Main Thermal
            if($vendor->getConfig('printnode_print_main_thermal_when',"restaurant_approve")=="order_received"){
                $this->printMainThermal($order,$key,$vendor->getConfig('print_node_main_thermal_id',''));
            }

            //Standard
            if($vendor->getConfig('printnode_print_afour_when',"order_received")=="order_received"){
                $this->printStandardOrder($order,$key,$vendor->getConfig('print_node_standard_id',''));
            }
         }

        
    }

    public function handleOrderAcceptedByVendor($event){
        $order=$event->order;
        $vendor=$order->restorant;
        $key=$vendor->getConfig('print_node_key','');

        //Check if api key is set
        if(strlen($key)>3){
            //Kitchen
            if($vendor->getConfig('printnode_print_kitchen_thermal_when',"restaurant_approve")=="restaurant_approve"){
                $this->printKitchenThermal($order,$key,$vendor->getConfig('print_node_kitchen_thermal_id',''));
            }

            //Main Thermal
            if($vendor->getConfig('printnode_print_main_thermal_when',"restaurant_approve")=="restaurant_approve"){
                $this->printMainThermal($order,$key,$vendor->getConfig('print_node_main_thermal_id',''));
            }

            //Standard
            if($vendor->getConfig('printnode_print_afour_when',"order_received")=="restaurant_approve"){
                $this->printStandardOrder($order,$key,$vendor->getConfig('print_node_standard_id',''));
            }  
        }

        
    }

    private function printStandardOrder($order,$key,$id){
        if(strlen($id)>3){
            if(strlen(config('print-node.convert_api_secret'))>3){

                //dd(config('print-node.convert_api_secret'));
                ConvertApi::setApiSecret(config('print-node.convert_api_secret'));

                $printer=new \Modules\PrintNode\Http\Controllers\App();
                $result = ConvertApi::convert('pdf', [
                        'Url' => route('pdfinvoice',$order->id)."?site_token=89324nkjdcs8c9234234",
                        'CssMediaType' => 'print',
                        'Background' => 'false',
                        'ShowElements' => '#print_area',
                        'WaitElement' => '#print_area',
                    ], 'web'
                );
                
                foreach ($result->getFiles() as $key => $file) {
                    $printer->sendToPrintNode('',$file->getUrl(),$id,$key);
                }
            }
        }

    }

    private function printMainThermal($order,$key,$id){
        if(strlen($id)>3){
            //Printer is set
            $printer=new \Modules\PrintNode\Http\Controllers\App();
            $printer->init($order);
            $printer->sendToPrintNode($printer->printReceipt(),'',$id,$key);
        }
    }

    private function printKitchenThermal($order,$key,$id){
        if(strlen($id)>3){
            //Printer is set
            $printer=new \Modules\PrintNode\Http\Controllers\App();
            $printer->init($order);
            $printer->sendToPrintNode($printer->printKOT(),'',$id,$key);
        }
    }

    public function subscribe($events)
    {
        $events->listen(
            'App\Events\OrderAcceptedByAdmin',
            [PrintOnOrder::class, 'handleOrderAcceptedByAdmin']
        );

        $events->listen(
            'App\Events\OrderAcceptedByVendor',
            [PrintOnOrder::class, 'handleOrderAcceptedByVendor']
        );
    }

}
