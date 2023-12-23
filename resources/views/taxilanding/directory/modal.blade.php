<!--Modal-->
<div class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
    
    <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
      
      <div class="modal-close absolute top-10 right-1 cursor-pointer flex flex-col items-center mt-4 mr-4 text-white text-sm z-50">
        <svg class="fill-current text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
          <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
        </svg>
        <span class="text-sm">(Esc)</span>
      </div>

      <!-- Add margin if you want to see some of the overlay behind the modal-->
      <div class="modal-content py-4 text-left px-6 m-1">
        <!--Title-->
        <div class="flex justify-between items-center pb-3">
          <p class="text-2xl font-bold">{{ __('taxi.confirmdialog_title')}}</p>
          <div class="modal-close cursor-pointer z-50">
            <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
              <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
            </svg>
          </div>
        </div>

        <!--Body-->
        <p class="pt-4">{{$start['name']}}</p>
        <img class="pl-4" style="height: 30px" src="/images/icons/common/down.svg" />
        <p>{{$end['name']}}</p>
        
        <p class="pt-5"><span class="opacity-70">{{__('Driver')}}:</span> <span id="selectedDriverName">XXXXXXXX</span></p>
        <p><span class="opacity-70">{{__('At your location in')}}:</span> <span id="selectedDriverTime">XXXXXXXX</span></p>
        <p><span class="opacity-70">{{__('Estimated cost')}}:</span> <span id="selectedDriverCost">XXXXXXXX</span></p>
       
        <!--Footer-->
        <div class="flex justify-end pt-4 mt-2">
          <button class="modal-close px-4 bg-transparent p-3 rounded-lg text-green-500 hover:bg-gray-100 hover:text-green-400 mr-2">{{__('Close')}}</button>
          @if (config('app.isdrive'))
            @auth
              <button id="requstRideButton" class="px-4 bg-green-500 p-3 rounded-lg text-white hover:bg-green-400">{{__('taxi.ride_now')}}</button>
            @endauth
            @guest
              <a href="{{ route('login') }}" class="px-4 bg-green-500 p-3 rounded-lg text-white hover:bg-green-400">{{__('taxi.login_to_continue')}}</a>
            @endguest
          @endif
          @if (config('app.issd'))
            <button id="requstRideButton" class="px-4 bg-green-500 p-3 rounded-lg text-white hover:bg-green-400">{{__('taxi.chat_now')}}</button>
          @endif
          
          
        </div>
        
      </div>
    </div>
</div>