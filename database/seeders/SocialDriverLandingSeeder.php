<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialDriverLandingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $posts = [
            ['post_type'=>'blog','link'=>'#','link_name'=>'{"en":"Learn more"}','title'=>'{"en":"The best drivers and taxi companies"}', 'description'=>'{"en":"We have partnered with more than 560 leading taxi companies and individual drivers to offer you the best possible experience. Finding your taxi now is lot easier."}', 'image'=>'https://i.imgur.com/JrkHJ9x.jpg'],
            ['post_type'=>'blog','link'=>'#','link_name'=>'{"en":"Request taxi now"}','title'=>'{"en":"Making taxi more social"}', 'description'=>'{"en":"We are changing how taxi work. By bringing it closer to theirs customers. Requesting a taxi via whatsapp, simple for both sides."}', 'image'=>'https://i.imgur.com/R7Oe2AB.jpg'], 
            ['post_type'=>'faq','title'=>'{"en":"How i get paid?"}', 'description'=>'{"en":"Client can pay you at the end of the ride with cash or with cart, PayPal or MercadoPago"}'], 
            ['post_type'=>'faq','title'=>'{"en":"Can I reject requested ride?"}', 'description'=>'{"en":"Yes, you can reject the ride based on your preferences. Client will be informed"}'], 
            ['post_type'=>'faq','title'=>'{"en":"I am individual driver. Can I use the Company plan?"}', 'description'=>'{"en":"Yes, you can subscribe to any plan offered."}'], 
        ];

        foreach ($posts as $key => $post) {
            DB::table('posts')->insert([
                'post_type' => $post['post_type'],
                'subtitle' => isset($post['subtitle'])?$post['subtitle']:"",
                'title' => $post['title'],
                'description' => $post['description'],
                'link' => isset($post['link'])?$post['link']:"",
                'link_name' => isset($post['link_name'])?$post['link_name']:"",
                'image' =>  isset($post['image'])?$post['image']:"",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


    }
}
