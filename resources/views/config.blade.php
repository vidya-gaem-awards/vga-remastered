@extends('base.standard')

@section('title')
    Config
@endsection

@pushonce('js')
    <script src="https://moment.github.io/luxon/global/luxon.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var datetime = $('#time');
            var update = function () {
                var date = luxon.DateTime.local().setZone('America/New_York');
                datetime.html(date.toFormat('yyyy-MM-dd hh:mm:ss a ZZZZ'));
            };

            update();
            setInterval(update, 1000);
        });
    </script>
@endpushonce

@pushonce('css')
    <style>
        .voting-time input {
            width: 200px;
        }
    </style>
@endpushonce

@section('content')
    <kbd class="float-end" id="time"></kbd>
    <h1 class="display-4">
        Site configuration
    </h1>

    <hr>

    @if($ultraAlerts)
    <div class="alert bg-danger">
        <strong>Configuration alert:</strong>
        <ul class="mb-0">
            @foreach($ultraAlerts as $alert)
            <li>{{ $alert }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form class="form-horizontal" action="{{ route('config.post') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-lg-5 col-md-12 mb-4">
                <label for="votingStart" class="h5">Voting times</label>
                <div class="input-group voting-time">
                    <input type="text" class="form-control" id="votingStart" name="voting_start"
                           value="{{ $config->voting_start?->setTimezone('America/New_York')->format('Y-m-d H:i') }}"
                           placeholder="yyyy-mm-dd hh:mm">

                    <span class="input-group-text" id="inputGroupPrepend2">to</span>

                    <input type="text" class="form-control" id="votingEnd" name="voting_end"
                           value="{{ $config->voting_end?->setTimezone('America/New_York')->format('Y-m-d H:i') }}"
                           placeholder="yyyy-mm-dd hh:mm">
                </div>
                <small class="form-text text-muted">Leaving the end date blank will leave voting open indefinitely.</small>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <label for="streamTime" class="h5">Stream time</label>
                <input type="text" class="form-control" id="streamTime" name="stream_time"
                       value="{{ $config->stream_time?->setTimezone('America/New_York')->format('Y-m-d H:i') }}"
                       placeholder="yyyy-mm-dd hh:mm">
                <small class="form-text text-muted">This is shown on the countdown page.</small>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <label for="defaultPage" class="h5">Default page</label>
                <select class="form-select" id="defaultPage" name="defaultPage">
                    @foreach($config::ALLOWED_DEFAULT_PAGES as $page => $title)
                    <option @selected($config->default_page === $page)
                            value="{{ $page }}">{{ $page }} &ndash; {{ $title }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">
                    The first page that appears when accessing the website.
                </small>
            </div>
        </div>

        <div class="row">
            <div class="col-md-5 mb-4">
                <label class="h5" for="navigationMenu">Navigation menu</label>
                <textarea class="form-control" rows="12" id="navigationMenu" name="navigationMenu">
{{ $navigationBarConfig }}
        </textarea>
                <small class="form-text text-muted">
                    This controls the links that appear in the navigation bar.
                    A link will only be shown if the user has access to it.<br>
                    The admin tools dropdown is hacked in, don't mess with it too much.
                </small>

                <label for="availableRoutes" class="mt-4 h5">Available routes</label>
                <select class="form-select" id="availableRoutes">
                    @foreach($routes as $route)
                        <option>{{ $route->getName() }} ({{ $route->uri() }})</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">
                    This is a list of routes you can use in the navigation menu.<br>
                    The dropdown doesn't do anything, it's just for reference.
                </small>
            </div>
            <div class="col-md-6 offset-md-1">
                <label class="h5">Public access</label>

                {{-- Note: parameters to isPagePublic must match the route name for those pages. --}}
                <div class="form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           id="public-videoGames"
                           name="publicPages[video-games]"
                           @checked($config->isPagePublic('video-games'))
                    >
                    <label class="form-check-label" for="public-videoGames">
                        <a href="{{ route('video-games') }}" target="_blank">Video games list</a>
                    </label>
                </div>
                <div class="form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           id="public-awards"
                           name="publicPages[awards]"
                           @checked($config->isPagePublic('awards'))
                    >
                    <label class="form-check-label" for="public-awards">
                        <a href="{{ route('awards') }}" target="_blank">Awards and Nominations</a>
                    </label>
                </div>
                <div class="form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           id="public-videos"
                           name="publicPages[videos]"
                           @checked($config->isPagePublic('videos'))
                    >
                    <label class="form-check-label" for="public-videos">
                        <a href="{{ route('videos') }}" target="_blank">Videos page</a>
                    </label>
                </div>
                <div class="form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           id="public-voting"
                           name="publicPages[voting]"
                           @checked($config->isPagePublic('voting'))
                    >
                    <label class="form-check-label" for="public-voting">
                        <a href="{{ route('voting') }}" target="_blank">Voting page</a>
                        <small class="form-text d-block text-muted">
                            Checking this box does not open voting: use the voting time settings for that.
                        </small>
                    </label>
                </div>
                <div class="form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           id="public-countdown"
                           name="publicPages[countdown]"
                           @checked($config->isPagePublic('countdown'))
                    >
                    <label class="form-check-label" for="public-countdown">
                        <a href="{{ route('countdown') }}" target="_blank">Stream countdown</a>
                        <small class="form-text d-block text-muted">
                            Set the stream time above.
                        </small>
                    </label>
                </div>
                <div class="form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           id="public-stream"
                           name="publicPages[stream]"
                           @checked($config->isPagePublic('stream'))
                    >
                    <label class="form-check-label" for="public-stream">
                        <a href="{{ route('stream') }}" target="_blank">Stream page</a>
                    </label>
                </div>
                <div class="form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           id="public-finished"
                           name="publicPages[finished]"
                           @checked($config->isPagePublic('finished'))
                    >
                    <label class="form-check-label" for="public-finished">
                        <a href="{{ route('finished') }}" target="_blank">Post-stream "thank you" page</a>
                    </label>
                </div>
                <div class="form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           id="public-credits"
                           name="publicPages[credits]"
                           @checked($config->isPagePublic('credits'))
                    >
                    <label class="form-check-label" for="public-credits">
                        <a href="{{ route('credits') }}" target="_blank">Credits</a>
                    </label>
                </div>
                <div class="form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           id="public-soundtrack"
                           name="publicPages[soundtrack]"
                           @checked($config->isPagePublic('soundtrack'))
                    >
                    <label class="form-check-label" for="public-soundtrack">
                        <a href="{{ route('soundtrack') }}" target="_blank">Soundtrack</a>
                    </label>
                </div>
                <div class="form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           id="public-results"
                           name="publicPages[results]"
                           @checked($config->isPagePublic('results'))
                    >
                    <label class="form-check-label dangerous" for="public-results">
                        <a href="{{ route('results') }}" target="_blank">Voting results</a>
                        and the <a href="{{ route('winners') }}" target="_blank">winners page</a>
                        <small class="form-text d-block text-danger">
                            <strong>Warning:</strong> this will reveal the results to everybody!
                        </small>
                    </label>
                </div>
                <label class="h5 mt-4">Other settings</label>
                <div class="form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           id="other-awardSuggestions"
                           name="awardSuggestions"
                           @checked($config->award_suggestions)
                    >
                    <label class="form-check-label" for="other-awardSuggestions">
                        Allow suggestions for new awards and award names
                    </label>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col">
                <button type="submit" class="btn btn-primary btn-lg w-100 d-block p-3" @disabled($config->read_only)>Save configuration</button>
            </div>
        </div>
    </form>

    <h2 class="mt-5">Other tools</h2>
    <hr>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <h5 class="card-header">Result generator</h5>
                <div class="card-body">
                    <p>
                        The result generation process is currently <span @class(['text-success' => $config->isVotingOpen()])>{{ $config->isVotingOpen() ? 'active' : 'disabled' }}</span>.
                    </p>
                    <a href="{{ route('config.cron') }}">More information</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <h5 class="card-header">Site caches</h5>
                <div class="card-body">
                    <p>
                        From here you can purge the caches for the site. This is usually only needed for developers after a deployment.
                    </p>
                    <form action="{{ route('config.purge-cache') }}" method="post" class="d-flex align-items-start gap-2">
                        @csrf
                        <button class="btn btn-warning btn-sm" name="type" value="laravel">Clear Laravel cache</button>
                        @if($cloudflareAvailable)
                        <button type="submit" class="btn btn-warning btn-sm" name="type" value="cloudflare"
                                onclick="return confirm('Are you sure you want to purge the Cloudflare cache?')">Purge Cloudflare cache</button>
                        @else
                        <div>
                            <button class="btn btn-secondary disabled btn-sm">Purge Cloudflare cache</button>
                            <div class="form-text">Not configured</div>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card {{ $config->read_only ? '' : 'border-danger' }}">
                <h5 class="card-header">Read-only mode</h5>
                <div class="card-body {{ $config->read_only ? '' : 'text-danger' }}">
                    @if(!$config->read_only)
                    <form action="{{ route('config.post') }}" method="post">
                        @csrf
                        <p>
                            <strong>Warning:</strong> turning on read-only mode will lock the site and prevent any more changes from
                            being made. This can only be undone by directly editing the database.
                        </p>
                        <input type="hidden" name="readOnly" value="1">
                        <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to turn on read-only mode?')">Turn on read-only mode
                        </button>
                    </form>
                    @else
                    <p class="lead">Read-only mode has been enabled.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-6">

        </div>
    </div>
@endsection
