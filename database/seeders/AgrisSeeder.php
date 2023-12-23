<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgrisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //INSERT FIELD
        $lastFielfId=DB::table('fields')->insertGetId([
            "name"=>"My first field",
            "image"=>"https://i.imgur.com/Poyhghp.png",
            "coordinates"=>"[[[22.113485794694583,41.51086784971196],[22.116261745874766,41.51192931279624],[22.119155822637538,41.50792662148967],[22.116350340062013,41.50690932348556],[22.113485794694583,41.51086784971196]]]",
            "area"=>130857.68,
            "crop_id"=> 8,
            "company_id"=> 1,
        ]);

        $anotherFielfId=DB::table('fields')->insertGetId([
            "name"=>"My second field",
            "image"=>"https://i.imgur.com/Poyhghp.png",
            "coordinates"=>"[[[22.11010452659289,41.50433660706673],[22.114226450348013,41.50597651873568],[22.117189083046867,41.5019570535365],[22.114655817406174,41.50169979926849],[22.11336771623266,41.501571171751635],[22.111779058118174,41.5020535236234],[22.11074857718009,41.50266449750367],[22.1098469063584,41.50407936225204],[22.11010452659289,41.50433660706673]]]",
            "area"=>176349.91,
            "crop_id"=> 181,
            "company_id"=> 1,
        ]);

        $posts = [
            ['type'=>"notestatus",'title'=>'{"en":"Just reported"}'],
            ['type'=>"notestatus",'title'=>'{"en":"Processing"}'],
            ['type'=>"notestatus",'title'=>'{"en":"Finished"}'],
            ['type'=>"notestatus",'title'=>'{"en":"Rejected"}'],
            ['type'=>"notetype",'title'=>'{"en":"Pest"}'],
            ['type'=>"notetype",'title'=>'{"en":"Harvest"}'],
            ['type'=>"notetype",'title'=>'{"en":"Fertilizer needed"}'],
        ];
        

        foreach ($posts as $key => &$post) {
            $postId=DB::table('posts')->insertGetId([
                'post_type' => $post['type'],
                'title' => $post['title'],
                'description' =>isset($post['description'])?$post['description']:"" ,
                'image' =>isset($post['image'])?$post['image']:null ,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $post['id']=$postId;
        }

        $lastNoteId=DB::table('fieldnotes')->insertGetId([
            "notetype_id"=>$posts[4]['id'],
            "notestatus_id"=>$posts[0]['id'],
            "field_id"=>$lastFielfId,
            'created_by'=>1,
            'assigned_to'=>1,
            "company_id"=>1,
            'uuid'=>"demonote",
            "lat"=>"41.51086784971196",
            "lng"=>"22.113485794694583",
            "title"=>"Snail found",
            "description"=>"The snails where found on the lower leafs. Please use something to remove them. You don't need to remove the damaged leafs",
            "image"=>"https://images.unsplash.com/photo-1570042707108-66761758315a?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=600&h=600&q=80",
            "is_public"=>1
        ]);


    }
}
