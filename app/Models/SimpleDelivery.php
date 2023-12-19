<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimpleDelivery extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'simple_delivery_areas';

    public function getPriceFormated()
    {
       return ucfirst($this->name) . ' ' . money($this->cost,config('settings.cashier_currency'), config('settings.do_convertion'))->format();
    }
}
