@extends('people.view-base')

@section('subContent')
<div class="col-md-12">
    <form method="POST" action="{{ route('people.edit', $user) }}" class="form-horizontal well"
          id="awardForm">
        @csrf
        <input type="hidden" name="action" value="edit-details">
        <div class="form-group">
            <label class="col-md-2 control-label" for="input01">Steam ID</label>
            <div class="col-md-4">
                <input type="text" class="form-control" id="input01" disabled value="{{ $user->id }}">
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label" for="input02">Last Known Name</label>
            <div class="col-md-4">
                <input type="text" class="form-control" id="input02" disabled value="{{ $user->name }}">
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label" for="input03">Primary Role</label>
            <div class="col-md-4">
                <input type="text" class="form-control" id="input03" name="PrimaryRole"
                       value="{{ $user->primary_role }}">
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label" for="input05">Email Address</label>
            <div class="col-md-4">
                <input type="text" class="form-control" id="input05" name="Email" value="{{ $user->email }}">
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-offset-2 col-md-4">
                <button type="submit" class="btn btn-primary">Save changes</button>
                <a href="{{ route('people.view', $user) }}" class="btn">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
