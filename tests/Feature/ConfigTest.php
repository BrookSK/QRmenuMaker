<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Restorant as Vendor;
use App\User;

class ConfigTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    
    /** @test */
    public function config_set_on_a_vendor(){

        //The user
        $user = User::create([
            'name'=>$this->faker->firstName()
        ]);

        $vendor = Vendor::create([
            'name' => $this->faker->firstName(),
            'user_id'=>$user->id,
            'subdomain'=>'test'
        ]);
        
        $vendorTwo = Vendor::create([
            'name' => $this->faker->firstName(),
            'user_id'=>$user->id,
            'subdomain'=>'test2'
        ]);
        
        //Single insert
        $vendor->setConfig("paypal_id","some_amazing_id");
        $this->assertEquals($vendor->getConfig("paypal_id"), "some_amazing_id");


        //Same key on another vendor
        $vendorTwo->setConfig("paypal_id","another");
        $this->assertEquals($vendorTwo->getConfig("paypal_id"), "another");

        //Multiple set
        $vendor->setMultipleConfig(['paypal_id'=>"some_amazing_id_updated",'paypal_secret'=>"212121212121212"]);
        $this->assertEquals($vendor->getConfig("paypal_secret"), "212121212121212");
        
        //Check update
        $this->assertEquals($vendor->getConfig("paypal_id"), "some_amazing_id_updated");

        //Get all configs
        $this->assertEquals($vendor->getAllConfigs(), ['paypal_id'=>"some_amazing_id_updated",'paypal_secret'=>"212121212121212"]);
        $this->assertEquals($vendorTwo->getAllConfigs(), ['paypal_id'=>"another"]);
        
    }

    
}
