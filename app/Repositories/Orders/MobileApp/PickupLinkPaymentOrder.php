<?php

namespace App\Repositories\Orders\MobileApp;
use App\Repositories\Orders\MobileAppOrderRepository;
use App\Traits\Payments\HasLinkPayment;
use App\Traits\Expedition\HasPickup;

class PickupLinkPaymentOrder extends MobileAppOrderRepository
{
    use HasPickup;
    use HasLinkPayment;
}