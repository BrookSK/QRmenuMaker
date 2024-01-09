<?php

namespace App\Repositories\Orders\Local;
use App\Repositories\Orders\LocalOrderRepository;
use App\Traits\Payments\HasCOD;
use App\Traits\Expedition\HasSimpleDelivery;

class DeliveryCODOrder extends LocalOrderRepository
{
    use HasSimpleDelivery;
    use HasCOD;
}