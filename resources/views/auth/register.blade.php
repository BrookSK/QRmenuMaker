@extends('layouts.app', ['class' => 'bg-default'])
@if (strlen(config('settings.recaptcha_site_key'))>2)
    @section('head')
    {!! htmlScriptTagJsApi([]) !!}
    @endsection
@endif

@section('content')
    @include('layouts.headers.guest')

    <div class="container mt--8 pb-5">
        <!-- Table -->
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card bg-secondary shadow border-0">
                    <div class="card-body px-lg-5 py-lg-5">
                        <form id="{{ getFormId() }}" role="form" method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                <div class="input-group input-group-alternative mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-hat-3"></i></span>
                                    </div>
                                    <input class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" placeholder="{{ __('Name') }}" type="text" name="name" value="{{ old('name') }}" required autofocus>
                                </div>
                                @if ($errors->has('name'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                                <div class="input-group input-group-alternative mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                    </div>
                                    <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('Email') }}" type="email" name="email" value="{{ old('email') }}" required>
                                </div>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('phone') ? ' has-danger' : '' }}">
                                <div class="input-group input-group-alternative mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-mobile-button"></i></span>
                                    </div>
                                    <input class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" placeholder="{{ __('Phone') }}" type="phone" name="phone" value="{{ old('phone') }}" required>
                                </div>
                                @if ($errors->has('phone'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    </div>
                                    <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('Password') }}" type="password" name="password" required>
                                </div>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    </div>
                                    <input class="form-control" placeholder="{{ __('Confirm Password') }}" type="password" name="password_confirmation" required>
                                </div>
                            </div>

                            @if(config('settings.enable_birth_date_on_register'))
                            <div class="form-group">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-badge"></i></span>
                                    </div>
                                    <input class="form-control" placeholder="{{ __('Date of Birth') }}" id="birth_date" type="date" name="birth_date" required>
                                </div>
                                @if ($errors->has('birth_date'))
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    <strong>{{ $errors->first('birth_date') }}</strong>
                                </span>
                            @endif
                            </div>
                            @endif

                            


                            <div class="text-center">
                                @if (strlen(config('settings.recaptcha_site_key'))>2)
                           
                                    @if ($errors->has('g-recaptcha-response'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                        </span>
                                    @endif

                                    {!! htmlFormButton(__('Save'), ['id'=>'thesubmitbtn','class' => 'btn btn-success mt-4', 'disabled'=>"true"]) !!}
                                @else
                                    <button disabled id="thesubmitbtn" type="submit" class="btn btn-primary mt-4">{{ __('Create account') }}</button>
                                @endif

                                <br /><br />
                                <div class="form-check"><input type="checkbox" name="termsCheckBox" id="termsCheckBox" class="h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top  "> 
                                    <label for="terms" class="form-check-label text-gray-500">
                                    &nbsp;&nbsp;{{__('i_agree_to')}}
                                    <a href="{{config('settings.link_to_ts')}}" target="_blank" style="text-decoration: underline;">{{__('terms_of_service')}}</a> {{__('and')}}
                                    <a href="{{config('settings.link_to_pr')}}" target="_blank" style="text-decoration: underline;">{{__('privacy_policy')}}</a>.
                                    </label>
                                </div>

                                <script>
                                    window.onload = function () {
                                
                                    $('#termsCheckBox').on('click',function () {
                                        $('#thesubmitbtn').prop("disabled", !$("#termsCheckBox").prop("checked"));
                                        if(this.checked){
                                            $('#thesubmitbtn').addClass('opacity-100');
                                        }else{
                                            $('#thesubmitbtn').removeClass('opacity-100');
                                             
                                        }
                                       
                                    })
                                }
                                </script>
                               
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-6">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-light">
                                <small>{{ __('Forgot password?') }}</small>
                            </a>
                        @endif
                    </div>
                    <div class="col-6 text-right">
                        <a href="{{ route('login') }}" class="text-light">
                            <small>{{ __('Back to login') }}</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
