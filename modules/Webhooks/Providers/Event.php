<?php

namespace Modules\Webhooks\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Webhooks\Listeners\WebhookOrder;

class Event extends ServiceProvider
{
    protected $listen = [];

    protected $subscribe = [
        WebhookOrder::class,
    ];
}