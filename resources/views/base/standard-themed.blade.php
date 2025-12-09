@extends('base.standard')

{{--
The standard themed template is an extension of the standard template, but with some extra CSS applied to give it a
year-specific theme.

Example pages:
- Credits
- Soundtrack
- Winners
--}}

@section('containerClass', ' ')

@prepend('css')
    @vite('resources/assets/voting.ts')

    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">

    <style>
        .center-container {
            padding-top: 80px;
            padding-bottom: 80px;
        }

        @media (min-width: 1300px) {
            .container {
                max-width: 1300px;
            }
        }
    </style>
@endprepend
