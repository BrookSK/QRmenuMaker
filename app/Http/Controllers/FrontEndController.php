<?php

namespace App\Http\Controllers;

use App\Banners;
use App\City;
use App\Items;
use App\Pages;
use App\Plans;
use App\Restorant;
use App\Categories;
use App\Settings;
use App\Tables;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Spatie\Geocoder\Exceptions\CouldNotGeocode;
use Spatie\Geocoder\Geocoder;
use App\Services\ConfChanger;
use App\Order;
use App\Models\Features;
use App\Models\Testimonials;
use App\Models\Process;
use Akaunting\Module\Facade as Module;
use App\Models\Allergens;
use App\Models\Config;
use DateTime;
use Spatie\OpeningHours\Exceptions\MaximumLimitExceeded;

class FrontEndController extends Controller
{
    /**
     * Gets subdomain.
     */
    public function getSubDomain()
    {
        $subdomain = substr_count(str_replace('www.', '', $_SERVER['HTTP_HOST']), '.') > 1 ? substr(str_replace('www.', '', $_SERVER['HTTP_HOST']), 0, strpos(str_replace('www.', '', $_SERVER['HTTP_HOST']), '.')) : '';
        if ($subdomain == '' | in_array($subdomain, config('app.ignore_subdomains'))) {
            return false;
        }

        return $subdomain;
    }

    private function customDomainMode(){
        //1 - Make sure the module is installed
        if(!in_array("domain", config('global.modules',[]))){
            return "";
        }

        //2 - Extract the domain
        $domain = request()->getHost();

        
        //3 - Make sure, this is no the project domain itself,
        if (strpos( config('app.url'),$domain) !== false) {
            return "";
        }
        
        //4 - The extracted domain is in the list of custom values
        $theConfig=Config::where('value','like',"%".$domain."%")->first();
        if($theConfig){
            //5 - Return the company subdomain if company is active
            $vendor_id=$theConfig->model_id;
 
            $vendor=Restorant::where('id',$vendor_id)->first();
            if($vendor){
                return $vendor->subdomain;
            }else{
                return "";
            }
            
        }else{
            //By default return no domain
            return "";
        }

       


    }

    /**
     * Returns restaurants based on the q parameter.
     * @param $restaurantIDS - the list of the restaurants to take into account
     * @return Restorant[] restaurants
     */
    private function filterRestaurantsOnQuery($restaurantIDS)
    {
        //1. Find all items
        $items = Items::where(['available' => 1])->where(function ($q) {
            $stripedQuery = '%'.strip_tags(\Request::input('q')).'%';
            $q->where('name', 'like', $stripedQuery)->orWhere('description', 'like', $stripedQuery);
        })->with('category.restorant')->get();

        //Find how many time happens on item level
        $restorants = [];
        foreach ($items as $item) {
            if (isset($item->category)) {

                //Check if this restaurant is part of the restaurant list
                if (in_array($item->category->restorant_id, $restaurantIDS)) {
                    if (isset($restorants[$item->category->restorant_id])) {
                        //Enlarge
                        $restorants[$item->category->restorant_id]->items_count++;
                    } else {
                        //Add
                        $restorants[$item->category->restorant_id] = $item->category->restorant;
                        $restorants[$item->category->restorant_id]->items_count = 1;
                    }
                }
            }
        }

        //Find how many time happens on restaurant level
        $restorantsQ = Restorant::where(['active' => 1])->where(function ($q) {
            $stripedQuery = '%'.strip_tags(\Request::input('q')).'%';
            $q->where('name', 'like', $stripedQuery)->orWhere('description', 'like', $stripedQuery);
        });
        //Calculate the values
        foreach ($restorantsQ->get() as $restorant) {
            if (in_array($restorant->id, $restaurantIDS)) {
                if (isset($results[$restorant->id])) {
                    //Enlarge - more value
                    $restorants[$restorant->id]->items_count += 5;
                } else {
                    //Add
                    $restorants[$restorant->id] = $restorant;
                    $restorants[$restorant->id]->items_count = 5;
                }
            }
        }

        //Now sort the restaurant based on how many times occures the search string
        usort($restorants, function ($a, $b) {
            return strcmp($a->items_count, $b->items_count);
        });

        return $restorants;
    }

