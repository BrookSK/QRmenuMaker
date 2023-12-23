<?php

namespace App\Repositories\Orders\Local;
use App\Repositories\Orders\LocalOrderRepository;
use App\Traits\Payments\HasStripe;
use App\Traits\Expedition\HasSimpleDelivery;

class DeliveryStripeOrder extends LocalOrderRepository
{
    use HasSimpleDelivery;
    use HasStripe;
}