@extends('layouts.app', ['title' => __('Clients Management')])

@section('content')
@include('drivers.partials.header', ['title' =>$client->name ])

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-9 order-xl-1">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">{{ __('Orders') }}</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('clients.index') }}"
                                class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <div class="pl-lg-0">


                        @if(count($orders))
                            <div class="table-responsive">
                                <table class="table align-items-center">
                                    @if (isset($financialReport))
                                        @include('finances.financialdisplay')
                                    @elseif (config('app.isqrsaas'))
                                        @include('orders.partials.orderdisplay_local',['hideAction'=>true])
                                    @else
                                        @include('orders.partials.orderdisplay',['hideAction'=>true])
                                    @endif
                                </table>
                            </div>
                            @endif
                            <div class=" py-4">
                                @if(count($orders))
                                <nav class="d-flex justify-content-end" aria-label="...">
                                    {{ $orders->appends(Request::all())->links() }}
                                </nav>
                                @else
                                    <h4>{{ __('No items') }} ...</h4>
                                @endif
                            </div>


                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 order-xl-2">
            @include('clients.profile')
        </div>

    </div>

    @include('layouts.footers.auth')
</div>
@endsection
