<!-- Top Notification Bar -->
@if (session('status'))
<a class="block pt-16 pb-6 bg-blue-900 opacity-100 md:h-16 md:pt-0 md:pb-0">
    <span class="relative flex items-center justify-center h-full max-w-6xl pl-10 pr-20 mx-auto leading-tight text-left md:text-center">
      <span class="text-white">{{ __("".session('status')) }}</span>
    </span>
</a>
@endif

<!-- Section 2 -->
<section id="heroSection" class="relative w-full bg-center bg-cover" style="background-image:url('https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80')" data-tails-scripts="//unpkg.com/alpinejs">

    <div class="absolute inset-0 bg-gradient-to-br from-blue-900 via-blue-700 to-blue-400 opacity-10"></div>

    <div class="mx-auto max-w-7xl opacity-90">

        <div class="flex flex-col items-center px-10 pt-20 pb-40 lg:flex-row">
            
            <div class="relative z-10 w-full max-w-2xl mt-20 lg:mt-0 lg:w-6/12">
                <div class="flex flex-col  items-start justify-start  bg-white shadow-2xl rounded-2xl">
                    <!-- Tabs  -->
                    <ul class="bg-gray-50 nav nav-tabs flex flex-row w-full justify-around list-none border-b-2 pl-0 mb-0 pt-10 px-10 pb-0 mb-0 rounded-2xl" id="tabs-tabFill" role="tablist">

                        <li  class="nav-item basis-1/3 text-center text-3xl" role="presentation">
                            <i onclick="fireClick(3)" class="las la-car text-3xl"></i>
                            <a onclick="changeBG(3)" href="#tabs-ride"
                                class="nav-link block w-full font-medium text-sm leading-tight uppercase border-x-0 border-t-0 border-b-4 border-transparent px-6 py-3 my-2 hover:border-transparent focus:border-transparent active"
                                id="tabs-ride-tab" data-bs-toggle="pill" data-bs-target="#tabs-ride" role="tab"
                                aria-controls="tabs-ide" aria-selected="false">Ride</a>
                        </li>
                        @if (config('settings.enable_external_system',false))
                            <li class="nav-item basis-1/3 text-center text-3xl" role="presentation">
                                <i onclick="fireClick(2)" class="las la-utensils text-3xl"></i>
                                <a  onclick="changeBG(2)" href="#tabs-eat"
                                    class="nav-link block w-full font-medium text-sm leading-tight uppercase border-x-0 border-t-0 border-b-4 border-transparent px-6 py-3 my-2 hover:border-transparent focus:border-transparent"
                                    id="tabs-eat-tab" data-bs-toggle="pill" data-bs-target="#tabs-eat" role="tab"
                                    aria-controls="tabs-eat" aria-selected="false">Eat</a>
                            </li>
                        @endif
                        
                        <li  class="nav-item basis-1/3 text-center text-3xl" role="presentation">
                            <i onclick="fireClick(1)" class="las la-biking text-3xl" ></i>
                            <a onclick="changeBG(1)" href="#tabs-deliver"
                                class="nav-link block w-full font-medium text-sm leading-tight uppercase border-x-0 border-t-0 border-b-4 border-transparent px-6 py-3 my-2 hover:border-transparent  focus:border-transparent"
                                id="tabs-deliver-tab" data-bs-toggle="pill" data-bs-target="#tabs-deliver" role="tab" aria-controls="tabs-home"
                                aria-selected="true">
                                Deliver</a>
                        </li>
                    </ul>
                    <div class="tab-content p-10" id="tabs-tabContent">
                        <div class="tab-pane fade show active w-full" id="tabs-ride" role="tabpanel" aria-labelledby="tabs-ride-tab">
                            <form action="{{ route('cockpit.findtaxi') }}">
                                <p class="text-6xl mb-10 mt-5">Request a ride</p>
                                <div>
                                    <div class="flex justify-center w-full">
                                        <div>
                                          <div class="form-floating mb-3 xl:w-96">
                                            <span id="search_location" class="absolute inset-y-0 right-2 flex items-center pl-2 opacity-40">
                                                <svg  fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4"><path d="M10.5 13.5L.5 11 21 3l-8 20.5-2.5-10z"></path></svg>
                                            </span>
                                            <input name="start" id="txtstartlocation" class="form-control
                                            block
                                            w-full
                                            px-3
                                            py-1.5
                                            text-base
                                            font-normal
                                            text-gray-700
                                            bg-white bg-clip-padding
                                            border border-solid border-gray-300
                                            rounded
                                            transition
                                            ease-in-out
                                            m-0
                                            focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="floatingInput" placeholder="name@example.com">
                                            <label for="txtstartlocation" class="text-gray-700">Pickup location</label>
                                          </div>
                                          <div class="form-floating mb-3 xl:w-96">
                                            <input name="end" id="txtendlocation" class="form-control
                                            block
                                            w-full
                                            px-3
                                            py-1.5
                                            text-base
                                            font-normal
                                            text-gray-700
                                            bg-white bg-clip-padding
                                            border border-solid border-gray-300
                                            rounded
                                            transition
                                            ease-in-out
                                            m-0
                                            focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="floatingPassword" placeholder="Password">
                                            <label for="txtendlocation" class="text-gray-700">Destination</label>
                                          </div>
                                        <div style="display: none">
                                            <input type="hidden" name="phone">
                                        </div>

                                        @if(config('settings.is_demo'))
                                            <div class=" bg-gray-100 p-6 pt-6 rounded-md ">
                                                
                                                
                                                <div>
                                                    <span>🚖</span><a  class="ml-3 mt-2 underline" href="/findtaxi?start=Brooklyn,NY,USA&end=Queens,New York,NY,USA&phone=+38977777777">Brooklyn to Queens</a><br />
                                                    <span>🚕</span><a  class="ml-3 mt-2 underline" href="/findtaxi?start=Bronx,NY,USA&end=Manhattan,New York,NY,USA&phone=+38977777777">Bronx to Manhattn</a><br />
                                                    <span>🚖</span><a  class="ml-3 mt-2 underline" href="/findtaxi?start=Queens,NY,USA&end=Manhattan,Brooklyn,NY,USA&phone=+38977777777">Queens to Brooklyn</a>
                                                </div>
                                            </div>
                                        @endif
                                          
                                        </div>
                                      </div>
                                </div>
                                <div class="my-10">
                                    <button disabled type="submit" id="submit" class="opacity-50 inline-block px-8 py-3.5 bg-gray-800 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-900 hover:shadow-lg focus:bg-gray-900 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-900 active:shadow-lg transition duration-150 ease-in-out">Request ride</button>
                                </div>
                                <p class="pb-10"></p>
                               
                            </form>
                           
                        </div>
                        @if (config('settings.enable_external_system',false))
                            <div class="tab-pane fade" id="tabs-eat" role="tabpanel" aria-labelledby="tabs-eat-tab">
                                <p class="text-6xl mb-10 mt-5">Discover delicious eats</p>
                                <p>Order delivery from restaurants you love.</p>
                                <div class="my-10">
                                    <a href="{{ config('settings.external_system_url')}}" target="_blank" type="button" class="inline-block px-8 py-3.5 bg-gray-800 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-900 hover:shadow-lg focus:bg-gray-900 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-900 active:shadow-lg transition duration-150 ease-in-out">Order now</a>
                                </div>
                                <a target="_blank" href="{{ config('settings.external_system_url')}}/new/restaurant/register" class="underline opacity-60">Own a restaurant? Partner with us</a>
                                <p class="pb-10"></p>
                            </div>
                        @endif
                        
                        <div class="tab-pane fade" id="tabs-deliver" role="tabpanel" aria-labelledby="tabs-deliver-tab">
                            <p class="text-6xl mb-10 mt-5">Get in the driver’s seat and get paid.</p>
                            <p>Drive on the platform with the largest network of active riders.</p>
                            <div class="my-10">
                                <a href="{{route('driver.register')}}" type="button" class="inline-block px-8 py-3.5 bg-gray-800 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-900 hover:shadow-lg focus:bg-gray-900 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-900 active:shadow-lg transition duration-150 ease-in-out">Sign up to drive</a>
                            </div>
                            
                           
                            <p class="pb-10"></p>
                        </div>
                        
                       
                    </div>
                    <!-- End Tabs-->
                    
                </div>

            </div>

            <div class="relative w-full max-w-2xl bg-cover lg:w-7/12">
                <div class="relative flex flex-col items-center justify-center w-full h-full lg:pr-10">
                    <div class="flex flex-col items-start space-y-8">
                        
                </div>
                </div>
            </div>

            
        </div>
    </div>

</section>