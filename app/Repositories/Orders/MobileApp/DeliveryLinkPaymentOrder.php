<?php

namespace App\Repositories\Orders\MobileApp;
use App\Repositories\Orders\MobileAppOrderRepository;
use App\Traits\Payments\HasLinkPayment;
use App\Traits\Expedition\HasDelivery;

class DeliveryLinkPaymentOrder extends MobileAppOrderRepository
{
    use HasDelivery;
    use HasLinkPayment;
}