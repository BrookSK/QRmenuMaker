@extends('layouts.app', ['class' => 'bg'])

@section('content')
    @include('layouts.headers.guest')

    <div class="container mt--6 pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">

                @if (session('status'))
                    <div class="card bg-secondary shadow border-0">
                        <div class="card-body">
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif


                @if(config('settings.is_show_credentials',false))
                <script>
                    function loginAs(email) {
                        document.getElementById("email").value = email;
                        document.getElementById("password").value = "secret";
                        document.getElementById("loginForm").submit()
                    }
                </script>
                <div class="card bg-secondary shadow border-0">
                    <div class="card-body">
                        <div class="text-center text-muted mb-4">
                            <br />
                           <button type="button" onclick="loginAs('admin@example.com')" class="btn btn-outline-danger">Login as Admin</button><br /><br />
                           <button type="button" onclick="loginAs('owner@example.com')" class="btn btn-outline-success">Login as Owner</button><br /><br />
                           
                           
                           

                            @if (config('app.isft'))
                                <button type="button" onclick="loginAs('driver@example.com')" class="btn btn-outline-info">Login as Driver</button><br /><br />
                                <button type="button" onclick="loginAs('client@example.com')" class="btn btn-outline-primary">Login as Client</button><br /><br />
                            @endif
                            @if (config('app.issd'))
                                <button type="button" onclick="loginAs('driver@example.com')" class="btn btn-outline-info">Login as Driver</button><br /><br />
                            @endif
                            @if (config('settings.is_pos_cloud_mode'))
                                <button type="button" onclick="loginAs('staff@example.com')" class="btn btn-outline-warning">Login as Staff</button><br /><br />
                            @endif

                        </div>
                    </div>
                </div>
                @endif
                <br/>

                <div class="card bg-secondary shadow border-0">
                    <div class="card-body px-lg-5 py-lg-5">

                        @if((config('app.isft')|| isset($_GET['showCreate']))&&(strlen(config('settings.google_client_id'))>3||strlen(config('settings.facebook_client_id'))>3))
                            <div class="card-header bg-transparent pb-5">
                                <div class="text-muted text-center mt-2 mb-3"><small>{{ __('Sign in with') }}</small></div>
                                <div class="btn-wrapper text-center">

                                    @if (strlen(config('settings.google_client_id'))>3)
                                        <a href="{{ route('google.login') }}" class="btn btn-neutral btn-icon">
                                            <span class="btn-inner--icon"><img src="{{ asset('argonfront/img/google.svg') }}"></span>
                                            <span class="btn-inner--text">Google</span>
                                        </a>
                                    @endif

                                    @if (strlen(config('settings.facebook_client_id'))>3)
                                        <a href="{{ route('facebook.login') }}" class="btn btn-neutral btn-icon">
                                            <span class="btn-inner--icon"><img src="{{ asset('custom/img/facebook.png') }}"></span>
                                            <span class="btn-inner--text">Facebook</span>
                                        </a>
                                    @endif

                                </div>
                            </div>
                        @endif


                        <form id="loginForm" role="form" method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }} mb-3">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                    </div>
                                    <input id="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('Email') }}" type="email" name="email" value="{{ old('email') }}" required autofocus>
                                </div>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    </div>
                                    <input id="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{ __('Password') }}" type="password" required>
                                </div>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="custom-control custom-control-alternative custom-checkbox">
                                <input class="custom-control-input" name="remember" id="customCheckLogin" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="customCheckLogin">
                                    <span class="text-muted">{{ __('Remember me') }}</span>
                                </label>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-danger my-4">{{ __('Sign in') }}</button>
                            </div>

                            @if(config('app.isft') || isset($_GET['showCreate']))
                                <div class="text-center">
                                    <hr />
                                    <a href="{{ route('register') }}" class="btn btn-success my-4">
                                        <small>{{ __('Create new account') }}</small>
                                    </a>
                                </div>
                            @endif
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
                    
                </div>
            </div>
        </div>
    </div>
@endsection
