<?php

namespace App\Repositories\Orders\PD;
use App\Repositories\Orders\PDOrderRepository;
use App\Traits\Payments\HasLinkPayment;
use App\Traits\Expedition\HasPickup;

class PickupLinkPaymentOrder extends PDOrderRepository
{
    use HasPickup;
    use HasLinkPayment;
}