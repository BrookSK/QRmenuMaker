<!--
=========================================================
* Argon Design System - v1.2.0
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-design-system
* Copyright 2020 Creative Tim (http://www.creative-tim.com)

Coded by www.creative-tim.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software. -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('argonfront') }}/img/apple-icon.png">
    <link rel="icon" type="image/png" href="{{ asset('argonfront') }}/img/favicon.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta property="og:image" content="{{ config('global.site_logo') }}">
    <title>{{ config('global.site_name','FoodTiger') }}</title>

    @notifyCss

    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link href="{{ asset('argonfront') }}/css/font-awesome.css" rel="stylesheet" />
    <link href="{{ asset('argonfront') }}/css/nucleo-svg.css" rel="stylesheet" />
    <link href="{{ asset('argonfront') }}/css/nucleo-icons.css" rel="stylesheet">

    <!-- CSS Files -->
    <link href="{{ asset('argonfront') }}/css/argon-design-system.min.css?v=1.4.0" rel="stylesheet" />

    <!-- Custom CSS -->
    <link type="text/css" href="{{ asset('custom') }}/css/custom.css" rel="stylesheet">

    <!-- Select2 -->
    <link type="text/css" href="{{ asset('custom') }}/css/select2.min.css" rel="stylesheet">
  

<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">

<!-- Custom CSS defined by admin -->
<link type="text/css" href="{{ asset('byadmin') }}/front.css" rel="stylesheet">

</head>

<body class="">
    




    <!-- End Navbar -->


    <div class="wrapper">
        @yield('content')
    </div>

    <!--   Core JS Files   -->
    <script src="{{ asset('argonfront') }}/js/core/jquery.min.js" type="text/javascript"></script>
    <script src="{{ asset('argonfront') }}/js/core/popper.min.js" type="text/javascript"></script>
    <script src="{{ asset('argonfront') }}/js/core/bootstrap.min.js" type="text/javascript"></script>
    <script src="{{ asset('argonfront') }}/js/plugins/perfect-scrollbar.jquery.min.js"></script>

    <!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
    <script src="{{ asset('argonfront') }}/js/plugins/bootstrap-switch.js"></script>

     <!-- Control Center for Argon UI Kit: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('argonfront') }}/js/argon-design-system.js?v=1.2.0" type="text/javascript"></script>


</body>

</html>
