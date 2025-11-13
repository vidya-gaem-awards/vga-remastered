@extends('people.view-base')

@section('subContent')
<div class="col-md-6">
    <h2>User information</h2>

    <ul class="list-unstyled">
        @if($user->primary_role)
        <li>
            <i class="fas fa-fw fa-user me-2"></i>
            {{ $user->primary_role }}
        </li>
        @endif

        <li>
            <i class="fab fa-fw fa-steam-symbol me-2"></i>
            <a href="https://steamcommunity.com/profiles/{{ $user->steam_id }}" target="_blank">Steam profile</a>
        </li>

        @if($user->email)
        <li>
            <i class="fas fa-fw fa-envelope me-2"></i>
            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
        </li>
        @endif

        <li>
            <i class="fas fa-fw fa-clock me-2"></i>
            @if($user->last_login)
            <span>Logged in on {{ $user->last_login->setTimezone('America/New_York')->format('F jS, Y') }}</span>
            @else
            <span>Never logged in</span>
            @endif
        </li>
    </ul>

    @if($can('profile_edit_details') && !$settings->read_only)
    <a class="btn btn-primary" href="{{ route('people.edit', $user) }}">Edit user information</a>
    @endif
</div>
<div class="col-md-6">
    @can('profile_edit_groups')
        @if($user->permissions()->count() > 0)
            <h2>Permissions</h2>
            <p>
                Hover over any permission for a description of that permission.
            </p>
            <form method="POST" action="{{ route('people.edit.post', $user) }}" class="form-inline mb-3">
                @csrf
                @foreach($user->permissions as $permission)
                <div class="input-group me-2 mb-2">
                    <div class="input-group-text" data-bs-toggle="tooltip" data-bs-placement="bottom"
                         title="{{ $permission->description }}">{{ $permission->id }}</div>
                    @if(!$settings->read_only)
                    <button class="btn btn-danger" name="RemoveGroup" value="{{ $permission->id }}" title="Remove this permission">
                        <i class="fas fa-times"></i>
                    </button>
                    @endif
                </div>
                @endforeach
            </form>
        @endif

        @if(!$settings->read_only)
            <h2>Add a permission</h2>
            <form method="POST" action="{{ route('people.edit.post', $user) }}">
                @csrf
                <select class="form-select input-small" name="GroupName">
                    @foreach($permissions->reject(fn ($p) => $user->permissions->pluck('id')->contains($p->id)) as $permission)
                        <option value="{{ $permission->id }}">
                            {{ $permission->id }} &ndash; {{ $permission->description }}
                            @if(!str_starts_with($permission->id, 'LEVEL'))
                            ({{ $permission->parents->pluck('id')->join(', ') }})
                            @endif
                        </option>
                    @endforeach
                </select>
                <input type="submit" class="btn btn-success mt-2" name="AddGroup" value="Add permission">
            </form>
        @endif
    @endcan
</div>
@endsection
