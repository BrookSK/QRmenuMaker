<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class RestorantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $restaurant_id=session('restaurant_id',null);
        if($restaurant_id){
            $builder->where('restorant_id', $restaurant_id);
        }
        \App\Services\ConfChanger::switchCurrencyDirectly(session('restaurant_currency',""),session('restaurant_convertion',1));
        
    }
}