<?php

namespace Modules\Expenses\Models;

use App\Scopes\RestaurantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expenses extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table="expenses";
    protected $fillable = [];
    protected $guarded=[];

    public function category()
    {
        return $this->hasOne(Categories::class, 'id', 'expenses_category_id');
    }

    public function vendor()
    {
        return $this->hasOne(Vendors::class, 'id', 'expenses_vendor_id');
    }


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
