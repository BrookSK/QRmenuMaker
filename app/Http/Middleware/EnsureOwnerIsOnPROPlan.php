<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureOwnerIsOnPROPlan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
         // if he is logged in, check, if he is an admin
         if(config('settings.forceUserToPay',false)){
            if (Auth::user()->hasRole('owner')) {
                if(Auth::user()->mplanid()==intval(config('settings.free_pricing_id'))){
                    //User is on free plan, but the system doesn't allow free plan
                    return redirect(route('plans.current'))->withError(__('You need to subscribe to a plan'));
                }
            }
         }
         
        return $next($request);
    }
}
