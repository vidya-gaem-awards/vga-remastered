@extends('base.standard')

@section('title', 'News')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <h1>Recent Updates</h1>
            <p class="lead">
                It is currently <strong>{{ $now->format('D, d M Y H:i:s') }}</strong> ({{ $now->format('P') }} UTC)
            </p>
        </div>
    </div>

    @if($can('news_manage') && !$settings->read_only)
        <div class="row">
            <div class="col-md-8">
                <div class="well">
                    <form action="{{ route('news.add') }}" method="post" autocomplete="off">
                        @csrf
                        <div class="form-group">
                            <label for="news_text">Add news item <span class="required" title="Required">*</span></label>
                            <input type="text" class="form-control" id="news_text" name="news_text" required autocomplete="off">
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="date">
                                    Date <span class="required" title="Required">*</span>
                                </label>
                                <input type="text" class="form-control" id="date" name="date" required
                                       placeholder="yyyy-mm-dd hh:mm:ss" value="{{ $now->format('Y-m-d H:i:s') }}">
                                <span class="form-text">The news item won't be shown until this date.</span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="user">
                                    Author <span class="required" title="Required">*</span>
                                </label>
                                <input type="text" class="form-control" id="user" disabled value="{{ Auth::user()->name }}">
                                <span class="form-text">Unprivileged users won't see your name.</span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add News</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-xs-12">
            @foreach($news as $item)
            <h3>{{ $item->show_at->setTimezone('America/New_York')->format('M j, Y, g:ia') }}</h3>
            <form action="{{ route('news.delete', $item) }}" method="post">
                <p>
                    {!! $item->text !!}
                    @can('news_view_user')
                    &ndash; <a href="https://steamcommunity.com/profiles/{{ $item->user->steam_id }}">{{ $item->user->name }}</a>
                    @endcan
                    @if($can('news_manage') && !$settings->read_only)
                    <button type="submit" class="btn btn-danger btn-sm" title="Delete this news item"
                            onclick="return confirm('Are you sure you want to delete this news item?')">
                        <span class="fa fa-trash"></span>
                    </button>
                    @endif
                </p>
            </form>
            @endforeach
        </div>
    </div>
@endsection
