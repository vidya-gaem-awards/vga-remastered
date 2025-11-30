@extends('base.root')

{{--
The special template does not include the navbars or a Bootstrap container, but it does have the themed CSS.

Example pages:
- Countdown
- Stream
- Voting
--}}

@pushonce('css')
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/special.css') }}">
@endpushonce
