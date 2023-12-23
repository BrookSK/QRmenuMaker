@extends('layouts.app', ['title' => __($title)])
@section('js')
    @yield('js')
@endsection

@section('content')

<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8 mt--4">
 </div>
    
    <div class="container-fluid mt--9">

        <div class="col-12">
            @include('partials.flash')
        </div>

        <div class="row">
            <div class="col-3">
                <div class="card shadow">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <h3 class="mb-0">{{ __($title) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @yield('cardbody')
                   </div>

                   <div class="card-footer ">
                    
                        @isset($action_link)
                            <a id="mainAction" onclick="{{ $action_link }}" class="btn  btn-success w-100" style="color:white">{{ __($action_name) }}</a>
                        @endisset
                        @isset($action_link2) 
                                <a href="{{ $action_link2 }}" class="btn btn-sm btn-primary">{{ __($action_name2) }}</a>
                        @endisset
                        @isset($action_link3) 
                                <a href="{{ $action_link3 }}" class="btn btn-sm btn-primary">{{ __($action_name3) }}</a>
                        @endisset
                        @isset($action_link4) 
                                <a href="{{ $action_link4 }}" class="btn btn-sm btn-primary">{{ __($action_name4) }}</a>
                        @endisset
                        @isset($usefilter)
                            <button id="show-hide-filters" class="btn btn-icon btn-1 btn-sm btn-outline-secondary" type="button">
                                <span class="btn-inner--icon"><i id="button-filters" class="ni ni-bold-down"></i></span>
                            </button>
                        @endisset
                    
                   </div>
                   
                </div>
            </div>
            <div class="col-9">
                @yield('details')
            </div>
        </div>
    </div>
@endsection
