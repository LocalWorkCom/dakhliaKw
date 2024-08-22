<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @yield('title')
    </title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('frontend/images/favicon/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('frontend/images/favicon/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('frontend/images/favicon/favicon-16x16.png')}}">
    <link rel="manifest" href="{{ asset('frontend/images/favicon/site.webmanifest')}}">
    <script type="application/javascript" src="{{ asset('frontend/js/bootstrap.min.js')}}"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap-->
    <link href="{{ asset('frontend/styles/bootstrap.min.css') }}" rel="stylesheet" id="bootstrap-css">
    <link src="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    </link>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    @stack('style')
    <link rel="stylesheet" href="{{ asset('frontend/styles/index.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/styles/responsive.css') }}">
    <!-- Select 2-->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
.select2-container .select2-selection--single {
    height: 45px;
    font-size: 14px;
    border: 0.2px solid #d9d4d4;
    border-radius: 10px;
    background-color: #f8f8f8;
    direction: rtl;
    display: flex !important;
    padding-top: 8px !important;

}
.select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__arrow {
    left: 1px;
    right: auto;
    padding-top: 5px !important;
    top: 9px !important;
}
.select2-container--default .select2-selection--multiple  {
    height: 45px;
    font-size: 14px;
    border: 0.2px solid #d9d4d4;
    border-radius: 10px;
    background-color: #f8f8f8;
    direction:rtl;
}
.select2-results__option--selectable {
    cursor: pointer;
    display: flex !important;
}
.custom-select{
    width: 100%;
}
.select2-container--default .select2-results__option--disabled {
    color: #999;
    display: flex !important;
}
    </style>
</head>
<body >
@include('layout.header')

<main>
        @yield('content')
    </main>


    @include('layout.footer')
</body>



</html>