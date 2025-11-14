@extends('base.standard')

@section('title')
    Award Manager
@endsection

@pushonce('css')
<style>
    #awards {
        background-color: white;
    }

    .label {
        display: block;
        text-align: center;
        padding: 13px 0;
    }

    #awards .aligned {
        text-align: center;
        vertical-align: middle;
    }

    .award-id {
        font-family: monospace;
        font-size: smaller;
        color: #9E9E9E;
    }

    .award-id:hover {
        color: inherit;
    }

    .sparkbar {
        margin: 4px 0;
        height: 10px;
        overflow: hidden;
    }

    .sparkbar-yes {
        float: left;
        height: 10px;
        background: #55A54E;
    }

    .sparkbar-no {
        float: right;
        height: 10px;
        background: #AA4643;
    }
</style>
@endpushonce

@pushonce('js')
    <script type="text/javascript">
        $(document).ready(function () {
            var editDialog = $("#dialog-edit");
            var currentlySubmitting = false;

            editDialog.on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.attr('data-id');

                // New award
                if (id === undefined) {
                    editDialog.removeAttr('data-id');
                    $("#dialog-edit-header").text("Add new award");

                    // Clear any existing information in the dialog
                    $("#info-id").val("");
                    editDialog.find("input[type=text]").val("");
                    editDialog.find("input[type=number]").val("");
                    $("#info-enabled").prop("checked", true);
                    $("#info-nominations").prop("checked", false);
                    $("#info-secret").prop("checked", false);
                    $("#deleteAward").hide();

                    // Editing an existing award
                } else {
                    editDialog.attr('data-id', id);
                    var award = awards[id];

                    $("#dialog-edit-header").text(award.name);
                    $("#deleteAward").show().text('Delete award (' + award.name + ')');
                    $("#info-id").val(id);
                    $("#info-slug").val(award.slug);
                    $("#info-name").val(award.name);
                    $("#info-subtitle").val(award.subtitle);
                    $("#info-comments").val(award.comments);
                    if (award.autocompleter != id) {
                        $("#info-autocomplete").val(award.autocompleter).change();
                    } else {
                        $("#info-autocomplete").val("").change();
                    }
                    $("#info-order").val(award.order);
                    $("#info-enabled").prop("checked", award.enabled);
                    $("#info-nominations").prop("checked", award.nominationsEnabled);
                    $("#info-secret").prop("checked", award.secret);
                }
            });

            $("#dialog-edit-form").submit(function (event) {
                event.preventDefault();

                if (currentlySubmitting) {
                    return;
                }
                currentlySubmitting = true;

                // Show the "please wait" message and disable the submit button
                $("#dialog-edit-status").show();
                $('#dialog-edit').find("button").attr("disabled", "disabled");
                $("#dialog-edit-error").parent().slideUp();

                // Grab the award ID from the dialog
                var id = editDialog.attr("data-id");
                var action = id ? 'edit' : 'new';

                // Send through the AJAX request
                var data = $("#dialog-edit-form").serializeArray();
                data.push({name: "action", value: action});
                if (action === 'edit') {
                    data.push({name: "id", value: id});
                }

                $.post("{{ route('awards.manage.post.ajax') }}", data, function (response) {
                    if (response.success) {
                        window.location.reload();
                    } else {
                        $("#dialog-edit-status").hide();
                        $("#dialog-edit").find("button").removeAttr("disabled");
                        $("#dialog-edit-error")
                            .html("<strong>Error:</strong> " + response.error)
                            .parent().fadeIn("fast");

                        currentlySubmitting = false;
                    }
                }, "json");
            });

            $("#massChangeNominations").submit(function () {
                return confirm("Are you sure you want to just fuck this shit up?");
            });

            $("#deleteAward").click(function () {
                if (currentlySubmitting) {
                    return;
                } else if (!confirm("Are you sure you want to just fuck this award up?")) {
                    return;
                }

                currentlySubmitting = true;
                // Show the "please wait" message and disable the submit button
                $("#dialog-edit-status").show();
                $("#dialog-edit").find("button").attr("disabled", "disabled");
                $("#dialog-edit-error").parent().slideUp();

                var data = [
                    {name: "action", value: "delete"},
                    {name: "id", value: editDialog.attr("data-id")}
                ];

                $.post("{{ route('awards.manage.post.ajax') }}", data, function (response) {
                    if (response.success) {
                        window.location.reload();
                    } else {
                        $("#dialog-edit-status").hide();
                        $("#dialog-edit").find("button").removeAttr("disabled");
                        $("#dialog-edit-error")
                            .html("<strong>Error:</strong> " + response.error)
                            .parent().fadeIn("fast");

                        currentlySubmitting = false;
                    }
                }, "json");

            });
        });

        $('.alert-danger .btn-close').on('click', function () {
            $(this).parent().fadeOut("fast");
        });
    </script>
