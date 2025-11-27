@extends('base.standard')

@section('title', 'Lootbox Settings')

@section('beforeContainer')
    @include('parts.lootbox-admin-bar')
@endsection

@section('content')
    <h1 class="page-header board-header">Lootbox Settings</h1>

    <div class="col-md-4 offset-md-4 mb-4 mt-4">
        <form method="post">
            @csrf
            <div class="form-group">
                <label for="cost">
                    Lootbox cost <span class="required" title="Required">*</span>
                </label>

                <div class="input-group mb-3">
                    <input type="number" class="form-control" step="1" min="0" id="cost" name="cost" required value="{{ $settings->lootbox_cost }}">
                    <span class="input-group-text">shekels</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Save Changes</button>
        </form>
    </div>
@endsection
