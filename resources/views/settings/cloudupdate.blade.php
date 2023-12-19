@extends('layouts.app', ['title' => __('Updates')])

@section('content')
<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-6 order-xl-1">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <h3 class="mb-0">{{ __('Updates Management') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if (isset($okMemory)&&!$okMemory)
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            {{ __('There is not enough PHP memory_limit. Please refer to docs on how to increase to at least 512MB')}}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($newVersionAvailable)
                        <a href="{{ route('settings.cloudupdate') }}?do_update=true" class="btn btn-sm btn-success">{{ __('New version available') }} - v{{$newVersion}}</a>
                        @else
                        <a class="btn btn-sm btn-white" href="javascript:alert('You do have the latest major version')">{{ __('Latest version')}}</a>
                    @endif     
                </div>
            </div>
        </div>

        @if (config('settings.enalbe_change_log_in_update'))
            <div class="col-xl-6 order-xl-1">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-4">
                                <h3 class="mb-0">{{ __('Change log') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        {{ Illuminate\Mail\Markdown::parse($theChangeLog) }} 
                        
                    </div>
                </div>
            </div>
        @endif
        


    </div>
</div>
<br/><br/>
</div>
@endsection




