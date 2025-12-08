@extends('base.standard')

@section('title', 'Pairwise voting results')

@pushonce('css')
    <style>
        .rotate th {
            font-size: 10px;
        }

        .pairwise th {
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
            background-color: white;
        }

        .pairwise td {
            text-align: center;
            background-color: white;
        }

        .pairwise td.better {
            background-color: #4CAF50;
        }

        .pairwise td.worse {
            background-color: #ef5350;
        }
    </style>
@endpushonce

@section('content')
    <h1 class="page-header">Pairwise Voting Results</h1>

    <p>
        The /v/GAs use a preferential voting system (specifically, the <a
            href="https://en.wikipedia.org/wiki/Schulze_method" target="_blank">Schulze method</a>) to calculate results. This allows us to
        more accurately determine the winner and runner-ups.
    </p>

    <p>
        Below is the data we used to calculate the rankings. Each row contains the number of people who preferred that
        nominee to the nominee in each column. The winner is the nominee that was preferred more than any other nominee.
    </p>

    @foreach($awards as $award)
        <h2>
            {{ $award->name }}
            <small>{{ $award->subtitle }}</small>
        </h2>

        @if(!$pairwise[$award->id])
            <div class="alert alert-warning">
                Results for this award have not yet been generated.
            </div>
        @else
            @php($width = 120 + (15 - $award->nominees->count()) * 20)
            <table class="table table-bordered table-hover table-condensed pairwise" style="table-layout: fixed;">
                <tr class='rotate'>
                    <th style='width: {{ $width }}px;'>&nbsp;</th>
                    @foreach($award->nominees as $nominee)
                        <th title="{{ $nominee->name }}">{{ $nominee->name }}</th>
                    @endforeach
                </tr>
                @foreach($award->nominees as $nominee1)
                    <tr>
                        <th title="{{ $nominee1->name }}">{{ $nominee1->name }}</th>
                        @foreach($award->nominees as $nominee2)
                            @if($nominee1->id === $nominee2->id)
                                <td>---</td>
                            @else
                                {{-- @TODO: use nominee slugs or IDs? --}}
                                <td title="{{ $pairwise[$award->id][$nominee1->slug][$nominee2->slug] }} vs {{ $pairwise[$award->id][$nominee2->slug][$nominee1->slug] }}"
                                    class="{{ $pairwise[$award->id][$nominee1->slug][$nominee2->slug] > $pairwise[$award->id][$nominee2->slug][$nominee1->slug] ? 'better' : 'worse' }}">
                                    {{ $pairwise[$award->id][$nominee1->slug][$nominee2->slug] }}
                                </td>
                           @endif
                        @endforeach
                    </tr>
                @endforeach
            </table>
        @endif
    @endforeach
@endsection
