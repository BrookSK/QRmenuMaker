
@extends('layouts.front', ['title' => __('User Profile')])
@if (strlen(config('settings.recaptcha_site_key'))>2)
    @section('head')
    {!! htmlScriptTagJsApi([]) !!}
    @endsection
@endif

@section('content')
    @include('users.partials.header', ['title' => ""])


    <div class="container-fluid mt--7">
        <div class="row">

            </div>
            <div class="col-xl-8 offset-xl-2">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <h3 class="col-12 mb-0">{{ $setup['title'] }}</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="{{ getFormId() }}" action="{{ $setup['action'] }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @isset($setup['isupdate'])
                                @method('PUT')
                            @endisset
                            @if (session('status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('status') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            @isset($setup['inrow'])
                                <div class="row">
                            @endisset
                                @include('partials.fields',['fields'=>$fields])
                            @isset($setup['inrow'])
                                </div>
                            @endisset
                            <br />
                            @isset($setup['action_link'])
                                <a href="{{ $setup['action_link'] }}" class="btn btn-secondary">{{ __($setup['action_name']) }}</a>
                            @endisset

                           

                            @if (strlen(config('settings.recaptcha_site_key'))>2)
                           
                                @if ($errors->has('g-recaptcha-response'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                </span>
                                @endif

                                {!! htmlFormButton(__('Save'), ['id'=>'thesubmitbtn','class' => 'btn btn-success mt-4']) !!}
                            @else
                       
                                <button type="submit" id="thesubmitbtn" class="btn btn-success mt-4">{{__('Save')}}</button>
                            @endif


                        </form>
                    </div>
                </div>
            </div>
        </div>
        <br/>
    </div>
@endsection

@section('js')
@if (isset($_GET['name'])&&$errors->isEmpty())
<script>
    "use strict";
    document.getElementById("thesubmitbtn").click();
</script>
@endif
@endsection

