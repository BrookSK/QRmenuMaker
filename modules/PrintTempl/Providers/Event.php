<?php

namespace Modules\PrintTempl\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\PrintTempl\Listeners\PrintOnOrder;

class Event extends ServiceProvider
{
    protected $listen = [];

    protected $subscribe = [
        PrintOnOrder::class,
    ];
}