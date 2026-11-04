@props(['votingNotYetOpen', 'votingOpen', 'votingClosed', 'firstAward'])

<section class="start-message poster">
    @if($votingNotYetOpen || $votingOpen)
        <div class="start-message__intro">
            <div class="start-message__logo"></div>
            <div class="start-message__greeting">
                <p class="start-message__headline">Aloha from the 2025 <span class="start-message__nowrap">/v/GAs</span></p>
                <p class="start-message__tagline">Have a relaxing visit during our voting season</p>
                <p class="start-message__fineprint">No spaghetti to be stowed. $80 fee.</p>
            </div>
        </div>

        <hr class="start-message__divider">

        <h1 class="start-message__heading">HOW TO VOTE</h1>
        <p class="start-message__text start-message__text--instructions">
            Click on the nominee you want to win most first, followed by the nominee you want to win second, etc.<br>
            <b>You can preference as many or as few nominees as you want.</b>
        </p>

        <div class="start-message__vote-styles">
            <div class="vote-style vote-style--virgin">
                <img class="vote-style__image" src="/img/virgin.png" alt="">
                <span class="vote-style__text">
                    <strong>The Virgin Vote</strong><br>
                    click on the nominee you want to win, then hit submit.
                </span>
            </div>
            <div class="vote-style">
                <img class="vote-style__image" src="/img/chad.png" alt="">
                <span class="vote-style__text">
                    <strong>The Chad Choice</strong><br>
                    click on multiple nominees in the order you want them to win.
                </span>
            </div>
        </div>
    @endif

    @if($votingNotYetOpen)
        <p class="start-message__text">
            Voting isn't open yet, but you can still browse the awards and have a look at the nominees. You can
            use the list of awards at the bottom and the meme arrows at the top to navigate.
        </p>
    @endif

    @if($votingOpen)
        <p class="start-message__text">
            Use the award list at the bottom to navigate, the arrows at the top of the page, or the arrow that appears after you
            click submit.
        </p>

        <div class="start-message__actions">
            <a class="btn start-message__start" href="{{ route('voting', $firstAward) }}">Start Voting</a>
        </div>
    @endif

    @if($votingClosed)
        <h2 class="start-message__subheading">Thanks to everybody who voted.</h2>
        <p class="start-message__text">No new votes can be made, but if you've already voted you can still see the votes you made.</p>
    @endif
</section>
