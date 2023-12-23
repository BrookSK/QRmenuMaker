<?php

namespace App\Repositories\Orders\Social;
use App\Repositories\Orders\SocialOrderRepository;
use App\Traits\Payments\HasLinkPayment;
use App\Traits\Expedition\HasSimpleDelivery;

class DeliveryLinkPaymentOrder extends SocialOrderRepository
{
    use HasSimpleDelivery;
    use HasLinkPayment;
}