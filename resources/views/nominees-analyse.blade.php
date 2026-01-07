@extends('base.standard')

@section('title', 'Analyse Nominations')

@section('beforeContainer')
    @include('parts.award-admin-bar')
@endsection

@pushonce('css')
<style>
    .award-title-container {
        font-family: Tahoma, sans-serif;
    }

    .award-title-container .award-title {
        color: #789922;
        font-weight: 700;
        letter-spacing: -1px;
        font-size: 2.4rem;
        line-height: 1em;
    }

    .award-title-container .award-subtitle {
        letter-spacing: -1px;
        font-weight: 700;
    }
</style>
@endpushonce

@section('content')
    <h1 class="page-header board-header mt-2 mb-3">/fbi/ - Nomination Investigation</h1>
    <div class="board-subtitle">
        In the /v/GAs, fraudulent nominations are considered especially heinous.<br>
        In New York City, the dedicated detectives who investigate this vicious rigging are members of an elite squad known as the Autism Unit.<br>
        These are their stories.
    </div>
    <hr class="blue-hr mb-3">
    <div class="award-title-container text-center mb-3">
        <div id="award-name" class="award-title">&gt;{{ $award->name }}</div>
        <div id="award-subtitle" class="award-subtitle">{{ $award->subtitle }}</div>
    </div>
    <hr class="blue-hr mb-3">
    <h2 class="page-header board-header mt-2">{{ $group->name }}</h2>
    <div class="text-center">
        <a href="{{ route('nominees.manage', $award) }}">View other nominees for award</a>
    </div>

    <div class="alert alert-info mt-3">
        <h6><i class="fa-regular fa-circle-info" aria-hidden="true"></i> A note on analysis</h6>
        <div>
            In general, you should assume that a nomination is legitimate unless you have a compelling reason not to.
        </div>
        <div>
            The use of a VPN does not necessarily indicate suspicious activity.
        </div>
        <div>
            There is more data available than what is shown here. For more detailed analysis, provide the IDs shown below to a developer.
        </div>
    </div>

    <table class="table table-bordered table-sm">
        <thead>
        <tr>
            <th>ID</th>
            <th>Timestamp</th>
            <th>Nomination</th>
            <th>IP address</th>
            <th>IP type</th>
            <th>Suspected VPN?</th>
        </tr>
        </thead>
        <tbody>
            @foreach($group->userNominations as $nomination)
                <tr>
                    <td>
                        <code>{{ $nomination->id }}</code>
                    </td>
                    <td>
                        {{ $nomination->created_at->setTimezone('America/New_York')->format('d M Y H:i:s P') }}
                    </td>
                    <td>
                        {{ $nomination->nomination }}
                    </td>
                    <td>
                        @if(str_starts_with($nomination->fuzzy_user_id, 'user_'))
                            Not checked <i class="fa-regular fa-question-circle" data-bs-toggle="tooltip" title="Nomination made by a logged in user." aria-hidden="true"></i>
                        @else
                            {{ redact_ip($nomination->fuzzy_user_id) }}
                        @endif
                    </td>
                    <td>
                        @if(isset($ips[$nomination->fuzzy_user_id]))
                            {{ $ips[$nomination->fuzzy_user_id]->usage_type }}
                        @endif
                    </td>
                    <td>
                        @if(isset($ips[$nomination->fuzzy_user_id]))
                            {{ $ips[$nomination->fuzzy_user_id]->suspicious ? 'Yes' : 'No' }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@pushonce('js')
    <script type="text/javascript">
        jQuery('[data-bs-toggle="tooltip"]').tooltip();
    </script>
@endpushonce
