@extends('base.standard')

@section('title', 'Nominees')

@pushonce('css')
    <link rel="stylesheet" href="{{ asset('css/votingNominees.css') }}">

    <style>
        .ui-autocomplete {
            max-height: 300px;
            overflow-y: auto;
            /* prevent horizontal scrollbar */
            overflow-x: hidden;
            /* add padding to account for vertical scrollbar */
            width: 200px;
            z-index: 100000;
        }

        #nominee-container .nominee {
            background-color: rgba(255,255,255,0.8);
            cursor: pointer;
            transition: 200ms linear all;
            margin: 0 auto 3px auto;
        }

        #nominee-container .nominee .flavorText {
            display: block;
        }

        ol.nominations, ul.nominations {
            margin-right: 5px;
            list-style-type: none;
            padding-left: 0;
        }

        .nominee {
            border-color: #333;
        }

        .modal-body .nominee {
            margin: 0 auto 10px auto;
        }

        #award-selector .subtitle.hoverShow {
            display: none;
        }

        #award-selector li:hover .subtitle.hoverShow {
            display: block;
        }

        #award-selector li.disabled {
            text-decoration: line-through;
        }

        #award-selector .emoji {
            max-height: 18px;
            width: auto;
        }

        #award-selector .dropdown-menu {
            position: static;
            display: block;
            top: auto;
            left: auto;
            float: none;
            border: none;
        }

        .impressive-title {
            text-align: center;
            color: #789922;
            font-family: 'Asap', sans-serif;
            font-size: 2.4em;
            line-height: 1em;
            font-weight: bold;
        }

        .impressive-title .emoji {
            width: 36px;
        }

        .impressive-subtitle {
            text-align: center;
            font-family: 'Asap', sans-serif;
            color: #1F1F1F;
            font-weight: bold;
            font-size: 1.1em;
        }

        .impressive-description {
            text-align: center;
            margin-top: 10px;
            font-family: 'Asap', sans-serif;
        }

        .award-container {
            border: 1px solid #BBB;
            background-color: #f5f5f5;
        }

        .award-container-middle {
            background-color: #eaeaea;
            box-shadow: rgba(0,0,0,0.1) 0 2px 1px;
        }

        .award-container-padding {
            padding: 15px;
            border-bottom: 1px solid #BBB;
        }

        .hoverOverlay {
            width: 100%;
            height: 100%;
            z-index: 50;
            position: absolute;
            opacity: 0;
            background-color: rgba(0,0,0,0.8);
            color: white;
            transition: 200ms linear all;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        #nominee-container .nominee:hover .hoverOverlay {
            opacity: 1;
        }

        #nominee-container.inaccurate .nominee {
            height: 60px;
            width: 100%;
            border-width: 1px;
        }

        #nominee-container.accurate .nominee, .modal-dialog .nominee {
            width: 274px;
            height: 140px;
        }

        #nominee-container.inaccurate .flavorText {
            opacity: 0;
        }

        #nominee-container.inaccurate .nomineeName, #nominee-container.inaccurate .nomineeSubtitle {
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
            max-width: calc(100% - 10px);
        }

        #dialog-edit-delete {
            position: relative;
            padding-left: 46px;
        }

        img.delete-this {
            height: calc(100% + 2px);
            top: -1px;
            left: -1px;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
            position: absolute;
        }

        .sidebar-sticky {
            height: calc(100vh - 56px - 56px - 38px);
        }

        .sidebar .nav-item .nav-link {
            padding: 0.4rem 1rem;
        }

        .sidebar .award-name .emoji {
            width: 20px;
        }

        .sidebar .award-name {
            font-size: 0.9rem;
        }

        .sidebar .award-subtitle {
            font-size: .75rem;
        }

        .blue-hr {
            border-top: 1px solid #b7c5d9;
        }

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

        .nomination {
            display: flex;
            align-items: center;
            border: 1px solid #BBB;
            /*border-bottom: none;*/
            padding-left: 10px;
            flex-grow: 1;
            background: #E9E9E9;
            align-self: stretch;
            margin-top: -1px;

            --bs-btn-border-radius: auto;
            text-align: left;

            /*--bs-btn-hover-color: red;*/
            /*--bs-btn-hover-bg: red;*/
            /*--bs-btn-hover-border-color: red;*/
        }

        .nominations .nomination-container:first-child .nomination {
            /*border-top-left-radius: .25rem;*/
            /*border-top-right-radius: .25rem;*/
        }

        .nomination-last-visible .nomination {
            /*border-bottom-left-radius: .25rem;*/
            /*border-bottom-right-radius: .25rem;*/
            /*border-bottom: 1px solid #BBB;*/
        }

        .nomination-container {
            display: flex;
            align-items: center;
            height: 30px;
        }

        .nomination-container {
            .dropdown-menu {
                border-top-left-radius: 0;
                border-top-right-radius: 0;
            }
        }

        .nomination-count {
            width: 2em;
            margin-right: 1rem;
            text-align: center;
            font-weight: bold;
        }

        .nomination-title {
            flex-grow: 1;
        }

        .nomination-index {
            width: 16px;
            margin-right: 1rem;
            text-align: center;
        }

        .nomination-overflow {
            display: none;
        }

        .nomination:hover {
        }

        .nomination-container.hidden {
            display: none;
            text-decoration: line-through;
        }

        .nomination-container.merging-from .nomination {
            background-color: #b6d4fe;
        }

        .nominations.show-all .nomination-container.hidden {
            display: flex;
        }
    </style>
