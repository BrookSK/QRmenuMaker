<?php

namespace App\Repositories\Orders\POS;
use App\Repositories\Orders\POSOrderRepository;
use App\Traits\Payments\HasCOD;
use App\Traits\Expedition\HasPickup;

class PickupCODOrder extends POSOrderRepository
{
    use HasPickup;
    use HasCOD;
}