<?php

namespace App\Repositories\Orders\POS;
use App\Repositories\Orders\POSOrderRepository;
use App\Traits\Payments\HasLinkPayment;
use App\Traits\Expedition\HasDineIn;

class DineinLinkPaymentOrder extends POSOrderRepository
{
    use HasDineIn;
    use HasLinkPayment;
}