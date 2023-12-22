<?php

namespace App\Http\Controllers\CRUD;

use App\Models\Allergens;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AllergensController extends Controller
{
    protected $imagePath = 'uploads/restorants/';

    private function validateAccess()
    {
        if (! auth()->user()->hasRole('admin')) {
            abort(404);
        }
    }

    private function getFields()
    {
        return [
            ['ftype'=>'image', 'name'=>__('Allergen image'), 'id'=>'image'],
            ['ftype'=>'input', 'name'=>'Title', 'id'=>'title', 'placeholder'=>__('Enter title'), 'required'=>true],
            //['ftype'=>'input', 'name'=>'Description', 'id'=>'description', 'placeholder'=>__('Enter description'), 'required'=>true],
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->validateAccess();

        
        return view('crud.allergens.index', ['setup' => [
            'iscontent' => true,
            'title' => __('Allergens'),
            'action_link' => route('admin.allergens.create'),
            'action_name' => __('Add new allergen'),
            'items' => Allergens::where('post_type', 'allergen')->get(),
            'item_names' => 'allergens',
            'breadcrumbs' => [
                //[__('Landing Page'), route('admin.landing')],
                [__('Allergens'), route('admin.allergens.index')],
            ],
        ]]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->validateAccess();

        return view('general.form', ['setup' => [
            'title' => __('New allergen'),
            'action_link' => route('admin.allergens.index'),
            'action_name' => __('Back'),
            'iscontent' => true,
            'action' => route('admin.allergens.store'),
            'breadcrumbs' => [
               // [__('Landing Page'), route('admin.landing')],
                [__('Allergens'), route('admin.allergens.index')],
                [__('New'), null],
            ],
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
        $this->validateAccess();
        //Validate first
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            //'description' => ['required', 'string', 'max:255'],
        ]);

        $allergen = Allergens::create([
            'post_type' => 'allergen',
            'title' => $request->title,
           // 'description' => $request->description,
            'image'=>'',
        ]);

        $allergen->save();

        if ($request->hasFile('image')) {
            $allergen->image = $this->saveImageVersions(
                $this->imagePath,
                $request->image,
                [
                    ['name'=>'large', 'w'=>48, 'h'=>48],
                ]
            );
            $allergen->update();
        }

        return redirect()->route('admin.allergens.index')->withStatus(__('Allergen was added'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Allergens  $allergens
     * @return \Illuminate\Http\Response
     */
    public function show(Allergens $allergens)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Allergens  $allergens
     * @return \Illuminate\Http\Response
     */
    public function edit(Allergens $allergen)
    {
        $this->validateAccess();
        $fields = $this->getFields();
        $fields[0]['value'] = $allergen->image_link;
        $fields[1]['value'] = $allergen->title;
        //$fields[2]['value'] = $allergen->description;

        return view('general.form', ['setup' => [
            'title' => __('Edit this allergen'),
            'action_link' => route('admin.allergens.index'),
            'action_name' => __('Back'),
            'iscontent' => true,
            'isupdate' => true,
            'action' => route('admin.allergens.update', ['allergen' => $allergen->id]),
            'breadcrumbs' => [
                //[__('Landing Page'), route('admin.landing')],
                [__('Allergens'), route('admin.allergens.index')],
                [$allergen->id, null],
            ],
        ],
        'fields'=>$fields, ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Allergens  $allergens
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Allergens $allergen)
    {
        $this->validateAccess();

        $allergen->title = $request->title;
        //$allergen->description = $request->description;

        if ($request->hasFile('image')) {
            $allergen->image = $this->saveImageVersions(
                $this->imagePath,
                $request->image,
                [
                    ['name'=>'large', 'w'=>48, 'h'=>48],
                ]
            );
        }

        $allergen->update();

        return redirect()->route('admin.allergens.index')->withStatus(__('Allergen was updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Allergens  $allergens
     * @return \Illuminate\Http\Response
     */
    public function destroy(Allergens $allergen)
    {
        $this->validateAccess();

        $allergen->delete();

        return redirect()->route('admin.allergens.index')->withStatus(__('Allergen was deleted.'));
    }
}
