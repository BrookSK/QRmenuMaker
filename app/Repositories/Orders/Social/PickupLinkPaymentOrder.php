<?php

namespace App\Repositories\Orders\Social;
use App\Repositories\Orders\SocialOrderRepository;
use App\Traits\Payments\HasLinkPayment;
use App\Traits\Expedition\HasPickup;

class PickupLinkPaymentOrder extends SocialOrderRepository
{
    use HasPickup;
    use HasLinkPayment;
}