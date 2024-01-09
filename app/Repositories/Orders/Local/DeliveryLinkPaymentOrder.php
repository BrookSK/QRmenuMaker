<?php

namespace App\Repositories\Orders\Local;
use App\Repositories\Orders\LocalOrderRepository;
use App\Traits\Payments\HasLinkPayment;
use App\Traits\Expedition\HasSimpleDelivery;

class DeliveryLinkPaymentOrder extends LocalOrderRepository
{
    use HasSimpleDelivery;
    use HasLinkPayment;
}