<?php

namespace App\Providers;

use App\Settings;
use App\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Akaunting\Module\Facade as Module;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //ignore default migrations from Cashier
        Cashier::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(method_exists(Cashier::class, 'useCustomerModel')){
            Cashier::useCustomerModel(User::class);
        }
        Paginator::defaultView('pagination::bootstrap-4');
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        Schema::defaultStringLength(191);
        try {
            \DB::connection()->getPdo();
            $settings = Schema::hasTable('settings') && Settings::find(1) ? Settings::find(1)->toArray() : [];

            //Site logo
            if ((isset($settings['site_logo']) && ! (strpos($settings['site_logo'], '/') !== false))) {
                $settings['site_logo'] = '/uploads/settings/'.$settings['site_logo'].'_logo.jpg';
            }

            //Site logo dark
            if ((isset($settings['site_logo_dark']) && ! (strpos($settings['site_logo_dark'], '/') !== false))) {
                $settings['site_logo_dark'] = '/uploads/settings/'.$settings['site_logo_dark'].'_site_logo_dark.jpg';
            }

            //Search
            if ((isset($settings['search']) && ! (strpos($settings['search'], '/') !== false))) {
                $settings['search'] = '/uploads/settings/'.$settings['search'].'_cover.jpg';
            }

            //Details default cover image
            if ((isset($settings['restorant_details_cover_image']) && ! (strpos($settings['restorant_details_cover_image'], '/') !== false))) {
                $settings['restorant_details_cover_image'] = '/uploads/settings/'.$settings['restorant_details_cover_image'].'_cover.jpg';
            }

            //Restaurant default image
            if ((isset($settings['restorant_details_image']) && ! (strpos($settings['restorant_details_image'], '/') !== false))) {
                $settings['restorant_details_image'] = '/uploads/settings/'.$settings['restorant_details_image'].'_large.jpg';
            }

            //Set the list of added modules
            $modules=[];
            foreach (Module::all() as $key => $module) {
                array_push($modules,$module->get('alias'));

            }
            $settings['modules']=$modules;

           
            
            config([
                'global' =>  $settings,
            ]);

            $moneyList=[];
            $rawMoney=config('money');
            foreach ($rawMoney as $key => $value) {
                $moneyList[$key]=$value['name']." - ".$value['symbol']." - ".$key;
            }

            //Setup for money list
            config(['config.env.2' =>  [
                'name'=>'Localization',
                'slug'=>'localizatino',
                'icon'=>'ni ni-world-2',
                'fields'=>[
                    ['title'=>'Default language', 'help'=>"If you make change, make sure you first have added the new language in Translations and you have done the translations.", 'key'=>'APP_LOCALE', 'value'=>'en', 'ftype'=>'select', 'data'=>config('languages')],
                    ['title'=>'List of available language on the landing page', 'help'=>'Define a list of Language short code and the name. If only one language is listed, the language picker will not show up', 'key'=>'FRONT_LANGUAGES', 'value'=>'EN,English,FR,French'],
                    ['title'=>'Time zone', 'help'=>'This value is important for correct vendors opening and closing times', 'key'=>'TIME_ZONE', 'value'=>'Europe/Berlin', 'ftype'=>'select', 'data'=>config('timezones')],
                    ['title'=>'Default currency', 'key'=>'CASHIER_CURRENCY', 'value'=>'usd', 'ftype'=>'select', 'data'=>$moneyList],
                    ['title'=>'Money conversion', 'help'=>'Some currencies need this field to be unselected. By default it should be selected', 'key'=>'DO_CONVERTION', 'value'=>'true', 'ftype'=>'bool'],
                    ['title'=>'Time format', 'key'=>'TIME_FORMAT', 'value'=>'AM/PM', 'ftype'=>'select', 'data'=>['AM/PM'=>'AM/PM', '24hours '=>'24 Hours']],
                    ['title'=>'Date and time display', 'key'=>'DATETIME_DISPLAY_FORMAT', 'value'=>'d M Y h:i A'],
                    ['title'=>'Working time display format','help'=>"For 24h use 'E HH:mm' and for AM/PM use 'E h:mm a'", 'key'=>'DATETIME_WORKING_HOURS_DISPLAY_FORMAT_NEW', 'value'=>'E HH:mm'],
    
                ],
            ]]);

            //Setup subscribe methods
            $subscriptionsModules=[];
            $subscriptionsModules["Stripe"]="Stripe"; // Stripe is default
            $subscriptionsModules["Local"]="Local bank transfers"; // Stripe is default

            //Templates
            $templatesModules=[];
            $templatesModules['defaulttemplate']=__('Default template');
            
            foreach (Module::all() as $key => $module) {
                if($module->get('isSubscriptionModule')){
                    $subscriptionsModules[$module->get('name')]=$module->get('name');
                }
                if($module->get('isTemplateModule')){
                    $templatesModules[$module->get('alias')]=$module->get('name');
                }
            }
            config(['config.env.1.fields.0.data' => $subscriptionsModules]); 
            config(['config.env.0.fields.7.data' => $templatesModules]); 
        } catch (\Exception $e) {
           
        }
    }
}
