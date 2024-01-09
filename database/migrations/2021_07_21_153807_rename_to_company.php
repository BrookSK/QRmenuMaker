<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameToCompany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $oldName="restorants";
        $newName="companies";
        
        //Rename the table
        Schema::rename($oldName, $newName);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $newName="restorants";
        $oldName="companies";
        
        //Rename the table
        Schema::rename($oldName, $newName);
    }
}
