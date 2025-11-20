@extends('base.standard')

@section('title', 'Vidya in ' . year())

@pushonce('css')
    <style>
        #games td {
            text-align: center;
        }

        .pc {
            background-color: rgb(10, 10, 10);
            color: white;
        }

        .vr {
            background-color: #b63438;
            color: white;
        }

        .sony {
            background-color: rgb(0, 64, 152);
            color: white;
        }

        .microsoft {
            background-color: rgb(17, 125, 16);
            color: white;
        }

        .nintendo {
            background-color: rgb(127, 127, 127);
            color: white;
        }

        .yes {
            display: block;
        }

        #games {
            border-color: black;
            border-radius: 0;
        }

        thead th {
            background-color: white;
            border-bottom: 2px black solid;
        }

        #games td, thead th {
            border-radius: 0 !important;
        }

        #games .divider {
            border-left: 1px solid rgba(0, 0, 0, 0.2);
        }

        #games {
            border: 1px solid rgba(0, 0, 0, 0.2);
        }

        .notable {
            font-weight: bold;
        }

        #new-game input[type=checkbox] {
            width: 100%;
            height: 25px;
        }

        #new-game td {
            vertical-align: middle;
        }

        .game-container {
            margin-left: 0;
        }

        .game-container .col-sm-2 {
            display: flex;
            padding-left: 0;
        }

        .game-container .card {
            flex-grow: 1;
        }

        .title {
            font-weight: 500;
        }

        .small-title {
            font-size: small;
        }

        .game {
            padding: 0.75rem 1rem;
            display: flex;
            flex-direction: column;
        }

        .fa-desktop {
            color: #2BA8E1;
        }

        .fa-xbox {
            color: #187B18;
        }

        .fa-playstation {
            color: #2A32AD;
        }

        .fa-n {
            color: #E50019;
        }

        .remove-game {
            display: none;
        }

        .game:hover .remove-game {
            display: block;
        }
    </style>
@endpushonce

@pushonce('js')
    @can('add_video_game')
    <script type="text/javascript">
        $(document).ready(function () {
            var dialog = $("#addGameModal");
            var currentlySubmitting = false;

            $('.alert .btn-close').on('click', function () {
                $(this).parent().fadeOut("fast");
            });

            function handleError(error) {
                dialog.find('.saving').hide();
                dialog.find('button').removeAttr('disabled');
                dialog.find('.alert-text')
                    .html("<strong>Error:</strong> " + error)
                    .parent().fadeIn("fast");

                currentlySubmitting = false;
            }

            dialog.find('form').submit(function (event) {
                event.preventDefault();

                if (currentlySubmitting) {
                    return;
                }
                currentlySubmitting = true;

                dialog.find('.saving').show();
                dialog.find('button').attr('disabled', 'disabled');
                dialog.find('.alert').slideUp();

                var data = $(this).serializeArray();

                $.post("{{ route('video-games.add') }}", data, function (response) {
                    if (response.success) {
                        window.location.reload();
                    } else {
                        handleError(response.error);
                    }
                }, 'json').fail(function (response) {
                    handleError(response.status);
                });
            });

            $('.remove-game').click(function (event) {
                event.preventDefault();

                var title = $(this).data('title');
                var id = $(this).data('id');
                if (!confirm('Are you sure you want to remove ' + title + ' from the list of video games?')) {
                    return;
                }

                $.post("{{ route('video-games.remove') }}", {id: id}, function (response) {
                    if (response.success) {
                        window.location.reload();
                    } else {
                        alert('An error occurred: ' + response.error);
                    }
                }, 'json').fail(function (response) {
                    alert('An error occured: ' + response.status);
                });
            });

            $('#wikipedia').on('click', function (event) {
                event.preventDefault();

                if (!confirm('Are you sure you want to do this? Any games that have been manually added will be kept.')) {
                    return;
                }

                $.post("{{ route('video-games.reload.wikipedia') }}", null, function (response) {
                    if (response.success) {
                        window.location.reload();
                    } else {
                        alert('An error occurred: ' + response.error);
                    }
                }, 'json').fail(function (response) {
                    alert('An error occured: ' + response.status);
                });
            });

            $('#igdb').on('click', function (event) {
                event.preventDefault();

                if (!confirm('Are you sure you want to do this? Any games that have been manually added will be kept. (Note: this process gets 500 games at a time so it may take a while to finish. The page will appear to be doing nothing in the meantime. Do not click the button again.)')) {
                    return;
                }

                $('#wikipedia').attr('disabled', 'disabled');
                $('#igdb').attr('disabled', 'disabled');

                $.post("{{ route('video-games.reload.igdb') }}", null, function (response) {
                    if (response.success) {
                        window.location.reload();
                    } else {
                        alert('An error occurred: ' + response.error);
                    }
                }, 'json').fail(function (response) {
                    alert('An error occured: ' + response.status);
                });
            });
        });
    </script>
    @endcan
