<?php

namespace App\Repositories\Orders\POS;
use App\Repositories\Orders\POSOrderRepository;
use App\Traits\Payments\HasLinkPayment;
use App\Traits\Expedition\HasPickup;

class PickupLinkPaymentOrder extends POSOrderRepository
{
    use HasPickup;
    use HasLinkPayment;
}