    public function index()
    {
        $hasQuery = \Request::has('q') && strlen(\Request::input('q')) > 1;
        $hasLocation = \Request::has('location') && strlen(\Request::input('location')) > 1;

        //0. Check if it has DB access - otherwise go to install
        try {
            \DB::connection()->getPdo();
        } catch (\Exception $e) {
            return redirect()->route('LaravelInstaller::welcome');
        }

        //SITE SWITCHER

        //1. Single mode
        if (config('settings.single_mode') && config('settings.single_mode_id')) {
            return $this->singleMode();
        }

        //2. Subdomain mode
        if ($this->getSubDomain()) {
            return $this->subdomainMode();
        }

        //2a. Custom domain mode
        $customDomain=$this->customDomainMode();
        if($customDomain!="") {
            return $this->restorant($customDomain);
        }

        //3. QR Mode ( WhatsApp  or POS ) - here we have landing switch
        if (config('app.isqrsaas')) {
            if(config('settings.landing_to_use')!="system"){
                if(config('settings.landing_to_use')=="whatsapp"){
                    return $this->whatsappMode();
                }else if(config('settings.landing_to_use')=="pos"){
                    return $this->posMode();
                }else {
                    return $this->qrsaasMode();
                }
            }
            if(config('settings.is_whatsapp_ordering_mode')){
                //WhatsApp Mode
                return $this->whatsappMode();
            }else if(config('settings.is_pos_cloud_mode')){
                //POS Cloud Mode
                return $this->posMode();
            }else if(config('settings.is_agris_mode')){
                //Agris Mode
                return $this->agrisMode();
            }else if(config('app.issd')){
                //Social Drive Mode
                return $this->taxiMode();
            }else{
                //Default QR
                return $this->qrsaasMode();
            }
            
        }

        //Multy City mode, and we don't have location atm
        if (config('settings.multi_city') && ! ($hasLocation || $hasQuery)) {
            return $this->multyCityMode();
        }

        //ELSE - Query and Location mode //Default
        return $this->showStores(null);
    }

    /**
     * 1. Single mode, only one restorant.
     */
    public function singleMode()
    {
        $restorant = Restorant::findOrFail(config('settings.single_mode_id'));

        return $this->restorant($restorant->subdomain);
    }

    /**
     * 2. Subdomain mode - directly show store.
     */
    public function subdomainMode()
    {
        $subDomain = $this->getSubDomain();
        if ($subDomain) {
            $restorant = Restorant::whereRaw('REPLACE(subdomain, "-", "") = ?', [str_replace("-","",$subDomain)])->get();
            if (count($restorant) != 1) {
                //When Subdomain mode is disabled, show the error
                if(!config('settings.wildcard_domain_ready')){
                    return view('restorants.alertdomain', ['subdomain' =>$subDomain]);
                }else{
                    abort(404);
                }
               
            }

            return $this->restorant($subDomain);
        }
    }
    

    

    /**
     * 3. QR Mode.
     */
    public function qrsaasMode()
    {
        if (config('settings.disable_landing')) {
            //With disabled landing
            return redirect()->route('login');
        } else {
            //Normal, with landing
            $plans = config('settings.forceUserToPay',false)?Plans::where('id','!=',intval(config('settings.free_pricing_id')))->get()->toArray():Plans::get()->toArray();
            
            
            $colCounter = [4, 12, 6, 4, 3, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4];

            $availableLanguagesENV = config('settings.front_languages');
            $exploded = explode(',', $availableLanguagesENV);
            $availableLanguages = [];
            for ($i = 0; $i < count($exploded); $i += 2) {
                $availableLanguages[$exploded[$i]] = $exploded[$i + 1];
            }

            $locale = Cookie::get('lang') ? Cookie::get('lang') : config('settings.app_locale');
            $route = Route::current();
            $name = Route::currentRouteName();
            $query = 'lang.';
            if (substr($name, 0, strlen($query)) === $query) {
                //this is language route
                $exploded = explode('.', $name);
                $lang = strtoupper($exploded[1]);
                $locale = $lang;
            }
            App::setLocale(strtolower($locale));
            session(['applocale_change' => strtolower($locale)]);

            //Product features list
            $features = [];
            $images = [
                '',
                asset('impactfront').'/img/menubuilder.jpg',
                asset('impactfront').'/img/qr_image_builder.jpg',
                asset('impactfront').'/img/mobile_pwa.jpg',
                asset('impactfront').'/img/localorders.jpg',
                asset('impactfront').'/img/payments.jpg',
                asset('impactfront').'/img/customerlog.jpg',
            ];

            for ($i = 1; $i < 7; $i++) {
                array_push($features, [
                    'title' => ['key' => 'product'.$i.'_title', 'value' => __('qrlanding.product'.$i.'_title')],
                    'subtitle' => ['key' => 'product'.$i.'_subtitle', 'value' => __('qrlanding.product'.$i.'_subtitle')],
                    'features'=>[
                        ['key' => 'product'.$i.'_feature1', 'value' => __('qrlanding.product'.$i.'_feature1')],
                        ['key' => 'product'.$i.'_feature2', 'value' => __('qrlanding.product'.$i.'_feature2')],
                        ['key' => 'product'.$i.'_feature3', 'value' => __('qrlanding.product'.$i.'_feature3')],
                       ],
                    'button_name'=>__('qrlanding.product'.$i.'_button'),
                    'image'=>$images[$i],
                ]);
            }

            $featured_vendors=Restorant::where('active',1)->where('is_featured',1)->get()->shuffle();
           

            $response = new \Illuminate\Http\Response(view('qrsaas.'.config('settings.qr_landing'), [
                'col'=>$colCounter[count($plans)],
                'plans'=>$plans,
                'features'=>$features,
                'availableLanguages'=>$availableLanguages,
                'locale'=>$locale,
                'pages'=>Pages::where('showAsLink', 1)->get(),
                'featured_vendors'=>$featured_vendors
            ]));

            $response->withCookie(cookie('lang', $locale, 120));
            App::setLocale(strtolower($locale));

            return $response;
        }
    }

