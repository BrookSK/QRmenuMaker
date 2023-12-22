<?php

namespace App\Http\Controllers;
use Akaunting\Module\Facade as Module;
use App\Address;
use App\Categories;
use App\Extras;
use App\Items;
use App\Models\LocalMenu;
use App\Models\Options;
use App\Notifications\SystemTest;
use App\Restorant;
use App\Settings;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Image;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cookie;

class SettingsController extends Controller
{
    private function validateAccess()
    {
        if (! auth()->user()->hasRole('admin')) {
            abort(404);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected static $currencies;
    protected static $jsfront;
    protected $imagePath = '/uploads/settings/';

    public function systemstatus()
    {
        $totalTasks = 2;

         //Verify Stripe
         $processor=config('settings.subscription_processor','stripe');
         $plansEnabled=config('settings.enable_pricing',false);
         $doStripeVerification=false;
         if($processor=="Stripe"&&$plansEnabled){
             $totalTasks++;
             $doStripeVerification=true;
         }

         $percent = 100 / $totalTasks;
         $taskDone = 0;

        //Verify system is setup correctly.
        if (! auth()->user()->hasRole('admin')) {
            abort(404);
        }

        $testResutls = [];
        //1. Make sure admin email is not admin@example.com
        if (auth()->user()->email !== 'admin@example.com') {
            array_push($testResutls, ['settings_default_admin_email', 'OK', true]);
            $taskDone++;

            //Continue to verify smtp setup
            if (config('mail.mailers.smtp.username') != '802fc656dd8029') {
                try {
                    auth()->user()->notify(new SystemTest(auth()->user()));
                    array_push($testResutls, ['settings_smtp', 'OK', true]);
                    $taskDone++;

                    //Stripe
                    if($doStripeVerification){
                        if(strlen(config('settings.stripe_secret'))>3
                        && strlen(config('settings.stripe_key'))>3){
                            array_push($testResutls, ['settings_stripe', 'OK', true]);
                            $taskDone++;
                        }else{
                            array_push($testResutls, ['settings_stripe', 'settings_stripe_not_ok', false, 'https://mobidonia.gitbook.io/plugins/subscriptions-plugins/stripe']);
                        }
                    }

                } catch (\Exception $e) {
                    array_push($testResutls, ['settings_smtp', 'settings_smtp_not_ok', false, 'https://mobidonia.gitbook.io/qr-menu-maker/define-basics/obtain-smtp']);
                }
            } else {
                array_push($testResutls, ['settings_smtp', 'settings_smtp_not_ok', false, 'https://mobidonia.gitbook.io/qr-menu-maker/define-basics/obtain-smtp']);
            }
        } else {
            array_push($testResutls, ['settings_default_admin_email', 'settings_using_default_admin_solution', false, 'https://mobidonia.gitbook.io/qr-menu-maker/usage/getting-started#login-as-admin']);
        }

        if($taskDone==$totalTasks){
            $data = json_encode(['date' => date('Y/m/d h:i:s')]);
            file_put_contents(storage_path('verified'), $data, FILE_APPEND | LOCK_EX);
        }

       
        return view('settings.status', [
            'progress'=>ceil($taskDone * $percent),
            'testResutls' => $testResutls, ]);
    }

    private function translateModel($tableName, $provider, $fields, $locale)
    {
        $items = DB::table($tableName)->get();

        foreach ($items as $key => $item) {
            $object = $provider::find($item->id);
            foreach ($fields as $keyFields => $valueField) {
                $valueToStore="";
                if($object){
                    if($valueField=="name"){
                        $valueToStore=$item->name; 
                    }else if($valueField=="description"){
                        $valueToStore=$item->description;
                    }
                    
                    if(is_numeric($valueToStore)){
                        $valueToStore=$valueToStore.".";
                    }
                    $object->setTranslation($valueField, $locale, $valueToStore)->save();
                }
                
            }
        }
    }

    public function translateMenu()
    {
        if (auth()->user()->hasRole('admin')) {
            $locale = config('settings.app_locale');

            //Translate categories
            $this->translateModel('categories', Categories::class, ['name'], $locale);

            //Translate items
            $this->translateModel('items', Items::class, ['name', 'description'], $locale);

            //Translate extras
            $this->translateModel('extras', Extras::class, ['name'], $locale);

            //Translate Options
            $this->translateModel('options', Options::class, ['name'], $locale);

            //Create the local model for all restaurants
            $allRestaurants = Restorant::where('id', '>', 0)->get();
            $currentEnvLanguage = isset(config('config.env')[2]['fields'][0]['data'][$locale]) ? config('config.env')[2]['fields'][0]['data'][$locale] : 'UNKNOWN';
            foreach ($allRestaurants as $key => $restaurant) {
                $localMenu = new LocalMenu([
                    'restaurant_id'=>$restaurant->id,
                     'language'=>$locale,
                      'languageName'=>$currentEnvLanguage,
                      'default'=>'1', ]
                );
                $localMenu->save();
            }

            //Set that we have done the translation
            $data = json_encode([
                'date' => date('Y/m/d h:i:s'),
            ], JSON_THROW_ON_ERROR);
            file_put_contents(storage_path('multilanguagemigrated'), $data, FILE_APPEND | LOCK_EX);

            //Redirect
            return redirect()->route('settings.index')->withStatus(__('Successfully migrated to multi language menus'));
        }
    }

    public function getCurrentEnv()
    {
        $envConfigs = config('config.env');

        //Extra fields from included modules
        $extraFields=[];
        foreach (Module::all() as $key => $module) {
            if($module->get('global_fields')){
                $extraFields=array_merge($extraFields,$module->get('global_fields'));
            }
            
        }
        $envConfigs['3']['fields']=array_merge($extraFields,$envConfigs['3']['fields']);


        //Since 2.2.x there is custom modules
        $envMerged = [];
        foreach ($envConfigs as $key => $group) {
            $theMegedGroupFields = [];
            foreach ($group['fields'] as $key => $field) {
                if (! (isset($field['onlyin']) && $field['onlyin'] != config('settings.app_project_type'))) {
                    array_push($theMegedGroupFields, [
                        'ftype'=>isset($field['ftype']) ? $field['ftype'] : 'input',
                        'type'=>isset($field['type']) ? $field['type'] : 'text',
                        'id'=>'env['.$field['key'].']',
                        'name'=>isset($field['title']) && $field['title'] != '' ? $field['title'] : $field['key'],
                        'placeholder'=>isset($field['placeholder']) ? $field['placeholder'] : '',
                        'value'=>env($field['key'], $field['value']),
                        'required'=>false,
                        'separator'=>isset($field['separator']) ? $field['separator'] : null,
                        'additionalInfo'=>isset($field['help']) ? $field['help'] : null,
                        'data'=>isset($field['data']) ? $field['data'] : [],
                     ]);
                }
            }
            array_push($envMerged, [
             'name'=>$group['name'],
             'slug'=>$group['slug'],
             'icon'=>$group['icon'],
             'fields'=>$theMegedGroupFields,
            ]);
        }

        return $envMerged;
    }

    public function cloudupdate(){
        //Always run migration
        Artisan::call('migrate', ['--force' => true]);

        Artisan::call('module:migrate', ['--force' => true]);

        if (auth()->user()->hasRole('admin')) {
            
            $memory_limit = ini_get('memory_limit');
            if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
                if ($matches[2] == 'M') {
                    $memory_limit = $matches[1] * 1024 * 1024; // nnnM -> nnn MB
                } else if ($matches[2] == 'K') {
                    $memory_limit = $matches[1] * 1024; // nnnK -> nnn KB
                } else if ($matches[2] == 'G') {
                    $memory_limit = $matches[1] * 1024*1024*1024; // nnnM -> GB
                }
            }
            $okMemory=true;
            if($memory_limit==-1||$memory_limit >= 512 * 1024 * 1024){
               
            }else{
                //Alert
                $okMemory=false;
            }
            


            $updater = new \Codedge\Updater\UpdaterManager(app());

            //With update
            if(isset($_GET['do_update'])){
                if($updater->source()->isNewVersionAvailable()) {

            
                    // Get the new version available
                    $versionAvailable = $updater->source()->getVersionAvailable();
            
                    // Create a release
                    $release = $updater->source()->fetch($versionAvailable);
            
                    // Run the update process
                    $updater->source()->update($release);

                    return redirect()->route('settings.cloudupdate')->withStatus(__('Successfully updated to version v').$versionAvailable);
                    
                } else {
                    return redirect()->route('settings.cloudupdate')->withStatus(__('There is nothing to update!'));
                }
            }

            //Check for new version
            $updater->source()->deleteVersionFile();
            $newVersion="";
            $newVersionAvailable = $updater->source()->isNewVersionAvailable();
            if($newVersionAvailable){
                $newVersion=$updater->source()->getVersionAvailable();
            }
            
            $theChangeLog="";
            if(config('settings.enalbe_change_log_in_update')){
                $ftChange="https://raw.githubusercontent.com/dimovdaniel/foodtigerdocs/master/changelog/changelog.md";
                $qrChange="https://raw.githubusercontent.com/dimovdaniel/qrmakerdocs/master/changelog/changelog.md";
                $wpChange="https://raw.githubusercontent.com/mobidonia/whatsappfooddocs/master/changelog/changelog.md";
                $pcChange="https://raw.githubusercontent.com/dimovdaniel/poscloud/master/changelog/changelog.md";
                $agChange="https://raw.githubusercontent.com/mobidonia/agrisdocs/master/changelog/changelog.md";
                $wdChange="https://raw.githubusercontent.com/mobidonia/whatsappdrive/master/changelog/changelog.md";
                if(config('app.isft')){
                    $theChangeLog=@file_get_contents($ftChange);
                }else {
                    if(config('settings.is_whatsapp_ordering_mode')){
                        $theChangeLog=@file_get_contents($wpChange);
                    }else if(config('settings.is_pos_cloud_mode')){
                        $theChangeLog=@file_get_contents($pcChange);
                    }else if(config('settings.is_agris_mode')){
                        $theChangeLog=@file_get_contents($agChange);
                    }else if(config('app.issd')){
                        $theChangeLog=@file_get_contents($wdChange);
                    }else{
                        $theChangeLog=@file_get_contents($qrChange);
                    }
                }
                $theChangeLog=str_replace('{% embed url="https://youtu.be/','<iframe width="560" height="315" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen src="https://www.youtube.com/embed/',$theChangeLog);
                $theChangeLog=str_replace('%}','></iframe>',$theChangeLog);
                $theChangeLog=str_replace('\\','',$theChangeLog);
            }
           
        
            
            return view('settings.cloudupdate', [
                'newVersionAvailable'=>$newVersionAvailable,
                'newVersion'=>$newVersion,
                'theChangeLog'=>$theChangeLog,
                'okMemory'=>$okMemory
               ]);

        }else{
            return redirect()->route('orders.index')->withStatus(__('No Access'));
        }
    }

    public function index(Settings $settings)
    {
        if (auth()->user()->hasRole('admin')) {

            $curreciesArr = [];
            static::$currencies = require __DIR__.'/../../../config/money.php';

            foreach (static::$currencies as $key => $value) {
                array_push($curreciesArr, $key);
            }

            $jsfront = File::get(base_path('public/byadmin/front.js'));
            $jsfrontmenu = File::get(base_path('public/byadmin/frontmenu.js'));
            $jsback = File::get(base_path('public/byadmin/back.js'));
            $cssfront = File::get(base_path('public/byadmin/front.css'));
            $cssfrontmenu = File::get(base_path('public/byadmin/frontmenu.css'));
            $cssback = File::get(base_path('public/byadmin/back.css'));


            $hasDemoRestaurants = Restorant::where('phone', '(530) 625-9694')->count() > 0;

            if (config('settings.is_demo') | config('settings.is_demo')) {
                $hasDemoRestaurants = false;
            }

            return view('settings.index', [
                'settings' => $settings->first(),
                'currencies' => $curreciesArr,
                'jsfront'=>$jsfront,
                'jsfrontmenu'=>$jsfrontmenu,
                'jsback'=>$jsback,
                'cssfront'=>$cssfront,
                'cssfrontmenu'=>$cssfrontmenu,
                'cssback'=>$cssback,
                'hasDemoRestaurants'=>$hasDemoRestaurants,
                'envConfigs'=>$this->getCurrentEnv(),
                'showMultiLanguageMigration'=>env('ENABLE_MILTILANGUAGE_MENUS', false) && ! file_exists(storage_path('multilanguagemigrated')),
                ]);
        } else {
            return redirect()->route('orders.index')->withStatus(__('No Access'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->route('settings.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function setEnvironmentValue(array $values)
    {
       
        $envFile = app()->environmentFilePath();
        $str = "\n";
        $str .= file_get_contents($envFile);
        $str .= "\n"; // In case the searched variable is in the last line without \n
        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                if ($envValue == trim($envValue) && strpos($envValue, ' ') !== false) {
                    $envValue = '"'.$envValue.'"';
                }

                $keyPosition = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

                // If key does not exist, add it
                if ((! $keyPosition && $keyPosition != 0) || ! $endOfLinePosition || ! $oldLine) {
                    $str .= "{$envKey}={$envValue}\n";
                } else {
                    if($envKey=="DB_PASSWORD"){
                        $str = str_replace($oldLine, "{$envKey}=\"{$envValue}\"", $str);
                    }else{
                        $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
                    }
                    
                }
            }
        }

        $str = substr($str, 1, -1);
        if (! file_put_contents($envFile, $str)) {
            return false;
        }

        return true;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (config('settings.is_demo') | config('settings.is_demo')) {
            //Demo, don;t allow
            return redirect()->route('settings.index')->withStatus(__('Settings not allowed to be updated in DEMO mode!'));
        }

        $this->setEnvironmentValue($request->env);
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Cache::flush();

        $settings = Settings::find($id);

        $settings->site_name = strip_tags($request->site_name);
        $settings->description = strip_tags($request->site_description);
        $settings->header_title = $request->header_title;
        $settings->header_subtitle = $request->header_subtitle;
        $settings->facebook = strip_tags($request->facebook) ? strip_tags($request->facebook) : '';
        $settings->instagram = strip_tags($request->instagram) ? strip_tags($request->instagram) : '';
        $settings->playstore = strip_tags($request->playstore) ? strip_tags($request->playstore) : '';
        $settings->appstore = strip_tags($request->appstore) ? strip_tags($request->appstore) : '';
        $settings->typeform = strip_tags($request->typeform) ? strip_tags($request->typeform) : '';
        $settings->mobile_info_title = strip_tags($request->mobile_info_title) ? strip_tags($request->mobile_info_title) : '';
        $settings->mobile_info_subtitle = strip_tags($request->mobile_info_subtitle) ? strip_tags($request->mobile_info_subtitle) : '';
        $settings->delivery = (float) $request->delivery;
        $settings->order_fields=$request->order_fields;
        $settings->update();
        

        fwrite(fopen(__DIR__.'/../../../public/byadmin/front.js', 'w'), str_replace('tagscript', 'script', $request->jsfront));
        fwrite(fopen(__DIR__.'/../../../public/byadmin/back.js', 'w'), str_replace('tagscript', 'script', $request->jsback));
        fwrite(fopen(__DIR__.'/../../../public/byadmin/front.css', 'w'), str_replace('tagscript', 'script',$request->cssfront) );
        fwrite(fopen(__DIR__.'/../../../public/byadmin/back.css', 'w'),  str_replace('tagscript', 'script',$request->cssback) );

        fwrite(fopen(__DIR__.'/../../../public/byadmin/frontmenu.js', 'w'), str_replace('tagscript', 'script', $request->jsfrontmenu));
        fwrite(fopen(__DIR__.'/../../../public/byadmin/frontcss.css', 'w'), str_replace('tagscript', 'script',$request->cssfrontmenu) );

        if ($request->hasFile('site_logo')) {
            $settings->site_logo = $this->saveImageVersions(
                $this->imagePath,
                $request->site_logo,
                [
                    ['name'=>'logo', 'type'=>'png'],
                ]
            );
        }

        if ($request->hasFile('site_logo_dark')) {
            $settings->site_logo_dark = $this->saveImageVersions(
                $this->imagePath,
                $request->site_logo_dark,
                [
                    ['name'=>'site_logo_dark', 'type'=>'png'],
                ]
            );
        }

        if ($request->hasFile('search')) {
            $settings->search = $this->saveImageVersions(
                $this->imagePath,
                $request->search,
                [
                    ['name'=>'cover'],
                ]
            );
        }

        if ($request->hasFile('restorant_details_image')) {
            $settings->restorant_details_image = $this->saveImageVersions(
                $this->imagePath,
                $request->restorant_details_image,
                [
                    ['name'=>'large', 'w'=>590, 'h'=>400],
                    ['name'=>'thumbnail', 'w'=>200, 'h'=>200],
                ]
            );
        }

        if ($request->hasFile('restorant_details_cover_image')) {
            $settings->restorant_details_cover_image = $this->saveImageVersions(
                $this->imagePath,
                $request->restorant_details_cover_image,
                [
                    ['name'=>'cover', 'w'=>2000, 'h'=>1000],
                ]
            );
        }

        if ($request->hasFile('qrdemo')) {
            $imDemo = Image::make($request->qrdemo->getRealPath())->fit(512, 512);
            $imDemo->save(public_path().'/impactfront/img/qrdemo.jpg');
        }

        if ($request->hasFile('wphomehero')) {
            $wpDemo = Image::make($request->wphomehero->getRealPath());
            $wpDemo->save(public_path().'/social/img/wpordering.svg'); 
        }

        if ($request->hasFile('poshomehero')) {
            $wpDemo = Image::make($request->poshomehero->getRealPath());
            $wpDemo->save(public_path().'/soft/img/poshero.jpeg'); 
        }
        
        $images = [
            public_path().'/impactfront/img/flayer.png',
            public_path().'/impactfront/img/menubuilder.jpg',
            public_path().'/impactfront/img/qr_image_builder.jpg',
            public_path().'/impactfront/img/mobile_pwa.jpg',
            public_path().'/impactfront/img/localorders.jpg',
            public_path().'/impactfront/img/payments.jpg',
            public_path().'/impactfront/img/customerlog.jpg',
        ];

        for ($i = 0; $i < 7; $i++) {
            if ($request->hasFile('ftimig'.$i)) {
                chmod($images[$i], 0777);
                if($i==0){
                    $imDemo = Image::make($request->all()['ftimig'.$i]->getRealPath())->fit(600, 600);
                }else{
                    $imDemo = Image::make($request->all()['ftimig'.$i]->getRealPath())->fit(480, 320);
                }   
                $imDemo->save($images[$i]);
            }
        }

        if ($request->hasFile('favicons')) {
            $imAC256 = Image::make($request->favicons->getRealPath())->fit(256, 256);
            $imgAC192 = Image::make($request->favicons->getRealPath())->fit(192, 192);
            $imgMS150 = Image::make($request->favicons->getRealPath())->fit(150, 150);

            $imgApple = Image::make($request->favicons->getRealPath())->fit(120, 120);
            $img32 = Image::make($request->favicons->getRealPath())->fit(32, 32);
            $img16 = Image::make($request->favicons->getRealPath())->fit(16, 16);

            $imAC256->save(public_path().'/android-chrome-256x256.png');
            $imgAC192->save(public_path().'/android-chrome-192x192.png');
            $imgMS150->save(public_path().'/mstile-150x150.png');

            $imgApple->save(public_path().'/apple-touch-icon.png');
            $img32->save(public_path().'/favicon-32x32.png');
            $img16->save(public_path().'/favicon-16x16.png');
        }

        $settings->update();

        return redirect()->route('settings.index')->withStatus(__('Settings successfully updated!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function regenerateSitemap(){
        $exitCodeForMigration=Artisan::call('sitemap:generate', []);
        return redirect()->route('settings.index')->withStatus(__('Sitemap Regenerated'));
    }

    public function landing(){

        $locale = Cookie::get('lang') ? Cookie::get('lang') : config('settings.app_locale');
        if (isset($_GET['lang'])) {
            //3. Change locale to the new local
            app()->setLocale($_GET['lang']);
            $locale =$_GET['lang'];
            session(['applocale_change' => $_GET['lang']]);
        }

        $this->validateAccess();


        $availableLanguagesENV = config('settings.front_languages');
        $exploded = explode(',', $availableLanguagesENV);
        $availableLanguages = [];
        for ($i = 0; $i < count($exploded); $i += 2) {
            $availableLanguages[$exploded[$i]] = $exploded[$i + 1];
        }

        $sections = ["Features"=>"feature", "Testimonials"=>"testimonial", "Processes"=>"process","FAQs"=>"faq","Blog links"=>"blog"];

        $currentEnvLanguage = isset(config('config.env')[2]['fields'][0]['data'][config('app.locale')]) ? config('config.env')[2]['fields'][0]['data'][config('app.locale')] : 'UNKNOWN';

        
        return view('landing.index', [
            'sections' => $sections,
            'locale'=>$locale,
            'availableLanguages'=> $availableLanguages,
            'currentLanguage'=>$currentEnvLanguage
            ]);
    }
    
}
