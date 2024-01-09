<?php

namespace App\Repositories\Orders\SocialDrive;
use App\Repositories\Orders\SocialDriveOrderRepository;
use App\Traits\Payments\HasLinkPayment;
use App\Traits\Expedition\HasComplexDelivery;

class DeliveryLinkPaymentOrder extends SocialDriveOrderRepository
{
    use HasComplexDelivery;
    use HasLinkPayment;
}