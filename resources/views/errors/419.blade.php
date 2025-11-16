@extends('base.standard')

@section('title')
    419
@endsection

@section('content')
    <h1 class="page-header board-header mb-4">/419/ - Page Expired</h1>
    <p class="text-center">
        If you're seeing this, then you either took too long to fill in a form or our developers have made a grave mistake.
    </p>
    <p class="text-center">
        Only you will know which one it is!
    </p>
    @if(url()->previous())
        <p class="text-center">
            <a class="btn btn-dark" href="{{ url()->previous() }}">
                Try Again
            </a>
        </p>
    @endif
@endsection
