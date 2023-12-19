<?php

namespace App\Repositories\Orders\POS;
use App\Repositories\Orders\POSOrderRepository;
use App\Traits\Payments\HasLinkPayment;
use App\Traits\Expedition\HasDelivery;

class DeliveryLinkPaymentOrder extends POSOrderRepository
{
    use HasDelivery;
    use HasLinkPayment;
}