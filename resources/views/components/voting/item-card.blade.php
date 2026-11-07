@props(['image' => null, 'name' => '', 'actions' => false])

<div {{ $attributes->class('item-card') }}>
    <img class="item-card__image" data-js="item-image" @if($image) src="{{ $image }}" @endif alt="">
    <div class="item-card__name" data-js="item-name">{{ $name }}</div>
    @if($actions)
        <div class="item-card__actions">
            <button type="button" class="item-card__action" data-js="item-action" data-type="buddie" aria-pressed="false"><i class="far fa-fw fa-dog"></i></button>
            <button type="button" class="item-card__action" data-js="item-action" data-type="music" aria-pressed="false"><i class="far fa-fw fa-music"></i></button>
            <button type="button" class="item-card__action" data-js="item-action" data-type="css" aria-pressed="false"><i class="far fa-fw fa-palette"></i></button>
        </div>
    @endif
</div>
