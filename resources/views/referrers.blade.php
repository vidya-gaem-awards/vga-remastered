@extends('base.standard')

@section('title', 'Referrers')

@pushonce('css')
    <style>
        .referrer {
            overflow: hidden;
        }
    </style>
@endpushonce

@section('content')
    <h1 class="display-4">
        Referrers
    </h1>

    <nav>
        <ol class="breadcrumb">
            @foreach(['1', '3', '7', '14', '30', 'all'] as $_days)
                <li class="breadcrumb-item {{ $days === $_days ? 'active' : '' }}">
                    <a href="{{ route('referrers', ['days' => $_days]) }}">
                        {{ $_days === 'all' ? 'All time' : ($_days . ' ' . Str::plural('day', $_days)) }}
                    </a>
                </li>
            @endforeach
        </ol>
    </nav>

    <table class="table table-bordered table-striped" style="background-color: white;">
        <thead>
        <tr>
            <th style="width: 120px;">Total hits</th>
            <th style="width: 140px;">Latest hit</th>
            <th style="width: 140px;">Link</th>
            <th>Referrer</th>
        </tr>
        </thead>
        @foreach($referrers as $referrer)
            <tr class="{{ $referrer['class'] }}">
                <td><strong>{{ $referrer['total'] }}</strong></td>
                <td><abbr title="{{ $referrer['latest']->setTimezone('America/New_York')->format('D d/m/Y - H:i:s') }}">{{ $referrer['latest']->fromNow() }}</abbr></td>
                <td>
                    @if($referrer['type'] === 'android')
                        <a href="https://play.google.com/store/apps/details?id={{ $referrer['referer'] }}" target="_blank" rel="noindex nofollow">Play Store</a>
                    @elseif($referrer['type'] === 'twitter')
                        <a href="https://www.google.com/search?q={{ urlencode($referrer['referer']) }}" target="_blank" rel="noindex nofollow">Google search</a>
                    @elseif(str_starts_with($referrer['referer'], 'http'))
                        <a href="{{ $referrer['referer'] }}" target="_blank" rel="noindex nofollow">Follow link</a>
                    @endif
                </td>
                <td class="referrer">
                    @if($referrer['type'] === 'android')
                        <strong>Android app:</strong>
                    @endif
                    @if(strlen($referrer['referer']) > 75)
                        {{ substr($referrer['referer'], 0, 74) }}…
                    @else
                        {{ $referrer['referer'] }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
@endsection
