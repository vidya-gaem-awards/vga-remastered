@extends('base.standard-themed')

@section('title', 'Soundtrack')

@pushonce('css')
    <style type="text/css">
        h1 {
            /*text-shadow: rgba(0, 235, 219, 0.5) 1px 1px 5px;*/
            text-align: center;
            margin-top: 10px;
        }

        .album-art {
            max-width: 500px;
            /*margin: 0;*/
            /*transition: all 0.3s ease-in-out;*/
            /*box-shadow: 0 1px 3px rgb(0 0 0 / 12%), 0 1px 2px rgb(0 0 0 / 24%);*/
        }

        .download-now {
            margin: 20px;
        }

        .download-now a {
            font-size: 38px;
            font-weight: normal;
            line-height: 0.95em;
        }

        #track-list {
            max-width: 900px;
            margin: 25px auto 0;

            padding: 12px 8px;
            font-family: "Handwritten", sans-serif;
            font-size: 22px;

            margin-top: 20px;
            border-collapse: initial;
        }

        #track-list td {
            border: none;
            padding: .25rem .75rem;
            /*text-shadow: 1px 1px black;*/
        }

        #track-list td:nth-child(1) {
            /*font-family: "Dot Matrix", sans-serif;*/
            font-weight: bold;
            /*font-family: "OratorStd", "Courier New", serif;*/
            /*color: white;*/
        }

        #track-list td:nth-child(2) {
            /*color: yellow;*/
        }

        #track-list td:nth-child(3) {
            /*color: #fec544;*/
            /*font-family: "Dot Matrix", sans-serif;*/
            text-decoration: none;
            /*font-family: "OratorStd", "Courier New", serif;*/
            /*text-shadow: #00000080 3px 3px 3px;*/

            /*color: #fec544;*/
        }

        #track-list .line-after td {
            /*border-bottom: 1px solid #fec544;*/
            padding-bottom: 15px;
        }

        #track-list .line-before td {
            /*border-top: 1px solid #fec544;*/
            padding-top: 15px;
        }

        .track-title {
            transition: all 0.2s ease-in-out;
            color: #bf0000;
            /*font-family: "OratorStd", "Courier New", serif;*/
            margin-left: 10px;
        }

        .preshow-download a {
            /*background: rgb(0, 0, 0);*/
            /*border: 2px solid #f81317;*/
            /*font-family: "OratorStd", "Courier New", serif;*/
            /*text-shadow: #f29823 0 0 3px;*/
            /*color: #f29823;*/
            padding: 8px 20px;
            /*cursor: pointer;*/
            margin-top: 10px;
            font-size: 26px;
            font-weight: bold;
        }

        .soon {
            font-family: "Dot Matrix", sans-serif;
            font-weight: bold;
            font-size: 30px;
        }

        .center-container { /*just to make the planks fit the content*/
            width: fit-content;
            margin-top: 80px;
            margin-bottom: 80px;
            padding: 30px 30px;
            mask-image: url(/2024images/paper-edge-mask-tall.png);
            -webkit-mask-image: url(/2024images/paper-edge-mask-tall.png);
        }

        .table {
            background: initial;
        }

    </style>
@endpushonce

@section('content')
    <div class="center-container poster-background">
        @empty($tracks)
            <p class="text-center soon">Full tracklist coming soon</p>
        @else
            <table class="table" id="track-list">
                @foreach($preshow as $track)
                    <tr class="{{ $loop->first ? 'line-before' : '' }}">
                        <td></td>
                        <td><span class="track-title">{{ $track[0] }}</span> <span class="artist">{{ $track[1] }}</span></td>
                        <td>{{ $track[2] }}</td>
                    </tr>
                @endforeach
                @foreach($tracks as $track)
                    <tr class="{{ $loop->first ? 'line-before' : '' }}">
                        <td>#{{ $loop->index + 1 }}</td>
                        <td><span class="track-title">{{ $track[0] }}</span> <span class="artist">{{ $track[1] }}</span></td>
                        <td>{{ $track[2] }}</td>
                    </tr>
                @endforeach
            </table>
        @endempty
    </div>
@endsection
