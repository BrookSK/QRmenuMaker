<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Akaunting\Module\Facade as Module;
use Illuminate\Support\Facades\File;
use ZipArchive;

class AppsController extends Controller
{
    public function index(){

        $this->adminOnly();
        
        //1. Get all available apps
        //$appsLink="https://raw.githubusercontent.com/mobidonia/foodtigerapps/main/apps30.json";
        $appsLink="https://apps.poslion.com?version=".config('config.version');

        $installed = [];
        foreach (Module::all() as $key => $module) {
            array_push($installed,$module->alias);
            
        }
        $installedAsString=implode(',',$installed);
        $appsLink.="&installed=".$installedAsString;
      
        //Code
        $appsLink.="&code=".config('settings.extended_license_download_code');
        $response = (new \GuzzleHttp\Client())->get($appsLink);

        $rawApps=[];
        if ($response->getStatusCode() == 200) {
            $rawApps = json_decode($response->getBody());
        }

        //2. Merge info
        foreach ($rawApps as $key => &$app) {
            $app->installed=Module::has($app->alias);
            if($app->installed){
                $app->version=Module::get($app->alias)->get('version');
                if($app->version==""){
                    $app->version="1.0";
                }

                //Check if app needs update
                if($app->latestVersion){
                    $app->updateAvailable=$app->latestVersion!=$app->version."";
                }else{
                    $app->updateAvailable=false;
                }
                
            }
            if(!isset($app->category)){
                $app->category=['tools'];
            }
        }

        //Filter apps by type
        $apps=[];
        $newRawApps=unserialize(serialize($rawApps));;
        foreach ($newRawApps as $key => $app) {
            if(isset($app->rule)&&$app->rule){
                $rules=explode(',',$app->rule);
                $alreadyAdded=false;
                foreach ($rules as $keyrule => $rule) {
                    if(!$alreadyAdded&&config('app.'.$rule)){
                        $alreadyAdded=true;
                        array_push($apps,$app);
                    }
                }
            }else{
                $alreadyAdded=true;
               array_push($apps,$app);
            }
            
            //remove
            if($alreadyAdded&&isset($app->rulenot)&&$app->rulenot){
                $alreadyRemoved=false;
                $rulesNot=explode(',',$app->rulenot);
                //dd( $rulesNot);
                foreach ($rulesNot as $keyrulnot => $rulenot) {
                    if(!$alreadyRemoved&&config('app.'.$rulenot)){
                        $alreadyRemoved=true;
                        array_pop($apps);
                    }
                }
            }
        }
        

        //3. Return view
        return view('apps.index',compact('apps'));

    }

    public function remove($alias){
        if (!auth()->user()->hasRole('admin') || strlen($alias)<2 || (config('settings.is_demo') || config('settings.is_demo'))) {
            abort(404);
        }
        $destination=Module::get($alias)->getPath();
        if(File::exists($destination)){
            File::deleteDirectory($destination);
            return redirect()->route('apps.index')->withStatus(__('Removed'));
        }else{
            abort(404);
        }
    }

    public function store(Request $request){
       
        $path=$request->appupload->storeAs('appupload', $request->appupload->getClientOriginalName());
        
        $fullPath = storage_path('app/'.$path);
        $zip = new ZipArchive;

        if ($zip->open($fullPath)) {

            //Modules folder - for plugins
            $destination=public_path('../modules');
            $message=__('App is installed');

            //If it is language pack
            if (strpos($fullPath, '_lang') !== false) {
                $destination=public_path('../resources/lang');
                $message=__('Language pack is installed');
            }


            // Extract file
            $zip->extractTo($destination);
            
            // Close ZipArchive     
            $zip->close();
            return redirect()->route('apps.index')->withStatus($message);
        }else{
            return redirect(route('apps.index'))->withError(__('There was an error on app install. Please try manual install'));
        }
    }
}
