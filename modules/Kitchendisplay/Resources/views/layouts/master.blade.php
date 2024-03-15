<!--
=========================================================
* Soft UI Dashboard - v1.0.1
=========================================================

* Product Page: https://www.creative-tim.com/product/black-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://github.com/creativetimofficial/black-dashboard/blob/master/LICENSE.md)

* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('softd') }}/img/apple-icon.png">
  <link rel="icon" type="image/png" href="{{ asset('softd') }}/img/favicon.png">
  <title>
    {{ $vendor->name." - ".config('app.name')}}
  </title>
  <!--     Fonts and icons     -->
  <link href="{{ asset('css') }}/gfonts.css" rel="stylesheet">

  <!-- Nucleo Icons -->
  <link href="{{ asset('softd') }}/css/nucleo-icons.css" rel="stylesheet" />
  <link href="{{ asset('softd') }}/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="{{ asset('vendor') }}/fa/fa.js" crossorigin="anonymous"></script>
  <link href="{{ asset('softd') }}/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('softd') }}/css/soft-ui-dashboard.css?v=1.0.1" rel="stylesheet" />

  <!-- Select2  -->
  <link type="text/css" href="{{ asset('custom') }}/css/select2.min.css" rel="stylesheet">

  <!--Custom CSS -->
  <link type="text/css" href="{{ asset('custom') }}/css/custom.css" rel="stylesheet">

  <link type="text/css" href="{{ asset('custom') }}/css/pos.css" rel="stylesheet">
  
  @laravelPWA

</head>

<body class="g-sidenav-show bg-gray-100">
  <div class="main-content position-relative bg-gray-100">
    @include('kitchendisplay::navbar')
    @yield('orders')
    </div>
  </div>
  <!--   Core JS Files   -->
  <script src="{{ asset('softd') }}/js/core/popper.min.js"></script>
  <script src="{{ asset('softd') }}/js/core/bootstrap.min.js"></script>
  <script src="{{ asset('softd') }}/js/plugins/smooth-scrollbar.min.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{ asset('softd') }}/js/soft-ui-dashboard.min.js?v=1.0.1"></script>

  <script src="{{ asset('argon') }}/vendor/jquery/dist/jquery.min.js"></script>

  <!-- Import Vue -->
  <script src="{{ asset('vendor') }}/vue/vue.js"></script>
  <!-- Import AXIOS --->
  <script src="{{ asset('vendor') }}/axios/axios.min.js"></script>

  <!-- Import Interact --->
  <script src="{{ asset('vendor') }}/interact/interact.min.js"></script>
  
  <!-- Import Select2 --->
  <script src="{{ asset('vendor') }}/select2/select2.min.js"></script>

  <!-- printThis -->
  <script src="{{ asset('vendor') }}/printthis/printThis.js"></script> 



   <!-- Add to Cart   -->
   <script>
      var LOCALE="<?php echo  App::getLocale() ?>";
      var CASHIER_CURRENCY = "<?php echo  config('settings.cashier_currency') ?>";
      var USER_ID = '{{  auth()->user()&&auth()->user()?auth()->user()->id:"" }}';
      var PUSHER_APP_KEY = "{{ config('broadcasting.connections.pusher.key') }}";
      var PUSHER_APP_CLUSTER = "{{ config('broadcasting.connections.pusher.options.cluster') }}";
      var CASHIER_CURRENCY = "<?php echo  config('settings.cashier_currency') ?>";
      var LOCALE="<?php echo  App::getLocale() ?>";
      var SELECT_OR_ENTER_STRING="{{ __('Select, or enter keywords to search items') }}";
      var TOKEN="<?php echo $token ?>"

      // "Global" flag to indicate whether the select2 control is oedropped down).
      var _selectIsOpen = false;

      
   </script>
   <script src="{{ asset('custom') }}/js/cartKDSFunctions.js"></script>
   
   <!-- Cart custom sidemenu -->
   <script src="{{ asset('custom') }}/js/cartSideMenu.js"></script>

   <!-- All in one -->
   <script src="{{ asset('custom') }}/js/js.js?id={{ config('config.version')}}"></script>

   <!-- Notify JS -->
   <script src="{{ asset('custom') }}/js/notify.min.js"></script>


  @stack('js')
  @yield('js')

  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }

  </script>


  <script type="text/javascript">

      $(function() {
        //INterval getting orders
        setInterval(() => {
          getAllOrders();

        }, 12000);


      });
      
    
  

   


  </script>


    
     

</body>

</html>