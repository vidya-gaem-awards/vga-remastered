@extends('base.standard')

@section('title', 'Version')

@section('content')
    <h1 class="page-header board-header">/g/ - Technology</h1>

    <p class="board-subheader">
        Website version information
    </p>

    <div class="row">
        <div class="col-12 col-lg-6 offset-lg-3">
            <table class="table table-bordered table-striped" style="background-color: white;">
                <tr>
                    <th style="width: 200px;">Repository</th>
                    <td>
                        <i class="fab fa-github"></i> <a href="https://github.com/vidya-gaem-awards/vga-remastered" target="_blank">vga-remastered</a>
                    </td>
                </tr>
                <tr>
                    <th style="width: 200px;">Environment</th>
                    <td>{{ config('app.env') }}</td>
                </tr>
                <tr>
                    <th>Commit</th>
                    <td>
                        @if($commit)
                            <a href="https://github.com/vidya-gaem-awards/vga-remastered/tree/{{ $commit }}">
                                <code>{{ substr($commit, 0, 8) }}</code>
                            </a>
                        @else
                            [unknown]
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Build Date</th>
                    <td>
                        @if(config('app.build_date'))
                            {{ Date::parse(config('app.build_date'))->format('F j, Y H:i:s') }} UTC
                            <br>
                            <small>
                                {{ Date::parse(config('app.build_date'))->ago() }}
                            </small>
                        @else
                            <div class="text-muted">unknown</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
@endsection