    /**
     * 4. Multy city mode.
     */
    public function multyCityMode()
    {

        //Default headers
        $settings = Settings::findOrFail(1)->first();
        config(['global.header_title' => $settings->header_title]);
        config(['global.header_subtitle' => $settings->header_subtitle]);

        //Set the cookie of the last entered address
        $lastaddress = Cookie::get('lastaddress');

        $langs=$this->handleLangs();
        $response = new \Illuminate\Http\Response(view('welcome', [
            'sections' =>[['super_title'=>__('Cities'), 'title'=>__('Find us in these cities and many more!'), 'cities'=>City::where('id', '>', 0)->get()]],
            'lastaddress'=>$lastaddress,
            'availableLanguages'=>$langs[0],
            'locale'=>$langs[1]
        ]));
        if (\Request::has('location') && strlen(\Request::input('location')) > 1) {
            $response->withCookie(cookie('lastaddress', \Request::input('location'), 120));
        }
        $response->withCookie(cookie('lang', $langs[1], 120));


        return $response;
    }

     /**
     * 5. WhatsApp Mode.
     */
    public function whatsappMode()
    {
        if (config('settings.disable_landing')) {
            //With disabled landing
            return redirect()->route('login');
        } else {
            //Normal, with landing
            $plans = config('settings.forceUserToPay',false)?Plans::where('id','!=',intval(config('settings.free_pricing_id')))->get()->toArray():Plans::get()->toArray();
            $colCounter = [12, 6, 4, 3, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4];

            $availableLanguagesENV = config('settings.front_languages');
            $exploded = explode(',', $availableLanguagesENV);
            $availableLanguages = [];
            for ($i = 0; $i < count($exploded); $i += 2) {
                $availableLanguages[$exploded[$i]] = $exploded[$i + 1];
            }

            $locale = Cookie::get('lang') ? Cookie::get('lang') : config('settings.app_locale');
            $route = Route::current();
            $name = Route::currentRouteName();
            $query = 'lang.';
            if (substr($name, 0, strlen($query)) === $query) {
                //this is language route
                $exploded = explode('.', $name);
                $lang = strtoupper($exploded[1]);
                $locale = $lang;
            }
            App::setLocale(strtolower($locale));
            session(['applocale_change' => strtolower($locale)]);

            //Landing page content
            $features = Features::where('post_type', 'feature')->get();
            $testimonials = Testimonials::where('post_type', 'testimonial')->get();
            $processes = Process::where('post_type', 'process')->get();

            $featured_vendors=Restorant::where('active',1)->where('is_featured',1)->get()->shuffle();
            $demoLink="#";
            $demoVendor=Restorant::where('subdomain',config('settings.demo_restaurant_slug'))->first();
            if($demoVendor){
                $demoLink=$demoVendor->getLinkAttribute();
            }

            $response = new \Illuminate\Http\Response(view('social.home', [
                'col' => $colCounter[count($plans)-1],
                'plans' => $plans,
                'availableLanguages' => $availableLanguages,
                'demoLink'=>$demoLink,
                'locale' => $locale,
                'pages' => Pages::where('showAsLink', 1)->get(),
                'features' => $features,
                'testimonials' => $testimonials,
                'processes' => $processes,
                'faq' => Process::where('post_type', 'faq')->get(),
                'featured_vendors'=>$featured_vendors
            ]));

            $response->withCookie(cookie('lang', $locale, 120));
            App::setLocale(strtolower($locale));

            return $response;
        }
    }

