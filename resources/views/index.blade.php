@extends('base.standard')

@pushonce('css')
    <style>
        .jumbotron {
            color: #AE1216;
            font-family: Tahoma, sans-serif;
            padding-bottom: 2rem;
        }
        .jumbotron h1 {
            font-weight: 700;
            font-size: 50px;
        }
        .jumbotron p {
            font-size: 25px;
        }
        .masthead {
            margin-bottom: 0;
        }

        .news li {
            background-color: #d6daf0;
            border: 1px solid #b7c5d9;
            border-left: none;
            border-top: none;
            padding: 2px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 15px;
            position: relative;
            color: #000;
        }

        .news a, .news a:visited {
            color: #34345c !important;
        }

        .news a:hover, .new {
            color: #d00 !important;
        }

        .news .name.admin {
            color: #FF0000;
        }

        .news .admin img {
            margin-bottom: 4px;
        }

        .news .sticky {
            position: absolute;
            top: 5px;
            right: 5px;
        }

        .news li.redboard {
            background-color: #f0e0d6;
            border: 1px solid #d9bfb7;
        }

        .news .postInfo {
            margin-left: 4px;
        }

        .news .name {
            color: #117743;
            font-weight: 700;
        }

        .news .postText {
            margin: 1em 40px;
        }
    </style>
@endpushonce


@section('content')
    <header class="jumbotron masthead text-center" style="background: none;">
        <h1>@year() Vidya Gaem Awards</h1>
        <p>>implying you're opinion is worth shit</p>
    </header>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <ul class="news list-unstyled">
                <li class="redboard">
                    <img src="{{ asset('img/sticky.gif') }}" class="sticky">
                    {{-- @TODO: add support for dynamic/home_static_panel --}}
                    <div class="postText">
                        <h4>
                            Want to get involved??
                        </h4>
                        <p>
                            If you'd like to be involved or just want to shitpost, join our <a href="https://discord.gg/4e8JQB4" target="_blank">Discord server</a>.
                        </p>
                    </div>
                </li>

                @foreach($news as $item)
                <li>
                    <div class="postInfo">
                        <span class="name">
                            {{ Gate::allows('news_view_user') ? $item->user->name : 'Anonymous' }}
                        </span>
                        <span>{{ $item->created_at->format('m/d/y(D)H:i:s') }}</span>
                        <span>No. {{ $item->id }}</span>
                        @if($item->new)
                            <span class="new float-end">(New)</span>
                        @endif
                    </div>
                    <div class="postText">{!! $item->text !!}</div>
                </li>
                @endforeach
            </ul>

            @can('news_manage')
                <p>
                    <a href="{{ route('news') }}">Add news post</a>
                </p>
            @endcan
        </div>
    </div>
@endsection
