<?php

namespace Modules\PrintTempl\Database\Seeds;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class PrintTemplDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");

        Model::reguard();
    }
}
