<?php

namespace Modules\PrintNode\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\PrintNode\Listeners\PrintOnOrder;

class Event extends ServiceProvider
{
    protected $listen = [];

    protected $subscribe = [
        PrintOnOrder::class,
    ];
}