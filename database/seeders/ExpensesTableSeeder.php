<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Expenses\Models\Expenses;

class ExpensesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name'=>'Suppliers','code'=>'C1'],
            ['name'=>'Utilities','code'=>'C2'],
        ];
        foreach ($categories as $key => $category) {
            DB::table('expenses_category')->insert([
                'name'=>$category['name'],
                'code'=>$category['code'],
                'restaurant_id'=>1
            ]);
        }

        $vendors = [
            ['name'=>'Supplier 1','code'=>'S1' ],
            ['name'=>'Supplier 2','code'=>'S2'],
            ['name'=>'Supplier 3','code'=>'S3'],
        ];
        foreach ($vendors as $key => $vendors) {
            DB::table('expenses_vendor')->insert([
                'name'=>$vendors['name'],
                'code'=>$vendors['code'],
                'restaurant_id'=>1
            ]);
        }

        //Expenses
        $faker = Factory::create();
        for ($i=0; $i < 20; $i++) { 
            
            DB::table('expenses')->insert([
                'date'=>$faker->dateTimeBetween('-2 months', 'now'),
                'reference'=>'EXP'.$i,
                'restaurant_id'=>1,
                'amount'=>$faker->numberBetween(10, 50),
                'expenses_category_id'=>$faker->numberBetween(1, 2),
                'expenses_vendor_id'=>$faker->numberBetween(1, 3),
                'updated_at' => now(),
                'created_at' => now(),
            ]);
        }
        


        
    }
}
