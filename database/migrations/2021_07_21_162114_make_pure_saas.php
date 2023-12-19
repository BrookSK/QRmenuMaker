<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakePureSaas extends Migration
{
    private $tables=['banners','cart_storage','cities','coupons','expenses','expenses_category','expenses_vendor','variants_has_extras','variants','extras','options','order_has_items','items','localmenus','order_has_status','ratings','orders','paths','visits','tables','restoareas','simple_delivery_areas','sms_verifications','status','address','categories'];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(config('settings.makePureSaaS',false)){
            foreach ($this->tables as $key => $table) {
                Schema::dropIfExists($table);
            }
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
