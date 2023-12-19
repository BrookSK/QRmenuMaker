<?php

namespace App\Http\Controllers;

use App\Traits\Fields;
use App\Items;
use App\Models\Variants;
use App\Order;
use App\Restorant;
use App\Tables;
use App\Plans;
use Carbon\Carbon;
use Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Services\ConfChanger;
use Akaunting\Module\Facade as Module;
use App\Models\CartStorageModel;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    use Fields;

    private function setSessionID($session_id){
        //We have session ID only from POS. So then use the CartDBStorageRepository
        config(['shopping_cart.storage' => \App\Repositories\CartDBStorageRepository::class]); 
        Cart::session($session_id);
    }

    public function add(Request $request)
    {
        if(isset($request->session_id)){
            $this->setSessionID($request->session_id);

            //Mark the POS order as not KDS finished
            if($row = CartStorageModel::find($request->session_id."_cart_items"))
            {
                $row->updateAttribute('kds_finished',0);
                $row->save();
            }
        }
    
        $item = Items::find($request->id);
        $restID = $item->category->restorant->id;

        $restaurant = Restorant::findOrFail($restID);
        \App\Services\ConfChanger::switchCurrency($restaurant);
        

        //Check if added item is from the same restorant as previus items in cart
        $canAdd = false;
        if (Cart::getContent()->isEmpty()) {
            $canAdd = true;
        } else {
            $canAdd = true;
            foreach (Cart::getContent() as $key => $cartItem) {
                if ($cartItem->attributes->restorant_id.'' != $restID.'') {
                    $canAdd = false;
                    break;
                }
            }
        }

        if ($item && $canAdd) {

            //are there any extras
            $cartItemPrice = $item->price;
            $cartItemName = $item->name;
            $theElement = '';

            //Is there a varaint

            //variantID
            if ($request->variantID) {
                //Get the variant
                $variant = Variants::findOrFail($request->variantID);

                //Validate is this variant is from the current item
                if ($variant->item->id == $item->id) {
                    $cartItemPrice = $variant->price;

                    //For each option, find the option on the
                    $cartItemName = $item->name.' '.$variant->optionsList;
                }
            }

            foreach ($request->extras as $key => $value) {
                $cartItemName .= "\n+ ".$item->extras()->findOrFail($value)->name;
                $cartItemPrice += $item->extras()->findOrFail($value)->price;
                $theElement .= $value.' -- '.$item->extras()->findOrFail($value)->name.'  --> '.$cartItemPrice.' ->- ';
            }

            Cart::add((new \DateTime())->getTimestamp(), $cartItemName, $cartItemPrice, $request->quantity, ['id'=>$item->id, 'variant'=>$request->variantID, 'extras'=>$request->extras, 'restorant_id'=>$restID, 'image'=>$item->icon, 'friendly_price'=>  Money($cartItemPrice, config('settings.cashier_currency'), config('settings.do_convertion'))->format()]);

            return response()->json([
                'status' => true,
                'errMsg' => $theElement,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errMsg' => __("You can't add items from other restaurant!"),
            ]);
        }
    }

    public function getContent()
    {

        //In this case, we need to use the cookies for storagee
        if(isset($_GET['session_id'])){
            $this->setSessionID($_GET['session_id']);
        }

        return response()->json([
            'data' => Cart::getContent(),
            'total' => Cart::getSubTotal(),
            'status' => true,
            'errMsg' => '',
        ]);
    }

    public function getContentPOS()
    {
        if(isset($_GET['session_id'])){
            $this->setSessionID($_GET['session_id']);
        }else{
            return response()->json([
                'status' => false,
                'errMsg' => 'Session is not started yet',
            ]);
        }
        

        $cs=CartStorageModel::where('id',$_GET['session_id']."_cart_items")->first();

        return response()->json([
            'data' => Cart::getContent(),
            'config'=> $cs?$cs->getAllConfigs():[],
            'id'=>$_GET['session_id'],
            'total' => Cart::getSubTotal(),
            'status' => true,
            'errMsg' => '',
        ]);
    }

    public function cart()
    {
        
        if(isset($_GET['session_id'])){
            $this->setSessionID($_GET['session_id']);
        }


        $fieldsToRender=[];
        if(strlen(config('global.order_fields'))>10){
            $fieldsToRender=$this->convertJSONToFields(json_decode(config('global.order_fields'),true)); 
        }
        $isEmpty = false;
        if (Cart::getContent()->isEmpty()) {
            $isEmpty = true;
        }

        if(!$isEmpty){
            //Cart is not empty
            $restorantID = null;
            foreach (Cart::getContent() as $key => $cartItem) {
                $restorantID = $cartItem->attributes->restorant_id;
                break;
            }

            

            //The restaurant
            $restaurant = Restorant::findOrFail($restorantID);

            //Set cover image to be the cover image of the restaurant
            config(['global.restorant_details_cover_image'=>$restaurant->coverm]);

            //Set config based on restaurant
            config(['app.timezone' => $restaurant->getConfig('time_zone',config('app.timezone'))]);

            

            $enablePayments=true;
            if(config('app.isqrsaas')){
                

                //In case, we use vendor defined Stripe, we need to check if keys are present
                if(config('settings.stripe_useVendor')){
                    if($restaurant->getConfig('stripe_enable')=="true"){
                        //We have stripe
                        config(['settings.enable_stripe' => true]);
                        config(['settings.stripe_key' => $restaurant->getConfig('stripe_key')]);
                        config(['settings.stripe_secret' => $restaurant->getConfig('stripe_secret')]);
                        config(['cashier.key' => $restaurant->getConfig('stripe_key')]);
                        config(['cashier.secret' => $restaurant->getConfig('stripe_secret')]);

                    }else{
                        //Stripe for this vendor is disabled
                        config(['settings.enable_stripe' => false]);
                    }
                }
            }

            //Change currency
            \App\Services\ConfChanger::switchCurrency($restaurant);

            //Create all the time slots
            $timeSlots = $this->getTimieSlots($restaurant);

           
            //user addresses
            $addresses = [];
            if (config('app.isft')) {
                $addresses = $this->getAccessibleAddresses($restaurant, auth()->user()->addresses->reverse());
            }

            $tables = Tables::where('restaurant_id', $restaurant->id)->get();
            $tablesData = [];
            foreach ($tables as $key => $table) {
                $tablesData[$table->id] = $table->full_name;
            }


            $extraPayments=[];
            foreach (Module::all() as $key => $module) {
                if($module->get('isPaymentModule')){
                    array_push($extraPayments,$module->get('alias'));
                }
            }
  
            $businessHours=$restaurant->getBusinessHours();
            $now = new \DateTime('now');

            $formatter = new \IntlDateFormatter(config('app.locale'), \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
            $formatter->setPattern(config('settings.datetime_workinghours_display_format_new'));
        
            //Table ID
            $tid = Cookie::get('tid') ? Cookie::get('tid') :null;
            if($tid==""){$tid=null;}
            $tables=['ftype'=>'select', 'name'=>'', 'id'=>'table_id', 'placeholder'=>'Select table', 'data'=>$tablesData, 'required'=>true];
            $tableName="";
            if($tid!=null){
                $tables['value']=$tid;
                $tableName=Tables::findOrFail($tid)->full_name;
            }

            $doWeHaveOrderAfterHours=Module::has('orderdatetime')&&$restaurant->getConfig('order_date_time_enable',false);

            if($doWeHaveOrderAfterHours&&count($timeSlots)==0){
                $timeSlots=[null];
            }
            $params = [
                'enablePayments'=>$enablePayments,
                'title' => 'Shopping Cart Checkout',
                'tables' =>  $tables,
                'restorant' => $restaurant,
                'timeSlots' => $timeSlots,
                'doWeHaveOrderAfterHours'=>$doWeHaveOrderAfterHours,
                'openingTime' => $businessHours->isClosed()?$formatter->format($businessHours->nextOpen($now)):null,
                'closingTime' => $businessHours->isOpen()?$formatter->format($businessHours->nextClose($now)):null,
                'addresses' => $addresses,
                'fieldsToRender'=>$fieldsToRender,
                'extraPayments'=>$extraPayments,
                'tid'=>$tid,
                'tableName'=>$tableName
            ];

            return view('cart')->with($params);
        }else{
            //Cart is empty
            if(config('app.isft')) {
                return redirect()->route('front')->withError('Your cart is empty!');
            }else{
                $previousOrders = Cookie::get('orders') ? Cookie::get('orders') : '';
                $previousOrderArray = array_filter(explode(',', $previousOrders));

                if(count($previousOrderArray) > 0){
                    foreach($previousOrderArray as $orderId){
                        $restorant = Order::where(['id'=>$orderId])->get()->first()->restorant;
                       
                        $restorantInfo = $this->getRestaurantInfo($restorant, $previousOrderArray);

                        return view('restorants.show', [
                            'restorant' => $restorantInfo['restorant'],
                            'openingTime' => $restorantInfo['openingTime'],
                            'closingTime' => $restorantInfo['closingTime'],
                            'usernames' => $restorantInfo['usernames'],
                            'canDoOrdering'=>$restorantInfo['canDoOrdering'],
                            'currentLanguage'=>$restorantInfo['currentLanguage'],
                            'showLanguagesSelector'=>$restorantInfo['showLanguagesSelector'],
                            'hasGuestOrders'=>$restorantInfo['hasGuestOrders'],
                            'fields'=>$restorantInfo['fields'],
                        ])->withError(__('Your cart is empty!'));
                    }
                }else{
                    return redirect()->route('front')->withError('Your cart is empty!');
                }                
            }
        }
    }

    public function getRestaurantInfo($restorant, $previousOrderArray)
    {
        //In QRsaas with plans, we need to check if they can add new items.
        $currentPlan = Plans::findOrFail($restorant->user->mplanid());
        $canDoOrdering = $currentPlan->enable_ordering == 1;

        //ratings usernames
        $usernames = [];
        if ($restorant && $restorant->ratings) {
            foreach ($restorant->ratings as $rating) {
                $user = User::where('id', $rating->user_id)->get()->first();

                if (! array_key_exists($user->id, $usernames)) {
                    $new_obj = (object) [];
                    $new_obj->name = $user->name;

                    $usernames[$user->id] = (object) $new_obj;
                }
            }
        }

        //Working hours
        $ourDateOfWeek = date('N') - 1;

        $format = 'G:i';
        if (config('settings.time_format') == 'AM/PM') {
            $format = 'g:i A';
        }

        //tables
        $tables = Tables::where('restaurant_id', $restorant->id)->get();
        $tablesData = [];
        foreach ($tables as $key => $table) {
            $tablesData[$table->id] = $table->restoarea ? $table->restoarea->name.' - '.$table->name : $table->name;
        }

        //Change Language
        ConfChanger::switchLanguage($restorant);

        //Change currency
        ConfChanger::switchCurrency($restorant);

        $currentEnvLanguage = isset(config('config.env')[2]['fields'][0]['data'][config('app.locale')]) ? config('config.env')[2]['fields'][0]['data'][config('app.locale')] : 'UNKNOWN';

        $businessHours=$restorant->getBusinessHours();
        $now = new \DateTime('now');

        $formatter = new \IntlDateFormatter(config('app.locale'), \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
        $formatter->setPattern(config('settings.datetime_workinghours_display_format_new'));

        return  [
            'restorant' => $restorant,
            'openingTime' => $businessHours->isClosed()?$formatter->format($businessHours->nextOpen($now)):null,
            'closingTime' => $businessHours->isOpen()?$formatter->format($businessHours->nextClose($now)):null,
            'usernames' => $usernames,
            'canDoOrdering'=>$canDoOrdering,
            'currentLanguage'=>$currentEnvLanguage,
            'showLanguagesSelector'=>env('ENABLE_MILTILANGUAGE_MENUS', false) && $restorant->localmenus()->count() > 1,
            'hasGuestOrders'=>count($previousOrderArray) > 0,
            'fields'=>[['class'=>'col-12', 'classselect'=>'noselecttwo', 'ftype'=>'select', 'name'=>'Table', 'id'=>'table_id', 'placeholder'=>'Select table', 'data'=>$tablesData, 'required'=>true]],
        ];
    }

    public function clear(Request $request)
    {
        if(isset($request->session_id)){
            $this->setSessionID($request->session_id);
        }

        //Get the client_id from address_id

        $oreder = new Order;
        $oreder->address_id = strip_tags($request->addressID);
        $oreder->restorant_id = strip_tags($request->restID);
        $oreder->client_id = auth()->user()->id;
        $oreder->driver_id = 2;
        $oreder->delivery_price = 3.00;
        $oreder->order_price = strip_tags($request->orderPrice);
        $oreder->comment = strip_tags($request->comment);
        $oreder->save();

        foreach (Cart::getContent() as $key => $item) {
            $oreder->items()->attach($item->id);
        }

        //Find first status id,
        Cart::clear();

        return redirect()->route('front')->withStatus(__('Cart clear.'));
    }

    /**
     * Create a new controller instance.

     *

     * @return void
     */
    public function remove(Request $request)
    {

        if(isset($request->session_id)){
            $this->setSessionID($request->session_id);
        }

        Cart::remove($request->id);

        return response()->json([
            'status' => true,
            'errMsg' => '',
        ]);
    }

    /**
     * Makes general api resonse.
     */
    private function generalApiResponse()
    {
        return response()->json([
            'status' => true,
            'errMsg' => '',
        ]);
    }

    /**
     * Updates cart.
     */
    private function updateCartQty($howMuch, $item_id)
    {
        if(isset($_GET['session_id'])){
            $this->setSessionID($_GET['session_id']);
        }

        Cart::update($item_id, ['quantity' => $howMuch]);

        return $this->generalApiResponse();
    }

    /**
     * Increase cart.
     */
    public function increase($id)
    {
        if(isset($_GET['session_id'])){
            $this->setSessionID($_GET['session_id']);
        }

        return $this->updateCartQty(1, $id);
    }

    /**
     * Decrese cart.
     */
    public function decrease($id)
    {
        if(isset($_GET['session_id'])){
            $this->setSessionID($_GET['session_id']);
        }
        
        return $this->updateCartQty(-1, $id);
    }
}
