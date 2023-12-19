<?php

namespace App;

use App\SmsVerification;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Spatie\Permission\Traits\HasRoles;
use Twilio\Rest\Client;
use App\Traits\HasConfig;
use Akaunting\Module\Facade as Module;
use App\Models\Config;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use Billable;
    use HasConfig;
    use SoftDeletes;

    protected $modelName="App\User";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'api_token', 'birth_date', 'working', 'lat', 'lng', 'numorders', 'rejectedorders','restaurant_id','expotoken',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    public function getAcceptanceratingAttribute()
    {
        if ($this->numorders == 0) {
            return 'No orders';
        } else {
            return round(((1 - ($this->rejectedorders / $this->numorders)) * 100), 2);
        }
    }


    public function drivercategories()
    {
        return $this->belongsToMany(\App\Models\Drivercategories::class,'user_has_categories','user_id', 'category_id');
    }

    public function getManagerVendors(){
        if(auth()->user()->hasRole('manager')){
            //Find all the vendors who have this email as their manager
           return Config::where('key','manager_email')->where('value',auth()->user()->email)->get()->pluck('model_id')->toArray();
        }else{
            return [];
        }
    }

    public function getExtraMenus(){
        $menus=[];
        if($this->hasRole('admin')){
            foreach (Module::all() as $key => $module) {
                if(is_array($module->get('adminmenus'))){
                    foreach ($module->get('adminmenus') as $key => $menu) {
                        if(isset($menu['onlyin'])){
                            if(config('app.'.$menu['onlyin'])){
                                array_push($menus,$menu);
                            }
                        }else{
                            array_push($menus,$menu);
                        }
                       
                    }
                }
            }
        }else if($this->hasRole('owner')){
            $allowedPluginsPerPlan = auth()->user()->restorant?auth()->user()->restorant->getPlanAttribute()['allowedPluginsPerPlan']:null;
            foreach (Module::all() as $key => $module) {
                if(is_array($module->get('ownermenus'))  &&  ($allowedPluginsPerPlan==null || in_array($module->get('alias'),$allowedPluginsPerPlan)) ){
                    foreach ($module->get('ownermenus') as $key => $menu) {
                       
                        if(isset($menu['onlyin'])){
                            if(config('app.'.$menu['onlyin'])){
                                array_push($menus,$menu);
                            }
                        }else{
                            array_push($menus,$menu);
                        }
                    }
                }
            }
        }
        else if($this->hasRole('staff')){
            foreach (Module::all() as $key => $module) {
                if(is_array($module->get('staffmenus'))){
                    foreach ($module->get('staffmenus') as $key => $menu) {
                       array_push($menus,$menu);
                    }
                }
            }
        }
        return $menus;
    }

    public function myDrivers(){
        if($this->hasRole('admin')){
            return User::role('driver')->where(['active'=>1])->whereNull('restaurant_id')->get();
        }else if($this->hasRole('owner')){
            //owner
            return User::role('driver')->where(['active'=>1,'restaurant_id'=>auth()->user()->restorant->id])->get();
        }else{
            return [];
        }
    }

    public function restorant()
    {
        if($this->hasRole('owner')){
            return $this->hasOne(\App\Restorant::class);
        }else{
            //staff
            return $this->hasOne(\App\Restorant::class,'id','restaurant_id');
        }
        
    }

    public function restaurant()
    {
        if($this->hasRole('owner')){
            return $this->hasOne(\App\Restorant::class);
        }else{
            //staff
            return $this->hasOne(\App\Restorant::class,'id','restaurant_id');
        }
    }

    public function restaurants()
    {
        return $this->hasMany(\App\Restorant::class);
    }

    

    public function plan()
    {
        return $this->hasOne(\App\Plans::class, 'id', 'plan_id');
    }

    public function mplanid()
    {
        return $this->plan_id ? $this->plan_id : intval(config('settings.free_pricing_id'));
    }

    public function addresses()
    {
        return $this->hasMany(\App\Address::class)->where(['address.active' => 1]);
    }

    public function paths()
    {
        return $this->hasMany(\App\Paths::class, 'user_id', 'id')->where('created_at', '>=', Carbon::now()->subHours(2));
    }

    public function orders()
    {
        return $this->hasMany(\App\Order::class, 'client_id', 'id');
    }

    public function driverorders()
    {
        return $this->hasMany(\App\Order::class, 'driver_id', 'id');
    }

    public function routeNotificationForExpo()
    {
        return $this->expotoken.""; //"ExponentPushToken[".$this->expotoken."]";
    }


    public function routeNotificationForOneSignal()
    {
        return ['include_external_user_ids' => [$this->id.'']];
    }

    public function routeNotificationForTwilio()
    {
        return $this->phone;
    }

    public function hasVerifiedPhone()
    {
        return ! is_null($this->phone_verified_at);
    }

    public function markPhoneAsVerified()
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function callToVerify()
    {
        $code = random_int(100000, 999999);
        $this->forceFill(['verification_code' => $code])->save();
        $client = new Client(config('settings.twilio_sid'), config('settings.twilio_auth_token'));
        $body = __('Hi').' '.$this->name.".\n\n".__('Your verification code is').': '.$code;
        $client->messages->create($this->phone, ['from' => config('settings.twilio_from'), 'body' => $body]);
    }

    public function setExpoToken($token){
        $this->expotoken=$token;
        $this->update();
    }

    public function removeUserPersonalData(){
        $this->email="removeduser".$this->id;
        $this->name="Removeduser".$this->id;
        $this->phone="";
        $this->expotoken="";
        $this->update();
    }


    public function setImpersonating($id)
    {
        Session::put('impersonate', $id);
    }

    public function stopImpersonating()
    {
        Session::forget('impersonate');
    }

    public function isImpersonating()
    {
        return Session::has('impersonate');
    }





}
