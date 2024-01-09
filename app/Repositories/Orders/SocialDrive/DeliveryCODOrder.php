<?php

namespace App\Repositories\Orders\SocialDrive;
use App\Repositories\Orders\SocialDriveOrderRepository;
use App\Traits\Payments\HasCOD;
use App\Traits\Expedition\HasComplexDelivery;

class DeliveryCODOrder extends SocialDriveOrderRepository
{
    use HasComplexDelivery;
    use HasCOD;
}