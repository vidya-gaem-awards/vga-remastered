@extends('base.standard')

@section('title', 'Autocompleters – Award Manager')

@section('beforeContainer')
    @include('parts.award-admin-bar')
@endsection

@section('content')
<h1 class="page-header board-header">Manage Autocompleters</h1>

<p class="text-justify">
    During the nomination process, a problem that's often encountered is the same nominee being entered multiple times,
    due to spelling the nominee wrong or using a slightly different name.
</p>
<p class="text-justify">
    To minimise this issue, you can specify a <strong>list of suggested nominees</strong> for each award (referred to as an <em>autocompleter</em> for short). Once a user
    has typed a couple of characters into the nominee box, a dropdown list will appear with nominees that match what
    they've typed in so far. It's still possible for users to submit nominations that don't appear in the autocomplete list,
    but this drastically reduces the chance of nominees not being grouped together properly.
</p>

<table class="table">
    <thead>
    <tr>
        <th style="width: 200px;">Slug</th>
        <th>Name</th>
        <th>Suggestions</th>
        <th>Awards with this autocompleter</th>
    </tr>
    </thead>
    <tbody>
    @foreach($autocompleters as $autocompleter)
    <tr>
        <td><code>{{ $autocompleter->slug }}</code></td>
        <td>
            @if($autocompleter->slug === 'video-games')
            <a href="{{ route('video-games') }}" target="_blank">{{ $autocompleter->name }} <i class="far fa-external-link fa-xs"></i></a><br>
            @else
            <a href data-bs-toggle="modal" data-bs-target="#autocompleterModal" data-id="{{ $autocompleter->id }}">{{ $autocompleter->name }}</a>
            @endif
        </td>
        <td>
            {{ $autocompleter->slug === 'video-games' ? $gameReleases : count($autocompleter->strings) }} entries
        </td>
        <td>
            @if($autocompleter->awards->isEmpty())
            <span class="text-muted">None</span>
            @elseif($autocompleter->awards->count() === 1)
            <em>&ldquo;{{ $autocompleter->awards->first()->name }}&rdquo;</em>
            @else
            {{ $autocompleter->awards->count() }} different awards
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>

<button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#autocompleterModal">
    <i class="far fa-plus"></i> Add a new autocompleter
</button>

<!-- Modal -->
<div class="modal fade" id="autocompleterModal" tabindex="-1" role="dialog" aria-labelledby="autocompleterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-dismissible alert-danger" style="display: none;">
                        <span class="alert-text"></span>
                        <button type="button" class="btn-close"></button>
                    </div>

                    <input type="hidden" name="id" id="info-id">

                    <div class="form-group row">
                        <label class="col-lg-2 col-sm-3 col-form-label" for="info-slug">
                            Slug <span class="required" title="Required">*</span>
                        </label>
                        <div class="col-lg-10 col-sm-9">
                            <input class="form-control" type="text" id="info-slug" required name="slug" pattern="[0-9a-zA-Z-]+" maxlength="30">
                            <small class="form-text text-muted">Slug can only include letters, numbers, and dashes</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-sm-3 col-form-label" for="info-name">
                            Name <span class="required" title="Required">*</span>
                        </label>
                        <div class="col-lg-10 col-sm-9">
                            <input class="form-control" type="text" id="info-name" required name="name" maxlength="100">
                            <small class="form-text text-muted">The name is only visible to those with award edit access</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-sm-3 col-form-label" for="info-suggestions">Suggestions</label>
                        <div class="col-lg-10 col-sm-9">
                            <textarea class="form-control" name="suggestions" id="info-suggestions" rows="10"></textarea>
                            <small class="form-text text-muted">One suggestion per line</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-2 col-sm-3 col-form-label">Import</div>
                        <div class="col-lg-10 col-sm-9">
                            <button class="btn btn-outline-secondary" type="button" onclick="$('#wikipedia-selection').toggle();" id="wikipedia-button">
                              Import from Wikipedia article
                            </button>

                            <div class="input-group my-2" id="wikipedia-selection" style="display: none; max-width: 300px;">
                                  <select class="form-select">
                                        <option value>Select a year</option>
                                        @foreach(range(1995, year()) as $year)
                                          <option value="{{ $year }}">{{ $year }} in video gaming</option>
                                        @endforeach
                                      </select>
                                  <button class="btn btn-outline-secondary" type="button">Import</button>
                                </div>
                            <small class="form-text text-dark" id="wikipedia-message"></small>

                            <button class="btn btn-outline-secondary" type="button" onclick="$('#igdb-selection').toggle();" id="igdb-button">
                                Import from IGDB
                            </button>

                            <div class="input-group my-2" id="igdb-selection" style="display: none; max-width: 300px;">
                                <input class="form-control" placeholder="Year" type="number" min="1950" max="{{ "now" | date('Y') }}">
                                <button class="btn btn-outline-secondary" type="button">Import</button>
                            </div>

                            <small class="form-text text-dark" id="igdb-message"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="delete-autocompleter" class="btn btn-danger me-auto">Delete autocompleter</button>
                    <span class="saving me-2" style="display: none;">
                        <i class="far fa-circle-notch fa-spin me-1"></i> Saving...
                    </span>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@pushonce('js')
