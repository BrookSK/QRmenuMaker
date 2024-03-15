<section class="w-full py-12 bg-white lg:py-24">
    <div class="max-w-6xl px-12 mx-auto text-center">
        <div class="text-left">
            <div class="mb-10 sm:mx-auto">
                <h3 class="relative text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Featured clients')}}</h3>
               
            </div>
        </div>

        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($featured_vendors as $vendor)
                <a href="{{$vendor->getLinkAttribute()}}">
                    <div class="w-full border border-gray-200 rounded-lg shadow-sm">
                        <div class="flex flex-col items-center justify-center p-10">
                        
                                <img class="w-50 h-50 mb-6" src="{{ $vendor->logom }}">
                                <h2 class="text-lg font-medium">{{ $vendor->name }}</h2>
                                <p class="text-gray-400">{{$vendor->description}}</p>
                            
                        </div>
                    </div>
                </a>
                       
            @endforeach
        </div>

    </div>
</section>