@props(['nextAward'])

<section class="ballot poster" data-js="ballot-panel">
    <div class="poster__inlay poster__inlay--white"></div>

    <div class="ballot__inner" data-js="ballot-inner">
        <h2 class="ballot__title" data-js="ballot-title">Your Votes</h2>

        {{-- Must stay empty (no whitespace) so the :empty placeholder letter shows. --}}
        <div class="nominee-grid nominee-grid--ballot" data-js="ballot"></div>

        <div class="ballot__actions">
            <button type="button" class="ballot__action" data-js="vote-reset" title="Reset Votes">
                <span class="ballot__action-arrow">&gt;</span>RESET
            </button>
            <button type="button" class="ballot__action ballot__action--submit" data-js="vote-submit" title="Submit Votes">
                <span class="ballot__action-arrow">&gt;</span>SEND
            </button>
        </div>
    </div>

    <div class="ballot__coconuts"></div>
</section>

<div class="next-award">
    <a href="{{ route('voting', $nextAward) }}" class="next-award__link" data-js="award-nav" title="Next award">
        <img class="next-award__sign" src="{{ asset('2024images/right-sign.png') }}" alt="">
        {{ $nextAward->name }}
    </a>
</div>
