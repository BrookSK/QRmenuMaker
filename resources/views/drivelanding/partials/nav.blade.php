<!-- Section 1 -->
<section class="relative bg-black" data-tails-scripts="//unpkg.com/alpinejs">
    <div class="flex items-center justify-between h-20 px-8 mx-auto max-w-7xl">

        <a href="#_" class="relative z-10 flex items-center w-auto text-2xl font-extrabold leading-none text-white select-none">
            {{ strtolower(config('global.site_name','FindMeTaxi')) }}
        </a>

        <nav class="items-center justify-center hidden space-x-8 text-gray-200 md:flex">
            
            <!-- Languages -->
            @if(isset($availableLanguages)&&count($availableLanguages)>1)

                <div x-data="{ isOpen: false }" @mouseenter="isOpen = true" @mouseleave="isOpen = false" class="relative flex items-center w-full h-12 border-t border-gray-800 md:border-0 md:w-auto md:h-full">
                    <div class="relative z-10 flex items-center w-full pl-10 space-x-1 text-gray-300 cursor-pointer md:w-auto md:pl-0 hover:text-gray-200 focus:outline-none">
                        <span class="">
                            @foreach ($availableLanguages as $short => $lang)
                                @if(strtolower($short) == strtolower($locale))<span class="nav-link-inner--text  text-base font-bold text-gray-200 uppercase transition duration-150 ease hover:text-white">{{ $lang }}</span>@endif
                            @endforeach

                        </span>
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    </div>

                    

                    <div x-show="isOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute top-0 left-0 z-20 w-full mt-4 -ml-0 overflow-hidden transform bg-black border-2 border-gray-800 rounded-lg shadow-lg md:mt-16 lg:left-1/2 lg:-ml-24 md:w-48" style="display: none;">
                        @foreach ($availableLanguages as $short => $lang)
                            <a href="/{{ strtolower($short) }}" class="block p-4 px-5 text-sm text-gray-300 capitalize cursor-pointer hover:bg-gray-900 hover:text-gray-200">
                                {{ $lang }} 
                            </a>
                        @endforeach
                    
                    </div>
                </div>
            @endif

          
            <div class="w-0 h-5 border border-r border-gray-700"></div>

            @guest()
                        <a href="{{ route('register') }}" class="relative text-lg font-medium tracking-wide text-green-400 transition duration-150 ease-out md:text-sm md:text-white" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                            <span class="block  text-base font-bold text-gray-200 uppercase transition duration-150 ease hover:text-white">{{ __('Sign up') }}</span>
                            <span class="absolute bottom-0 left-0 inline-block w-full h-1 -mb-1 overflow-hidden">
                                <span x-show="hover" class="absolute inset-0 inline-block w-full h-1 h-full transform border-t-2 border-green-400" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="scale-0" x-transition:enter-end="scale-100" x-transition:leave="transition ease-out duration-300" x-transition:leave-start="scale-100" x-transition:leave-end="scale-0" style="display: none;"></span>
                            </span>
                        </a>
                        <a href="/login" class="flex items-center px-3 py-2 text-sm font-medium tracking-normal text-white transition duration-150 bg-green-400 rounded hover:bg-green-500 ease">
                            <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            {{ __('taxi.login') }}
                        </a>
                    @endguest
                    @auth()
                        <a href="/login" class="flex items-center px-3 py-2 text-sm font-medium tracking-normal text-white transition duration-150 bg-green-400 rounded hover:bg-green-500 ease">
                            <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            {{ __('taxi.dashboard') }}
                        </a>
                    @endauth
           
        </nav>

        <!-- Mobile Button -->
        <div class="flex items-center justify-center h-full text-white md:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
            </svg>
        </div>

    </div>
</section>