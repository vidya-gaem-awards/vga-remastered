@extends('base.standard')

@section('title', 'Manage Soundtrack')

@php($sectionLabels = collect(\App\Enums\SoundtrackSection::cases())->mapWithKeys(fn ($section) => [$section->value => $section->label()]))

@pushonce('css')
    <style>
        .song-section .drag-handle {
            cursor: grab;
            width: 1%;
            vertical-align: middle;
        }
        .song-section .song-actions {
            width: 1%;
        }
        .song-section .empty-section td {
            font-style: italic;
        }
        .song-section:has(.song-row) .empty-section {
            display: none;
        }
        .song-placeholder {
            height: 3rem;
            background: rgba(0, 0, 0, 0.05);
        }
        /* The header line sits on top of the textarea so it reads as the first line of the CSV, without being editable */
        .bulk-editor-header {
            background-color: #e9ecef;
            border-bottom: 0;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            user-select: none;
            white-space: pre;
        }
        .bulk-editor-body {
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>
@endpushonce

@pushonce('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.7.0/papaparse.min.js"
            integrity="sha512-wyNDcNr/iqpMDH6ud/BVoy2SoWXnBZ6Bnq3kZa5XXYq7HZ4l8/ECCZkauMv5HDScNBYSl99P1fkiNOo74zig1Q=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var form = $('#songs-form');
            var sections = form.find('.song-section');

            sections.sortable({
                items: '.song-row',
                handle: '.drag-handle',
                connectWith: '.song-section',
                placeholder: 'song-placeholder',
                axis: 'y',
            });

            function addSongRow(section) {
                var row = $($('#song-row-template').html());
                sections.filter('[data-section="' + section + '"]').append(row);
                return row;
            }

            form.on('click', '.add-song', function () {
                var row = addSongRow($(this).data('section'));
                row.find('input').first().trigger('focus');
            });

            form.on('click', '.remove-song', function () {
                $(this).closest('.song-row').remove();
            });

            // Bulk editor: the tracklist as CSV text, converted to and from the table when switching views

            var sectionLabels = @json($sectionLabels);
            var bulkEditor = $('#bulk-editor');
            var bulkText = $('#bulk-editor-csv');
            var tableEditor = $('#table-editor');
            var toggleButton = $('#toggle-bulk-editor');

            function tableToCsv() {
                var rows = [];

                form.find('.song-row').each(function () {
                    var row = $(this);
                    rows.push([
                        sectionLabels[row.closest('.song-section').data('section')],
                        row.find('[data-field="title"]').val(),
                        row.find('[data-field="artist"]').val(),
                        row.find('[data-field="context"]').val(),
                        row.find('[data-field="link"]').val(),
                    ]);
                });

                return Papa.unparse(rows, {newline: '\n'});
            }

            // Accepts a section name in any case. A blank section means Main.
            function findSection(name) {
                var section = name.toLowerCase() || 'main';
                return sectionLabels[section] ? section : null;
            }

            // Replaces the table rows with the songs from the CSV text. Returns false (and leaves the table alone) if it has errors.
            function csvToTable() {
                var result = Papa.parse(bulkText.val(), {
                    delimiter: ',',
                    skipEmptyLines: 'greedy',
                    transform: function (value) {
                        return $.trim(value);
                    },
                });
                var errors = [];
                var songs = [];

                result.errors.forEach(function (error) {
                    errors.push('Song ' + (error.row + 1) + ': ' + error.message + '.');
                });

                result.data.forEach(function (values, index) {
                    var prefix = 'Song ' + (index + 1) + ': ';
                    // Missing columns at the end of a line are left blank
                    var song = {
                        section: findSection(values[0]),
                        title: values[1] || '',
                        artist: values[2] || '',
                        context: values[3] || '',
                        link: values[4] || '',
                    };

                    // Extra values usually mean a value containing a comma wasn't wrapped in quotes
                    if (values.length > 5) {
                        errors.push(prefix + 'too many values (expected at most 5, found ' + values.length + '). Values containing commas need to be wrapped in double quotes.');
                    } else if (!song.section) {
                        errors.push(prefix + 'unknown section "' + values[0] + '". Expected one of: ' + Object.values(sectionLabels).join(', ') + '.');
                    } else if (!song.title) {
                        errors.push(prefix + 'a title is required.');
                    } else if (song.link && !/^https?:\/\//.test(song.link)) {
                        errors.push(prefix + 'the link "' + song.link + '" isn\'t a valid URL. If a value before it contains a comma, it needs to be wrapped in double quotes.');
                    } else {
                        songs.push(song);
                    }
                });

                if (errors.length) {
                    alert('The bulk editor has some problems that need fixing first:\n\n' + errors.join('\n'));
                    return false;
                }

                form.find('.song-row').remove();
                songs.forEach(function (song) {
                    var row = addSongRow(song.section);
                    row.find('[data-field="title"]').val(song.title);
                    row.find('[data-field="artist"]').val(song.artist);
                    row.find('[data-field="context"]').val(song.context);
                    row.find('[data-field="link"]').val(song.link);
                });

                return true;
            }

            function isBulkEditorOpen() {
                return bulkEditor.is(':visible');
            }

            toggleButton.on('click', function () {
                if (isBulkEditorOpen()) {
                    if (!csvToTable()) {
                        return;
                    }
                    bulkEditor.hide();
                    tableEditor.show();
                    toggleButton.text('Bulk editor');
                } else {
                    bulkText.val(tableToCsv());
                    // The table is rebuilt from the text when switching back, so its rows (and their required fields)
                    // shouldn't block the form from being submitted in the meantime
                    form.find('.song-row').remove();
                    tableEditor.hide();
                    bulkEditor.show();
                    toggleButton.text('Table editor');
                }
            });

            // The order and section of each song come from where its row is in the table
            form.on('submit', function (event) {
                if (isBulkEditorOpen() && !csvToTable()) {
                    event.preventDefault();
                    return;
                }

                form.find('.song-row').each(function (index) {
                    var section = $(this).closest('.song-section').data('section');
                    $(this).find('[data-field]').each(function () {
                        $(this).attr('name', 'songs[' + index + '][' + $(this).data('field') + ']');
                    });
                    $(this).find('[data-field="section"]').val(section);
                });
            });
        });
    </script>
