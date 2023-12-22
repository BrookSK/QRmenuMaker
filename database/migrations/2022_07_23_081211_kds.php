<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Kds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('kds_finished')->default(0);
        });
        Schema::table('order_has_items', function (Blueprint $table) {
            $table->integer('kds_finished')->default(0);
        });
        Schema::table('cart_storage', function (Blueprint $table) {
            $table->integer('kds_finished')->default(0);
        });
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
