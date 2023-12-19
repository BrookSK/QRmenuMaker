<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses_category', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->unsignedBigInteger('restaurant_id')->nullable();
            $table->foreign('restaurant_id')->references('id')->on('restorants');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('expenses_vendor', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->unsignedBigInteger('restaurant_id')->nullable();
            $table->foreign('restaurant_id')->references('id')->on('restorants');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('reference');
            $table->text('description');
            
            $table->float('amount',8,2);
            $table->unsignedBigInteger('restaurant_id')->nullable();
            $table->foreign('restaurant_id')->references('id')->on('restorants');

            $table->unsignedBigInteger('expenses_category_id')->nullable();
            $table->foreign('expenses_category_id')->references('id')->on('expenses_category');

            $table->unsignedBigInteger('expenses_vendor_id')->nullable();
            $table->foreign('expenses_vendor_id')->references('id')->on('expenses_vendor');


            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expenses_vendor');
        Schema::dropIfExists('expenses_category');
    }
}
