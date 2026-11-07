@props(['nominees', 'showVoteColumn', 'votingOpen'])

<div @class(['legacy-ballot', 'legacy-ballot--votable' => $votingOpen]) data-js="legacy-ballot">
    <div class="legacy-ballot__column legacy-ballot__column--pool" data-js="legacy-pool">
        <img class="legacy-ballot__heading" src="/2016images/pickYourNominees.png" width="204" height="70" alt="Pick your nominees">

        @foreach($nominees->shuffle() as $nominee)
            <div class="legacy-ballot__slot" data-js="legacy-slot">
                <div class="legacy-nominee" data-js="legacy-nominee" data-nominee="{{ $nominee->id }}" data-order="{{ $loop->index }}">
                    <img class="legacy-nominee__frame" src="/2016images/votebox_foreground.png" alt="">
                    <img class="legacy-nominee__frame legacy-nominee__frame--locked" src="/2016images/votebox_foreground_locked.png" alt="">
                    @if($nominee->flavor_text)
                        <div class="legacy-nominee__flavor">{!! nl2br(e($nominee->flavor_text)) !!}</div>
                    @endif
                    <img class="legacy-nominee__image" src="{{ $nominee->image?->getUrl() }}" alt="">
                    <div class="legacy-nominee__info">
                        <div class="legacy-nominee__rank" data-js="nominee-rank"></div>
                        <div class="legacy-nominee__name">{{ $nominee->name }}</div>
                        <div class="legacy-nominee__subtitle">{!! $nominee->subtitle !!}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($showVoteColumn)
        <div class="legacy-ballot__column legacy-ballot__column--votes" data-js="legacy-votes">
            <img class="legacy-ballot__heading legacy-ballot__heading--votes" src="/2016images/dragAndDrop.png" width="307" height="70" alt="Drag and drop to vote">
            @foreach($nominees as $nominee)
                <div class="legacy-ballot__slot" data-js="legacy-slot" data-slot="{{ $loop->index }}"></div>
            @endforeach
        </div>
    @endif
</div>
