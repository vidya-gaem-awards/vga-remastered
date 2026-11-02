<tr class="song-row">
    <td class="drag-handle text-muted" title="Drag to reorder">
        <span class="fa fa-grip-vertical"></span>
        <input type="hidden" data-field="section" value="{{ $song?->section->value }}">
    </td>
    <td><input type="text" class="form-control form-control-sm" data-field="title" maxlength="255" required value="{{ $song?->title }}"></td>
    <td><input type="text" class="form-control form-control-sm" data-field="artist" maxlength="255" value="{{ $song?->artist }}"></td>
    <td><input type="text" class="form-control form-control-sm" data-field="context" maxlength="255" value="{{ $song?->context }}"></td>
    <td><input type="url" class="form-control form-control-sm" data-field="link" maxlength="255" value="{{ $song?->link }}"></td>
    <td class="song-actions">
        <button type="button" class="btn btn-sm btn-outline-danger remove-song" title="Remove this song">
            <span class="fa fa-trash"></span>
        </button>
    </td>
</tr>
