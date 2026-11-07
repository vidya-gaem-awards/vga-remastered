@props(['award', 'prevAward', 'nextAward', 'votingOpen', 'votingClosed', 'votingStyle'])

<section class="award-poster poster">
    <div class="poster__inlay poster__inlay--art"></div>

    <header class="award-header">
        <a href="{{ route('voting', $prevAward) }}" class="award-header__arrow award-header__arrow--prev" data-js="award-nav" aria-label="Previous award"></a>
        <div class="award-header__titles">
            <h1 class="award-header__name">
                <span class="award-header__tagline" data-js="title-tagline"></span>
                <span class="award-header__name-text" data-text="{{ $award->name }}">{{ $award->name }}</span>
            </h1>
            <p class="award-header__subtitle">{{ $award->subtitle }}</p>
        </div>
        <a href="{{ route('voting', $nextAward) }}" class="award-header__arrow award-header__arrow--next" data-js="award-nav" aria-label="Next award"></a>
    </header>

    @if($votingStyle === 'legacy')
        <x-voting.legacy-ballot :nominees="$award->nominees" :show-vote-column="$votingOpen || $votingClosed" :voting-open="$votingOpen" />
    @else
        @if($votingOpen)
            <p class="award-poster__mobile-hint">
                Tap on any nominee to make them your <span data-js="next-preference">1st</span> preference.
            </p>
        @endif
        <div class="nominee-grid nominee-grid--pool" data-js="pool">
            @foreach($award->nominees->shuffle() as $nominee)
                <x-voting.nominee-card :nominee="$nominee" :order="$loop->index" :voting-open="$votingOpen" />
            @endforeach
        </div>
    @endif
</section>
