@extends('base.standard')

@section('title')
    {{ $user->name }}
@endsection

@pushonce('css')
<style>
    h1 {
        display: flex;
        align-items: center;
    }
    h1 img {
        width: 60px;
    }
</style>
@endpushonce

@pushonce('js')
<script type="text/javascript">
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    })
</script>
@endpushonce

@section('content')
<h1 class="display-4">
    <img class="profile-pic me-3" src="{{ $user->avatar_url }}">
    <span>{{ $user->name }}</span>
</h1>

<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item ">
            <a href="{{ route('people') }}">Team members</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="{{ route('people.view', $user) }}">{{ $user->name }}</a>
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

<div class="row">
    @yield('subContent')
</div>
@endsection
