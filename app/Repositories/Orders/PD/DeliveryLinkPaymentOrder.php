<?php

namespace App\Repositories\Orders\PD;
use App\Repositories\Orders\PDOrderRepository;
use App\Traits\Payments\HasLinkPayment;
use App\Traits\Expedition\HasSimpleDelivery;

class DeliveryLinkPaymentOrder extends PDOrderRepository
{
    use HasSimpleDelivery;
    use HasLinkPayment;
}