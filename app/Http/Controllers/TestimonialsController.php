<?php

namespace App\Http\Controllers;

use App\Models\Testimonials;
use Illuminate\Http\Request;

class TestimonialsController extends Controller
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
            ['ftype'=>'image', 'name'=>__('Author image ( 48x48 )'), 'id'=>'image'],
            ['ftype'=>'input', 'name'=>'Author', 'id'=>'title', 'placeholder'=>__('Enter author name'), 'required'=>true],
            ['ftype'=>'input', 'name'=>'Description', 'id'=>'subtitle', 'placeholder'=>__('Enter short description'), 'required'=>true],
            ['ftype'=>'input', 'name'=>'Comment', 'id'=>'description', 'placeholder'=>__('Enter testimonial comment'), 'required'=>true],
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

        return view('landing.testimonials.index', ['setup' => [
            'iscontent' => true,
            'title' => __('Testimonials'),
            'action_link' => route('admin.landing.testimonials.create'),
            'action_name' => __('Add new testimonial'),
            'items' => Testimonials::where('post_type', 'testimonial')->get(),
            'item_names' => 'testimonials',
            'breadcrumbs' => [
                [__('Landing Page'), route('admin.landing')],
                [__('Testimonials'), route('admin.landing.testimonials.index')],
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
            'title' => __('New testimonial'),
            'action_link' => route('admin.landing.testimonials.index'),
            'action_name' => __('Back'),
            'iscontent' => true,
            'action' => route('admin.landing.testimonials.store'),
            'breadcrumbs' => [
                [__('Landing Page'), route('admin.landing')],
                [__('Testimonials'), route('admin.landing.testimonials.index')],
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
            'subtitle' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        $testimonial = Testimonials::create([
            'post_type' => 'testimonial',
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'image'=>'',
        ]);

        $testimonial->save();

        if ($request->hasFile('image')) {
            $testimonial->image = $this->saveImageVersions(
                $this->imagePath,
                $request->image,
                [
                    ['name'=>'large', 'w'=>48, 'h'=>48],
                ]
            );
            $testimonial->update();
        }

        return redirect()->route('admin.landing.testimonials.index')->withStatus(__('Testimonial was added'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Testimonials  $testimonials
     * @return \Illuminate\Http\Response
     */
    public function show(Testimonials $testimonials)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Testimonials  $testimonials
     * @return \Illuminate\Http\Response
     */
    public function edit(Testimonials $testimonial)
    {
        $this->validateAccess();
        $fields = $this->getFields();
        $fields[0]['value'] = $testimonial->image_link;
        $fields[1]['value'] = $testimonial->title;
        $fields[2]['value'] = $testimonial->subtitle;
        $fields[3]['value'] = $testimonial->description;

        return view('general.form', ['setup' => [
            'title' => __('Edit this testimonial'),
            'action_link' => route('admin.landing.testimonials.index'),
            'action_name' => __('Back'),
            'iscontent' => true,
            'isupdate' => true,
            'action' => route('admin.landing.testimonials.update', ['testimonial' => $testimonial->id]),
            'breadcrumbs' => [
                [__('Landing Page'), route('admin.landing')],
                [__('Testimonials'), route('admin.landing.testimonials.index')],
                [$testimonial->id, null],
            ],
        ],
        'fields'=>$fields, ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Testimonials  $testimonials
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Testimonials $testimonial)
    {
        $this->validateAccess();

        $testimonial->title = $request->title;
        $testimonial->subtitle = $request->subtitle;
        $testimonial->description = $request->description;

        if ($request->hasFile('image')) {
            $testimonial->image = $this->saveImageVersions(
                $this->imagePath,
                $request->image,
                [
                    ['name'=>'large', 'w'=>48, 'h'=>48],
                ]
            );
        }

        $testimonial->update();

        return redirect()->route('admin.landing.testimonials.index')->withStatus(__('Testimonial was updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Testimonials  $testimonials
     * @return \Illuminate\Http\Response
     */
    public function destroy(Testimonials $testimonial)
    {
        $this->validateAccess();

        $testimonial->delete();

        return redirect()->route('admin.landing.testimonials.index')->withStatus(__('Testimonial was deleted.'));
    }
}
