<?php

namespace App\Repositories\Orders\POS;
use App\Repositories\Orders\POSOrderRepository;
use App\Traits\Payments\HasCOD;
use App\Traits\Expedition\HasDelivery;

class DeliveryCODOrder extends POSOrderRepository
{
    use HasDelivery;
    use HasCOD;
}