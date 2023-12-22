<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CompatibleWithStripe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('pm_type')->nullable();
            $table->string('pm_last_four', 4)->nullable();
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('stripe_price')->nullable();
        });

        Schema::table('subscription_items', function (Blueprint $table) {
            $table->string('stripe_product');
            $table->string('stripe_price');
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
