<?php

namespace App\Repositories\Orders\Local;
use App\Repositories\Orders\LocalOrderRepository;
use App\Traits\Payments\HasCOD;
use App\Traits\Expedition\HasDineIn;

class DineinCODOrder extends LocalOrderRepository
{
    use HasDineIn;
    use HasCOD;
}