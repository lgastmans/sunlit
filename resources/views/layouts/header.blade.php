<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{asset('images/favicon.ico')}}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title> @yield('title')</title>

    <!-- third party css -->
    <link href="{{ asset("/css/vendor/dataTables.bootstrap4.css") }}" rel="stylesheet" type="text/css" /> 
    <link href="{{ asset("/css/vendor/responsive.bootstrap4.css") }}" rel="stylesheet" type="text/css" /> 

    @if (Request::segment(3) == "proforma")
        <link href="{{ asset("/css/proforma.css") }}" rel="stylesheet" type="text/css" /> 
    @endif
    
    <!-- third party css end -->

    <!-- App css -->
    <link href="{{ mix('css/icons.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ mix('css/hyper-app.css') }}" rel="stylesheet" type="text/css" id="light-style" />

    <!-- main style.css -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.11.5/b-2.2.2/b-colvis-2.2.2/b-html5-2.2.2/r-2.2.9/sr-1.1.0/datatables.min.css"/>


    @yield('page-styles')

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>

@yield('page-style')

</head>
<body class="loading" data-layout-config='{"leftSideBarTheme":"dark","layoutBoxed":false, "leftSidebarCondensed":true, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>


