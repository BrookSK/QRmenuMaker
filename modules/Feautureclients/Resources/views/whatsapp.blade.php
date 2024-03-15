
<div id="featured" class="section features-6">
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto text-center">
            <h3 class="display-3 ckedit" key="featured_clients" id="featured_clients">{{ __('whatsapp.featured_clients') }}</h3>
            <p class="lead ckedit" key="list_of_featured_clients" id="list_of_featured_clients">{{ __('whatsapp.list_of_featured_clients') }}</p>
            </div>
          </div>
          <br />
      <div class="row">
          
        @foreach ($featured_vendors as $vendor)

        <div class="col-md-4 col-lg-3 mb-5">
            <div class="card cardWithShadow cardWithShadowAnimated">
                <div class="card-header p-0">
                    <a href="{{$vendor->getLinkAttribute()}}"><img src="{{$vendor->logom}}"class="card-img-top rounded-top" alt="image"></a>
                </div>
                <div class="card-body">
                    <h4 class="mb-3 text-black">{{$vendor->name}}</h3>
                    <p class="card-text">{{$vendor->description}}</p>
                </div>
            </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
