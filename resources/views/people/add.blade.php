@extends('base.standard')

@section('title', 'Add team member')

@pushonce('js')
<script type="text/javascript">
    var currentlySubmitting = false;
    var addButton = $("#btn-add");

    $('#search-form').submit(function (event) {
        event.preventDefault();

        if (currentlySubmitting) {
            return;
        }

        currentlySubmitting = true;

        $("#error-box").hide();
        $("#result-box").hide();
        $("#searching").show();

        $.post("{{ route('people.add.search') }}", {id: $('#search-id').val(), add: 0}, function (data) {
            currentlySubmitting = false;
            $("#searching").hide("fast");

            if ('error' in data) {
                var msg;
                if (data.error === "no matches") {
                    msg = "couldn't find a Steam profile with that ID.";
                } else if (data.error === "already special") {
                    msg = data.name + " is already in the user list.";
                } else {
                    msg = data.error;
                }
                $("#error-msg").text(msg);
                $("#error-box").show("fast");
            } else {
                var name = $('<div></div>').text(data.name).html();
                msg = "<img class='profile-pic' src='" + data.avatar + "'>";
                msg += "&nbsp;&nbsp;<a href='https://steamcommunity.com/profiles/" + data.steamID + "'>";
                msg += name + "</a>";
                addButton.attr("data-id", data.steamID);
                addButton.show();
                $('#add-form').show();
                $("#btn-success").hide();
                $("#result-msg").html(msg);
                $("#result-box").show("fast");
            }

        }, "json");
    });

    addButton.click(function () {

        if (currentlySubmitting) {
            return;
        }

        currentlySubmitting = true;

        $("#error-box").hide();
        addButton.show();
        $("#btn-success").hide();
        $("#searching").show();

        var data = {
            id: addButton.attr('data-id'),
            add: 1,
            permission: $('#starting-permission').val()
        };

        $.post("{{ route('people.add.search') }}", data, function (data) {
            currentlySubmitting = false;
            $("#searching").hide("fast");

            if ('error' in data) {
                var msg;
                if (data.error === "no matches") {
                    msg = "couldn't find a Steam profile with that ID.";
                } else if (data.error === "already special") {
                    msg = "it looks like that user is already on <a href='{{ route('people') }}'>the list</a>.";
                    msg += " It may have been double submitted for some reason.";
                } else {
                    msg = data.error;
                }
                $("#error-msg").html(msg);
                $("#error-box").show("fast");
            } else {
                $('#add-form').hide();
                $("#btn-success").show();
            }
        }, "json");
    });
</script>
@endpushonce

@section('content')
<h1 class="display-4">Add team member</h1>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('people') }}">Team members</a>
        </li>
        <li class="breadcrumb-item ms-auto">
            <a href="{{ route('people.permissions') }}">Your permissions</a>
        </li>
        @if($can('add_user') && !$settings->read_only)
            <li class="breadcrumb-item">
                <a href="{{ route('people.add') }}">Add new team member</a>
            </li>
        @endif
    </ol>
</nav>
<div class="row">
    <div class="col-md-6">
        <p>
            You can use this page to add a new user to the team.
        </p>
        <p>
            You can add a person by entering their <strong>Steam Community ID</strong> (which looks something
            like {{ Auth::user()->steam_id }}) or their <strong>profile URL</strong>.
        </p>
    </div>
    <div class="col-md-6">
        <div class="well well-small">
            <form id="search-form" class="mb-2">
                <label class="form-label" for="search-id"><strong>Steam user search</strong> <span class="required" title="Required">*</span></label>
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Steam Community ID or profile URL" style="min-width: 220px;"
                           id="search-id" required>
                    <input type="submit" class="btn btn-primary" value="Search">
                </div>
            </form>
            <p id="searching" style="display: none;">
                Processing...
                <img src="{{ asset('img/loading.gif') }}" style="height: 16px; width: 16px;"/>
            </p>
            <div class="alert alert-danger" style="display: none;" id="error-box">
                <strong>Error:</strong>
                <span id="error-msg"></span>
            </div>
            <div class="alert alert-success alert-block" id="result-box" style="display: none;">
                <h4>User found:</h4>
                <p id="result-msg" style="margin-top: 10px; margin-bottom: 10px; font-size: large;"></p>

                <div id="add-form">
                    <label for="starting-permission">
                        Select starting permissions:
                    </label>
                    <select class="form-select" id="starting-permission" style="margin-bottom: 3px; max-width: 100%;">
                        <option style="font-style: italic;" value="">No permissions</option>
                        @foreach($permissions as $permission)
                        <option value="{{ $permission->id }}" @selected($permission->id === 'LEVEL_1')>
                            {{ $permission->id }} &ndash; {{ $permission->description }}
                            @if(!str_starts_with($permission->id, 'LEVEL'))
                            ({{ $permission->parents->pluck('id')->join(', ') }})
                            @endif
                        </option>
                        @endforeach
                    </select>

                    <button class='btn btn-success btn-large' id='btn-add'>Add to team</button>
                </div>
                <strong id='btn-success' style="display: none;">Success!</strong>
            </div>
        </div>
    </div>
</div>
@endsection
