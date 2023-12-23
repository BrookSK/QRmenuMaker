<?php

namespace App\Repositories\Orders\Local;
use App\Repositories\Orders\LocalOrderRepository;
use App\Traits\Payments\HasLinkPayment;
use App\Traits\Expedition\HasPickup;

class PickupLinkPaymentOrder extends LocalOrderRepository
{
    use HasPickup;
    use HasLinkPayment;
}