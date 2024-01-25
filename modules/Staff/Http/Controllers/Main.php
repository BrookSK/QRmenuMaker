<?php

namespace Modules\Staff\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


use App\RestoArea;
use App\Tables;
use App\User;
use Illuminate\Support\Facades\Auth;

class Main extends Controller
{
    /**
     * Provide class.
     */
    private $provider = User::class;

    /**
     * Web RoutePath for the name of the routes.
     */
    private $webroute_path = 'staff.';

    /**
     * View path.
     */
    private $view_path = 'staff::';

    /**
     * Parameter name.
     */
    private $parameter_name = 'table';

    /**
     * Title of this crud.
     */
    private $title = 'staff';

    /**
     * Title of this crud in plural.
     */
    private $titlePlural = 'staff';

    /**
     * Auth checker functin for the crud.
     */
    private function authChecker()
    {
        if (!auth()->user()->hasRole('owner')) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function getFields()
    {
        return [
            ['class'=>'col-md-4', 'ftype'=>'input', 'name'=>'Name', 'id'=>'name', 'placeholder'=>'First and Last name', 'required'=>true],
            ['class'=>'col-md-4', 'ftype'=>'input', 'name'=>'Email', 'id'=>'email', 'placeholder'=>'Enter email', 'required'=>true],
            ['class'=>'col-md-4', 'ftype'=>'input','type'=>"password", 'name'=>'Password', 'id'=>'password', 'placeholder'=>'Enter password', 'required'=>true],
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authChecker();
        $fields=$this->getFields();
        unset($fields[2]);

        return view($this->view_path.'index', ['setup' => [
            'title'=>__('crud.item_managment', ['item'=>__($this->titlePlural)]),
            'action_link'=>route($this->webroute_path.'create'),
            'action_name'=>__('crud.add_new_item', ['item'=>__($this->title)]),
            'items'=>$this->getRestaurant()->staff()->paginate(config('settings.paginate')),
            'item_names'=>$this->titlePlural,
            'webroute_path'=>$this->webroute_path,
            'fields'=>$fields,
            'parameter_name'=>$this->parameter_name,
        ]]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authChecker();

        return view('general.form', ['setup' => [
            'inrow'=>true,
            'title'=>__('crud.new_item', ['item'=>__($this->title)]),
            'action_link'=>route($this->webroute_path.'index'),
            'action_name'=>__('crud.back'),
            'iscontent'=>true,
            'action'=>route($this->webroute_path.'store'),
        ],
        'fields'=>$this->getFields(), ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->authChecker();
        $item = $this->provider::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>$request->password,
            'password' => Hash::make($request->password),
            'api_token' => Str::random(80),
            'restaurant_id'=>$this->getRestaurant()->id,
        ]);
        $item->save();

        $item->assignRole('staff');

        return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_been_added', ['item'=>__($this->title)]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Tables  $tables
     * @return \Illuminate\Http\Response
     */
    public function show(Tables $tables)
    {
        //
    }

    public function loginas($id){
        $this->authChecker();

        $staff=User::findOrFail($id);

        if ($staff->restaurant->user->id!=auth()->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        Auth::login($staff, true);
        return  redirect(route('home'));


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Tables  $tables
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authChecker();
        
        $item = $this->provider::findOrFail($id);
        if (!$this->getRestaurant()->id==$item->restaurant_id) {
            abort(403, 'Unauthorized action.');
        }

        $fields = $this->getFields();
        $fields[0]['value'] = $item->name;
        $fields[1]['value'] = $item->email;

        $parameter = [];
        $parameter[$this->parameter_name] = $id;

        return view('general.form', ['setup' => [
            'inrow'=>true,
            'title'=>__('crud.edit_item_name', ['item'=>__($this->title), 'name'=>$item->name]),
            'action_link'=>route($this->webroute_path.'index'),
            'action_name'=>__('crud.back'),
            'iscontent'=>true,
            'isupdate'=>true,
            'action'=>route($this->webroute_path.'update', $parameter),
        ],
        'fields'=>$fields, ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tables  $tables
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authChecker();
        $item = $this->provider::findOrFail($id);
        $item->name = $request->name;
        $item->email = $request->email;
        if($request->password&&strlen( $request->password)>2){
            $item->password = Hash::make($request->password);
        }
        $item->update();

        return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_been_updated', ['item'=>__($this->title)]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tables  $tables
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authChecker();
        $item = $this->provider::findOrFail($id);
        if (!$this->getRestaurant()->id==$item->restaurant_id) {
            abort(403, 'Unauthorized action.');
        }
        $item->delete();
        return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_been_removed', ['item'=>__($this->title)]));
    }
}