@endpushonce

@section('beforeContainer')
    @include('parts.award-admin-bar')
@endsection

@section('containerClass', 'container-fluid')

@section('content')
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-top-padding">
                <h6 class="m-0">Award list</h6>
            </div>
            <div class="sidebar-sticky">
                <ul class="nav flex-column">
                    @foreach($awards as $_award)
                    <li class="nav-item">
                        <a class="nav-link {{ $_award->id === $award?->id ? 'active' : '' }}" href="{{ route('nominees.manage', $_award) }}">
                            <div class="award-name">{{ $_award->name }}</div>
                            <div class="award-subtitle d-none d-md-block">{{ $_award->subtitle }}</div>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-4" style="padding-bottom: 50px;">

            <h1 class="page-header board-header mt-2 mb-3">/nm/ - Nominee Manager</h1>

            <hr class="blue-hr mb-3">

            <div class="row">
                <div class="col-xl-8 offset-xl-2">

                    @if(!$award)

                    <p class="text-center">
                        Welcome to the nominee manager. Select an award on the left to get started.
                    </p>

                    <div class="text-center">
                        <a href="{{ route('nominees.export.user-nominations') }}" target="_blank"><i class="fal fa-download fa-fw"></i> Export user nominations (.csv)</a>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('nominees.export') }}" target="_blank"><i class="fal fa-download fa-fw"></i> Export award nominees (.csv)</a>
                    </div>

                    @else
                    <div class="award-title-container text-center mb-3">
                        <div id="award-name" class="award-title">&gt;{{ $award->name }}</div>
                        <div id="award-subtitle" class="award-subtitle">{{ $award->subtitle }}</div>
                    </div>

                    <div class="row" style="margin-top: 15px;">
                        <div class="col-lg-6">
                            <div class="award-container">
                                <div class="award-container-middle award-container-padding impressive-subtitle">
                                    <span id="official-count">{{ $award->nominees->count() }}</span>
                                    {{ Str::plural('nominee', $award->nominees->count()) }}

                                    @if($can('nominations_edit') && !$settings->read_only)
                                        <button class="btn btn-outline-dark" type="button" id="new" style="margin: -12px 0 -10px 15px">
                                            <i class="far fa-plus fa-fw"></i> Add Nominee
                                        </button>
                                    @endif
                                </div>

                                @if($award->nominees->isNotEmpty())
                                    <div class="award-container-padding">
                                        <div style="font-size: small; text-align: center;">
                                            <input type="checkbox" id="accurateDimensions"> <label for="accurateDimensions">Show "accurate" dimensions</label>
                                        </div>
                                        <div id="nominee-container" class="inaccurate">
                                            @foreach($award->nominees()->with('image')->get() as $nominee)
                                            <div class="nominee" style="background-size: cover; background-image: url('{{ $nominee->image?->getUrl() ?? asset('img/no-image-available.png') }}')"
                                                 data-nominee="{{ $nominee->id }}">
                                                @if($can('nominations_edit') && !$settings->read_only)
                                                    <div class="hoverOverlay">
                                                        Click to edit nominee
                                                    </div>
                                                @endif
                                                @if($nominee->flavor_text)
                                                    <div class="flavorText">{{ $nominee->flavor_text }}</div>
                                                @endif
                                                <div class="nomineeInfo">
                                                    <div class="nomineeName">{{ $nominee->name }}</div>
                                                    {{-- @TODO: this should not need to be raw --}}
                                                    <div class="nomineeSubtitle">{!! $nominee->subtitle !!}</div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                @if($navbar->canAccessRoute('voting'))
                                    <div class="award-container-middle award-container-padding" style="text-align: center;">
                                        <a href="{{ route('voting', $award) }}">View the voting page for this award</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="award-container">
                                <div class="award-container-middle award-container-padding impressive-subtitle" style="text-align: center;">
                                    {{ $award->userNominations->count() ?: 'No' }} user {{ Str::plural('nomination', $award->userNominations->count()) }}
                                </div>
                                @if($award->userNominations->isNotEmpty())
                                <div class="award-container-padding">
                                    @if($award->nominations_enabled)
                                    <p style="font-weight: bold; text-align: center;">
                                        Nominations for this award are currently open.
                                    </p>
                                    @endif

                                    <p style="text-align: center; font-size: small;">
                                        <label><input type="checkbox" id="show-all-user-nominations"> Show merged and ignored nominations </label>
                                    </p>

                                    <div class="alert alert-primary d-none" id="merge-message">
                                        <i class="fa-solid fa-merge fa-fw"></i> You are in merge mode. Select another user nomination and all nominations from <strong id="merging-from-name"></strong> will be moved to it, or select the same user nomination to cancel.
                                    </div>

                                    <div class="nominations mb-2">
                                        @php($count = 1)
                                        @foreach($userNominationGroups as $group)
                                        <div class="nomination-container {{ $group->ignored || $group->mergedInto ? 'hidden' : '' }} {{ $count === 15 ? 'nomination-last-visible' : '' }} {{ $count > 15 ? 'nomination-overflow' : '' }}" data-name="{{ $group->name }}" data-id="{{ $group->id  }}">
                                            @if($group->nominee)
                                                <span class="nomination-index text-success"><i class="fas fa-check"></i></span>
                                                @php($count++)
                                            @elseif($group->mergedInto)
                                                <span class="nomination-index text-muted"><i class="fas fa-merge"></i></span>
                                            @elseif($group->ignored)
                                                <span class="nomination-index text-muted" title="Ignored"><i class="far fa-ban"></i></span>
                                            @else
                                                <small class="nomination-index text-muted">{{ $count }}</small>
                                                @php($count++)
                                            @endif
                                            <button class="nomination btn btn-outline-dark dropdown-toggle" data-bs-toggle="dropdown" data-bs-offset="0,0">
                                                <div class="nomination-title">{{ $group->name }}</div>
                                                <div class="nomination-count">{{ $group->user_nominations_count }}</div>
                                            </button>
                                            <ul class="dropdown-menu nomination-dropdown">
                                                @if($group->nominee)
                                                    <li><a class="dropdown-item" href="#" data-action="unlink"><i class="fa-solid fa-unlink fa-fw"></i> Unlink from nominee</a></li>
                                                @else
                                                    @if(!$group->ignored && !$group->mergedInto)
                                                        <li><a class="dropdown-item" href="#" data-action="add-nominee"><i class="fa-solid fa-plus fa-fw"></i> Add as nominee</a></li>
                                                    @endif
                                                    @if($group->mergedInto)
                                                        <li><a class="dropdown-item" href="#" data-action="demerge"><i class="fa-solid fa-arrow-rotate-left fa-fw"></i> Undo merge</a></li>
                                                    @else
                                                        <li><a class="dropdown-item" href="#" data-action="merge"><i class="fa-solid fa-merge fa-fw"></i> Merge into...</a></li>
                                                    @endif
                                                    @if(!$group->mergedInto)
                                                        @if($group->ignored)
                                                            <li><a class="dropdown-item" href="#" data-action="unignore"><i class="fa-regular fa-eye fa-fw"></i> Stop ignoring nominations</a></li>
                                                        @else
                                                            <li><a class="dropdown-item" href="#" data-action="ignore"><i class="fa-regular fa-eye-slash fa-fw"></i> Ignore nominations</a></li>
                                                        @endif
                                                    @endif
                                                @endif
                                            </ul>
                                        </div>
                                        @endforeach
                                    </div>

                                    <small class="d-block text-center">
                                        <a href="#" id="show-more">Show more <i class="far fa-angle-down"></i></a>
                                    </small>
                                </div>
                                @endif
                                <div class="award-container-middle award-container-padding impressive-subtitle" style="text-align: center;">
                                    {{ $award->awardSuggestions->count() ?: 'No' }} award name {{ Str::plural('suggestion', $award->awardSuggestions) }}
                                </div>
                                @if($award->awardSuggestions->isNotEmpty())
                                    <div class="award-container-padding">
                                        <ul class="nominations">
                                            @foreach($award->getNameSuggestions($alphabeticalSort) as $suggestion)
                                                <li><strong>{{ $suggestion['count'] }} x</strong> {{ $suggestion['title'] }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    @if($can('nominations_edit') && $award)
        <div id="dialog-delete" class="modal" role="dialog" style="z-index: 1099">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <p>Are you sure you want to remove <span id="dialog-delete-nominee" style="font-weight: bold;"></span> from
                            the list of nominees?</p>
                    </div>
                    <div class="modal-footer">
                        <span id="dialog-delete-status"><img src='{{ asset('img/loading.gif') }}' style="width: 16px;"> deleting...&nbsp;</span>
                        <button class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger" id="dialog-delete-nominee-confirm">Confirm</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="dialog-edit" class="modal" role="dialog" style="z-index: 1098" tabindex="-1">
            <div class="modal-dialog" role="document">
                <form class="form-horizontal" id="dialog-edit-form" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="dialog-edit-header">Add new nominee</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="alert alert-dismissible alert-danger" style="display: none;">
                                <span id="dialog-edit-error"></span>
                                <button type="button" class="btn-close"></button>
                            </div>

                            <div style="text-align: center; padding-bottom: 5px;  ">
                                Note: the design of the nominee box may change over time.
                            </div>

                            <div class="nominee" id="dialog-edit-image" style="background-size: cover">
                                <div class="flavorText" id="dialog-edit-flavor"></div>
                                <div class="nomineeInfo">
                                    <div class="nomineeName" id="dialog-edit-name"></div>
                                    <div class="nomineeSubtitle" id="dialog-edit-subtitle"></div>
                                </div>
                            </div>

                            <input type="hidden" name="action" id="form-action">
                            <input type="hidden" name="id" id="form-id">

                            <div class="form-group">
                                <small class="form-text text-success"><strong>New in 2025:</strong> Nominee IDs are now automatically generated.</small>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="info-name">
                                    Name <span class="required" title="Required">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input class="form-control" type="text" id="info-name" placeholder="Monster Girl Quest" required
                                           name="name">
                                    <small id="info-nomination" class="form-text" style="display: none;">
                                        This nominee will be linked to a user nomination. The name can safely be changed without unlinking it.
                                    </small>
                                    <input id="info-group" type="hidden" name="group">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="info-subtitle">Subtitle</label>
                                <div class="col-sm-9">
                                    <input class="form-control" type="text" id="info-subtitle" placeholder="Toro Toro Resistance"
                                           name="subtitle"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="info-image">Image</label>
                                <div class="col-sm-9">
                                    <input type="file" id="info-image" name="image" class="form-control">
                                    <small class="form-text">Recommended image dimensions: <strong>548 x 300</strong></small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="info-flavor">Flavor Text</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" id="info-flavor" name="flavorText"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-danger me-auto" id="dialog-edit-delete" type="button">
                                <img src="{{ asset('img/delete-this.png') }}" class="delete-this" alt="A picture of Counter pointing a gun at you, the viewer">
                                Delete this
                            </button>
                            <span id="dialog-edit-status" style="display: none;">
                                <i class="far fa-circle-notch fa-spin me-1"></i> saving...&nbsp;
                            </span>
                            <button class="btn btn-outline-dark" type="button" data-bs-dismiss="modal">Cancel</button>
                            <button class="btn btn-primary" id="dialog-edit-submit" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@pushif($award, 'js')
    <script>
        var nominationNames = {{ Js::from($nominationNames) }};
        var nominees = {{ Js::from($nominees) }};
        var currentlySubmitting = false;

        $(document).ready(function () {
            $('.alert-danger .btn-close').on('click', function () {
                $(this).parent().fadeOut("fast");
            });

            $("#info-name").autocomplete({
                source: nominationNames,
                minLength: 2,
                delay: 0
            });

            $('#accurateDimensions').change(function () {
                $('#nominee-container').toggleClass('inaccurate');
                $('#nominee-container').toggleClass('accurate');
            });

            // When a delete button is clicked
            $("#dialog-edit-delete").click(function () {
                // Get the nominee name and ID from the contents of the nominee box
                var nomineeID = $("#form-id").attr("value");
                var nomineeName = nominees[nomineeID].name;

                // Set up the dialog elements for this nominee
                $("#dialog-delete-status").hide();
                $("#dialog-delete-nominee-confirm").removeAttr("disabled");
                $("#dialog-delete-nominee").html(nomineeName);
                $("#dialog-delete").attr("data-nominee", nomineeID);

                // Finally, show the dialog
                $("#dialog-delete").modal("show");
                $("#dialog-delete-nominee-confirm").focus();
            });

            // When the confirm button is clicked in the delete dialog
            $("#dialog-delete-nominee-confirm").click(function () {
                if (currentlySubmitting) {
                    return;
                }
                currentlySubmitting = true;

                // Show the "please wait" message and disable the submit button
                $("#dialog-delete-status").show();
                $("#dialog-delete-nominee-confirm").attr("disabled", "disabled");

                // Grab the nomineeID from the attribute we set earlier
                var nomineeID = $("#dialog-delete").attr("data-nominee");

                // Send through the AJAX request to do the actual deleting
                $.post("{{ route('nominees.manage.post', $award) }}", {action: "delete", id: nomineeID}, function (data) {
                    currentlySubmitting = false;

                    if (data.success) {
                        $(".nominee[data-nominee=" + nomineeID + "]").slideUp(500, function () {
                            $(this).remove();
                            $("#official-count").text($("#official-count").text() - 1);
                        });
                        delete nominees[nomineeID];
                    } else {
                        alert("An error occurred: " + data.error);
                    }

                    // Close the dialogs
                    $("#dialog-delete").modal("hide");
                    $("#dialog-edit").modal("hide");
                }, "json");
            });

            var editDialogInterval;
            var lastImageValue = "";
            var lastID = "";
            let mergingFrom = null;
            let mergingFromName = null;

            // When the new nominee button is clicked
            function openNewNomineeModal(name = '', group = '') {
                // Update the dialog action
                $("#form-id").removeAttr("name");
                $("#form-action").attr("value", "new");
                $("#dialog-edit").removeAttr("data-nominee");
                $("#dialog-edit-header").text("Add new nominee");

                // Clear any existing information in the dialog
                $("#dialog-edit-name").text("");
                $("#dialog-edit-subtitle").text("");
                $("#dialog-edit-image").css("background-image", "");

                $("#info-name").val(name);
                $("#info-nomination").toggle(name !== '');
                $('#info-group').val(group);
                $("#info-subtitle").val("");
                $("#info-image").val("");
                $("#info-flavor").val("");

                // Show the dialog
                $("#dialog-edit").modal("show");
            }

            $("#new").on('click', () => openNewNomineeModal());

            // When an edit button is clicked
            $("#nominee-container").find(".nominee").click(function () {
                // Grab the ID
                var nomineeID = $(this).attr("data-nominee");

                // Update the dialog action
                $("#form-id").attr("value", nomineeID);
                $("#form-id").attr("name", "id");
                $("#form-action").attr("value", "edit");
                $("#dialog-edit").attr("data-nominee", nomineeID);
                $("#dialog-edit-header").text("Editing " + nominees[nomineeID].name);

                // Grab all the relevant information
                $("#dialog-edit-name").text(nominees[nomineeID].name);
                $("#dialog-edit-subtitle").html(nominees[nomineeID].subtitle);
                if (nominees[nomineeID].image) {
                    $("#dialog-edit-image").css("background-image", "url(" + nominees[nomineeID].image.url + ")");
                } else {
                    $("#dialog-edit-image").css("background-image", "url('{{ asset('img/no-image-available.png') }}'");
                }

                $("#info-name").val(nominees[nomineeID].name);
                $("#info-nomination").hide();
                $('#info-group').val('');
                $("#info-subtitle").val(nominees[nomineeID].subtitle);
                $("#info-image").val("");
                $("#info-flavor").val(nominees[nomineeID].flavorText);

                // Show the dialog
                $("#dialog-edit").modal("show");
            });


            // When the dialog is opened, start the monitor
            $("#dialog-edit").on("show.bs.modal", function () {
                $("#dialog-edit-status").hide();
                $("#dialog-edit-submit").removeAttr("disabled");
                $("#dialog-edit-error").parent().hide();

                lastImageValue = "";
                lastID = "";
                editDialogInterval = setInterval(updateDialogNominee, 500);
            });

            // When the dialog is closed, clear it
            $("#dialog-edit").on("hide.bs.modal", function () {
                clearInterval(editDialogInterval);
            });

            // Update the information in the template nominee box
            function updateDialogNominee() {
                $("#dialog-edit-name").text($("#info-name").val());
                $("#dialog-edit-subtitle").html($("#info-subtitle").val());
                $("#dialog-edit-flavor").html($("#info-flavor").val());
            }

            // When the confirm button is clicked in the edit dialog
            $("#dialog-edit-form").submit(function (event) {
                event.preventDefault();

                if (currentlySubmitting) {
                    return;
                }
                currentlySubmitting = true;

                // Show the "please wait" message and disable the submit button
                $("#dialog-edit-status").show();
                $("#dialog-edit-submit").attr("disabled", "disabled");
                $("#dialog-edit-error").parent().slideUp();

                // Grab the nomineeID and action from the dialog
                var nomineeID = $("#dialog-edit").attr("data-nominee");
                var action = $("#form-action").attr("value");

                // Send through the AJAX request to do the actual editing
                var formData = new FormData(this);
                $.ajax({
                    url: "{{ route('nominees.manage.post', $award) }}",
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false
                }).done(function (data) {
                    currentlySubmitting = false;

                    if (data.success) {

                        if (action === "new") {
                            // @TODO: This needs to read the new ID from the reponse
                            nomineeID = $("#info-id").val();
                            var clone = $("#dialog-edit").find(".nominee").clone(true, true);
                            clone.find("*").removeAttr("id");
                            clone.removeAttr("style");
                            clone.attr("id", "nominee" + nomineeID);
                            clone.attr("data-nominee", nomineeID);
                            clone.find("form").show();
                            clone.find("header p").text("ID: " + nomineeID);

                            $("#nominee-container").append(clone);

                            nominees[nomineeID] = {};
                            nominees[nomineeID].nomineeID = nomineeID;

                            $("#official-count").text(parseInt($("#official-count").text()) + 1);

                        }

                        var nomineeBox = $("#nominee-" + nomineeID);
                        nomineeBox.find("h3").text($("#info-name").val());
                        nomineeBox.find("footer p").html($("#info-subtitle").val());

                        nominees[nomineeID].name = $("#info-name").val();
                        nominees[nomineeID].subtitle = $("#info-subtitle").val();
                        nominees[nomineeID].flavorText = $("#info-flavor").val();

                        // Close the dialog
                        $("#dialog-edit").modal("hide");
                    } else {
                        // Something went wrong! Show the error message.
                        $("#dialog-edit-status").hide();
                        $("#dialog-edit-submit").removeAttr("disabled");
                        $("#dialog-edit-error").html("<strong>Error:</strong> " + data.error);
                        $("#dialog-edit-error").parent().fadeIn("fast");
                    }
                }, "json");
            });

            var numberToReveal = 15;

            $("#show-more").click(function (e) {
                e.preventDefault();
                $(".nomination-last-visible").removeClass('nomination-last-visible');
                $(".nomination-overflow").slice(0, numberToReveal).removeClass('nomination-overflow');
                $(".nomination-container:not(.nomination-overflow)").last().addClass('nomination-last-visible');

                // Show twice as many nominees each time
                numberToReveal *= 2;

                if ($(".nomination-overflow").length === 0) {
                    $(this).hide();
                }
            });

            $(".nomination-dropdown a").on('click', function (event) {
                event.preventDefault();

                const $button = $(this);
                const $group = $button.closest('.nomination-container');
                const groupId = $group.data('id');
                const action = $button.data('action');
                const name = $group.data('name');

                if (action === 'ignore' || action === 'unignore') {
                    if (!confirm(`Are you sure you want to ${action} nominations for "${name}"?`)) {
                        return;
                    }

                    $.post("{{ route('nominations.group.ignore', $award) }}", {group: groupId, ignore: action === 'ignore'}, function (data) {
                        if (data.success) {
                            // TODO: handle better
                            window.location.reload();
                        } else {
                            alert("An error occurred: " + data.error);
                        }
                    }, "json");

                    return;
                }

                if (action === 'merge') {
                    $('.nominations').addClass('merging');
                    $group.addClass('merging-from');
                    mergingFrom = groupId;
                    mergingFromName = name;

                    $('#merge-message').removeClass('d-none');
                    $('#merging-from-name').text(name);

                    setTimeout(() => {
                        $('.nomination').attr('data-bs-toggle', 'disabled').removeClass('dropdown-toggle');
                    }, 100);
                }

                if (action === 'demerge') {
                    if (!confirm(`Are you sure you want to undo the merge of nominations from "${name}"?`)) {
                        return;
                    }

                    $.post("{{ route('nominations.group.demerge', $award) }}", {group: groupId}, function (data) {
                        if (data.success) {
                            // TODO: handle better
                            window.location.reload();
                        } else {
                            alert("An error occurred: " + data.error);
                        }
                    }, "json");
                }

                if (action === 'unlink') {
                    if (!confirm(`Are you sure you want to unlink the nominee from "${name}"?`)) {
                        return;
                    }

                    $.post("{{ route('nominations.group.unlink', $award) }}", {group: groupId}, function (data) {
                        if (data.success) {
                            // TODO: handle better
                            window.location.reload();
                        } else {
                            alert("An error occurred: " + data.error);
                        }
                    }, "json");
                }

                if (action === 'add-nominee') {
                    openNewNomineeModal(name, groupId);
                }
            });

            $('#show-all-user-nominations').on('change', function (e) {
                e.preventDefault();

                if ($(this).is(':checked')) {
                    $('.nominations').addClass('show-all');
                } else {
                    $('.nominations').removeClass('show-all');
                }
            });

            $('.nomination').on('click', function (e) {
                if (!mergingFrom) {
                    return;
                }

                const $group = $(this).closest('.nomination-container');
                const groupId = $group.data('id');

                if (groupId === mergingFrom) {
                    finishMerge();
                    return;
                }

                if (!confirm(`Are you sure you want to merge all nominations from "${mergingFromName}" into "${name}"?`)) {
                    return;
                }

                $.post("{{ route('nominations.group.merge', $award) }}", {from: mergingFrom, to: groupId}, function (data) {
                    if (data.success) {
                        // TODO: handle better
                        window.location.reload();
                    } else {
                        alert("An error occurred: " + data.error);
                    }
                }, "json");
            });

            function finishMerge() {
                mergingFrom = null;
                $('.nominations').removeClass('merging');
                $('.nomination-container').removeClass('merging-from');
                $('.nomination').attr('data-bs-toggle', 'dropdown').addClass('dropdown-toggle');
                $('.nomination').removeClass('merging-from');
                $('#merge-message').addClass('d-none');
            }
        });
    </script>
@endpushif
