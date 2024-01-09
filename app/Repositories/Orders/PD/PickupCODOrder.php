<?php

namespace App\Repositories\Orders\PD;
use App\Repositories\Orders\PDOrderRepository;
use App\Traits\Payments\HasCOD;
use App\Traits\Expedition\HasPickup;

class PickupCODOrder extends PDOrderRepository
{
    use HasPickup;
    use HasCOD;
}