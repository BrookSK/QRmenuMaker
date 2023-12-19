<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordRequest;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Hash;
use App\Traits\Fields;
use App\Traits\Modules;

class ProfileController extends Controller
{

    use Fields;
    use Modules;

    /**
     * Show the form for editing the profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $rawFields=$this->vendorFields(auth()->user()->getAllConfigs(),auth()->user()->roles->toArray()[0]['name']."_fields");
        $appFields=$this->convertJSONToFields($rawFields);
        return view('profile.edit',[
            'appFields'=>$appFields
        ]);
    }

    /**
     * Update the profile.
     *
     * @param  \App\Http\Requests\ProfileRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $request)
    {
        auth()->user()->update($request->all());

        //Update custom fields
        $rawFields=$this->vendorFields(auth()->user()->getAllConfigs(),auth()->user()->roles->toArray()[0]['name']."_fields");
        //dd($request->all());
        $this->setMultipleConfig(auth()->user(),$request,$rawFields);
        

        return back()->withStatus(__('Profile successfully updated.'));
    }

    /**
     * Change the password.
     *
     * @param  \App\Http\Requests\PasswordRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function password(PasswordRequest $request)
    {
        auth()->user()->update(['password' => Hash::make($request->get('password'))]);

        return back()->withPasswordStatus(__('Password successfully updated.'));
    }
}
