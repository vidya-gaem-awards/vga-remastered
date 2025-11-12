<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="theme-color" content="#2C3E50">

    <title>@yield('title') &ndash; {{ config('app.name') }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/core.css') }}">

    <script src="https://kit.fontawesome.com/a7a6918ba5.js" crossorigin="anonymous"></script>

{{--    @vite('resources/js/app.js')--}}

    @stack('css')

    @yield('head')
</head>

<body>
@yield('body')

<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.2/js/bootstrap.bundle.min.js'></script>

@stack('js')
</body>
