<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Authenticates user, send api token
     */
    public function getToken(Request $request)
    {
        $user = User::where(['active'=>1, 'email'=>$request->email])->first();
        if ($user != null) {
            if (Hash::check($request->password, $user->password)) {
                if ($user->hasRole(['driver'])) {
                    if( $request->has('expotoken')){
                        $user->setExpoToken($request->expotoken);
                    }
                    return response()->json([
                        'status' => true,
                        'token' => $user->api_token,
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'errMsg' => __('User is not a driver!'),
                    ]);
                }
            } else {
                return $this->invalidResponse();
            }
        } else {
            return $this->invalidResponse();
        }
    }

    public function register(Request $request)
    {
        
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'unique:users', 'max:255'],
                'phone' => ['required', 'string'],
                'password' => ['required', 'string', 'min:4'],
                'app_secret'=>['required', 'string'] 
            ]);
            
            

            if (!$validator->fails()) {
                
                if(config('settings.app_secret')==null||config('settings.app_secret').""!=$request->app_secret){
                     return response()->json([
                        'status' => false,
                        'errMsg' => ['app_secret'=>__("App secret is incorrectly set")],
                    ]);
                }
                $driver = new User;

                $driver->name = $request->name;
                $driver->email = $request->email;
                $driver->phone = $request->phone;
                $driver->password = Hash::make($request->password);
                $driver->api_token = Str::random(80);
                $driver->active = 0; //Disabled by default
                $driver->save();

                

                //Assign role
                $driver->assignRole('driver');

                if( $request->has('expotoken')){
                    $driver->setExpoToken($request->expotoken);
                }

                return response()->json([
                    'status' => false,
                    'errMsg' => __("Driver account created. Please wait for a call from us to activate your account.")
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'errMsg' => $validator->errors(),
                ]);
            }
        
    }

    /**
     * Return invalid user data
     */
    private function invalidResponse($message='Driver not found!'){
        return response()->json([
            'status' => false,
            'errMsg' => __($message),
        ]);
    }

    public function goOffline()
    {
        $user=auth()->user();
        auth()->user()->working = 0;
        auth()->user()->update();

        return response()->json([
            'status' => true,
            'message' => __('Driver now off line'),
            'data' => [
                'working' => $user->working,
                'onorder' => $user->onorder,
                'numorders' => $user->numorders,
                'rejectedorders'=>$user->rejectedorders,
            ]
        ]);
    }

    public function goOnline()
    {
        $user=auth()->user();
        auth()->user()->working = 1;
        auth()->user()->update();

        return response()->json([
            'status' => true,
            'message' => __('Driver now online'),
            'data' => [
                'working' => $user->working,
                'onorder' => $user->onorder,
                'numorders' => $user->numorders,
                'rejectedorders'=>$user->rejectedorders,
            ]
        ]);
    }

    public function deactivate()
    {
        $user=User::where(['api_token' => $_GET['api_token']])->first();
        
        if ($user) {
            $user->working = 0;
            $user->active = 0;
            $user->update();

            return response()->json([
                'status' => true,
                'message' => __('User deactivated')
            ]);
        }else{
            return $this->invalidResponse();
        }
    }
  
    /**
     * Get driver data
     */
    public function getUseData()
    {
        $user = User::where(['api_token' => $_GET['api_token']])->first();

        if ($user) {
            return response()->json([
                'status' => true,
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ? $user->phone : '',
                    'working' => $user->working,
                    'onorder' => $user->onorder,
                    'numorders' => $user->numorders,
                    'rejectedorders'=>$user->rejectedorders,
                ]
            ]);
        } else {
            return $this->invalidResponse();
        }
    }
}
