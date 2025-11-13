@extends('base.standard')

@section('title')
    Permissions
@endsection

@section('content')
<h1 class="display-4">Permissions</h1>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('people') }}">Team members</a>
        </li>
        <li class="breadcrumb-item active ms-auto">
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
    <div class="col-md-12">
        <p>
            There are five main levels of access, from level 1 (the lowest) to level 5 (the highest). Every user gets the
            permissions from their level and every level below. There are also a few specific groups that only provide
            access to one or two things which can be assigned if needed.
        </p>
    </div>
</div>

<h2>Your permissions</h2>

<dl class="row">
    @foreach(auth()->user()->allPermissions() as $permission)
    <dt class="col-sm-3">
        {{ $permission->id }}
        @if($permission->parents->count() > 0)
            <small class='text-muted ms-1'>{{ $permission->parents->pluck('id')->join(', ') }}</small>
        @endif
    </dt>
    <dd class="col-sm-8">
        {{ $permission->description }}
    </dd>
    @endforeach
</dl>
@endsection
