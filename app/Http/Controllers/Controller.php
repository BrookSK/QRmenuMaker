<?php

namespace App\Http\Controllers;

use App\Restorant;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;
use Image;
use Carbon\Carbon;
use Akaunting\Module\Facade as Module;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param {String} folder
     * @param {Object} laravel_image_resource, the resource
     * @param {Array} versinos
     */
    public function saveImageVersions($folder, $laravel_image_resource, $versions,$return_full_url=false)
    {
        //Make UUID
        $uuid = Str::uuid()->toString();
       

        //If tinypng is set
        if(Module::has('tinypng')&&config('tinypng.enabled',false)){
            //Go with tiny png

            //Step 1 - upload file locally
            $path = $laravel_image_resource->store(null,'public_uploads');
            $url=config('app.url')."/uploads/restorants/".$path;

            //Build the post
            $dataToSend=[
                "source"=>[
                    "url"=>$url
                ]
            ];
            //Amazon S3
            if(strlen(config('tinypng.aws_access_key_id',"")>5)){
                $dataToSend['store']=array(
                    'service' => 's3',
                    'aws_access_key_id' => config('tinypng.aws_access_key_id',""),
                    'aws_secret_access_key' => config('tinypng.aws_secret_access_key',""),
                    'region' => config('tinypng.region',""),
                    'headers' =>  array(
                      'Cache-Control' => 'public, max-age=31536000',
                   ),
                    'path' => config('tinypng.bucket',"").'/'.$uuid.'.jpg',
                );
            }else if(strlen(config('tinypng.gcp_access_token',"")>5)){
                //Google Cloud
                $dataToSend['store']=array(
                    'service' => 'gcs',
                    'gcp_access_token' => config('tinypng.gcp_access_token',""),
                    'headers' => 
                   (object) array(
                      'Cache-Control' => 'public, max-age=31536000',
                   ),
                    'path' => config('tinypng.gcp_path',"").'/'.$uuid.'.jpg'
                );
            }

            $authCode=base64_encode("api:".config('tinypng.api_key'));
            $response = Http::withHeaders([
                'Authorization' => 'Basic '.$authCode,
                'Content-Type' => 'application/json'
            ])
            ->post('https://api.tinify.com/shrink',$dataToSend);

            //Delete the original file
            //Storage::disk('public_uploads')->delete($path);
            //dd('After submit');

            if($response->successful()){
                $url = $response->json()['output']['url'];
                $contents = file_get_contents($url);
                $name = substr($url, strrpos($url, '/') + 1);
                //dd( $url );
                Storage::disk('public_uploads')->put($name, $contents);
                return $url=config('app.url')."/uploads/restorants/".$name;
            }else{
                //dd($response->getBody()->getContents());
                abort(500,"There is problem with tinyPNG");
            }
        }else{
            //Regular upload
            

            //Make the versions
            foreach ($versions as $key => $version) {
                $ext="jpg";
                if(isset($version['type'])){
                    $ext=$version['type'];
                }

                //Save location
               $saveLocation=public_path($folder).$uuid.'_'.$version['name'].'.'.'jpg';
               if(strlen(config('settings.image_store_path'.'')>3)){
                $saveLocation=config('settings.image_store_path'.'').$uuid.'_'.$version['name'].'.'.'jpg';
               }

                if (isset($version['w']) && isset($version['h'])) {
                    $img = Image::make($laravel_image_resource->getRealPath())->fit($version['w'], $version['h']);
                    $img->save($saveLocation,100,$ext);
                } else {
                    //Original image
                    $img = Image::make($laravel_image_resource->getRealPath());
                    $img->save($saveLocation,100,$ext);
                }
            }

            if($return_full_url){
                return config('app.url')."/".$folder.$uuid.'_'.$version['name'].'.'.'jpg';
            }else{
                return $uuid;
            }

            
        }

        
    }

    private function withinArea($point, $polygon, $n)
    {
        if ($polygon[0] != $polygon[$n - 1]) {
            $polygon[$n] = $polygon[0];
        }
        $j = 0;
        $oddNodes = false;
        $x = $point->lng;
        $y = $point->lat;
        for ($i = 0; $i < $n; $i++) {
            $j++;
            if ($j == $n) {
                $j = 0;
            }
            if ((($polygon[$i]->lat < $y) && ($polygon[$j]->lat >= $y)) || (($polygon[$j]->lat < $y) && ($polygon[$i]->lat >= $y))) {
                if ($polygon[$i]->lng + ($y - $polygon[$i]->lat) / ($polygon[$j]->lat - $polygon[$i]->lat) * ($polygon[$j]->lng - $polygon[$i]->lng) < $x) {
                    $oddNodes = ! $oddNodes;
                }
            }
        }

        return $oddNodes;
    }

    public function calculateDistance($latitude1, $longitude1, $latitude2, $longitude2, $unit)
    {
        $theta = $longitude1 - $longitude2;
        $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $distance = $distance * 60 * 1.1515;
        switch ($unit) {
          case 'Mi':
            break;
          case 'K':
            $distance = $distance * 1.609344;
        }

        return round($distance, 2);
    }

    public function getAccessibleAddresses($restaurant, $addressesRaw)
    {
        $addresses = [];
        $polygon = json_decode(json_encode($restaurant->radius));
        $numItems = $restaurant->radius ? count($restaurant->radius) : 0;

        if ($addressesRaw) {
            foreach ($addressesRaw as $address) {
                $point = json_decode('{"lat": '.$address->lat.', "lng":'.$address->lng.'}');

                if (! array_key_exists($address->id, $addresses)) {
                    $new_obj = (object) [];
                    $new_obj->id = $address->id;
                    $new_obj->address = $address->address;

                    if (! empty($polygon)) {
                        if (isset($polygon[0]) && $this->withinArea($point, $polygon, $numItems)) {
                            $new_obj->inRadius = true;
                        } else {
                            $new_obj->inRadius = false;
                        }
                    } else {
                        $new_obj->inRadius = true;
                    }

                    $distance = floatval(round($this->calculateDistance($address->lat, $address->lng, $restaurant->lat, $restaurant->lng,config('settings.unit','K'))));

                    $rangeFound = false;
                    if (config('settings.enable_cost_per_distance')&&config('settings.enable_cost_per_range')) {
                        //Range based pricing

                        //Find the range
                        $ranges = [];

                        //Put the ranges
                        $ranges[0] = explode('-', config('settings.range_one'));
                        $ranges[1] = explode('-', config('settings.range_two'));
                        $ranges[2] = explode('-', config('settings.range_three'));
                        $ranges[3] = explode('-', config('settings.range_four'));
                        $ranges[4] = explode('-', config('settings.range_five'));

                        //Put the prices
                        $ranges[0][2] = floatval(config('settings.range_one_price'));
                        $ranges[1][2] = floatval(config('settings.range_two_price'));
                        $ranges[2][2] = floatval(config('settings.range_three_price'));
                        $ranges[3][2] = floatval(config('settings.range_four_price'));
                        $ranges[4][2] = floatval(config('settings.range_five_price'));

                        
                        //Find the range
                        foreach ($ranges as $key => $range) {
                            if (floatval($range[0]) <= $distance && floatval($range[1]) >= $distance) {
                                $rangeFound = true;
                                $new_obj->range=$range;
                                $new_obj->cost_per_km = floatval($range[2]);
                                $new_obj->cost_total = floatval($range[2]);
                            }
                        }
                        
                    }

                    if (! $rangeFound) {
                        if (config('settings.enable_cost_per_distance') && config('settings.cost_per_kilometer')) {
                            $new_obj->distance = floor($distance);
                            $new_obj->real_cost_m = floatval(config('settings.cost_per_kilometer'));
                            $new_obj->cost_per_km = floor($distance) * floatval(config('settings.cost_per_kilometer'));
                            $new_obj->cost_total = floor($distance)  * floatval(config('settings.cost_per_kilometer'));

                        } else {
                            //Use the static price for delivery
                            $new_obj->cost_per_km = config('global.delivery');
                            $new_obj->cost_total = config('global.delivery');
                        }
                    }

                    if($restaurant->free_deliver==1){
                        $new_obj->cost_per_km = 0;
                        $new_obj->cost_total = 0;
                    }

                    $new_obj->rangeFound=$rangeFound;

                    $addresses[$address->id] = (object) $new_obj;
                }
            }
        }
        return $addresses;
    }

    public function getRestaurant()
    {
        if (!auth()->user()->hasRole('owner')&&!auth()->user()->hasRole('staff')) {
            return null;
        }

        //If the owner hasn't set auth()->user()->restaurant_id set it now
        if (auth()->user()->hasRole('owner')){
            if(auth()->user()->restaurant_id==null){
                auth()->user()->restaurant_id=Restorant::where('user_id', auth()->user()->id)->first()->id;
                auth()->user()->update();
            }
            //Get restaurant for currerntly logged in user
            return Restorant::where('user_id', auth()->user()->id)->first();
        }else{
            //Staff
            return Restorant::findOrFail(auth()->user()->restaurant_id);
        }
        

        
    }

    public function ownerOnly()
    {
        if (! auth()->user()->hasRole('owner')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function adminOnly()
    {
        if (! auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function simple_replace_spec_char($subject) {
        $char_map = array(
        );
        return $subject;
        //return strtr($subject, $char_map);
    }

    public function replace_spec_char($subject) {
        $char_map = array(
            "ъ" => "-", "ь" => "-", "Ъ" => "-", "Ь" => "-",
            "А" => "A", "Ă" => "A", "Ǎ" => "A", "Ą" => "A", "À" => "A", "Ã" => "A", "Á" => "A", "Æ" => "A", "Â" => "A", "Å" => "A", "Ǻ" => "A", "Ā" => "A", "א" => "A",
            "Б" => "B", "ב" => "B", "Þ" => "B",
            "Ĉ" => "C", "Ć" => "C", "Ç" => "C", "Ц" => "C", "צ" => "C", "Ċ" => "C", "Č" => "C", "©" => "C", "ץ" => "C",
            "Д" => "D", "Ď" => "D", "Đ" => "D", "ד" => "D", "Ð" => "D",
            "È" => "E", "Ę" => "E", "É" => "E", "Ë" => "E", "Ê" => "E", "Е" => "E", "Ē" => "E", "Ė" => "E", "Ě" => "E", "Ĕ" => "E", "Є" => "E", "Ə" => "E", "ע" => "E",
            "Ф" => "F", "Ƒ" => "F",
            "Ğ" => "G", "Ġ" => "G", "Ģ" => "G", "Ĝ" => "G", "Г" => "G", "ג" => "G", "Ґ" => "G",
            "ח" => "H", "Ħ" => "H", "Х" => "H", "Ĥ" => "H", "ה" => "H",
            "I" => "I", "Ï" => "I", "Î" => "I", "Í" => "I", "Ì" => "I", "Į" => "I", "Ĭ" => "I", "I" => "I", "И" => "I", "Ĩ" => "I", "Ǐ" => "I", "י" => "I", "Ї" => "I", "Ī" => "I", "І" => "I",
            "Й" => "J", "Ĵ" => "J",
            "ĸ" => "K", "כ" => "K", "Ķ" => "K", "К" => "K", "ך" => "K",
            "Ł" => "L", "Ŀ" => "L", "Л" => "L", "Ļ" => "L", "Ĺ" => "L", "Ľ" => "L", "ל" => "L",
            "מ" => "M", "М" => "M", "ם" => "M",
            "Ñ" => "N", "Ń" => "N", "Н" => "N", "Ņ" => "N", "ן" => "N", "Ŋ" => "N", "נ" => "N", "ŉ" => "N", "Ň" => "N",
            "Ø" => "O", "Ó" => "O", "Ò" => "O", "Ô" => "O", "Õ" => "O", "О" => "O", "Ő" => "O", "Ŏ" => "O", "Ō" => "O", "Ǿ" => "O", "Ǒ" => "O", "Ơ" => "O",
            "פ" => "P", "ף" => "P", "П" => "P",
            "ק" => "Q",
            "Ŕ" => "R", "Ř" => "R", "Ŗ" => "R", "ר" => "R", "Р" => "R", "®" => "R",
            "Ş" => "S", "Ś" => "S", "Ș" => "S", "Š" => "S", "С" => "S", "Ŝ" => "S", "ס" => "S",
            "Т" => "T", "Ț" => "T", "ט" => "T", "Ŧ" => "T", "ת" => "T", "Ť" => "T", "Ţ" => "T",
            "Ù" => "U", "Û" => "U", "Ú" => "U", "Ū" => "U", "У" => "U", "Ũ" => "U", "Ư" => "U", "Ǔ" => "U", "Ų" => "U", "Ŭ" => "U", "Ů" => "U", "Ű" => "U", "Ǖ" => "U", "Ǜ" => "U", "Ǚ" => "U", "Ǘ" => "U",
            "В" => "V", "ו" => "V",
            "Ý" => "Y", "Ы" => "Y", "Ŷ" => "Y", "Ÿ" => "Y",
            "Ź" => "Z", "Ž" => "Z", "Ż" => "Z", "З" => "Z", "ז" => "Z",
            "а" => "a", "ă" => "a", "ǎ" => "a", "ą" => "a", "à" => "a", "ã" => "a", "á" => "a", "æ" => "a", "â" => "a", "å" => "a", "ǻ" => "a", "ā" => "a", "א" => "a",
            "б" => "b", "ב" => "b", "þ" => "b",
            "ĉ" => "c", "ć" => "c", "ç" => "c", "ц" => "c", "צ" => "c", "ċ" => "c", "č" => "c", "©" => "c", "ץ" => "c",
            "Ч" => "ch", "ч" => "ch",
            "д" => "d", "ď" => "d", "đ" => "d", "ד" => "d", "ð" => "d",
            "è" => "e", "ę" => "e", "é" => "e", "ë" => "e", "ê" => "e", "е" => "e", "ē" => "e", "ė" => "e", "ě" => "e", "ĕ" => "e", "є" => "e", "ə" => "e", "ע" => "e",
            "ф" => "f", "ƒ" => "f",
            "ğ" => "g", "ġ" => "g", "ģ" => "g", "ĝ" => "g", "г" => "g", "ג" => "g", "ґ" => "g",
            "ח" => "h", "ħ" => "h", "х" => "h", "ĥ" => "h", "ה" => "h",
            "i" => "i", "ï" => "i", "î" => "i", "í" => "i", "ì" => "i", "į" => "i", "ĭ" => "i", "ı" => "i", "и" => "i", "ĩ" => "i", "ǐ" => "i", "י" => "i", "ї" => "i", "ī" => "i", "і" => "i",
            "й" => "j", "Й" => "j", "Ĵ" => "j", "ĵ" => "j",
            "ĸ" => "k", "כ" => "k", "ķ" => "k", "к" => "k", "ך" => "k",
            "ł" => "l", "ŀ" => "l", "л" => "l", "ļ" => "l", "ĺ" => "l", "ľ" => "l", "ל" => "l",
            "מ" => "m", "м" => "m", "ם" => "m",
            "ñ" => "n", "ń" => "n", "н" => "n", "ņ" => "n", "ן" => "n", "ŋ" => "n", "נ" => "n", "ŉ" => "n", "ň" => "n",
            "ø" => "o", "ó" => "o", "ò" => "o", "ô" => "o", "õ" => "o", "о" => "o", "ő" => "o", "ŏ" => "o", "ō" => "o", "ǿ" => "o", "ǒ" => "o", "ơ" => "o",
            "פ" => "p", "ף" => "p", "п" => "p",
            "ק" => "q",
            "ŕ" => "r", "ř" => "r", "ŗ" => "r", "ר" => "r", "р" => "r", "®" => "r",
            "ş" => "s", "ś" => "s", "ș" => "s", "š" => "s", "с" => "s", "ŝ" => "s", "ס" => "s",
            "т" => "t", "ț" => "t", "ט" => "t", "ŧ" => "t", "ת" => "t", "ť" => "t", "ţ" => "t",
            "ù" => "u", "û" => "u", "ú" => "u", "ū" => "u", "у" => "u", "ũ" => "u", "ư" => "u", "ǔ" => "u", "ų" => "u", "ŭ" => "u", "ů" => "u", "ű" => "u", "ǖ" => "u", "ǜ" => "u", "ǚ" => "u", "ǘ" => "u",
            "в" => "v", "ו" => "v",
            "ý" => "y", "ы" => "y", "ŷ" => "y", "ÿ" => "y",
            "ź" => "z", "ž" => "z", "ż" => "z", "з" => "z", "ז" => "z", "ſ" => "z",
            "™" => "tm",
            "@" => "at",
            "Ä" => "ae", "Ǽ" => "ae", "ä" => "ae", "æ" => "ae", "ǽ" => "ae",
            "ĳ" => "ij", "Ĳ" => "ij",
            "я" => "ja", "Я" => "ja",
            "Э" => "je", "э" => "je",
            "ё" => "jo", "Ё" => "jo",
            "ю" => "ju", "Ю" => "ju",
            "œ" => "oe", "Œ" => "oe", "ö" => "oe", "Ö" => "oe",
            "щ" => "sch", "Щ" => "sch",
            "ш" => "sh", "Ш" => "sh",
            "ß" => "ss",
            "Ü" => "ue",
            "Ж" => "zh", "ж" => "zh",
        );
        return strtr($subject, $char_map);
    }

    public function makeAlias($name)
    {
        $name=$this->replace_spec_char($name);
        $name = str_replace(" ", "-", $name);
        //return strtolower(preg_replace('/[^A-Za-z0-9-]/', '', $name));
        return Str::slug($name, '');
    }

    public function scopeIsWithinMaxDistance($query, $latitude, $longitude, $radius = 25, $table = 'companies')
    {
        $haversine = "(6371 * acos(cos(radians($latitude))
                        * cos(radians(".$table.'.lat))
                        * cos(radians('.$table.".lng)
                        - radians($longitude))
                        + sin(radians($latitude))
                        * sin(radians(".$table.'.lat))))';

        return $query
           ->select(['name', 'id']) //pick the columns you want here.
           ->selectRaw("{$haversine} AS distance")
           ->whereRaw("{$haversine} < ?", [$radius])
           ->orderBy('distance');
    }

    public function getTimieSlots($vendor)
    {


        $tz=$vendor->getConfig('time_zone',config('app.timezone'));

        //Set config based on restaurant
        config(['app.timezone' => $tz]);

        $businessHours=$vendor->getBusinessHours();
        $now = new \DateTime('now', new \DateTimeZone($tz));
        if($businessHours->isClosed()){
            return [];
        }


         //Interval
         $intervalInMinutes = $vendor->getConfig('delivery_interval_in_minutes',config('settings.delivery_interval_in_minutes'));

        $from = Carbon::now()->setTimezone($tz)->diffInMinutes(Carbon::today()->setTimezone($tz)->startOfDay());

        $to = $this->getMinutes($businessHours->nextClose($now)->format('G:i'));



        if($from>$to){
            $to+=1440;
           
        }
       
        //To have clear interval
        $missingInterval = $intervalInMinutes - ($from % $intervalInMinutes); //21

        //Time to prepare the order in minutes
        $timeToPrepare = $vendor->getConfig('time_to_prepare_order_in_minutes',config('settings.time_to_prepare_order_in_minutes')); //30


        //First interval
        $from += $timeToPrepare <= $missingInterval ? $missingInterval : ($intervalInMinutes - (($from + $timeToPrepare) % $intervalInMinutes)) + $timeToPrepare;

        //Enlarge to, since that is not the delivery time
        $to+= $missingInterval+$intervalInMinutes+$timeToPrepare;

        $timeElements = [];
        for ($i = $from; $i <= $to; $i += $intervalInMinutes) {
            array_push($timeElements, $i%1440);
        }
        
        $slots = [];
        for ($i = 0; $i < count($timeElements) - 1; $i++) {
            array_push($slots, [$timeElements[$i], $timeElements[$i + 1]]);
        }

        //INTERVALS TO TIME
        $formatedSlots = [];
        for ($i = 0; $i < count($slots); $i++) {
            $key = $slots[$i][0].'_'.$slots[$i][1];
            $value = $this->minutesToHours($slots[$i][0]).' - '.$this->minutesToHours($slots[$i][1]);
            $formatedSlots[$key] = $value;
        }
        return $formatedSlots;
    }

    
    public function getMinutes($time)
    {
        $parts = explode(':', $time);

        return ((int) $parts[0]) * 60 + (int) $parts[1];
    }

    public function minutesToHours($numMun)
    {
        $h = (int) ($numMun / 60);
        $min = $numMun % 60;
        if ($min < 10) {
            $min = '0'.$min;
        }

        $time = $h.':'.$min;
        if (config('settings.time_format') == 'AM/PM') {
            $time = date('g:i A', strtotime($time));
        }

        return $time;
    }
}
