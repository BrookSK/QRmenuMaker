<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureSetupIsDone
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
        if (Auth::user()->hasRole('admin')) {
            if(file_exists(storage_path('verified'))){
                return $next($request);
            }else{
                return redirect(route('systemstatus'));
            }
        }
        
        return $next($request);
        
    }
}
