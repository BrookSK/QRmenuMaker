<?php

namespace App\Repositories\Orders\PD;
use App\Repositories\Orders\PDOrderRepository;
use App\Traits\Payments\HasCOD;
use App\Traits\Expedition\HasSimpleDelivery;

class DeliveryCODOrder extends PDOrderRepository
{
    use HasSimpleDelivery;
    use HasCOD;
}