@endpushonce

@section('content')
    <h1 class="page-header board-header">Manage Soundtrack</h1>

    <p>
        <a href="{{ route('soundtrack') }}" target="_blank">View the soundtrack page</a>
        @if(!$settings->isPagePublic('soundtrack'))
            <span class="text-muted">(not currently public)</span>
        @endif
    </p>

    @if($settings->read_only)
        <div class="alert alert-warning">
            The site is currently in read-only mode. No changes can be made.
        </div>
    @endif

    <form id="songs-form" action="{{ route('soundtrack.manage.songs') }}" method="post" autocomplete="off">
        @csrf

        <div class="d-flex align-items-center mb-2">
            <button type="button" class="btn btn-outline-secondary me-2" id="toggle-bulk-editor">Bulk editor</button>
            <button type="submit" class="btn btn-primary" @disabled($settings->read_only)>Save tracklist</button>
        </div>

        <div id="bulk-editor" style="display: none;">
            <strong>Bulk editor instructions:</strong>
            <ul>
                <li>One song per line, following the columns shown at the top.</li>
                <li>The section can be <code>Preshow</code>, <code>Main</code> or <code>Bonus</code>.</li>
                <li>Values containing commas need to be wrapped in double quotes. Example: <code>Main,"No Woman, No Cry",Bob Marley</code> </li>
                <li>Values containing double quotes are complicated. Example: <code>Main,"No More ""I Love You's""",Annie Lennox</code></li>
                <li>If in doubt... just do what you can and use the table editor for the rest.</li>
            </ul>
            <div class="mb-3">
                <div class="form-control font-monospace bulk-editor-header" aria-hidden="true">Section,Title,Artist,Context,Link</div>
                <textarea class="form-control font-monospace bulk-editor-body" id="bulk-editor-csv" rows="25" wrap="off" spellcheck="false"
                          aria-label="Tracklist as CSV, with the columns Section, Title, Artist, Context, Link"></textarea>
            </div>
        </div>

        <table class="table table-sm align-middle  border" id="table-editor">
            <thead>
                <tr>
                    <th></th>
                    <th>Title <span class="required" title="Required">*</span></th>
                    <th>Artist or Album<br><span class="form-text">Album is common for OSTs</span></th>
                    <th>Context</th>
                    <th>Link</th>
                    <th></th>
                </tr>
            </thead>
            @foreach(\App\Enums\SoundtrackSection::cases() as $section)
                <tbody>
                    <tr>
                        <th colspan="5">{{ $section->label() }}</th>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-secondary text-nowrap add-song" data-section="{{ $section->value }}">
                                <span class="fa fa-plus"></span> Add song
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tbody class="song-section" data-section="{{ $section->value }}">
                    <tr class="empty-section">
                        <td colspan="6" class="text-muted">No tracks in this section.</td>
                    </tr>
                    @foreach($sections->get($section->value, []) as $song)
                        @include('parts.soundtrack-song-row', ['song' => $song])
                    @endforeach
                </tbody>
            @endforeach
        </table>

        <button type="submit" class="btn btn-primary" @disabled($settings->read_only)>Save tracklist</button>
    </form>

    <div class="h5 mt-4">Settings</div>
    <form action="{{ route('soundtrack.manage.settings') }}" method="post" autocomplete="off" enctype="multipart/form-data">
        @csrf
        <div class="row mb-3">
            <div class="col-sm-8">
                <label for="download_url" class="form-label">Soundtrack download URL</label>
                <input type="url" class="form-control" id="download_url" name="download_url"
                       value="{{ old('download_url', $soundtrack->download_url) }}">
            </div>
            <div class="col-sm-4">
                <label for="download_size" class="form-label">File size</label>
                <input type="text" class="form-control" id="download_size" name="download_size"
                       placeholder="e.g. 250 MB" value="{{ old('download_size', $soundtrack->download_size) }}">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-8">
                <label for="preshow_download_url" class="form-label">Preshow download URL</label>
                <input type="url" class="form-control" id="preshow_download_url" name="preshow_download_url"
                       value="{{ old('preshow_download_url', $soundtrack->preshow_download_url) }}">
            </div>
            <div class="col-sm-4">
                <label for="preshow_download_size" class="form-label">File size</label>
                <input type="text" class="form-control" id="preshow_download_size" name="preshow_download_size"
                       placeholder="e.g. 250 MB" value="{{ old('preshow_download_size', $soundtrack->preshow_download_size) }}">
            </div>
        </div>
        <div class="mb-3">
            <label for="album_art" class="form-label">Album art</label>
            <input class="form-control" type="file" id="album_art" name="album_art">
            @if($albumArt)
                <small class="text-muted form-text">
                    Existing image:
                </small><br>
                <a href="{{ $albumArt->getUrl() }}" target="_blank" class="d-inline-block">
                    <img src="{{ $albumArt->getUrl() }}" style="height: 50px;">
                </a>
            @else
                <small class="text-muted form-text">
                    No image currently uploaded
                </small>
            @endif
        </div>
        <button type="submit" class="btn btn-primary" @disabled($settings->read_only)>Save settings</button>
    </form>

    <template id="song-row-template">
        @include('parts.soundtrack-song-row', ['song' => null])
    </template>
@endsection
