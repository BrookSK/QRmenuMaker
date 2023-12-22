<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pages')->insert([
            'title' => 'Terms and conditions',
            'content' => 'Your terms and conditions goes here',
            'showAsLink' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pages')->insert([
            'title' => 'Privacy Policy',
            'content' => 'Your privacy policy content goes here',
            'showAsLink' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}
