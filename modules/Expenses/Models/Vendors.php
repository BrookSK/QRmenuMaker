<?php

namespace Modules\Expenses\Models;

use App\Scopes\RestaurantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendors extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table="expenses_vendor";
    protected $fillable = [];
    protected $guarded=[];

    protected static function booted(){
        static::addGlobalScope(new RestaurantScope);

        static::creating(function ($model){
           $restaurant_id=session('restaurant_id',null);
            if($restaurant_id){
                $model->restaurant_id=$restaurant_id;
            }
        });
    }

   
}
