<?php
    namespace Modules\PrintNode\Http\Controllers;

    //require __DIR__.'/../../vendor/autoload.php';

    use Mike42\Escpos\PrintConnectors\DummyPrintConnector;

    use Mike42\Escpos\Printer;



    use App\Order;

    class App
    {
        private $order;
        private $printer;
        private $connector;

        public function init($order){
            $this->order=Order::findOrFail($order->id);

            $this->connector = new DummyPrintConnector();
            $this->printer = new Printer($this->connector);
        }

        public function printKOT(){
            $this->printer->initialize();
            $this->printHeder();
            $this->printClient();
            $this->printComment();
            $this->printTable();
            $this->printItemsForKOT();
            $this->printTotals();
            $this->printQR();
            $this->printer->cut();
            $code=$this->connector->getData();
            $this->printer -> close();

            return base64_encode($code);
        }

        public function printReceipt(){

            $this->printer->initialize();
            $this->printHeder();
            $this->printClient();
            $this->printComment();
            $this->printAddress();
            $this->printTable();
            $this->printPaymentStatus();
            $this->printDeliveryOrDine();
            $this->printItems();
            $this->printTotals();
            $this->printQR();
            $this->printer->cut();
            $code=$this->connector->getData();
            $this->printer -> close();

            return base64_encode($code);

        }

        public function sendToPrintNode($cmd,$file,$id,$token){
            $curl = curl_init();
            $postData="";
            if(strlen($cmd)>5){
                $postData='printerId='.$id.'&contentType=raw_base64&content='.$cmd;
            }else {
                $postData='printerId='.$id.'&contentType=pdf_uri&content='.$file;
            }

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.printnode.com/printjobs',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
            ));

            curl_setopt($curl, CURLOPT_USERPWD, $token . ":");

            $response = curl_exec($curl);

            curl_close($curl);
            return true;
        }


        
        private function printHeder(){
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->feed();
            $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            if(config('settings.hide_project_branding',true)){
                $this->printer->text("#".$this->order->id);
            }else{
                $this->printer->text(config('app.name')." #".$this->order->id);
            }
            $this->printer->selectPrintMode();
            $this->printer->feed();
            $this->printer->setEmphasis(true);
            $this->printer->text($this->order->restorant->name);
            $this->printer->feed();
            $this->printer->text($this->order->created_at->format(config('settings.datetime_display_format')));
            $this->printer->setEmphasis(false);
            $this->printer->feed();
            $this->printLine();
        }

        private function printComment(){
            $this->printer->setEmphasis(true);
                $this->printer->feed();
                $this->printer->text(__("Comment").":");
                $this->printer->feed();
                $this->printer->text($this->order->comment);
                $this->printer->feed();
                $this->printer->setEmphasis(false);
                $this->printer->feed();
        }

        private function printClient(){
            if($this->order->client){
                $this->printer->setEmphasis(true);
                $this->printer->feed();
                $this->printer->text(__("Customer").":");
                $this->printer->feed();
                $this->printer->text($this->order->client->name);
                $this->printer->feed();
                $this->printer->text(__("Phone").":".$this->order->client->phone);
                $this->printer->feed();
                $this->printer->setEmphasis(false);
                $this->printer->feed();
            }else{
                
                $this->printer->setEmphasis(true);
                $this->printer->feed();
                $this->printer->text(__("Customer").":");
                $this->printer->feed();
                if($this->order->getConfig('client_name',"")!=null){
                    $this->printer->text($this->order->getConfig('client_name',""));
                    $this->printer->feed();
                }
                
                //Phones
                if($this->order->getConfig('client_phone',"")!=null){
                    $this->printer->text(__("Phone").":".$this->order->getConfig('client_phone',""));
                    $this->printer->feed();
                }
                if(strlen($this->order->phone)>2){
                    $this->printer->text(__("Phone").":".$this->order->phone);
                    $this->printer->feed();
                }
                $this->printer->setEmphasis(false);
                $this->printer->feed();
                
               
                
            }
            
        }

        private function printAddress(){
            if($this->order->address){
                $this->printer->setEmphasis(true);
                $this->printer->feed();
                $this->printer->text(__("Address").":");
                $this->printer->feed();
                $this->printer->text($this->order->address?$this->order->address->address:"");
                $this->printer->feed();
                $this->printer->setEmphasis(false);
                $this->printer->feed();
            }

            if (!empty($this->order->whatsapp_address)){
                $this->printer->setEmphasis(true);
                $this->printer->feed();
                $this->printer->text(__("Address").":");
                $this->printer->feed();
                $this->printer->text($this->order->whatsapp_address);
                $this->printer->feed();
                $this->printer->setEmphasis(false);
                $this->printer->feed();
            }
           
            
        }

        private function printTable(){
            if($this->order->table){
                $this->printer->setEmphasis(true);
                $this->printer->feed();
                $this->printer->text(__("Area").": ".$this->order->table->restoarea->name);
                $this->printer->feed();
                $this->printer->text(__("Table").": ".$this->order->table->name);
                $this->printer->feed();
                $this->printer->setEmphasis(false);
                $this->printer->feed();
            }
            
        }

        private function printPaymentStatus(){
            $this->printer->text(__("Payment method").": ".__(strtoupper($this->order->payment_method)));
            $this->printer->feed();
            $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $this->printer->text(__(ucfirst($this->order->payment_status)));
            $this->printer->feed();
            $this->printer->selectPrintMode();
        }

        private function printDeliveryOrDine(){
            $this->printer->feed();
            $this->printer->text(__("Delivery method").": ".$this->order->getExpeditionType());
            $this->printer->feed(2);
            if(strlen($this->order->time_formated)>2){
                $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                $this->printer->text(__("Time slot"));
                $this->printer->feed();
                $this->printer->selectPrintMode();
                $this->printer->text($this->order->time_formated);
                $this->printer->feed();
            }
        }

    
        private function printItemsForKOT(){
            $this->printer->feed();
            $this->printLine();
            $this->printer->feed();
            $this->printer->setPrintLeftMargin(0);
            $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->printer->selectPrintMode();
            $this->printer->setEmphasis(true);
            $this->printer->text(rtrim($this->columnify("    ".__('QTY'),__('Item'),60,40,32)));
            $this->printer->setEmphasis(false);
            $this->printer->feed();
            foreach ($this->order->items as $key => $item) {
                $this->printer->text(rtrim($this->columnify($item->pivot->qty, $item->name,76,24,32))."\n");

                if(strlen($item->pivot->variant_name)>3){
                    $this->printer->text(rtrim($this->columnify(__('Variant:'),$item->pivot->variant_name,30,70,32))."\n");
                }
            
                if(strlen($item->pivot->extras)>3){
                    foreach (json_decode($item->pivot->extras) as $key => $extra) {
                        $this->printer->text(rtrim($this->columnify("",$extra,5,95,32))."\n");
                    }
                }
            }
            $this->printer->feed();
            $this->printer->feed();
        }

        private function printItems(){
            $this->printer->feed();
            $this->printLine();
            $this->printer->feed();
            $this->printer->setPrintLeftMargin(0);
            $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->printer->selectPrintMode();
            $this->printer->setEmphasis(true);
            $this->printer->text(rtrim($this->columnify("    ".__('Item'),__('Price')." (".config('settings.cashier_currency').")",60,40,32)));
            $this->printer->setEmphasis(false);
            $this->printer->feed();
            foreach ($this->order->items as $key => $item) {
                $theItemPrice= ($item->pivot->variant_price?$item->pivot->variant_price:$item->price);
                $this->printer->text(rtrim($this->columnify($item->pivot->qty." X ".$item->name,$item->pivot->qty*$theItemPrice,76,24,32))."\n");

                if(strlen($item->pivot->variant_name)>3){
                    $this->printer->text(rtrim($this->columnify(__('Variant:'),$item->pivot->variant_name,30,70,32))."\n");
                }
            
                if(strlen($item->pivot->extras)>3){
                    foreach (json_decode($item->pivot->extras) as $key => $extra) {
                        $this->printer->text(rtrim($this->columnify("",$extra,5,95,32))."\n");
                    }
                }
            
            }
            $this->printer->feed();
            $this->printer->feed();
        }


        private function printTotals(){
            $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->printer->selectPrintMode();
            $this->printer->setEmphasis(true);
            $this->printer->text(rtrim($this->columnify(__('Subtotal'),config('settings.cashier_currency')." ".$this->order->order_price,60,40,32)));
            $this->printer->setEmphasis(false);
            $this->printer->feed();
            $this->printer->setEmphasis(true);
            $this->printer->feed();

            if($this->order->discount>0){
                $this->printer->text(rtrim($this->columnify(__('Discount'),config('settings.cashier_currency')." ".$this->order->discount,60,40,32)));
                $this->printer->setEmphasis(false);
                $this->printer->feed();
                $this->printer->setEmphasis(true);
                $this->printer->feed();
            }

            if($this->order->delivery_price>0){
                $this->printer->text(rtrim($this->columnify(__('Delivery'),config('settings.cashier_currency')." ".$this->order->delivery_price,60,40,32)));
                $this->printer->setEmphasis(false);
                $this->printer->feed();
                $this->printer->setEmphasis(true);
                $this->printer->feed();
            }


            $this->printer->text(rtrim($this->columnify(__('Total'),config('settings.cashier_currency')." ". ($this->order->delivery_price+$this->order->order_price_with_discount),60,40,32)));
            $this->printer->setEmphasis(false);
            $this->printer->feed();
        }

        private function printQR(){
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->qrCode($this->order->id,Printer::QR_ECLEVEL_L,8);
            $this->printer->feed();
        }
        
        public function columnify($leftCol, $rightCol, $leftWidthPercent, $rightWidthPercent, $char_per_line=32,$space = 2)
        {
        
            $leftWidth = $char_per_line * $leftWidthPercent / 100;
            $rightWidth = $char_per_line * $rightWidthPercent / 100;

            $leftWrapped = wordwrap($leftCol, $leftWidth, "\n", true);
            $rightWrapped = wordwrap($rightCol, $rightWidth, "\n", true);

            $leftLines = explode("\n", $leftWrapped);
            $rightLines = explode("\n", $rightWrapped);
            $allLines = array();
            for ($i = 0; $i < max(count($leftLines), count($rightLines)); $i++) {
                $leftPart = str_pad(isset($leftLines[$i]) ? $leftLines[$i] : '', $leftWidth, ' ');
                $rightPart = str_pad(isset($rightLines[$i]) ? $rightLines[$i] : '', $rightWidth, ' ');
                $allLines[] = $leftPart . str_repeat(' ', $space) . $rightPart;
            }
            

            if (!defined('PHP_VERSION_ID')) {
                $version = explode('.', PHP_VERSION);
            
                define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
            }

            $imploded=implode("\n",$allLines) . "\n";
            return $imploded;

        
            
        }

        

        private function printLine(){
            $this->printer->text("___________________________");
            $this->printer->feed();
        }
    }
