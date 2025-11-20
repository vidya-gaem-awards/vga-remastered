@extends('base.standard')

@section('title', '401')

@section('content')
<h1 class="page-header board-header mb-4">/401/ - Authentication Required</h1>
<p class="text-center">
    To view this page, you'll need to sign in with a Steam account that's been added to the /v/GA team.
</p>
<p class="text-center">
    <a class="btn btn-dark" href="{{ route('login', ['redirect' => Request::url()]) }}">
        <i class="fab fa-fw fa-steam"></i> Sign in with Steam
    </a>
</p>
@endsection