    /**
     * 5. POS Mode.
     */
    public function posMode(){
        if (config('settings.disable_landing')) {
            //With disabled landing
            return redirect()->route('login');
        } else {
            //Normal, with landing
            $plans = config('settings.forceUserToPay',false)?Plans::where('id','!=',intval(config('settings.free_pricing_id')))->get()->toArray():Plans::get()->toArray();
            $colCounter = [12, 6, 4, 3, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4];

            $availableLanguagesENV = config('settings.front_languages');
            $exploded = explode(',', $availableLanguagesENV);
            $availableLanguages = [];
            for ($i = 0; $i < count($exploded); $i += 2) {
                $availableLanguages[$exploded[$i]] = $exploded[$i + 1];
            }

            $locale = Cookie::get('lang') ? Cookie::get('lang') : config('settings.app_locale');
            $route = Route::current();
            $name = Route::currentRouteName();
            $query = 'lang.';
            if (substr($name, 0, strlen($query)) === $query) {
                //this is language route
                $exploded = explode('.', $name);
                $lang = strtoupper($exploded[1]);
                $locale = $lang;
            }
            App::setLocale(strtolower($locale));
            session(['applocale_change' => strtolower($locale)]);

            //Landing page content
            $features = Features::where('post_type', 'feature')->get();
            $testimonials = Testimonials::where('post_type', 'testimonial')->get();
            $processes = Process::where('post_type', 'process')->get();

            $response = new \Illuminate\Http\Response(view('poslanding.home', [
                'col' => $colCounter[count($plans)-1],
                'plans' => $plans,
                'availableLanguages' => $availableLanguages,
                'locale' => $locale,
                'pages' => Pages::where('showAsLink', 1)->get(),
                'features' => $features,
                'testimonials' => $testimonials,
                'processes' => $processes
            ]));

            $response->withCookie(cookie('lang', $locale, 120));
            App::setLocale(strtolower($locale));

            return $response;
        }
    }

     /**
     * 6. Agris Mode.
     */
    public function agrisMode(){
        if (config('settings.disable_landing')) {
            //With disabled landing
            return redirect()->route('login');
        } else {
            //Normal, with landing
            $plans = config('settings.forceUserToPay',false)?Plans::where('id','!=',intval(config('settings.free_pricing_id')))->get()->toArray():Plans::get()->toArray();
            $colCounter = [12, 6, 4, 3, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4];

            $availableLanguagesENV = config('settings.front_languages');
            $exploded = explode(',', $availableLanguagesENV);
            $availableLanguages = [];
            for ($i = 0; $i < count($exploded); $i += 2) {
                $availableLanguages[$exploded[$i]] = $exploded[$i + 1];
            }

            $locale = Cookie::get('lang') ? Cookie::get('lang') : config('settings.app_locale');
            $route = Route::current();
            $name = Route::currentRouteName();
            $query = 'lang.';
            if (substr($name, 0, strlen($query)) === $query) {
                //this is language route
                $exploded = explode('.', $name);
                $lang = strtoupper($exploded[1]);
                $locale = $lang;
            }
            App::setLocale(strtolower($locale));
            session(['applocale_change' => strtolower($locale)]);

            //Landing page content
            $features = Features::where('post_type', 'feature')->get();
            $testimonials = Testimonials::where('post_type', 'testimonial')->get();
            $processes = Process::where('post_type', 'process')->get();

            $response = new \Illuminate\Http\Response(view('agrislanding.home', [
                'col' => $colCounter[count($plans)-1],
                'plans' => $plans,
                'availableLanguages' => $availableLanguages,
                'locale' => $locale,
                'pages' => Pages::where('showAsLink', 1)->get(),
                'featured_vendors'=>Restorant::where('active',1)->where('is_featured',0)->get()->shuffle(),
                'features' => $features,
                'faqs' =>  Process::where('post_type', 'faq')->get(),
                'testimonials' => $testimonials,
                'processes' => $processes
            ]));

            $response->withCookie(cookie('lang', $locale, 120));
            App::setLocale(strtolower($locale));

            return $response;
        }
    }

