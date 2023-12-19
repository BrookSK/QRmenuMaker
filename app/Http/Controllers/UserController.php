<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Notifications\CloseAccount;
use App\Restorant;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @param  \App\User  $model
     * @return \Illuminate\View\View
     */
    public function index(User $model)
    {
        $this->adminOnly();
        return view('users.index', ['users' => $model->paginate(15)]);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->adminOnly();
        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\User  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request, User $model)
    {
        $model->create($request->merge(['password' => Hash::make($request->get('password'))])->all());

        return redirect()->route('user.index')->withStatus(__('User successfully created.'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \App\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        $this->adminOnly();
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request, User $user)
    {
        $user->update(
            $request->merge(['password' => Hash::make($request->get('password'))])
                ->except([$request->get('password') ? '' : 'password']
        ));

        return redirect()->route('user.index')->withStatus(__('User successfully updated.'));
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
       
        if (auth()->user()->hasRole('owner')&&$user->id==auth()->user()->id) {
            $data = Restorant::with('orders.items')->with('orders.status')->with('categories.items.variants.extras')->with('categories.items.extras')->with('categories.items.options')->where('id', $user->restorant->id)->get()->toArray();

            //delete restaurant
            $user->restorant->forceDelete();

            $user->notify(new CloseAccount($user, json_encode($data)));

            //Logout user
            auth()->logout();

            //delete user
            $user->delete();

            $user->removeUserPersonalData();

            return redirect()->route('front')->withStatus(__('notifications_acc_closed_msg'));
        }else if(auth()->user()->hasRole('client')&&$user->id==auth()->user()->id){
            //Same user, allow removal
            $data=$user->orders()->select(['id','created_at','restorant_id','delivery_price','order_price'])->get()->toArray();
            //Sent him the data
            $user->notify(new CloseAccount($user, json_encode($data)));

            //Logout user
            auth()->logout();

            //delete user
            $user->delete();

            $user->removeUserPersonalData();

            //Redirect to home page
            return redirect()->route('front')->withStatus(__('notifications_acc_closed_msg'));

        } else {
            
            $this->adminOnly();
            $user->delete();
            return redirect()->route('user.index')->withStatus(__('User successfully deleted.'));
        }
    }

    public function checkPushNotificationId(UserRequest $request)
    {
        return response()->json([
            'userId' => $request->userId,
            'status' => true,
            'errMsg' => '',
        ]);
    }

    public function stripeReAuth(Request $request)
    {
        dd($request->all());
    }

    public function stripeOnNoardResponse(Request $request)
    {
        dd($request->all());
    }
}
