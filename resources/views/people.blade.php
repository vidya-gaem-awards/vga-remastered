@extends('base.standard')

@section('title', 'Production team')

@pushonce('js')
    <script type="text/javascript">
        $(document).ready(function () {
            $("#users").tablesorter();
        });
    </script>
@endpushonce

@pushonce('css')
    <style>
        #users .profile-pic {
            width: 40px;
            margin-right: 0.85rem;
        }
        .permission {
            margin-right: 0.75rem;
        }
        .permission:last-child {
            margin-right: 0;
        }
        .LEVEL_5, .LEVEL_4, .LEVEL_3, .LEVEL_2, .LEVEL_1 {
            font-weight: 500;
        }
    </style>
@endpushonce

@section('content')
<h1 class="display-4">Production team</h1>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">
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

<table class="table table-bordered table-striped tablesorter" id="users">
    <thead>
    <tr>
        <th style="width: 230px;">Name</th>
        <th>Permissions</th>
        <th>Primary Role</th>
    </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
    <tr>
        <td class="py-2">
            <a href="{{ route('people.view', $user) }}"><img class="profile-pic" src="{{ $user->avatar_url }}">{{ $user->name }}</a>
        </td>
        <td>
            @forelse($user->permissions as $permission)
            <span class="permission {{ $permission->id }} {{ $permission->id === 'LEVEL_5' ? 'text-success' : '' }}">{{ $permission->id }}</span>
            @empty
            <em class="text-muted">No permissions</em>
            @endforelse
        </td>
        <td>{{ $user->primary_role }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
@endsection