    /**
     * 7. Taxi Mode.
     */
    public function taxiMode(){
        if (config('settings.disable_landing')) {
            //With disabled landing
            return redirect()->route('login');
        } else {
            //Normal, with landing
            $plans = config('settings.forceUserToPay',false)?Plans::where('id','!=',intval(config('settings.free_pricing_id')))->get()->toArray():Plans::get()->toArray();
            $colCounter = [12, 6, 4, 3, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4];

            $availableLanguagesENV = config('settings.front_languages');
            $exploded = explode(',', $availableLanguagesENV);
            $availableLanguages = [];
            for ($i = 0; $i < count($exploded); $i += 2) {
                $availableLanguages[$exploded[$i]] = $exploded[$i + 1];
            }

            $locale = Cookie::get('lang') ? Cookie::get('lang') : config('settings.app_locale');
            $route = Route::current();
            $name = Route::currentRouteName();
            $query = 'lang.';
            if (substr($name, 0, strlen($query)) === $query) {
                //this is language route
                $exploded = explode('.', $name);
                $lang = strtoupper($exploded[1]);
                $locale = $lang;
            }
            App::setLocale(strtolower($locale));
            session(['applocale_change' => strtolower($locale)]);

            //Landing page content
            $features = Features::where('post_type', 'feature')->get();
            $testimonials = Testimonials::where('post_type', 'testimonial')->get();
            $processes = Process::where('post_type', 'process')->get();
            $blog_posts = Process::where('post_type', 'blog')->get();

            $response = new \Illuminate\Http\Response(view('taxilanding.home', [
                'col' => $colCounter[count($plans)-1],
                'plans' => $plans,
                'availableLanguages' => $availableLanguages,
                'locale' => $locale,
                'pages' => Pages::where('showAsLink', 1)->get(),
                'featured_vendors'=>Restorant::where('active',1)->where('is_featured',0)->get()->shuffle(),
                'features' => $features,
                'faqs' =>  Process::where('post_type', 'faq')->get(),
                'testimonials' => $testimonials,
                'processes' => $processes,
                'blog_posts' => $blog_posts
            ]));

            $response->withCookie(cookie('lang', $locale, 120));
            App::setLocale(strtolower($locale));

            return $response;
        }
    }