@endpushonce

@section('beforeContainer')
    @include('parts.award-admin-bar')
@endsection

@section('content')
    <h1 class="page-header board-header">Award Manager</h1>

    @foreach(Session::get('formError', []) as $message)
        <div class="alert alert-dismissible alert-danger" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endforeach

    @foreach(Session::get('formSuccess', []) as $message)
        <div class="alert alert-dismissible alert-success" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endforeach

    <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped form-table" id="awards">
            <thead>
            <tr>
                <th style="width: 60px;" class="hidden-sm">Order</th>
                <th>Name</th>
                <th style="width: 60px;">Nominees</th>
                <th style="width: 220px;">
                    Feedback
                    <span style="float: right;">
                        <small><a href='?sort=percentage'>sort %</a> or <a href='?sort=net'>sort net</a></small>
                    </span>
                </th>
                <th style="width: 130px;">Status</th>
                @if($can('awards_edit') && !$settings->read_only)
                <th style="width: 80px;">Controls</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @foreach($awards as $award)
            <tr @class(['warning' => !$award->enabled, 'info' => $award->enabled && $award->secret])>
                <td class="aligned hidden-sm">{{ $award->order }}</td>
                <td class="align-middle p-2">
                    <div class="float-end award-id hidden-sm">
                        <abbr title="The internal ID of the award. Visible in some places, such as URLs.">{{ $award->slug }}</abbr>
                    </div>
                    <div class="{{ !$award->enabled ? 'text-muted' : '' }}">
                        {!! !$award->enabled ? '<s>' : '' !!}
                        <strong>{{ $award->name }}</strong> <br>
                        <small>{{ $award->subtitle }}</small>
                        {!! !$award->enabled ? '</s>' : '' !!}
                    </div>
                </td>
                <td class="aligned">
                    @can('nominations_edit')
                    <a href="{{ route('nominees.manage', $award) }}" target="_blank">
                        {{ $award->nominees()->count() }}
                    </a>
                    @elsecan
                    {{ $award->nominees()->count() }}
                    @endcan
                    @if($award->awardSuggestions()->count() > 0)
                    <div class="text-muted" style="font-size: x-small;">{{ $award->awardSuggestions()->count() }} names</div>
                    @endif
                </td>
                <td class="align-middle p-2">
                    @if(!$award->secret)
                    <div class="sparkbar">
                        <div class="sparkbar-yes" style="width: {{ $award->getFeedbackPercent()['positive'] }}%"></div>
                        <div class="sparkbar-no" style="width: {{ $award->getFeedbackPercent()['negative'] }}%"></div>
                    </div>
                    {{ $award->awardFeedback()->count() }} votes
                    <abbr style="float: right;"
                          title="{{ $award->getGroupedFeedback()['positive'] }} - {{ $award->getGroupedFeedback()['negative'] }} = {{ $award->getGroupedFeedback()['net'] }}">
                        {{ round($award->getFeedbackPercent()['positive']) }}%
                    </abbr>
                    @endif
                </td>
                @if(!$award->enabled)
                <td class="aligned bg-warning">
                    <span class="badge">Award Disabled</span>
                </td>
                @elseif($award->secret)
                <td class="aligned bg-info">
                    <span class="badge">Secret Award!</span>
                </td>
                @elseif($award->nominations_enabled)
                <td class="aligned bg-success">
                    <span class="badge">Nominations Open</span>
                </td>
                @else
                <td class="aligned">
                    <span class="badge text-black">Nominations Closed</span>
                </td>
                @endif
                @if($can('awards_edit') && !$settings->read_only)
                <td class="aligned">
                    <button class="btn btn-primary" title="Edit award" data-bs-toggle="modal" data-bs-target="#dialog-edit" data-id="{{ $award->id }}">
                        <i class="fal fa-edit"></i> &nbsp;Edit
                    </button>
                </td>
                @endif
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="panel">
        <div class="panel-body">
            @if($can('awards_edit') && !$settings->read_only)
            <div class="btn-group">
                <form method="POST" action="{{ route('awards.manage.post') }}" id="massChangeNominations">
                    @csrf
                    <input type="hidden" id="action" name="action" value="massChangeNominations"/>
                    <button class="btn btn-success" type="submit" name="todo" value="open">Open all nominations</button>
                    <button class="btn btn-danger" type="submit" name="todo" value="close">Close all nominations</button>
                </form>
            </div>
            @endif
            <div class="btn-group pull-right">
                <button class="btn btn-outline-dark" id="view-award-suggestions" data-bs-toggle="modal" data-bs-target="#dialog-award-suggestions" @disabled($awardSuggestions->isEmpty())>
                    View award suggestions ({{ $awardSuggestions->count() }})
                </button>
                @if($can('awards_edit') && !$settings->read_only)
                <button class="btn btn-primary" id="new-award" type="button" data-bs-toggle="modal" data-bs-target="#dialog-edit">
                    <i class="fal fa-plus"></i> Add a new award
                </button>
                @endif
            </div>
        </div>
    </div>

    <div id="dialog-award-suggestions" class="modal" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Award suggestions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Protip:</strong> if you have any hot ideas about how you can make this list easier to use, let Clamburger know.
                    </div>

                    <ul style="margin-bottom: 0;">
                        @foreach($awardSuggestions as $suggestion)
                        <li>{{ $suggestion->suggestion }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-dark" type="button" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="dialog-edit" class="modal" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="dialog-edit-form">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="dialog-edit-header">Add new award</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="alert alert-dismissible alert-danger" style="display: none;">
                            <span id="dialog-edit-error"></span>
                            <button type="button" class="btn-close"></button>
                        </div>

                        <input type="hidden" id="info-id" name="id" value="">

                        <div class="form-group row">
                            <label class="col-lg-2 col-sm-3 col-form-label" for="info-id">
                                Slug <span class="required" title="Required">*</span>
                            </label>
                            <div class="col-lg-10 col-sm-9">
                                <input class="form-control" type="text" id="info-slug" placeholder="best-writing" required
                                       name="slug" pattern="[0-9a-zA-Z-]+">
                                <small class="form-text text-muted">This will appear in the URL when voting. Slug can only include letters, numbers, and dashes.</small><br>
                                <small class="form-text text-success"><strong>New in 2025:</strong> This can now be changed at any time.</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-sm-3 col-form-label" for="info-name">
                                Name <span class="required" title="Required">*</span>
                            </label>
                            <div class="col-lg-10 col-sm-9">
                                <input class="form-control" type="text" id="info-name" placeholder="Discworld Award" required
                                       name="name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-sm-3 col-form-label" for="info-subtitle">
                                Subtitle <span class="required" title="Required">*</span>
                            </label>
                            <div class="col-lg-10 col-sm-9">
                                <input class="form-control" type="text" id="info-subtitle" placeholder="for best writing / story"
                                       name="subtitle" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-sm-3 col-form-label" for="info-comments">Extra Details</label>
                            <div class="col-lg-10 col-sm-9">
                                <input class="form-control" type="text" id="info-comments" name="comments">
                                <small class="form-text text-muted">This will appear on the nominations page</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-sm-3 col-form-label" for="info-autocomplete">Autocompleter</label>
                            <div class="col-lg-10 col-sm-9">
                                <select class="form-select" name="autocompleter" id="info-autocomplete">
                                    <option></option>
                                    @foreach($autocompleters as $autocompleter)
                                    <option value="{{ $autocompleter->id }}">
                                        {{ $autocompleter->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    Determines what will be suggested when users are adding nominations.
                                    @can('autocompleter_edit')
                                    <a href="{{ route('autocompleters') }}" target="_blank">Edit autocompleters</a>
                                    @endcan
                                </small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-sm-3 col-form-label" for="info-order">
                                Position <span class="required" title="Required">*</span>
                            </label>
                            <div class="col-lg-10 col-sm-9">
                                <input class="form-control" type="number" min="1" max="10000" id="info-order" name="order" required>
                                <small class="form-text text-muted">Awards with a lower position will be sorted first. When adding awards for the first time, leave a gap of 10 to make it easier to change the order later.</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-10 col-sm-9 offset-lg-2 offset-sm-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="info-enabled" name="enabled" checked>
                                    <label class="form-check-label" for="info-enabled">
                                        Award enabled?
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-10 col-sm-9 offset-lg-2 offset-sm-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="info-nominations" name="nominationsEnabled" checked>
                                    <label class="form-check-label" for="info-nominations">
                                        Nominations enabled?
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-10 col-sm-9 offset-lg-2 offset-sm-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="info-secret" name="secret" checked>
                                    <label class="form-check-label" for="info-secret">
                                        Secret award?
                                    </label>
                                    <small class="form-text d-block text-muted">Secret awards don't show up until the voting stage</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        @can('awards_delete')
                            <button class="btn btn-danger me-auto" type="button" id="deleteAward">Delete award</button>
                        @endcan
                        <span id="dialog-edit-status" class="saving me-2" style="display: none;">
                            <i class="far fa-circle-notch fa-spin me-1"></i> Saving...
                        </span>

                        <button class="btn btn-outline-dark" type="button" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var awards = {{ Js::from($awards) }};
    </script>
@endsection
