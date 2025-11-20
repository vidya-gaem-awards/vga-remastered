@extends('base.standard')

@section('title', 'Result Generator – Config')

@section('content')
    <h1 class="display-4">Result generator</h1>
    <hr>
    <div class="alert alert-success" role="alert">
        <strong>New in 2025:</strong> there's no longer a need to manually start the result generator. It will now run automatically when voting is open.
    </div>
    <p>
        It can take up the five minutes to calculate Sweep Points&trade; and determine all of the award winners.
        This makes it impractical to display live results, so instead, an automated process calculates the results every
        30 minutes and saves the results to the database.
    </p>
{{--    <div class="row">--}}
{{--        <div class="col-lg-6">--}}
{{--            <div class="card">--}}
{{--                <div class="card-body">--}}
{{--                    The result generation process is currently <strong class="text-{{ $config->isVotingOpen() ? 'success' : 'dark' }}">{{ $config->isVotingOpen() ? 'active' : 'disabled' }}</strong>.--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
@endsection