    /**
     * Show stores.
     * @param {String} city_alias - city alias to show results for - can be null
     */
    public function showStores($city_alias)
    {
        //Variants
        /**
         * 1. Nothing no city, no q, no location
         * 2. Just city
         * 3. City and Query
         * 4. Just location
         * 5. Location and query
         * 6. Just Qury.
         */

        //DATA
        $hasQuery = \Request::has('q') && strlen(\Request::input('q')) > 1;
        $hasLocation = \Request::has('location') && strlen(\Request::input('location')) > 1;
        $expedition = \Request::input('expedition') && strlen(\Request::input('expedition')) > 1 && \Request::input('expedition') == 'delivery' ? 'can_deliver' : 'can_pickup';
        $hasCity = $city_alias != null;

        $sections = [];
        $aditionInTitle = $hasQuery ? ' '.__('where you can find').' '.\Request::input('q') : '';

        //Default headers
        $settings = Settings::findOrFail(1)->first();
        config(['global.header_title' => $settings->header_title]);
        config(['global.header_subtitle' => $settings->header_subtitle]);

        if ($hasCity) {
            //CITY BASED SEARCH CASE 4 and 5

            //Find the city
            $city = City::where('alias', $city_alias)->first();

            config(['global.header_title' => $city->header_title]);
            config(['global.header_subtitle' => $city->header_subtitle]);

            if (! $city) {
                abort(404);
            }
            $theRestorants = Restorant::where(['active'=>1, 'city_id'=>$city->id]);

            if ($hasQuery) {
                //With Query
                $restorants = $this->filterRestaurantsOnQuery($theRestorants->pluck('id')->toArray());
            } else {
                //No query
                $restorants = $theRestorants->get()->shuffle();
            }
            array_push($sections, ['title'=>__('Restaurants in').' '.$city->name.$aditionInTitle, 'restorants' =>$restorants]);
        } elseif ($hasLocation) {
            //LOCATION BASED SEARCH CASE 4 and 5
            //First, find the provided location, convert it to lat/lng
            $client = new \GuzzleHttp\Client();
            $geocoder = new Geocoder($client);
            $geocoder->setApiKey(config('geocoder.key'));

            try {
                $geoResults = $geocoder->getCoordinatesForAddress(\Request::input('location'));
            } catch (CouldNotGeocode $e) {
                report($e);

                return view('restorants.error_location', ['message'=>'The provided api key GOOGLE_MAPS_API_KEY has restrictions and we can not geocode the address. Please look into the documentation of this product to see what APIs are required to be enabled.']);
            }

            if ($geoResults['formatted_address'] == 'result_not_found') {
                //No results found
                return view('restorants.error_location', ['message'=>'You have provided address that we can not find']);
            } else {
                //Ok, we have lat and lng
                $restorantsQuery = Restorant::where(['active' => 1, $expedition => 1]);

                $restorantsWithGeoIDS = $this->scopeIsWithinMaxDistance($restorantsQuery, $geoResults['lat'], $geoResults['lng'], config('settings.location_search_radius'))->pluck('id');

                $restorants = Restorant::whereIn('id', $restorantsWithGeoIDS)->get();

                //Furthure, check restaurant's delivery area
                $allRestorantDelivering = [];
                $nearBytDelivering = [];
                $featuredDelivering = [];

                $allRestorantDeliveringIDS = [];
                $nearBytDeliveringIDS = [];
                $featuredDeliveringIDS = [];

                $hasRestorantsWithDelivery = false;

                $limitOfNearby = config('settings.most_nearby_limit');

                $point = json_decode('{"lat": '.$geoResults['lat'].', "lng":'.$geoResults['lng'].'}');
                foreach ($restorants as $key => $restorant) {
                    //Check if restorant delivery area is within
                    if (! is_array($restorant->radius)) {
                        continue;
                    }
                    $polygon = json_decode(json_encode($restorant->radius));
                    $numItems = count($restorant->radius);

                    //If disabled deliver - no delivery area

                    if ($restorant->can_deliver == 0 || (isset($polygon[0]) && $this->withinArea($point, $polygon, $numItems))) {
                        $hasRestorantsWithDelivery = true;

                        //add in allRestorantDelivering
                        array_push($allRestorantDelivering, $restorant);
                        array_push($allRestorantDeliveringIDS, $restorant->id);

                        if (count($nearBytDelivering) < $limitOfNearby) {
                            array_push($nearBytDelivering, $restorant);
                            array_push($nearBytDeliveringIDS, $restorant->id);
                        }

                        //Featured
                        if ($restorant->is_featured.'' == '1') {
                            array_push($featuredDelivering, $restorant);
                            array_push($featuredDeliveringIDS, $restorant->id);
                        }
                    }
                }

                if ($hasQuery) {
                    //CASE 5
                    //we have some query
                    $allRestorantDeliveringCollection = collect($this->filterRestaurantsOnQuery($allRestorantDeliveringIDS));
                    $nearBytDeliveringCollection = collect($this->filterRestaurantsOnQuery($nearBytDeliveringIDS));
                    $featuredDeliveringCollection = collect($this->filterRestaurantsOnQuery($featuredDeliveringIDS));
                } else {
                    //CASE 4
                    //No additinal qury
                    $allRestorantDeliveringCollection = collect($allRestorantDelivering)->shuffle();
                    $nearBytDeliveringCollection = collect($nearBytDelivering)->shuffle();
                    $featuredDeliveringCollection = collect($featuredDelivering)->shuffle();
                }

                if ($featuredDeliveringCollection->count() > 0) {
                    array_push($sections, ['title'=>__('Featured restaurants').$aditionInTitle, 'restorants' =>$featuredDeliveringCollection]);
                }
                if ($nearBytDeliveringCollection->count() > 0) {
                    array_push($sections, ['title'=>__('Popular restaurants near you').$aditionInTitle, 'restorants' =>$nearBytDeliveringCollection]);
                }

                $allReastaurantsTitle = __('All restaurants delivering to your address');
                
                if (! $hasRestorantsWithDelivery) {
                    $allReastaurantsTitle = __('All restaurants');
                }

                array_push($sections, ['title'=>$allReastaurantsTitle.$aditionInTitle, 'restorants' => $allRestorantDeliveringCollection]);
            }
        } elseif ($hasQuery) {
            //CASE 6
            //IS IS Query String Search
            $restorants = $this->filterRestaurantsOnQuery(Restorant::where(['active' => 1, $expedition => 1])->pluck('id')->toArray());
            array_push($sections, ['title'=>__('Restaurants').$aditionInTitle, 'restorants' =>$restorants]);
        } else {
            //CASE 1 - nothing at all
            //No query at all
            array_push($sections, ['title'=>__('Popular restaurants'), 'restorants' =>Restorant::where('active', 1)->get()->shuffle()]);
        }

        $banners_data = Banners::all();
        $banners = [];
        foreach ($banners_data as $key => $banner) {
            if ((new Carbon($banner->active_to))->gt(new Carbon($banner->active_from)) && Carbon::now()->between(new Carbon($banner->active_from), new Carbon($banner->active_to)) || (new Carbon($banner->active_from))->eq(new Carbon($banner->active_to)) && (new Carbon(Carbon::now()->toDateString()))->eq(new Carbon($banner->active_from)) && (new Carbon(Carbon::now()->toDateString()))->eq(new Carbon($banner->active_to))) {
                array_push($banners, $banner);
            }
        }

        

        
        $langs=$this->handleLangs();

        //Set the cookie of the last entered address
        $lastaddress = Cookie::get('lastaddress');
        $response = new \Illuminate\Http\Response(view('welcome', [
            'sections' => $sections,
            'lastaddress'=> $lastaddress,
            'banners' => $banners,
            'availableLanguages'=>$langs[0],
            'locale'=>$langs[1]
        ]));
        if (\Request::has('location') && strlen(\Request::input('location')) > 1) {
            $response->withCookie(cookie('lastaddress', \Request::input('location'), 120));
        }
        $response->withCookie(cookie('lang', $langs[1], 120));

        return $response;
    }

