@props(['nominee', 'order', 'votingOpen'])

@php
    $imageUrl = $nominee->image ? $nominee->image->getUrl() : asset('img/no-image-available.png');
    $subtitleHasLink = str_contains($nominee->subtitle ?? '', 'href=');

    $flavorLength = strlen($nominee->flavor_text ?? '');
    $flavorSize = match (true) {
        $flavorLength < 50 => 'short',
        $flavorLength < 170 => 'medium',
        $flavorLength < 200 => 'long',
        default => 'extra-long',
    };
@endphp

<div @class(['nominee-card', 'nominee-card--votable' => $votingOpen]) data-js="nominee" data-nominee="{{ $nominee->id }}" data-order="{{ $order }}" data-zone="pool">
    <div class="nominee-card__rank" data-js="nominee-rank"></div>
    <div class="nominee-card__body">
        <div class="nominee-card__image" style="--nominee-image: url('{{ $imageUrl }}')"></div>
        <div class="nominee-card__drag-area" data-js="drag-handle"></div>

        {{-- Hints are hidden when the subtitle has a link so they don't cover it. --}}
        @if($votingOpen && !$subtitleHasLink)
            <div class="nominee-card__hint nominee-card__hint--add">
                <span>
                    Click to make <span class="nominee-card__hint-name">{{ $nominee->name }}</span>
                    your&nbsp;<span data-js="next-preference">1st</span>&nbsp;preference
                </span>
            </div>
            <div class="nominee-card__hint nominee-card__hint--remove">
                <span>
                    Click to remove <span class="nominee-card__hint-name">{{ $nominee->name }}</span>
                    from your votes
                </span>
            </div>
        @endif

        @if($nominee->flavor_text)
            <div class="nominee-card__flavor nominee-card__flavor--{{ $flavorSize }}">{!! nl2br(e($nominee->flavor_text)) !!}</div>
        @endif

        <div class="nominee-card__info">
            <div class="nominee-card__name">{{ $nominee->name }}</div>
            <div class="nominee-card__subtitle">{!! str_replace('href=', 'target="blank" href=', $nominee->subtitle) !!}</div>
        </div>

        <div class="nominee-card__grip" data-js="drag-handle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</div>
