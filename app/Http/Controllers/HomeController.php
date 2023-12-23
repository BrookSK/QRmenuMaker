<?php

namespace App\Http\Controllers;

use App\Items;
use App\Order;
use App\Restorant;
use App\User;
use Carbon\Carbon;
use DB;
use Spatie\Permission\Models\Role;
use Akaunting\Module\Facade as Module;
use Modules\Expenses\Models\Expenses;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private  function driverInfo(){
        $driver = auth()->user();

             //Today paid orders
            $today=Order::where(['driver_id'=>$driver->id])->where('payment_status','paid')->where('created_at', '>=', Carbon::today());
        
            //Week paid orders
            $week=Order::where(['driver_id'=>$driver->id])->where('payment_status','paid')->where('created_at', '>=', Carbon::now()->startOfWeek());

            //This month paid orders
            $month=Order::where(['driver_id'=>$driver->id])->where('payment_status','paid')->where('created_at', '>=', Carbon::now()->startOfMonth());

            //Previous month paid orders 
            $previousmonth=Order::where(['driver_id'=>$driver->id])->where('payment_status','paid')->where('created_at', '>=',  Carbon::now()->subMonth(1)->startOfMonth())->where('created_at', '<',  Carbon::now()->subMonth(1)->endOfMonth());


            //This user driver_percent_from_deliver
            $driver_percent_from_deliver=intval(auth()->user()->getConfig('driver_percent_from_deliver',config('settings.driver_percent_from_deliver')))/100;

            $earnings = [
                'today'=>[
                    'orders'=>$today->count(),
                    'earning'=>$today->sum('delivery_price')*$driver_percent_from_deliver,
                    'icon'=>'bg-gradient-red'
                ],
                'week'=>[
                    'orders'=>$week->count(),
                    'earning'=>$week->sum('delivery_price')*$driver_percent_from_deliver,
                    'icon'=>'bg-gradient-orange'
                ],
                'month'=>[
                    'orders'=>$month->count(),
                    'earning'=>$month->sum('delivery_price')*$driver_percent_from_deliver,
                    'icon'=>'bg-gradient-green'
                ],
                'previous'=>[
                    'orders'=>$previousmonth->count(),
                    'earning'=>$previousmonth->sum('delivery_price')*$driver_percent_from_deliver,
                    'icon'=>'bg-gradient-info'
                ]
            ];

            return view('dashboard', [
                'earnings' => $earnings
            ]);
    }

    public function pureSaaSIndex($lang=null){
        $locale = Cookie::get('lang') ? Cookie::get('lang') : config('settings.app_locale');
        if ($lang!=null) {
            //this is language route
            $locale = $lang;
        }
        if($locale!="android-chrome-256x256.png"){
            App::setLocale(strtolower($locale));
            session(['applocale_change' => strtolower($locale)]);
        }

        $dataToDisplay=[];
        $response = new \Illuminate\Http\Response(view('dashboard_pure', $dataToDisplay));
        $response->withCookie(cookie('lang', $locale, 120));
        App::setLocale(strtolower($locale));

        return $response;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index($lang=null)
    {
        if (config('settings.makePureSaaS',false)) {
            return $this->pureSaaSIndex($lang);
        }

        $locale = Cookie::get('lang') ? Cookie::get('lang') : config('settings.app_locale');
        if ($lang!=null) {
            //this is language route
            $locale = $lang;
            
        }
        if($locale!="android-chrome-256x256.png"){
            App::setLocale(strtolower($locale));
            session(['applocale_change' => strtolower($locale)]);
        }
       
        if (auth()->user()->hasRole('owner')||auth()->user()->hasRole('staff')) {
            \App\Services\ConfChanger::switchCurrency(auth()->user()->restorant);
        }
        

        $last30days=Carbon::now()->subDays(30);

        //Driver
        if (auth()->user()->hasRole('driver')) {
            return $this->driverInfo();            
        }elseif (auth()->user()->hasRole('client')) {
            return redirect()->route('orders.index');
        }else if (auth()->user()->hasRole('admin')&&config('app.isft')){
            //Admin in FT
            $last30daysDeliveryFee = Order::all()->where('created_at', '>', $last30days)->where('payment_status','paid')->sum('delivery_price');
            $last30daysStaticFee = Order::all()->where('created_at', '>', $last30days)->where('payment_status','paid')->sum('static_fee');
            $last30daysDynamicFee = Order::all()->where('created_at', '>', $last30days)->where('payment_status','paid')->sum('fee_value');
            $last30daysTotalFee = DB::table('orders')
                                ->select(DB::raw('SUM(delivery_price + static_fee + fee_value) AS sumValue'))
                                ->where('created_at', '>', $last30days)
                                ->where('payment_status','paid')
                                ->value('sumValue');
        }else{
            $last30daysDeliveryFee = 0;
            $last30daysStaticFee = 0;
            $last30daysDynamicFee = 0;
            $last30daysTotalFee = 0;
        }

        

        $doWeHaveExpensesApp=false; // Be default for other don't enable expenses

        if (auth()->user()->hasRole('staff')) {
            if(in_array("poscloud", config('global.modules',[]))){
                //Redirect to POS
                return redirect()->route('poscloud.index');
            }else{
                 //Redirect to Orders
                return redirect()->route('orders.index');
            }
           
        }
        if (auth()->user()->hasRole('manager')) {
            return redirect()->route('admin.restaurants.index');
        }
        if(isset($_GET['page'])){

        }
        else  if (! config('app.ordering')) {
            if (auth()->user()->hasRole('owner')) {
                return redirect()->route('admin.restaurants.edit', auth()->user()->restorant->id);
            } elseif (auth()->user()->hasRole('admin')) {
                return redirect()->route('admin.restaurants.index');
            }
        }

        $expenses=[
            'costValue'=>[]
        ];

        $months = [
            1 => __('Jan'),
            2 => __('Feb'),
            3 => __('Mar'),
            4 => __('Apr'),
            5 => __('May'),
            6 => __('Jun'),
            7 => __('Jul'),
            8 => __('Aug'),
            9 => __('Sep'),
            10 => __('Oct'),
            11 => __('Nov'),
            12 => __('Dec'),
        ];
        
        $last30daysOrders = Order::where('created_at', '>', $last30days)->count();
        $last30daysOrdersValue = Order::where('created_at', '>', $last30days)
        ->where('payment_status','paid')
        ->select(DB::raw('ROUND(SUM(order_price+delivery_price),2) as order_price'),DB::raw('SUM(delivery_price + static_fee + fee_value) AS total_fee'),DB::raw('SUM(delivery_price) AS total_delivery'),DB::raw('SUM(static_fee) AS total_static_fee'),DB::raw('SUM(fee_value) AS total_fee_value'))
        ->first()->toArray();

        $sevenMonthsDate = Carbon::now()->subMonths(6)->startOfMonth();
        $salesValueRaw=Order::where('created_at', '>', $sevenMonthsDate)
                ->where('payment_status','paid')
                ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
                ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'), 'asc')
                ->select(DB::raw('count(id) as totalPerMonth'),DB::raw('ROUND(SUM(order_price + delivery_price),2) AS sumValue'),DB::raw('MONTH(created_at) month'))
                ->get()->toArray();
        $monthsIds = array_map(function($o) { return $o['month'];}, $salesValueRaw);
        $salesValue = array_combine($monthsIds, $salesValueRaw);
        foreach ($salesValue as $key => &$sale) {
            $sale['monthName']=$months[$key];
        }

        //Expenses  - Owner only
        if (auth()->user()->hasRole('owner')&&Module::has('expenses')) {
            $doWeHaveExpensesApp=true;
            $last30daysCostValue = Expenses::where([['created_at', '>', $last30days]])->sum('amount');

            $expensesValueRaw=Expenses::where('created_at', '>', $sevenMonthsDate)
            ->groupBy(DB::raw('YEAR(date), MONTH(date)'))
            ->orderBy(DB::raw('YEAR(date), MONTH(date)'), 'asc')
            ->select(DB::raw('SUM(amount) AS costValue'),DB::raw('MONTH(date) month'))
            ->get()->toArray();

            $monthsIds = array_map(function($o) { return $o['month'];}, $expensesValueRaw);
            $costValue = array_combine($monthsIds, $expensesValueRaw);
            foreach ($costValue as $monthKey => $cost) {
                if(isset($salesValue[$monthKey])){
                    $salesValue[$monthKey]['costValue']=$cost['costValue'];
                }
            }


            //Cost per group
            $last30daysCostPerGroup = Expenses::where([['created_at', '>', $last30days]])
                ->groupBy('expenses_category_id')
                ->select('id','expenses_category_id',DB::raw('SUM(amount) AS amount'))->get();
            $last30daysCostPerGroupLabels=[];
            $last30daysCostPerGroupValues=[];
            foreach ($last30daysCostPerGroup as $key => $category) {
                array_push($last30daysCostPerGroupLabels,$category->category->name);
                array_push($last30daysCostPerGroupValues,$category->amount);
            }
        
            //Cost per vedor
            $last30daysCostPerVendor = Expenses::where([['created_at', '>', $last30days]])
            ->groupBy('expenses_vendor_id')
            ->select('id','expenses_vendor_id',DB::raw('SUM(amount) AS amount'))->get();

            $last30daysCostPerVendorLabels=[];
            $last30daysCostPerVendorValues=[];
            foreach ($last30daysCostPerVendor as $key => $vendor) {
                array_push($last30daysCostPerVendorLabels,$vendor->vendor->name);
                array_push($last30daysCostPerVendorValues,$vendor->amount);
            }

            $expenses=[
                'last30daysCostValue'=>$last30daysCostValue,
                'costValue'=>$costValue,
                'last30daysCostPerGroupLabels'=>$last30daysCostPerGroupLabels,
                'last30daysCostPerGroupValues'=>$last30daysCostPerGroupValues,
                'last30daysCostPerVendorLabels'=>$last30daysCostPerVendorLabels,
                'last30daysCostPerVendorValues'=>$last30daysCostPerVendorValues,
            ];   
        }

        $monthList=[];
        foreach ($salesValue as $key => $salerecord) {
           array_push($monthList,$salerecord['monthName']);
        }

        $availableLanguagesENV = config('settings.front_languages');
        $exploded = explode(',', $availableLanguagesENV);
        $availableLanguages = [];
        for ($i = 0; $i < count($exploded); $i += 2) {
            $availableLanguages[$exploded[$i]] = $exploded[$i + 1];
        }

        $countItems=0;
        if(auth()->user()->hasRole('admin')){
            $countItems=Restorant::count();
        }
        if(auth()->user()->hasRole('owner')){
            if(auth()->user()->restorant&&auth()->user()->restorant->categories){
                $countItems=Items::whereIn('category_id', auth()->user()->restorant->categories->pluck('id')->toArray())->whereNull('deleted_at')->count();
            }
        }

        $dataToDisplay=[
            'availableLanguages'=>$availableLanguages,
            'locale'=>$locale,
            'expenses'=>$expenses,
            'doWeHaveExpensesApp'=>$doWeHaveExpensesApp,
            'last30daysOrders' => $last30daysOrders,
            'last30daysOrdersValue'=> $last30daysOrdersValue,
            'allViews' => auth()->user()->hasRole('owner')?auth()->user()->restorant->views:Restorant::sum('views'),
            'salesValue' => $salesValue,
            'monthLabels' =>  $monthList,
            'countItems'=>$countItems,
            'last30daysDeliveryFee' =>  $last30daysDeliveryFee,
            'last30daysStaticFee' =>  $last30daysStaticFee,
            'last30daysDynamicFee' =>  $last30daysDynamicFee,
            'last30daysTotalFee' =>  $last30daysTotalFee
        ];
        
        $response = new \Illuminate\Http\Response(view('dashboard', $dataToDisplay));
        $response->withCookie(cookie('lang', $locale, 120));
        App::setLocale(strtolower($locale));

        return $response;
    }
}
