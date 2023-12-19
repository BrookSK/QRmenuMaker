<?php

namespace App\Repositories\Orders\Local;
use App\Repositories\Orders\LocalOrderRepository;
use App\Traits\Payments\HasLinkPayment;
use App\Traits\Expedition\HasDineIn;

class DineinLinkPaymentOrder extends LocalOrderRepository
{
    use HasDineIn;
    use HasLinkPayment;
}