@props(['awards', 'currentAward', 'allVotes', 'votingOpen'])

<aside class="awards-list bamboo-frame">
    <h2 class="awards-list__title">
        AWARDS
        @if($votingOpen)
            <span class="awards-list__remaining">{{ $awards->filter(fn ($a) => !$allVotes[$a->id])->count() }} left</span>
        @endif
    </h2>

    <ul class="awards-list__items">
        @foreach($awards as $award)
            <li class="awards-list__item">
                <a href="{{ route('voting', $award) }}"
                   class="awards-list__link"
                   data-js="award-link"
                   data-award="{{ $award->id }}"
                   @if($currentAward?->id === $award->id) aria-current="page" @endif
                   @if($allVotes[$award->id]) data-voted @endif>
                    <span class="awards-list__name">{{ $award->name }}</span>
                    <span class="awards-list__subtitle">{{ $award->subtitle }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    @auth
        <p class="awards-list__home">
            <a class="awards-list__home-link" href="{{ route('home') }}">&lt; Return home</a>
        </p>
    @endauth

    <div class="awards-list__hibiscus"></div>
</aside>
