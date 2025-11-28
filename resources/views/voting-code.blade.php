@extends('base.standard')

@section('title', 'Voting Code')

@pushonce('css')
    <style>
        .voting-code {
            font-size: 16px;
        }
    </style>
@endpushonce

@section('content')
<h1 class="page-header board-header">Voting Code</h1>

<p class="board-subheader">
    Valid from {{ $date->setTimezone('America/New_York')->format('M jS Y g A T ') }}
</p>

<div class="row">
    <div class="offset-md-3 col-md-6">
        <div class="card text-center">
            <div class="card-body">
                <code class="voting-code">{{ $url }}<strong>{{ $code }}</strong></code>
            </div>
            <div class="card-footer">
                A new code is generated each hour. Codes are not automatically invalidated, so it's safe to use an older code
                for a few hours.
            </div>
        </div>
    </div>
</div>

<table class="table table-bordered table-sm mt-4">
    <thead>
    <tr>
        <th>Code</th>
        <th>Times used</th>
        <th>Code hour</th>
        <th>First use</th>
        <th>Last use</th>
    </tr>
    </thead>
    @foreach($logs as $log)
    <tr>
        <td>{{ $log->code }}</td>
        <td>{{ $log->count }}</td>
        <td>
            @if(isset($validCodes[$log->code]))
                <span class="text-success">{{ $validCodes[$log->code]->setTimezone('America/New_York')->format('Y-m-d H:i') }}</span>
            @else
                <span class="text-danger">Code not on record</span>
            @endif
        </td>
        <td>{{ Date::parse($log->first_use)->setTimezone('America/New_York') }}</td>
        <td>{{ Date::parse($log->last_use)->setTimezone('America/New_York') }}</td>
    </tr>
    @endforeach
</table>
@endsection
