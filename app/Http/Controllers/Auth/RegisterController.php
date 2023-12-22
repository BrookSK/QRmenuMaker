<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\WelcomeNotification;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function redirectTo()
    {
        $lastVendor=session('last_visited_restaurant_alias',null);
        if($lastVendor&&auth()->user()->hasRole('client')){
            return route('vendrobyalias',['alias'=>$lastVendor]);
        }else{
            return route('home');
        }
        
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
        if (strlen(config('settings.recaptcha_site_key')) > 2) {
            $rules['g-recaptcha-response'] = 'recaptcha';
        }
        if (config('settings.enable_birth_date_on_register') && config('settings.minimum_years_to_register')) {
            $rules['birth_date'] = 'required|date|date_format:Y-m-d|before:-'.config('settings.minimum_years_to_register').' years';
        }
        //dd($rules);
        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'api_token' => Str::random(80),
            'birth_date' => isset($data['birth_date']) ? $data['birth_date'] : '',
        ]);

        $user->assignRole('client');

        //Send welcome email
        return $user;
    }

    protected function registered(Request $request, User $user)
    {
        return redirect($this->redirectPath());
    }
}
