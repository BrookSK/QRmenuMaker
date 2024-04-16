<?php

namespace App\Exports;

use App\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FinancesExport implements FromArray, WithHeadings
{
    protected $orders;

    public function headings(): array
    {
        return [
            __('Order ID'),
            __('Restaurant Name'),
            __('Restaurant ID'),
            __('Created At'),
            __('Last Status'),
            __('Client Name'),
            __('Client ID'),
            __('Address'),
            __('Address ID'),
            __('Driver Name'),
            __('Driver ID'),
            __('Payment Method'),
            __('Stripe Payment ID'),
            __('Restaurant Fee'),
            __('Order Fee'),
            __('Restaurant Static Fee'),
            __('Platform Fee'),
            __('Processor Fee'),
            __('Delivery'),
            __('Net Price with VAT'),
            __('Discount'),
            __('VAT'),
            __('Net Price'),
            __('Order Total'),
        ];
    }

    public function __construct(array $orders)
    {
        $this->orders = $orders;
    }

    public function array(): array
    {
        return $this->orders;
    }
}
