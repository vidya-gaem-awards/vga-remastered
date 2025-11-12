@extends('base.root')

@section('body')
    <nav class="navbar fixed-top navbar-expand-md navbar-light bg-yotsuba">
        <div class="container">
            <a class="navbar-brand" href="{{ route('index') }}">@year() /v/GAs</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapsed" aria-controls="navbarCollapsed" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapsed">
{{--                <ul class="navbar-nav me-auto">--}}
{{--                    <li class="nav-item {{ Route::current()->getName() == 'winners' ? 'active' : '' }}">--}}
{{--                        <a class="nav-link" href="{{ route('winners', ['show' => $selectedShow]) }}">Winners</a>--}}
{{--                    </li>--}}
{{--                </ul>--}}

                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item d-none d-lg-block">
                            <a class="nav-link py-0" href="https://steamcommunity.com/profiles/{{ Auth::user()->steam_id }}" target="_blank">
                                {{ Auth::user()->name }}
                                <img class="profile-pic ms-2" src="{{ Auth::user()->avatar_url }}" style='height: 40px;'>
                            </a>
                        </li>
                    @endauth

                    @guest
                        <li class="nav-item">
                            <div class="btn-group">
                                <a class="btn btn-outline-dark" href="{{ route('login', ['redirect' => Request::url()]) }}" aria-label="Sign in with Steam">
                                    <i class="fab fa-fw fa-steam"></i> Team Login
                                </a>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div class="@yield('containerClass', 'container')" role="main" id="mainContainer">
        @if(Session::has('error'))
            <div class="alert alert-dismissible alert-danger" role="alert">
                @if(is_string(Session::get('error')))
                    {{ Session::get('error') }}
                @elseif(is_array(Session::get('error')) && count(Session::get('error')) === 1)
                    {{ Session::get('error')[0] }}
                @else
                    <ul class="mb-0">
                        @foreach(Session::get('error') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(Session::has('success'))
            <div class="alert alert-dismissible alert-success" role="alert">
                {{ Session::get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <nav class="navbar fixed-bottom navbar-expand-md navbar-light bg-yotsuba">
        <div class="container">

            <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarBottomCollapsed" aria-controls="navbarBottomCollapsed" aria-expanded="false" aria-label="Toggle bottom menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarBottomCollapsed">
                <ul class="nav navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="http://discord.gg/4e8JQB4" target="_blank">Discord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://steamcommunity.com/groups/vidyagaemawards" target="_blank">Steam Group</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="mailto:vidya@vidyagaemawards.com" target="_blank">Email</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://vidyagaemawards.com/previous-years" target="_blank">Previous /v/GAs</a>
                    </li>
                </ul>
                <ul class="nav navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('privacy') }}">Privacy Policy</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}">Logout</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
@endsection
