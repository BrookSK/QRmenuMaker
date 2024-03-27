<?php

namespace Modules\Expenses\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


use App\RestoArea;
use App\Tables;
use Modules\Expenses\Models\Expenses ;
use Modules\Expenses\Models\Categories ;
use Modules\Expenses\Models\Vendors ;

class ExpensesController extends Controller
{
    /**
     * Provide class.
     */
    private $provider = Expenses::class;

    /**
     * Web RoutePath for the name of the routes.
     */
    private $webroute_path = 'expenses.expenses.';

    /**
     * View path.
     */
    private $view_path = 'expenses::expenses.';

    /**
     * Parameter name.
     */
    private $parameter_name = 'expense';

    /**
     * Title of this crud.
     */
    private $title = 'expense';

    /**
     * Title of this crud in plural.
     */
    private $titlePlural = 'expenses';

    /**
     * Auth checker functin for the crud.
     */
    private function authChecker()
    {
        if (! auth()->user()->hasRole('owner')) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function getFields()
    {
        return [
            //date
            //amount
            //reference
            //expenses_category_id
            //expenses_vendor_id
            ['class'=>'col-md-4', 'type'=>'date','ftype'=>'input', 'name'=>'Date', 'id'=>'date', 'placeholder'=>'Date', 'required'=>true],
            ['class'=>'col-md-4', 'type'=>'number','ftype'=>'input', 'name'=>'Amount', 'id'=>'amount', 'placeholder'=>session('restaurant_currency',""), 'required'=>true],
            ['class'=>'col-md-4', 'ftype'=>'input', 'name'=>'Reference', 'id'=>'reference', 'placeholder'=>'Reference', 'required'=>true],
            ['class'=>'col-md-4', 'ftype'=>'select', 'name'=>'Category', 'id'=>'expenses_category_id', 'placeholder'=>'Category', 'required'=>true,'data'=>Categories::pluck('name','id')->toArray()],
            ['class'=>'col-md-4', 'ftype'=>'select', 'name'=>'Vendor', 'id'=>'expenses_vendor_id', 'placeholder'=>'Category', 'required'=>true,'data'=>Vendors::pluck('name','id')->toArray()],
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
    
       
        return view($this->view_path.'index', ['setup' => [
            'title'=>__('crud.item_managment', ['item'=>__($this->titlePlural)]),
            'action_link'=>route($this->webroute_path.'create'),
            'action_name'=>__('crud.add_new_item', ['item'=>__($this->title)]),

            'action_link2'=>route('expenses.vendors.index'),
            'action_name2'=>__('Vendors'),

            'action_link3'=>route('expenses.categories.index'),
            'action_name3'=>__('Categories'),


            'items'=>Expenses::paginate(config('settings.paginate')),
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

        //date
            //amount
            //reference
            //expenses_category_id
            //expenses_vendor_id

        //Validate
        $request->validate([
            'expenses_category_id' => ['required'],
            'expenses_vendor_id' => ['required']
        ]);

        $this->authChecker();
        $item = $this->provider::create([
            'date'=>$request->date,
            'amount'=>$request->amount,
            'reference'=>$request->reference,
            'expenses_category_id'=>$request->expenses_category_id,
            'expenses_vendor_id'=>$request->expenses_vendor_id
        ]);
        $item->save();

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
        $fields[0]['value'] = $item->date;
        $fields[1]['value'] = $item->amount;
        $fields[2]['value'] = $item->reference;
        $fields[3]['value'] = $item->expenses_category_id;
        $fields[4]['value'] = $item->expenses_vendor_id;

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

        //Validate
        $request->validate([
            'expenses_category_id' => ['required'],
            'expenses_vendor_id' => ['required']
        ]);

        $item->date = $request->date;
        $item->amount = $request->amount;
        $item->reference = $request->reference;
        $item->expenses_category_id = $request->expenses_category_id;
        $item->expenses_vendor_id = $request->expenses_vendor_id;
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


