<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="theme-color" content="#2C3E50">

    <title>Vidya Gaem Awards</title>

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

@stack('js')
</body>
