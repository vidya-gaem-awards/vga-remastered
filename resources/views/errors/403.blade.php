@extends('base.standard')

@section('title')
    403
    @endsection

@section('content')
    <h1 class="page-header board-header mb-4">/403/ - Forbidden</h1>

    @auth
        <p class="text-center">
            You aren't allowed to access this page.
        </p>
        <p class="text-center">
            If you think you should be able to, contact one of the producers on <a href="https://discord.gg/4e8JQB4">Discord</a>.<br>
            You'll need your Steam Community ID: {{ request()->user()->steam_id }}.
        </p>
    @else
        {{-- @TODO: Workaround. Logged out users should be getting sent to the 401 page, but because of how the routes are set up,
                    everybody just gets 403s at the moment, logged in or not. --}}
        <p class="text-center">
            To view this page, you'll need to sign in with a Steam account that's been added to the /v/GA team.
        </p>
        <p class="text-center">
            <a class="btn btn-dark" href="{{ route('login', ['redirect' => Request::url()]) }}">
                <i class="fab fa-fw fa-steam"></i> Sign in with Steam
            </a>
        </p>
    @endauth
@endsection
