<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('global.site_name','FindMeTaxi') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tw-elements/dist/css/index.min.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.0.2/tailwind.min.css">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    

    <!-- Google Analitics -->
    @include('layouts.ga')
    @yield('head')
    @laravelPWA
    
    <!-- RTL and Commmon ( Phone ) -->
    @include('layouts.rtl')

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">


    <script type="text/javascript">
        function fireClick(tabIndex){
            if(tabIndex==1){
                document.getElementById('tabs-deliver-tab').click();
            }else if(tabIndex==2){
                document.getElementById('tabs-eat-tab').click();
            }else if(tabIndex==3){
                document.getElementById('tabs-ride-tab').click();
            }
        }
        function changeBG(tabIndex){
            var bg="https://whatsapptaxis.com/taxi/bg.jpeg";
            if(tabIndex==1){
                bg="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80";
            }else if(tabIndex==2){
                bg="https://images.unsplash.com/photo-1598346762291-aee88549193f?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80";
            }else if(tabIndex==3){
                //bg="";
            }
            bg="url("+bg+")";
            console.log(bg);
            //document.getElementById('heroSection').style.backgroundImage="url("+heroSection+")";
            document.getElementById("heroSection").style.backgroundImage=bg
        }
    </script>
    <style>
        .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
            color: #000000;
            border-color: #000000;
        }

        .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link {
            margin-bottom: 0px;
        }
    </style>


    <!-- Custom CSS defined by admin -->
    <link type="text/css" href="{{ asset('byadmin') }}/front.css" rel="stylesheet">

    <link type="text/css" href="{{ asset('custom') }}/css/blob.css" rel="stylesheet">
    
</head>
<body class="landing-page">
    

    @include('drivelanding.partials.nav')
    @include('drivelanding.partials.header')
  

    <!-- AlpineJS Library -->
    <script src="{{ asset('vendor') }}/alpine/alpine.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/tw-elements/dist/js/index.min.js"></script>

    
    <!--   Core JS Files   -->
    <script src="{{ asset('vendor') }}/jquery/jquery.min.js" type="text/javascript"></script>
 

    <!-- All in one -->
    <script src="{{ asset('custom') }}/js/js.js?id={{ config('config.version')}}s"></script>

    <!-- Custom JS defined by admin -->
    <?php echo file_get_contents(base_path('public/byadmin/front.js')) ?>

    <!-- Google Map -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?libraries=geometry,drawing&key=<?php echo config('settings.google_maps_api_key'); ?>&libraries=places&callback=js.initializeGoogle"></script>


    <script>
        window.onload = function () {
    
        $('#termsCheckBox').on('click',function () {
            $('#submitRegister').prop("disabled", !$("#termsCheckBox").prop("checked"));
            if(this.checked){
                $('#submitRegister').addClass('opacity-100');
            }else{
                $('#submitRegister').removeClass('opacity-100');
                 
            }
           
        })

        $("#search_location").on("click",function () {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var pos = { lat: position.coords.latitude, lng: position.coords.longitude };

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type:'POST',
                        url: '/search/location',
                        dataType: 'json',
                        data: {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        },
                        success:function(response){
                            if(response.status){
                                $("#txtstartlocation").val(response.data.formatted_address);
                            }
                        }, error: function (response) {
                        }
                    })
                    }, function() {

                    });
                } 
        });

        var inputStart = document.getElementById('txtstartlocation');
        if(inputStart){
            var autocompleteStart = new google.maps.places.Autocomplete(inputStart, {  });
            google.maps.event.addListener(autocompleteStart, 'place_changed', function () {
                var place = autocompleteStart.getPlace();
                var filled=$("#txtstartlocation").val().length>3&&$("#txtendlocation").val().length>3;
                $('#submit').prop("disabled", !filled);
                if(filled){
                    $('#submit').removeClass('opacity-50');
                    $('#submit').addClass('opacity-100')
                }

            });
        }

        var inputEnd = document.getElementById('txtendlocation');
        if(inputEnd){
            var autocompleteEnd = new google.maps.places.Autocomplete(inputEnd, {  });
            google.maps.event.addListener(autocompleteEnd, 'place_changed', function () {
                var place = autocompleteEnd.getPlace();
                var filled=$("#txtstartlocation").val().length>3&&$("#txtendlocation").val().length>3;
                $('#submit').prop("disabled", !filled);
                if(filled){
                    $('#submit').removeClass('opacity-50');
                    $('#submit').addClass('opacity-100')
                }
                
            });
        }
    }
    </script>

</body>
</html>