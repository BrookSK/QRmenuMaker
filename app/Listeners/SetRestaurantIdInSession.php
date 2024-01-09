<?php

namespace App\Listeners;

use App\Restorant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SetRestaurantIdInSession
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $vendor=Restorant::where('user_id', $event->user->id)->first();
        if($vendor){
            session(['restaurant_id' => $vendor->id]);
            session(['restaurant_currency' => $vendor->currency]);
            session(['restaurant_convertion' => $vendor->do_covertion]);
        }
        
    }
}
