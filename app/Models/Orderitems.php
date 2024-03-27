<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTranslations;

class Orderitems extends Posts
{
    protected $table="order_has_items";

    protected $fillable = [
        'order_id',
        'item_id',
        'quantity',
        'price',
        'total',
        'created_at',
        'updated_at'
    ];
}
