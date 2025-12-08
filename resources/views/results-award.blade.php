@extends('base.standard')

@section('title', 'Results - ' . $award->name)

@section('beforeContainer')
<nav class="navbar navbar-expand-md navbar-light bg-yotsuba admin-navbar">
    <div class="container">
        <a class="navbar-brand me-auto" href="{{ route('results') }}">Results</a>
    </div>
</nav>
@endsection

@section('content')
    <h1 class="page-header">
        Result Trends and Forecasts
    </h1>

    <div class="dropdown mb-3">
        <button class="btn btn-outline-dark dropdown-toggle text-start" type="button" data-bs-toggle="dropdown">
          <span class="h5 d-inline">
            {{ $award->name }}<br>
            <small class="form-text">{{ $award->subtitle }}</small>
          </span>
        </button>
        <ul class="dropdown-menu" style="max-height: 200px; overflow-y: auto;">
            @foreach($awards as $_award)
                <li>
                    <a class="dropdown-item" href="{{ route('results.award', $_award) }}">
                        {{ $_award->name }}<br>
                        <small class="form-text">{{ $_award->subtitle }}</small>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <h3>Results over time</h3>

    <div class="alert alert-info">
        <h6><i class="fa-regular fa-circle-info"></i> A note on sweep points</h6>
        <p>
            Sweep points are an imperfect way of measuring the gap between nominees. The algorithm can lead to unexpected outcomes, but generally speaking, the higher the margin, the bigger the chance that the winner won't change.
        </p>
        <ul class="mb-0" style="font-size: small;">
            <li>> 20%: winner is almost guaranteed</li>
            <li>10-20%: winner is reasonably certain</li>
            <li>5-10%: winner probably won't change, but it might if margins slip</li>
            <li>2-5%: winner could easily change</li>
            <li>< 2%: winner could very easily change with only a few votes</li>
        </ul>
    </div>

    <table class="table table-bordered table-sm">
        <tr>
            <th>
                Time Key <i class="fa-regular fa-question-circle" data-bs-toggle="tooltip" title="Not a literal time, but are how the votes are bucketed"></i>
            </th>
            <th>Vote Count</th>
            <th>1st Place</th>
            <th>2nd Place</th>
            <th>Sweep Point Margin</th>
        </tr>
        @foreach($resultHistory as $history)
            <tr>
                <td>{{ $history->time_key }}</td>
                <td>{{ $history->votes }}</td>
                <td>
                    <strong style="color: {{ $nomineeColours[$history->results[1]] }}">{{ $nominees[$history->results[1]]->name }}</strong>
                    <small class="form-text">{{ floor($history->steps['sweepPoints'][$history->results[1]] ?? 0) }} SP</small>
                </td>
                <td>
                    <strong style="color: {{ $nomineeColours[$history->results[2]] }}">{{ $nominees[$history->results[2]]->name }}</strong>
                    <small class="form-text">{{ floor($history->steps['sweepPoints'][$history->results[2]] ?? 0) }} SP</small>
                </td>
                <td>
                    {{ floor($history->steps['sweepPoints'][$history->results[1]] ?? 0) - floor($history->steps['sweepPoints'][$history->results[2]] ?? 0) }} SP
                    <small class="form-text">{{ round((floor($history->steps['sweepPoints'][$history->results[1]] ?? 0) - floor($history->steps['sweepPoints'][$history->results[2]] ?? 0)) / $history->votes * 100, 1) }}%</small>
                </td>
            </tr>
        @endforeach
    </table>
@endsection

@pushonce('js')
    <script type="text/javascript">
        jQuery('[data-bs-toggle="tooltip"]').tooltip();
    </script>
@endpushonce