    private function handleLangs(){

        $availableLanguagesENV = config('settings.front_languages');
        $exploded = explode(',', $availableLanguagesENV);
        $availableLanguages = [];
        for ($i = 0; $i < count($exploded); $i += 2) {
            $availableLanguages[$exploded[$i]] = $exploded[$i + 1];
        }

        $locale = Cookie::get('lang') ? Cookie::get('lang') : config('settings.app_locale');
        $route = Route::current();
        $name = Route::currentRouteName();
        $query = 'lang.';
        if (substr($name, 0, strlen($query)) === $query) {
            //this is language route
            $exploded = explode('.', $name);
            $lang = strtoupper($exploded[1]);
            $locale = $lang;


            if($locale!="android-chrome-256x256.png"){
                App::setLocale(strtolower($locale));
                session(['applocale_change' => strtolower($locale)]);
            }
        }
      
        return [$availableLanguages,$locale];
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

    public function onboarding(){
        $plans = config('settings.forceUserToPay',false)?Plans::where('id','!=',intval(config('settings.free_pricing_id')))->get()->toArray():Plans::get()->toArray();
        $colCounter = [12, 6, 4, 3, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4];
        return view('taxilanding.onboarding',[
            'faqs' =>  Process::where('post_type', 'faq')->get(),
            'col'=>$colCounter[count($plans)],
            'plans'=>$plans,
        ]);
    }

    public function restorant($alias)
    {
       

        //Do we have impressum app
        $doWeHaveImpressumApp=Module::has('impressum');
       

        


        $subDomain = $this->getSubDomain();
        if ($subDomain && $alias !== $subDomain) {
            return redirect()->route('restorant', $subDomain);
        }
        $restorant = Restorant::whereRaw('REPLACE(subdomain, "-", "") = ?', [str_replace("-","",$alias)])->first();

        $doWeHaveOrderAfterHours=Module::has('orderdatetime')&&$restorant->getConfig('order_date_time_enable',false);

        //Template switcher
        $menuTemplate=config('settings.front_end_template','defaulttemplate');
        if(Module::has('themeswitcher')){
            $vendorTemplate=$restorant->getConfig('menu_template',$menuTemplate);
            //dd($vendorTemplate);    
            config(['settings.front_end_template' =>$vendorTemplate ]);
            $menuTemplate=$vendorTemplate;
        }
        

        //Do we have google translate app
        $doWeHaveGoogleTranslateApp=Module::has('googletranslate')&&$restorant->getConfig('gt_enable',false)=="true";
               
        if ($restorant && $restorant->active == 1) {

            if(config('settings.is_pos_cloud_mode')){
                return redirect(route('admin.restaurants.edit',$restorant->id));
            }
    
            //Set config based on restaurant
            config(['app.timezone' => $restorant->getConfig('time_zone',config('app.timezone'))]);
    
    

            if(isset($_GET['pay'])){
                //This is a payment link
                $order=Order::findOrFail($_GET['pay']);
                if($order->restorant_id==$restorant->id){
                    return redirect($order->payment_link);
                }
            }

            $restorant->increment('views');

            $canDoOrdering = $restorant->getPlanAttribute()['canMakeNewOrder'];
            
            //ratings usernames
            $usernames = [];
            if ($restorant && $restorant->ratings) {
                foreach ($restorant->ratings as $rating) {
                    $user = User::where('id', $rating->user_id)->get()->first();

                    if (! array_key_exists($user->id, $usernames)) {
                        $new_obj = (object) [];
                        $new_obj->name = $user->name;

                        $usernames[$user->id] = (object) $new_obj;
                    }
                }
            }

           
            $previousOrders = Cookie::get('orders') ? Cookie::get('orders') : '';
            $previousOrderArray = array_filter(explode(',', $previousOrders));

            //tables
            $tables = Tables::where('restaurant_id', $restorant->id)->get();
            $tablesData = [];
            foreach ($tables as $key => $table) {
                $tablesData[$table->id] = $table->restoarea ? $table->restoarea->name.' - '.$table->name : $table->name;
            }

            //Change Language
            ConfChanger::switchLanguage($restorant);

            //Change currency
            ConfChanger::switchCurrency($restorant);

            $currentEnvLanguage = isset(config('config.env')[2]['fields'][0]['data'][config('app.locale')]) ? config('config.env')[2]['fields'][0]['data'][config('app.locale')] : 'UNKNOWN';

            //dd($restorant->categories[1]->items[0]->extras);
           // dd(Categories::where('restorant_id',$restorant->id)->ordered()->get());

           $businessHours=$restorant->getBusinessHours();
           
           $tz= $restorant->getConfig('time_zone',config('app.timezone'));
           $now = new \DateTime('now',new \DateTimeZone($tz));

           $formatter = new \IntlDateFormatter(config('app.locale'), \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
           $formatter->setPattern(config('settings.datetime_workinghours_display_format_new'));
           $formatter->setTimeZone($tz);

         
           $viewFile='restorants.show';
           if($menuTemplate!='defaulttemplate'){
            $viewFile=config('settings.front_end_template','defaulttemplate')."::show";
           }
          

           $wh=$businessHours->forWeek();
           

           $canDoOrdering=$canDoOrdering&&($businessHours->isOpen()||$doWeHaveOrderAfterHours);
           if ($restorant->getConfig('disable_ordering', false)){
            $canDoOrdering=false;
           }
           
           $openingTime=null;
           $closingTime=null;
           try {
            $openingTime=$businessHours->isClosed()?$formatter->format($businessHours->nextOpen($now)):null;
            $closingTime=$businessHours->isOpen()?$formatter->format($businessHours->nextClose($now)):null;
           } catch (MaximumLimitExceeded $th) {
               //throw $th;
           }

           session(['last_visited_restaurant_alias' => $restorant->alias]);


           $viewData=[
                'wh'=>$wh,
                'allergens'=>in_array("allergens", config('global.modules',[]))?Allergens::where('post_type','allergen')->get():[],
                'currentDay'=>strtolower((new DateTime())->format('l')),
                'doWeHaveImpressumApp'=>$doWeHaveImpressumApp,
                'restorant' => $restorant,
                'openingTime' => $openingTime,
                'closingTime' => $closingTime,
                'doWeHaveOrderAfterHours'=> $doWeHaveOrderAfterHours,
                'usernames' => $usernames,
                'canDoOrdering'=>$canDoOrdering,
                'currentLanguage'=>$currentEnvLanguage,
                'showGoogleTranslate'=>$doWeHaveGoogleTranslateApp,
                'showAllGTLanguages'=>$restorant->getConfig('gt_all',true),
                'showGTLanguages'=>$restorant->getConfig('gt_list',""),
                'showLanguagesSelector'=>env('ENABLE_MILTILANGUAGE_MENUS', false) && $restorant->localmenus()->count() > 1,
                'hasGuestOrders'=>count($previousOrderArray) > 0,
                'fields'=>[['class'=>'col-12', 'classselect'=>'noselecttwo', 'ftype'=>'select', 'name'=>'Table', 'id'=>'table_id', 'placeholder'=>'Select table', 'data'=>$tablesData, 'required'=>true]],
           ];
    

           $response = new \Illuminate\Http\Response(view($viewFile,$viewData));

           if(isset($_GET['tid'])){
                $response->withCookie(cookie('tid', $_GET['tid'], 360));
           }else{
                $response->withCookie(cookie('tid',"", 360));
           }

            return $response;
        } else {
            return abort(404,__('The selected restaurant is not active at this moment!'));
        }
    }

    public function findByLocation(Request $request)
    {
        return view('restorants.location');
    }

    public function getCurrentLocation(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $geocoder = new Geocoder($client);
        $geocoder->setApiKey(config('geocoder.key'));
        $res = $geocoder->getAddressForCoordinates($request->lat, $request->lng);

        return response()->json([
            'data' => $res,
            'status' => true,
            'errMsg' => '',
        ]);
    }
}
