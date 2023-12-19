<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $logo = "";
        $darkLogo="";

        if(config('app.isqrsaas')){
            if(config('settings.is_whatsapp_ordering_mode')){
                $logo = "/default/logo_whatsapp_dark.png";
                $darkLogo = "/default/logo_whatsapp_white.png";
            }else if(config('app.issd')){
                $logo = "/default/logo_whatsapp_taxi_dark.png";
                $darkLogo = "/default/logo_whatsapp_taxi_white.png";
            }else if(config('settings.is_pos_cloud_mode')){
                $logo = "/default/logo_lionpos_dark.png";
                $darkLogo = "/default/logo_lionpos_white.png";
            }else if(config('settings.is_agris_mode')){
                $logo = "/default/agris_logo_dark.png";
                $darkLogo = "/default/agris_logo_white.png";
            }else{
                $logo = "/default/logo_qrzebra.png";
                $darkLogo = "/default/logo_qrzebra_white.png";
            }
        }else if(config('app.isft')){
            $logo = "/default/logo.png";
            $darkLogo = $logo;
        }

        DB::table('users')->insert([
            'name' => config('settings.admin_name'),
            'email' =>  config('settings.admin_email'),
            'password' => Hash::make(config('settings.admin_password')),
            'api_token' => Str::random(80),
            'email_verified_at' => now(),
            'phone' =>  '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        //Settings
        DB::table('settings')->insert([
            'site_name' => config('app.name'),
            'site_logo' =>  $logo,
            'site_logo_dark' => $darkLogo,
            'restorant_details_image' => '/default/restaurant_large.jpg',
            'restorant_details_cover_image' => config('settings.is_agris_mode')?'/default/agris_cover.jpeg':(config('app.issd')?'/taxi/bg.jpeg':'/default/cover.jpg'),
            'search' => '/default/cover.jpg',
            'description' => 'Food Delivery from best restaurants',
            'header_title' => 'Food Delivery from<br /><b>New York’s</b> Best Restaurants',
            'header_subtitle' => 'The meals you love, delivered with care',
            'created_at' => now(),
            'updated_at' => now(),
            'delivery'=> 0,
            'maps_api_key' => 'AIzaSyCZhq0g1x1ttXPa1QB3ylcDQPTAzp_KUgA',
            'mobile_info_title' => 'Download the food you love',
            'mobile_info_subtitle' => 'It`s all at your fingertips - the restaurants you love. Find the right food to suit your mood, and make the first bite last. Go ahead, download us.',
        ]);
    }
}
