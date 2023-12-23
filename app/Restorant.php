<?php

namespace App;

use App\MyModel;
use App\Traits\HasConfig;
use willvincent\Rateable\Rateable;
use Spatie\OpeningHours\OpeningHours;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restorant extends MyModel
{
    use SoftDeletes;
    use Rateable;
    use HasConfig;

    protected $modelName="App\Restorant";
    protected $fillable = ['name', 'subdomain', 'user_id', 'lat', 'lng', 'address', 'phone', 'logo', 'description', 'city_id'];
    protected $appends = ['alias', 'logom', 'icon', 'coverm'];
    protected $imagePath = '/uploads/restorants/';
    protected $table="companies";

    protected $casts = [
        'radius' => 'array',
    ];

    protected $attributes = [
        'radius' => '{}',
    ];

    /**
     * Get the user that owns the restorant.
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function getAliasAttribute()
    {
        return $this->subdomain;
    }

    public function getPlanAttribute(){
        $planInfo=[
            'plan'=>null,
            "canMakeNewOrder"=>false,
            "canAddNewItems"=>false,
            "itemsMessage"=>"",
            "itemsAlertType"=>"success",
            "ordersMessage"=>"",
            "ordersAlertType"=>"success"
        ];

        //Find the plan
        $currentPlan = Plans::withTrashed()->find($this->user->mplanid());
        if($currentPlan==null){
            //Make artificial plan - usefull when migrating the system  - or wrong free plan id
            $currentPlan=new Plans();
            $currentPlan->name =__('No plan found');
            $currentPlan->price = 0;
            $currentPlan->limit_items = 0;
            $currentPlan->enable_ordering = 1;
            $currentPlan->limit_orders = 0;
            $currentPlan->period=1;
        }
        $planInfo['plan']=$currentPlan->toArray();
        

        //Count items
        $itemsCount = Items::whereIn('category_id', $this->categories->pluck('id')->toArray())->whereNull('deleted_at')->count();
        if ($currentPlan->limit_items != 0) {
           
            $allowedNewItems=$currentPlan->limit_items - $itemsCount;
            $planInfo['canAddNewItems']=$allowedNewItems > 0;
            if($allowedNewItems > 0){
                $planInfo['itemsMessage']=__('You can add')." ".$allowedNewItems." ".__('more items.');
                if($allowedNewItems < 10){
                    $planInfo['itemsAlertType']="warning";
                }
            }
            if($allowedNewItems < 1){
                $planInfo['itemsMessage']=__('You can not add more items. Please subscribe to new plan.');
                $planInfo['itemsAlertType']="danger";
            }
            
        }else{
            //Unlimited items
            $planInfo['itemsMessage']=__('You can add unlimited number of items');
            $planInfo['canAddNewItems']=true;
        }

        //Count orders
        //Period
        if($currentPlan->period==1){
            //Monthly - get start of month
            $period=Carbon::now()->startOfMonth();
        }else{
            //Yearly - get start iof year
            $period=Carbon::now()->startOfYear();
        }
        $orderCount=$this->orders->where('created_at','>=',$period)->count();
        
        if ($currentPlan->limit_orders != 0 && $currentPlan->enable_ordering==1) {
            $allowedNewOrders=$currentPlan->limit_orders - $orderCount;
           
            $planInfo['canMakeNewOrder']=$allowedNewOrders > 0;
            if($allowedNewOrders > 0){
                $planInfo['ordersMessage']=__('You can receive')." ".$allowedNewOrders." ".__('more orders.')." ".__('Total included in this plan').": ".$currentPlan->limit_orders;
                if($allowedNewOrders < 20){
                    $planInfo['ordersAlertType']="warning";
                }
            }
            if($allowedNewOrders < 1){
                $planInfo['ordersMessage']=__('You can not receive more orders. Please subscribe to new plan.');
                $planInfo['ordersAlertType']="danger";
            }
            
        }else{
            //Unlimited orders - if plan has ordering
            if($currentPlan->enable_ordering==1){
                //Has ordering
                $planInfo['ordersMessage']=__('You can receive unlimited number of orders');
                $planInfo['canMakeNewOrder']=true;
            }else{
                //Doesn't have ordering
                $planInfo['ordersMessage']=__('This plan does not allow ordering.');
                $planInfo['canMakeNewOrder']=false;
                $planInfo['ordersAlertType']="danger";
            }
           
        }

        $plugins=$currentPlan->getConfig('plugins',null);
        
        if($plugins){
            $planInfo['allowedPluginsPerPlan']=json_decode($plugins,false);
        }else{
            $planInfo['allowedPluginsPerPlan']=null;
        }
        

        return $planInfo;

    }

    public function getLinkAttribute()
    {
        if (config('settings.wildcard_domain_ready')){
            //As subdomain
           // return (isset($_SERVER['HTTPS'])&&$_SERVER["HTTPS"] ?"https://":"http://").$this->subdomain.".".str_replace($this->subdomain.".","",str_replace("www.","",$_SERVER['HTTP_HOST']));
            return str_replace('://', '://'.$this->subdomain.".", config('app.url',''));
        }else{
            //As link
            return route('vendor',$this->subdomain);
        }
    }

    public function getLogomAttribute()
    {
        return $this->getImge($this->logo, config('global.restorant_details_image'));
    }

    public function getLogowideAttribute()
    {
        return $this->getImge($this->getConfig('resto_wide_logo',null),'/default/restaurant_wide.png','_original.png' );
    }

    public function getLogowidedarkAttribute()
    {
        return $this->getImge($this->getConfig('resto_wide_logo_dark',null),'/default/restaurant_wide_dark.png','_original.png' );
    }

    public function getIconAttribute()
    {
        return $this->getImge($this->logo, str_replace('_large.jpg', '_thumbnail.jpg', config('global.restorant_details_image')), '_thumbnail.jpg');
    }

    public function getCovermAttribute()
    {
        //Template based
        $defaultCover=config('global.restorant_details_cover_image');
        if(config('settings.front_end_template',"")=="elegant-template"&&$defaultCover=="/default/cover.jpg"){
            $defaultCover="/default/cover_dark.jpg";
        }
        return $this->getImge($this->cover, $defaultCover, '_cover.jpg');
    }

    public function categories()
    {
        return $this->hasMany(\App\Categories::class, 'restorant_id', 'id')->where(['categories.active' => 1])->ordered();
    }

    public function localmenus()
    {
        return $this->hasMany(\App\Models\LocalMenu::class, 'restaurant_id', 'id');
    }

    public function hours()
    {
        return $this->hasMany(\App\Hours::class, 'restorant_id', 'id');
    }

    public function getBusinessHours(){

        $creationArray=[
            'monday'     => [],
            'tuesday'    => [],
            'wednesday'  => [],
            'thursday'   => [],
            'friday'     => [],
            'saturday'   => [],
            'sunday'     => [],
            'overflow' => true
        ];

        $dayKeys=array_keys($creationArray);
        
        //Get all working hours
        $workingHours=$this->hours()->get()->toArray();

        foreach ($workingHours as $key => $shift) {
            for ($i = 0; $i < 7; $i++) {
                $from = $i.'_from';
                $to = $i.'_to';
                if($shift[$from]&&$shift[$to]){
                    $toHour=date("H:i", strtotime($shift[$to]));
                    array_push($creationArray[$dayKeys[$i]],date("H:i", strtotime($shift[$from]))."-".$toHour);
                }
                
            }
        }
        
        //Set config based on restaurant
        config(['app.timezone' => $this->getConfig('time_zone',config('app.timezone'))]);


        $tz= $this->getConfig('time_zone',config('app.timezone'));



        $mergedRanges = OpeningHours::mergeOverlappingRanges($creationArray);
       

        //Get all working hours
        return OpeningHours::create($mergedRanges,$tz);
    }

    public function tables()
    {
        return $this->hasMany(\App\Tables::class, 'restaurant_id', 'id');
    }

    public function staff()
    {
        return $this->hasMany(\App\User::class, 'restaurant_id', 'id')->role('staff');
    }

    public function areas()
    {
        return $this->hasMany(\App\RestoArea::class, 'restaurant_id', 'id');
    }

    public function deliveryareas()
    {
        return $this->hasMany(\App\Models\SimpleDelivery::class, 'restaurant_id', 'id');
    }


    public function visits()
    {
        return $this->hasMany(\App\Visit::class, 'restaurant_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(\App\Order::class, 'restorant_id', 'id');
    }

    public function coupons()
    {
        return $this->hasMany(\App\Coupons::class, 'restaurant_id', 'id');
    }

    public static function boot()
    {
        parent::boot();
        self::deleting(function (self $restaurant) {
            if (config('settings.is_demo')) {
                return false; //In demo disable deleting
            } else {
                //Delete orders
                foreach ($restaurant->orders()->get() as $order) {
                    $order->delete();
                }

                return true;
            }
        });
    }
}
