@extends('base.standard')

@section('title', 'Audit Log')

@section('content')
    <h1 class="page-header board-header">Audit Log</h1>

    <p class="board-subheader">
        This page is proudly sponsored by Lucien93
    </p>

    <table class="table table-bordered table-striped" style="background-color: white;">
        <thead>
        <tr>
            <th style="width: 160px;">Time</th>
            <th style="width: 540px;">Action</th>
            <th>Data</th>
        </tr>
        </thead>
        @foreach($actions as $action)
            @php($entities = $auditService->getMultiEntity($action))
            @php($entity = $entities['default'])
            <tr>
                <td>
                    <abbr title="{{ $action->created_at->setTimezone('America/New_York')->format('Y-m-d H:i:s') }}">
                        {{ $action->created_at->fromNow() }}
                    </abbr>
                    &nbsp; <small class="text-muted">{{ $action->created_at->setTimezone('America/New_York')->format('H:i') }}</small>
                </td>
                <td>
                    <div>
                        <img src="{{ $action->user->avatar_url }}" style="width: 25px; margin-right: 3px;"> <strong>{{ $action->user->name }}</strong>
                        {{ strtolower($actionTypes[$action->action]) }}
                    </div>
                    @if(str_starts_with($action->action, 'profile'))
                        <div style="margin-left: 31px; margin-top: 5px;">
                            @if($entity)
                                <a href="{{ route('people.view', $action->data1) }}">{{ $entity->name }}</a>
                            @else
                                unknown user ({{ $action->data1 }})
                            @endif
                            @if($action->data2)
                                : <code>{{ $action->data2 }}</code>
                            @endif
                        </div>
                    @endif

                    @if(str_starts_with($action->action, 'award') || str_starts_with($action->action, 'winner'))
                        <div style="margin-left: 31px; margin-top: 5px;">
                            @if($entity && $action->action !== 'award-delete')
                                {{ $entity->name }} :
                            @endif
                            <code>{{ $action->data1 }}</code>
                        </div>
                    @endif

                    @if(str_starts_with($action->action, 'nominee'))
                        <div style="margin-left: 31px; margin-top: 5px;">
                            @if($entity && $action->action !== 'nominee-delete')
                            {{ $entity->award->name }} : <code>{{ $action->data1 }}</code><br>
                            {{ $entity->name }} : <code>{{ $action->data2 }}</code>
                            @else
                            <code>{{ $action->data1 }}</code><br>
                            <code>{{ $action->data2 }}</code>
                            @endif
                        </div>
                    @endif

                    @if(str_starts_with($action->action, 'advert'))
                        <div style="margin-left: 31px; margin-top: 5px;">
                            @if($action->tableHistory)
                                {{ $action->tableHistory->values['name'] ?? '[unknown name]' }} :
                            @endif
                            <code>{{ $action->data1 }}</code><br>
                        </div>
                    @endif

                    @if(str_starts_with($action->action, 'item'))
                        <div style="margin-left: 31px; margin-top: 5px;">
                            @if($action->tableHistory)
                                {{ $action->tableHistory->values['name'] ?? '[unknown name]' }} :
                                <code>{{ $action->tableHistory->values['short-name'] ?? '[unknown name]' }}</code><br>
                            @endif
                        </div>
                    @endif

                    @if(str_starts_with($action->action, 'user-added'))
                        <div style="margin-left: 31px; margin-top: 5px;">
                            <a href="{{ route('people.view', $entity) }}">{{ $entity->name }}</a>
                            @if($action->data2)
                                : <small>permission <code>{{ $action->data2 }}</code></small>
                            @else
                                : <small>no permissions given</small>
                            @endif
                        </div>
                    @endif

                    @if(str_starts_with($action->action, 'template-'))
                        <div style="margin-left: 31px; margin-top: 5px;">
                            {{ $entity->name }} : <code>{{ $entity->filename }}</code>
                        </div>
                    @endif

                    @if(str_starts_with($action->action, 'autocompleter-'))
                        <div style="margin-left: 31px; margin-top: 5px;">
                            @if($entity && $action->action !== 'autocompleter-delete')
                                {{ $entity->name }} :
                            @endif
                            <code>{{ $action->data1 }}</code>
                        </div>
                    @endif

                    @if($action->action === 'add-video-game' && $entity)
                        <div style="margin-left: 31px; margin-top: 5px;">
                            {{ $entity->name }}
                        </div>
                    @endif

                    @if($action->action === 'remove-video-game')
                        <div style="margin-left: 31px; margin-top: 5px;">
                            <code>{{ $action->data1 }}</code>
                        </div>
                    @endif

                    @if($action->action === 'reload-video-games')
                        <div style="margin-left: 31px; margin-top: 5px;">
                            <code>{{ $action->data1 }}</code>
                        </div>
                    @endif

                    @if($action->action === 'nomination-group-merged' || $action->action === 'nomination-group-demerged')
                        <div style="margin-left: 31px; margin-top: 5px;">
                            <div>
                                @if($entities['data1']->award)
                                    <a class="text-dark" href="{{ route('nominees.manage', $entities['data1']->award) }}" target="_blank">{{ $entities['data1']->award->name }}</a>
                                @else
                                    <code>deleted award</code>
                                @endif
                            </div>
                            @if($action->action === 'nomination-group-merged')
                                <div>Merged from <code>{{ $action->data1 }}</code> : <em>{{ $entities['data1']->name }}</em></div>
                                <div>Merged into <code>{{ $action->data2 }}</code> : <em>{{ $entities['data2']->name }}</em></div>
                            @else
                                <div>Demerged <code>{{ $action->data1 }}</code> : <em>{{ $entities['data1']->name }}</em></div>
                                @if($action->data2)
                                    <div>Previously merged into <code>{{ $action->data2 }}</code> : <em>{{ $entities['data2']->name }}</em></div>
                                @endif
                            @endif
                        </div>
                    @endif

                    @if($action->action === 'nomination-group-ignored' || $action->action === 'nomination-group-unignored')
                        <div style="margin-left: 31px; margin-top: 5px;">
                            <div>
                                @if($entities['data1'])
                                    <a class="text-dark" href="{{ route('nominees.manage', $entities['data1']) }}" target="_blank">{{ $entities['data1']->name }}</a>
                                @else
                                    <code>{{ $action->data1 }}</code>
                                @endif
                            </div>
                            <div><code>{{ $action->data2 }}</code> : <em>{{ $entities['data2']->name }}</em></div>
                        </div>
                    @endif

                    @if($action->action === 'nomination-group-updated')
                        <div style="margin-left: 31px; margin-top: 5px;">
                            <div>
                                @if($entity->award)
                                    <a class="text-dark" href="{{ route('nominees.manage', $entity->award) }}" target="_blank">{{ $entity->award->name }}</a>
                                @else
                                    <code>deleted award</code>
                                @endif
                            </div>
                            <div><code>{{ $entity->id }}</code> : <em>{{ $entity->name }}</em></div>
                        </div>
                    @endif
                </td>
                <td>
                    @if($action->tableHistory)
                        <button class="btn btn-secondary" onclick="$('#data{{ $action->id }}').show();$(this).hide();">Show data</button>
                        <div id="data{{ $action->id }}" style="display: none;">
                            @foreach($action->tableHistory->values as $key => $value)
                                <div><strong>{{ $key }}:</strong> {{ is_iterable($value) ? json_encode($value) : $value }}</div>
                            @endforeach
                        </div>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
@endsection
