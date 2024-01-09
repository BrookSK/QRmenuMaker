@extends('layouts.app', ['title' => __('Orders')])
@section('admin_title')
    {{__('Orders')}}
@endsection
@section('content')
    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
    </div>


    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <!-- Order Card -->
                @include('orders.partials.ordercard')
            </div>
        </div>
        @include('layouts.footers.auth')
        @include('orders.partials.modals')
    </div>
@endsection


