<?php

namespace Modules\Pureadmindash\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Plans;
use App\Restorant;
use App\User;
use Carbon\Carbon;

class DashboardController extends Controller
{

    public function index()
    {
        if(auth()->user()->hasRole(['admin'])){
            return $this->asAdmin();
        }else{
            return null;
        }
    }
    
    public function asAdmin()
    {

        $data=[
            'total_users' => '',
            'users_this_month' => '',
            'total_paying_users'=> '',
            'total_paying_users_this_month' => '',
            'mrr' => 0,
            'customers'=>[],
            'arr'=>[],
        ];
        $startOfMonth=Carbon::now()->startOfMonth();

        //Count total users
        $users=User::role('owner');
        $data['total_users'] = $users->count();

        //Count total cards this month of the year
        $data['users_this_month'] = $users->where('created_at', '>=',$startOfMonth )->count();

        //Count  paying users given
        $usersPaing=User::role('owner')->where('plan_id', '!=',intval(config('settings.free_pricing_id')));
        $data['total_paying_users'] = $usersPaing->count();

        //Count total cards this month of the year
        $data['total_paying_users_this_month'] = $usersPaing->where('created_at', '>=',$startOfMonth )->count();
        $usersPaing=User::role('owner')->where('plan_id', '!=',intval(config('settings.free_pricing_id')));
        
        //Count  MRR
        $plansMonthly=Plans::where('id', '!=',intval(config('settings.free_pricing_id')))->where('period',1)->pluck('price','id')->toArray();
        $plansYearly=Plans::where('id', '!=',intval(config('settings.free_pricing_id')))->where('period',2)->pluck('price','id')->toArray();
        $plans=Plans::pluck('name','id')->toArray();

        foreach($usersPaing->get() as $user){
            if(isset($plansMonthly[$user->plan_id])){
                $data['mrr'] += $plansMonthly[$user->plan_id];
            }
            if(isset($plansYearly[$user->plan_id])){
                $data['mrr'] += $plansYearly[$user->plan_id]/12;
            }
            
        }
  
        $data['arr']=Money($data['mrr']*12,config('settings.cashier_currency'),config('settings.do_convertion'));
        $data['mrr']=Money($data['mrr'],config('settings.cashier_currency'),config('settings.do_convertion'));


        //Get last 5 customers
        $data['clients']=Restorant::orderBy('created_at','desc')->take(5)->get();
        $data['plans']=$plans;


        return $data;
    }
}