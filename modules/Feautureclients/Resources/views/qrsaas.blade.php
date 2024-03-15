<section id="pricing" class="section-header bg-primary text-white">
    <div class="container">

        <div class="row justify-content-center mb-6">
            <div class="col-12 col-md-10 text-center">
                <i class="fas fa-edit mr-2 text-white ckedit_btn" type="button" style="display: none"></i>
                <h1 class="display-2 mb-3 ckedit" key="featured_clients" id="featured_clients">
                    {{ __('qrlanding.featured_clients') }}</h1>
                <i class="fas fa-edit mr-2 text-white ckedit_btn" type="button" style="display: none"></i>
                <p class="lead px-5 ckedit" key="list_of_featured_clients" id="list_of_featured_clients">
                    {{__('qrlanding.list_of_featured_clients')}}</p>
            </div>
        </div>
        <div class="row text-gray">
            @foreach ($featured_vendors as $vendor)
            <div class="col-md-4 col-lg-3 mb-5">
                <div class="card shadow-soft border-light">
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

</section>