<script type="text/javascript">
    const autocompleters = {{ Js::from($autocompletersEncodable) }};

    $(document).ready(function () {
        var editDialog = $("#autocompleterModal");
        var currentlySubmitting = false;

        $('.alert .btn-close').on('click', function () {
            $(this).parent().fadeOut("fast");
        });

        editDialog.on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var modal = $(this);

            modal.find('.alert').hide();

            if (id === undefined) {
                modal.find('.modal-title').text('Add new autocompleter');
                modal.data('id', null);
                modal.find('[name=id]').val('');
                modal.find('[name=slug]').val('');
                modal.find('[name=name]').val('');
                modal.find('[name=suggestions]').val('');
                modal.find('#delete-autocompleter').hide();
            } else {
                var autocompleter = autocompleters[id];
                modal.find('.modal-title').text('Edit autocompleter – ' + autocompleter.name);
                modal.data('id', id);
                modal.find('[name=id]').val(autocompleter.id);
                modal.find('[name=slug]').val(autocompleter.slug);
                modal.find('[name=name]').val(autocompleter.name);
                modal.find('[name=suggestions]').val(autocompleter.suggestions.join('\n'));
                modal.find('#delete-autocompleter').show();
            }
        });

        function handleError(error) {
            editDialog.find('.saving').hide();
            editDialog.find('button').removeAttr('disabled');
            editDialog.find('.alert-text')
                .html("<strong>Error:</strong> " + error)
                .parent().fadeIn("fast");

            currentlySubmitting = false;
        }

        editDialog.find('form').submit(function (event) {
            event.preventDefault();

            if (currentlySubmitting) {
                return;
            }
            currentlySubmitting = true;

            editDialog.find('.saving').show();
            editDialog.find('button').attr('disabled', 'disabled');
            editDialog.find('.alert').slideUp();

            var data = $(this).serializeArray();
            var id = editDialog.data('id');
            var action = id ? 'edit' : 'new';
            data.push({name: 'action', value: action});
            if (action === 'edit') {
                data.push({name: 'id', value: id});
            }

            $.post("{{ route('autocompleters.ajax') }}", data, function (response) {
                if (response.success) {
                    window.location.reload();
                } else {
                    handleError(response.error);
                }
            }, 'json').fail(function (response) {
                handleError(response.status);
            });
        });

        editDialog.find("#delete-autocompleter").click(function () {
            if (currentlySubmitting) {
                return;
            } else if (!confirm("Are you sure you want to delete this autocompleter?")) {
                return;
            }

            currentlySubmitting = true;

            editDialog.find('.saving').show();
            editDialog.find('[type=submit]').attr('disabled', 'disabled');
            editDialog.find('.alert').slideUp();

            var data = [
                {name: "action", value: "delete"},
                {name: "id", value: editDialog.data("id")}
            ];

            $.post("{{ route('autocompleters.ajax') }}", data, function (response) {
                if (response.success) {
                    window.location.reload();
                } else {
                    handleError(response.error)
                }
            }, "json").fail(function (response) {
                handleError(response.status);
            });
        });

        editDialog.find("#wikipedia-selection button").click(function () {
            var year = editDialog.find("#wikipedia-selection select").val();
            if (!year || !confirm('Import suggestions using the list of games in the "' + year + ' in video gaming" article? Any existing suggestions will be removed.')) {
                return;
            }

            var msg = editDialog.find("#wikipedia-message");
            msg.text('Please wait...');
            editDialog.find('button').attr('disabled', 'disabled');

            $.get("{{ route('autocompleters.wikipedia') }}", {year: year}, function (response) {
                if (response.success) {
                    editDialog.find('button').removeAttr('disabled');
                    msg.text(response.suggestions.length + ' suggestions successfully imported. Review the list of games before saving.');
                    editDialog.find('textarea').val(response.suggestions.join("\r\n"));
                } else {
                    handleError(response.error);
                    msg.text('');
                }
            }, "json").fail(function (response) {
                handleError(response.status);
                msg.text('');
            });
        });

        editDialog.find('#igdb-selection button').on('click', function () {
            var year = editDialog.find('#igdb-selection input').val();

            if (!year || !year.match(/^\d{4}$/) || year < 1950 || year > new Date().getFullYear()) {
                alert('Year is invalid. Must be between 1950 and the current year.');
                return;
            }

            if (!year || !confirm('Import suggestions using the list of games released in ' + year + '? Any existing suggestions will be removed.')) {
                return;
            }

            var msg = editDialog.find('#igdb-message');
            msg.text('Please wait... we fetch games 500 at a time from IGDB, so this can take a little while to finish.');
            editDialog.find('button').attr('disabled', 'disabled');

            $.get("{{ route('autocompleters.igdb') }}", {year: year}, function (response) {
                if (response.success) {
                    editDialog.find('button').removeAttr('disabled');
                    msg.text(response.suggestions.length + ' suggestions successfully imported. Review the list of games before saving.');
                    editDialog.find('textarea').val(response.suggestions.join("\r\n"));
                } else {
                    handleError(response.error);
                    msg.text('');
                }
            }, 'json').fail(function (response) {
                handleError(response.status);
                msg.text('');
            });
        });
    });
</script>
@endpushonce
