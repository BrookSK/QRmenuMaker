<?php

namespace App\Exports;

use App\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientsExport implements FromArray, WithHeadings
{
    protected $clients;

    public function headings(): array
    {
        return [
            'client_name',
            'client_id',
            'created'
        ];
    }

    public function __construct(array $clients)
    {
        $this->clients = $clients;
    }

    public function array(): array
    {
        return $this->clients;
    }
}
