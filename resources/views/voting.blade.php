@extends('base.special')

@section('fullTitle', '/v/GAs – Voting')

@section('bodyAttr')class="voting-page"@endsection

@pushonce('js')
    @vite('resources/assets/voting.ts')

    {{-- Reward CSS comes after the page stylesheet so it wins any specificity tie. --}}
    <style>
        {{-- TODO: This is how we've always done it, but we should be loading from a separate CSS file. --}}
        {!! $rewardCSS !!}
    </style>

    @if($lootboxTest)
        <style data-js="lootbox-editor-style">
            {!! $lootboxTest->css_contents !!}
        </style>
    @endif
@endpushonce

@if(!$award)
    @pushonce('js')
        <script>
            const isMobile = /mobi|android|iphone|ipad|ipod/i.test(navigator.userAgent);
            if (isMobile) {
                const wrap = document.createElement('div');
                wrap.className = 'mobile-meme';

                const img = document.createElement('img');
                img.className = 'mobile-meme__image';
                img.src = '/2025images/phonepost.jpg';

                const top = document.createElement('div');
                top.className = 'mobile-meme__caption mobile-meme__caption--top';
                top.textContent = 'PHONEPOSTER';

                const bottom = document.createElement('div');
                bottom.className = 'mobile-meme__caption mobile-meme__caption--bottom';
                bottom.textContent = 'DETECTED';

                wrap.append(img, top, bottom);
                document.body.appendChild(wrap);

                // fade out, then remove
                setTimeout(() => wrap.classList.add('mobile-meme--fade'), 1500);
                wrap.addEventListener('transitionend', () => wrap.remove(), { once: true });
            }
        </script>
    @endpushonce
@endif

@section('head')
    @if($award)
        <script type="text/javascript">
            var votingEnabled = {{ $votingOpen ? 'true' : 'false' }};
            var lastVotes = {{ Js::from($votes) }};
            var postURL = "{{ route('voting.post', $award) }}";
            var votingStyle = "{{ $votingStyle }}";
        </script>
    @endif

    <script type="text/javascript">
        const lootboxTest = {{ $lootboxTest->id ?? 'null' }};
        @if($lootboxTest)
        const lootboxItemUpdateUrl = "{{ route('lootbox.items.css') }}";
        @endif
        const lootboxSettings = {{ Js::from($lootboxSettings) }};
        const lootboxTiers = {{ Js::from($lootboxTiers) }};
        const rewards = {{ Js::from($items) }};
        @if($award)
        var currentAward = "{{ $award->id }}";
        @else
        var currentAward;
        @endif
    </script>
@endsection

@section('body')
    {{-- Empty anchor elements that reward CSS can position buddies against. --}}
    <div class="buddy-helpers" aria-hidden="true">
        @for($i = 1; $i <= 6; $i++)
            <div class="buddy-helper buddy-helper--{{ $i }}"></div>
        @endfor
    </div>

    @if($award)
        <img class="reward-buddie" data-js="reward-buddie" alt="" hidden>
    @endif

    {{-- One .page-section per major piece of the page. --}}
    <div class="voting-layout">
        <div class="page-section page-section--header">
            <x-voting.site-header :text="$voteText" />
        </div>

        @if($award)
            <div class="page-section page-section--award">
                <x-voting.award-poster
                    :award="$award"
                    :prev-award="$prevAward"
                    :next-award="$nextAward"
                    :voting-open="$votingOpen"
                    :voting-closed="$votingClosed"
                    :voting-style="$votingStyle" />
            </div>

            @if($votingOpen && $votingStyle !== 'legacy')
                <div class="page-section page-section--ballot">
                    <x-voting.ballot :next-award="$nextAward" />
                </div>
            @endif
        @else
            <div class="page-section page-section--start">
                <x-voting.start-message
                    :voting-not-yet-open="$votingNotYetOpen"
                    :voting-open="$votingOpen"
                    :voting-closed="$votingClosed"
                    :first-award="$awards->first()" />
            </div>
        @endif

        <div class="page-section page-section--awards">
            <x-voting.awards-list :awards="$awards" :current-award="$award" :all-votes="$allVotes" :voting-open="$votingOpen" />
        </div>

        @if($award)
            <div class="page-section page-section--inventory">
                <x-voting.inventory />
            </div>
        @endif
    </div>

    @if($award)
        <x-voting.loot-modal />
        <x-voting.no-music-modal />
    @endif

    @if($lootboxTest)
        <x-voting.lootbox-editor :item="$lootboxTest" />
    @endif

    @if($votingOpen)
        <x-voting.leave-modal />
    @endif
@endsection