@endpushonce

@section('content')
    <h1 class="page-header board-header">/v/ - The Vidya</h1>
    <p class="board-subheader">A list of all the games released in @year().</p>

    <div class="row game-container">
        @foreach($games as $game)
        <div class="col-sm-2">
            <div class="card mb-2">
                <div class="card-body game">
                    <div class="title {{ strlen($game->name) > 30 ? 'small-title' : '' }} mb-3">
                        @if($game->url)
                        <a href="{{ $game->url }}" target="_blank" class="text-dark">{{ $game->name }}</a>
                        @else
                        <span class="text-dark">{{ $game->name }}</span>
                        @endif
                    </div>

                    <div class="platforms mt-auto">
                        @if($game->pc)<i class="fas fa-fw fa-desktop" title="PC"></i>@endif
                        @if($game->xbox)<i class="fab fa-fw fa-xbox" title="Xbox"></i>@endif
                        @if($game->playStation)<i class="fab fa-fw fa-playstation" title="PlayStation"></i>@endif
                        @if($game->nintendo)<i class="fas fa-fw fa-n" title="Nintendo"></i>@endif
                        @if($game->mobile)<i class="fas fa-fw fa-mobile-screen" title="Mobile"></i>@endif
                        @if($game->vr)<i class="fas fa-fw fa-head-side-goggles" title="VR"></i>@endif

                        <a href="#" class="remove-game float-end" data-title="{{ $game->name }}" data-id="{{ $game->id }}">×</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div>
        This page uses data from <a href="https://en.wikipedia.org">Wikipedia</a> and <a href="https://www.igdb.com/" target="_blank">IGDB.com</a>.
    </div>

    @if($can('add_video_game') && !$settings->read_only)
    <div class="d-flex">
        <button class="btn btn-primary my-3" type="button" data-bs-toggle="modal" data-bs-target="#addGameModal">
            <i class="far fa-plus fa-fw"></i> Add a video game
        </button>

        <button class="btn btn-warning my-3 ms-auto" type="button" id="wikipedia">
            @empty($games)
            <i class="far fa-file-download fa-fw me-1"></i> Import list of games from Wikipedia
            @else
            <i class="far fa-sync-alt fa-fw me-1"></i> Reload list of games from Wikipedia
            @endempty
        </button>

        {{-- The IGDB contains way, way too much garbage for it to be useful. --}}
{{--        <button class="btn btn-warning my-3 ms-auto" type="button" id="igdb">--}}
{{--            @empty($games)--}}
{{--            <i class="far fa-file-download fa-fw"></i> Import list of games from IGDB--}}
{{--            @else--}}
{{--            <i class="far fa-sync-alt fa-fw"></i> Reload list of games from IGDB--}}
{{--            @endempty--}}
{{--        </button>--}}
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addGameModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form>
                    <div class="modal-header">
                        <h5 class="modal-title">Add a video game</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-dismissible alert-danger" style="display: none;">
                            <span class="alert-text"></span>
                            <button type="button" class="btn-close"></button>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-2 col-sm-3 col-form-label" for="info-name">
                                Title <span class="required" title="Required">*</span>
                            </label>
                            <div class="col-lg-10 col-sm-9">
                                <input class="form-control" type="text" id="info-name" required name="name">
                                <span class="form-text text-muted">Check your spelling before submitting!</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-2 col-sm-3 col-form-label">Platforms</div>
                            <div class="col-lg-10 col-sm-9 pt-2">
                                @foreach(\App\Models\GameRelease::PLATFORMS as $id => $name)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="platform-{{ $id }}" name="{{ $id }}">
                                    <label class="form-check-label" for="platform-{{ $id }}">
                                        {{ $name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <span class="saving me-2" style="display: none;">
                            <i class="far fa-circle-notch fa-spin me-1"></i> Saving...
                        </span>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection
