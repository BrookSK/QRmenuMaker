<?php

namespace App\Repositories\Orders\WebService;
use App\Repositories\Orders\WebServiceOrderRepository;
use App\Traits\Payments\HasLinkPayment;
use App\Traits\Expedition\HasDelivery;

class DeliveryLinkPaymentOrder extends WebServiceOrderRepository
{
    use HasDelivery;
    use HasLinkPayment;
}