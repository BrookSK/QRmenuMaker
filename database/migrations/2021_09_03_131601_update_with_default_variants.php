<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateWithDefaultVariants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('items')){
            Schema::table('items', function (Blueprint $table) {
                $table->integer('enable_system_variants')->default(0);
            });
        }

        if(Schema::hasTable('variants')){
            Schema::table('variants', function (Blueprint $table) {
                $table->integer('is_system')->default(0);
            });
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
