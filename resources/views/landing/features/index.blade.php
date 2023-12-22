@extends('general.index', $setup)

@section('cardbody')
<div class="container-fluid">
    <div class="row">
    @foreach ($setup['items'] as $item)
        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4 mb-4">
            <br/>
            <a href="{{ route("admin.landing.features.edit",["feature"=>$item->id]) }}">
                <div class="card cardWithShadow cardWithShadowAnimated shadow" data-toggle="tooltip" data-placement="top" title="{{ __('Edit this feature')}}">
                    <div class="card-body">
                        <div class="imgHolderInCard">
                        <img 
                            class="image-in-card" 
                            src='{{ $item->image_link }}'

                            width="150" 
                            height="150"
                        />
                        </div>
                        <h3 class="card-title">{{ $item->title }}</h3>
                        <p class="card-text">{{ $item->description }}</p>
                    </div>
                </div>
            </a>
            <br/>
            <div class="text-center">
                <a href="{{ route("admin.landing.features.delete",["feature"=>$item->id]) }}" class="btn btn-danger btn-sm">{{ __('Delete') }}</a>
            </div>
        </div>
    @endforeach
    </div>
</div>

@endsection