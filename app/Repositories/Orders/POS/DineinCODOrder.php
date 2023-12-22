<?php

namespace App\Repositories\Orders\POS;
use App\Repositories\Orders\POSOrderRepository;
use App\Traits\Payments\HasCOD;
use App\Traits\Expedition\HasDineIn;

class DineinCODOrder extends POSOrderRepository
{
    use HasDineIn;
    use HasCOD;
}