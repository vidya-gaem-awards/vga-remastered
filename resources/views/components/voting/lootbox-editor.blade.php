@props(['item'])

<div class="lootbox-editor card bg-white text-black" data-js="lootbox-editor">
    @if($item->css)
        <form enctype="multipart/form-data" data-js="lootbox-editor-form">
            @csrf
            <input type="hidden" name="id" value="{{ $item->id }}">

            <div class="card-header lootbox-editor__header" data-js="lootbox-editor-handle">
                <div class="lootbox-editor__name">Editing <em>{{ $item->name }}</em></div>
            </div>

            <div class="card-body">
                <div class="d-flex justify-content-between align-content-center mb-1">
                    <span>CSS</span>
                    <label class="badge bg-secondary align-self-center lootbox-editor__status" data-js="lootbox-css-status">
                        <input type="checkbox" data-js="lootbox-css-toggle">
                        <span data-js="lootbox-css-status-label">Inactive</span>
                    </label>
                </div>
                <div class="lootbox-editor__code" data-js="lootbox-css-editor"></div>
                <textarea class="d-none" name="cssContents" data-js="lootbox-css-input">{!! $item->css_contents !!}</textarea>

                <div class="collapse lootbox-editor__details" id="lootbox-editor-details" data-js="lootbox-editor-details">
                    <small class="form-text">When this reward is equipped, the class <code>reward-{{ $item->slug }}</code> will be added to the root HTML element.</small>
                    <div class="mt-3">
                        <div>Keyboard shortcuts</div>
                        <div class="small">
                            <kbd>Esc</kbd> - unfocus input field<br>
                            <kbd>Ctrl+E</kbd> - toggle [<strong>E</strong>]ditor<br>
                            <kbd>Ctrl+C</kbd> - toggle [<strong>C</strong>]SS<br>
                            <kbd>Ctrl+R</kbd> - [<strong>R</strong>]eset all
                        </div>
                        <small class="form-text">Shortcuts can be used without Ctrl when outside an input field.</small>
                    </div>

                    <div class="mt-3">
                        <label class="form-label" for="lootbox-editor-opacity">Editor opacity</label>
                        <input type="range" class="form-range" id="lootbox-editor-opacity" data-js="lootbox-editor-opacity" min="0.3" max="1" step="0.1" value="1">
                        <small class="form-text d-flex justify-content-between">
                            <span>30%</span>
                            <span>100%</span>
                        </small>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex">
                <button class="btn btn-outline-dark me-auto" type="button" data-bs-toggle="collapse" data-bs-target="#lootbox-editor-details" aria-controls="lootbox-editor-details" aria-expanded="false" data-js="lootbox-editor-details-toggle"><i class="fa-solid fa-chevrons-down"></i></button>
                <i class="fa-solid fa-check text-success me-3 align-self-center lootbox-editor__saved" data-js="lootbox-editor-saved" hidden></i>
                <button class="btn btn-primary" type="submit">Save</button>
            </div>
        </form>
    @else
        <div class="card-body">
            <div>Previewing <em>{{ $item->name }}</em></div>
        </div>
    @endif
</div>
