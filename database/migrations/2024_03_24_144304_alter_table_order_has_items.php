<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOrderHasItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_has_items', function (Blueprint $table) {
            $table->boolean('is_item_fulfilled')->default(0)->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_has_items', function (Blueprint $table) {
            $table->dropColumn('is_item_fulfilled');
        });
    }
}
