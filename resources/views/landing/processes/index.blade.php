@extends('general.index', $setup)

@section('cardbody')

<div class="container-fluid">
    <div class="row">
    @foreach ($setup['items'] as $item)
        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
            <br/>
            <a href="{{ route("admin.landing.processes.edit",["process"=>$item->id]) }}">
            <div class="info info-horizontal info-hover-primary mt-5" data-toggle="tooltip" data-placement="top" title="{{ __('Edit this process')}}">
              <div class="description pl-4">
                @if (strlen( $item->image ))
                <img 
                  class="image-in-card" 
                  src='{{ $item->image_link }}'
                  width="150" 
                />
                @endif
                
                <h3 class="title">{{ $item->title }}</h3>
                <p>{{ $item->description }}</p>
                <a href="{{ $item->link }}" class="text-info">{{ $item->link_name }}</a>
              </div>
            </div>
            </a>
            <br/>
            <div class="text-center">
                <a href="{{ route("admin.landing.processes.delete",["process"=>$item->id]) }}" class="btn btn-danger btn-sm">{{ __('Delete') }}</a>
            </div>
        </div>
    @endforeach
    </div>
</div>

@endsection