@extends('base.special')

@section('fullTitle', '/v/GAs – Voting')

@pushonce('css')

    <link rel="stylesheet" href="{{ asset('css/votingNominees.css') }}">

    <style>
        {{-- TODO: This is how we've always done it, but we should be loading from a separate CSS file. --}}
        {!! $rewardCSS !!}
    </style>

    @if($lootboxTest)
        <style id="lootbox-editor-style">
            {!! $lootboxTest->css_contents !!}
        </style>
    @endif
@endpushonce

@pushonce('js')
    @vite('resources/assets/voting.ts')
@endpushonce

@section('body_attr', 'id="voting-page"')

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

    @if($votingOpen)
        <style>
            .aNominee {
                cursor: move;
            }
            .voteBox {
                cursor: pointer;
            }
        </style>
    @endif
@endsection

@section('body')
    <div class="buddy-helper-1"></div>
    <div class="buddy-helper-2"></div>
    <div class="buddy-helper-3"></div>
    <div class="buddy-helper-4"></div>
    <div class="buddy-helper-5"></div>
    <div class="buddy-helper-6"></div>
    <div class="background-decorations">
        {{--
        @foreach($decorations as $decoration)
        <img src="{{ $decoration['decoration']->image->getUrl() }}" class="decoration" style="rotate: {{ $decoration['angle'] }}deg; {{ $decoration['direction'] }}: {{ $decoration['x'] }}px; top: {{ $decoration['y'] }}px; mask-image: linear-gradient(to {{$decoration['direction']}}, #0000, #FFF 80%);">
        {% endfor %}
        --}}
    </div>

    @if($award)
        <img id="reward-buddie">
    @endif

    <div class="center-container">
        <header>
            <a class="logo" href="{{ route('index') }}">
                <picture>
                    <source media="(max-width: 800px)" srcset="{{ asset('2024images/logo1.png') }}">
                    <img src="{{ asset('2024images/logo1-long.png') }}">
                </picture>
            </a>
            <div class="right-container">
                <div class="title-text">
                    {{ $voteText }}
                </div>

                <div class="plank-background">
                    <div class="plank-inner-border"></div>
                </div>
            </div>
        </header>

        <div id="wrapper">
            @if($award)
                <div class="poster-background">
                    <div class="award-header">
                        <a href="{{ route('voting', $prevAward) }}" class="navigation left"></a>
                        <div class="award-name-container">
                            <div class="award-name">{{ $award->name }}</div>
                            <div class="award-subtitle">{{ $award->subtitle }}</div>
                        </div>
                        <a href="{{ route('voting', $nextAward) }}" class="navigation right"></a>
                    </div>

                    @if($votingStyle === 'legacy')
                        <div id="limitsDrag">
                            <div id="nomineeColumn" class="column">

                                <img src="/2016images/pickYourNominees.png" width="204" height="70" alt="Pick your nominees">

                                @foreach($award->nominees->shuffle() as $nominee)
                                    <div class="voteBox">
                                        <div id="nominee-{{ $nominee->id }}" class="aNominee" data-order="{{ $loop->index }}"
                                             data-nominee="{{ $nominee->id }}">
                                            <img class="fakeBorder" src="/2016images/votebox_foreground.png">
                                            <img class="fakeBorder locked" src="/2016images/votebox_foreground_locked.png">
                                            @if($nominee->flavor_text)
                                                <div class="flavorText">{!! nl2br(e($nominee->flavor_text)) !!}</div>
                                            @endif
                                            <img class="nomineeImage" src="{{ $nominee->image?->getUrl() }}">
                                            <div class="nomineeInfo">
                                                <div class="number"></div>
                                                <div class="nomineeName">{{ $nominee->name }}</div>
                                                <div class="nomineeSubtitle">{!! $nominee->subtitle !!}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>

                            <div id="spacerColumn" class="column">
                                &nbsp;
                            </div>

                            @if($votingOpen || $votingClosed)
                                <div id="voteColumn" class="column">
                                    <img src="/2016images/dragAndDrop.png" width="307px" height="70px" alt="Drag and drop to vote"/>
                                    @foreach($award->nominees as $nominee)
                                        <div id="voteBox{{ $loop->index }}" class="voteBox">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        @if($votingOpen)
                            <div class="mobileInstructions">
                                Tap on any nominee to make them your <span class="nextPreference">1st</span> preference.
                            </div>
                        @endif
                        <div class="flexContainer voteDropArea" id="voteDropAreaTop">
                            @foreach($award->nominees->shuffle() as $nominee)
                                <div class="voteGroup" id="nominee-{{ $nominee->id }}" data-nominee="{{ $nominee->id }}" data-order="{{ $loop->index }}">
                                    <div class="voteBox">
                                        <div class="number" style="display: none;">{{ $loop->index + 1 }}</div>
                                        <div class="nominee">
                                            <div class="fakeElement" style="background-image: url('{{ $nominee->image ? $nominee->image->getUrl() : asset('img/no-image-available.png') }}'); background-size: 100% 100%"></div>
                                            <div class="handle fakeHandle"></div>
                                            @if($votingOpen)
                                                <div class="hoverOverlay overlayWhenTop {{ str_contains($nominee->subtitle, 'href=') ? 'adjustForLink' : '' }}">
                                                    <span>
                                                      Click to make <strong>{{ $nominee->name }}</strong>
                                                      your&nbsp;<span class="nextPreference">1st</span>&nbsp;preference
                                                    </span>
                                                </div>
                                                <div class="hoverOverlay overlayWhenBottom {{ str_contains($nominee->subtitle, 'href=') ? 'adjustForLink' : '' }}">
                                                    <span>
                                                        Click to remove <strong>{{ $nominee->name }}</strong>
                                                        from your votes
                                                    </span>
                                                </div>
                                            @endif
                                            @if($nominee->flavor_text)
                                                <div class="flavorText
                                                    {{ strlen($nominee->flavor_text) < 50 ? ' shortFlavorText' : '' }}
                                                    {{ strlen($nominee->flavor_text) >= 50 && strlen($nominee->flavor_text) < 170 ? ' mediumFlavorText' : '' }}
                                                    {{ strlen($nominee->flavor_text) >= 170 && strlen($nominee->flavor_text) < 200 ? ' longFlavorText' : '' }}
                                                    {{ strlen($nominee->flavor_text) >= 200 ? ' extraLongFlavorText' : '' }}
                                                ">
                                                    {!! nl2br(e($nominee->flavor_text)) !!}
                                                </div>
                                            @endif
                                            <div class="nomineeInfo">
                                                <div class="nomineeName">{{ $nominee->name }}</div>
                                                <div class="nomineeSubtitle">{!! str_replace('href=', 'target="blank" href=', $nominee->subtitle) !!}</div>
                                            </div>
                                            <div class="handle realHandle">
                                                <i class="fas fa-bars"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($votingOpen)
                            <h3 style="text-align: center; margin-top: 10px; margin-bottom: 10px;" id="submitReminder">Click "Submit" after voting to save your choices!</h3>
                        @endif

                    </div>

                    <div id="dragLimit">
                        <div class="your-votes-container">
                            <div class="your-votes-title-area">
                                <div class="your-votes">
                                    Your Votes
                                </div>
                            </div>
                            <div class="flexContainer voteDropArea" id="voteDropAreaBottom"></div>

                            @if($votingOpen)
                                <div class="buttons" style="margin-top: 10px;">
                                    <div id="btnResetVotes" class="btnSubmit" title="Reset Votes">
                                        <div class="hoverArrow">&gt;</div>
                                        <div id="resetText">RESET</div>
                                    </div>
                                    <div id="btnLockVotes" class="btnSubmit" title="Submit Votes">
                                        <div class="hoverArrow">&gt;</div>
                                        <div id="submitText">SUBMIT</div>
                                    </div>
                                </div>
                            @endif

                            <div class="plank-background">
                                <div class="plank-inner-border"></div>
                            </div>

                        </div>

                        @if($votingOpen)
                            <div class="buttons" style="margin-top: 10px;">
                                <a href="{{ route('voting', $nextAward) }}" class="navigation next" title="Next award">
                                    <img src='{{ asset('2024images/right-sign.png') }}'/>
                                    {{ $nextAward->name }}
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="voteGroup placeholder" style="display: none;" id="dropPlaceholder">
                        <div class="voteBox">
                            <div class="nominee">
                                <div class="fakeElement"></div>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div id="startMessage" class="poster-background">
                    @if($votingNotYetOpen || $votingOpen)
                        <div class="wanted-title">
                            <h2>ALL VIDYALANTEES WANTED</h2>
                            <h3>$0,000,000 REWARD</h3>
                        </div>
                        <hr/>
                        <h1 style="text-align: center;">HOW TO VOTE</h1>
                        <div style="margin-top: 5px; margin-bottom: 15px;" class="virgin-chad">
                            <img src="{{ asset('img/virgin.png') }}"/>
                            <div>
                                <strong>The Varmint Voice</strong><br/>
                                click on the nominee you want to win, then hit submit.
                            </div>
                        </div>
                        <div class="virgin-chad">
                            <img src="{{ asset('img/chad.png') }}"/>
                            <div>
                                <strong>The Chief Choice</strong><br/>
                                click on multiple nominees in the order you want them to win.
                            </div>
                        </div>
                        <p style="padding-top: 30px;">
                            Click on the nominee you want to win most first, followed by the nominee you want to win second, etc.<br/>
                            <b>You can preference as many or as few nominees as you want.</b>
                        </p>
                    @endif

                    @if($votingNotYetOpen)
                        <p>Voting isn't open yet, but you can still browse the awards and have a look at the nominees. You can
                            use the list of awards at the bottom and the meme arrows at the top to navigate.</p>
                    @endif

                    @if($votingOpen)
                        <p>
                            Use the award list at the bottom to navigate, the arrows at the top of the page, or the arrow that appears after you
                            click submit.
                        </p>

                        <a href="{{ route('voting', $awards->first()) }}">
                            <button id="btnStart" class="btnSubmit btn">Start Voting</button>
                        </a>
                    @endif

                    @if($votingClosed)
                        <h2>Thanks to everybody who voted.</h2>
                        <p>No new votes can be made, but if you've already voted you can still see the votes you made.</p>
                    @endif
                </div>
            @endif
        </div>

        <div class="zig"></div>

        <div id="containerAwards" class="awards-list poster-background" style="margin-top: 10px;">
            <div class="your-votes" style="margin-bottom: 10px;">
                Awards
                @if($votingOpen)
                    <div class="votes-left">{{ $awards->filter(fn ($a) => !$allVotes[$a->id])->count() }} left </div>
                @endif
            </div>

            <ul class="awards">
                @foreach($awards as $_award)
                <li>
                    <a href="{{ route('voting', $_award) }}" id="{{ $_award->id }}"
                       class="award {{ $award?->id === $_award->id ? 'active' : '' }} {{ $allVotes[$_award->id] ? 'complete' : '' }}">
                        <span class="award-name">{{ $_award->name }}</span>
                        <span class="award-subtitle">{{ $_award->subtitle }}</span>
                    </a>
                </li>
                @endforeach
            </ul>

            @auth
                <div class="goBackLink">
                    <a href="{{ route('home') }}">< Back to the main part of the site</a>
                </div>
            @endauth

            @if($award)
                <div id="no-music" class="modal fade" role="dialog">
                    <div class="modal-dialog" role="document">

                        <div id="errorWindow" style="width: 100%;" class="modal-content">
                            <div class="inner">
                                <p style="font-weight: normal;">Oops! There's been a fucky wucky!</p>
                                <br>
                                <p>
                                    Your browser doesn't support OGG audio.
                                </p>

                                <button class="command_button right-button close-button" type="button" data-bs-dismiss="modal">
                                    iToddlers BTFO
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($award)
        <div class="center-container">
            <div id="containerInventory">
                <div class="inventory-title-area">
                    <div class="title-text">
                        Your Caravan
                    </div>
                </div>

                <div class="inventory-container-container">

                    <div id="inventory">
                        <div class="inventory-container">

                            <div class="inventory-item-outer-wrapper" id="shekelCount" style="display: none;">
                                <div class="inventory-item">
                                    <div class="item-inner-wrapper">
                                        <div class="item-tier"></div>
                                        <img src="{{ asset('2023images/gold-bars.png') }}">
                                        <div class="item-name">-1 gold</div>
                                    </div>
                                </div>
                                <div class="pole"></div>
                            </div>

                            <div class="inventory-item-outer-wrapper" id="item-template" style="display: none;">
                                <div class="inventory-item">
                                    <div class="item-inner-wrapper">
                                        <div class="item-tier"></div>
                                        <img>
                                        <div class="item-name"></div>
                                        <div class="item-button-container">
                                            <button class="item-button item-buddie" data-type="buddie"><i class="far fa-fw fa-dog"></i></button>
                                            <button class="item-button item-music" data-type="music"><i class="far fa-fw fa-music"></i></button>
                                            <button class="item-button item-css" data-type="css"><i class="far fa-fw fa-palette"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="pole"></div>
                            </div>
                        </div>
                    </div>

                    <div class="inventory-buttons">
                        <button class="btn btn-default btn-lootbox" id="buy-lootbox">Force encounter (<span id="lootboxCostText"></span> gold)</button>
                        <button class="btn btn-default btn-lootbox" id="unequipAll">Unequip all items</button>
                        <button class="btn btn-default btn-lootbox" id="restoreDrops" disabled>Restore drops</button>
                        <button class="btn btn-default btn-lootbox" id="resetRewardsButton" style="display: none;">Mute music</button>
                    </div>
                </div>

                <div class="plank-background">
                    <div class="plank-inner-border"></div>
                </div>
            </div>

            <form id="cheat-code" style="margin-bottom: 10px; margin-top: 20px; display: none;">
                <div class="your-votes" style="margin-bottom: 10px;">Enter cheat code</div>
                <div style="width: 400px; text-align: center; display: flex; margin: 0 auto;">
                    <input type="text" class="form-control" id="cheat-code-input">
                    <button type="submit" class="btn btn-default btn-lootbox" style="margin-left: 5px;">Activate</button>
                </div>
            </form>
        </div>
    @endif

    @if($award)
    <div id="rewards" class="modal fade" role="dialog" data-bs-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-title-container">
                LOOT GET
            </div>
            <div class="modal-content">
                <div class="loot-ratio-fixer"></div>
                <div style="position: absolute; top: 0px; width: 100%; height: 100%;">

                    <div class="modal-body" style="text-align: center;">
                        <div class="lootboxbox">
                            <div class="lootbox">
                                <img src="" class="lootbox-image">

                                <div class="inventory-item" style="display: none;">
                                    <div class="item-inner-wrapper">
                                        <div class="item-tier"></div>
                                        <img>
                                        <div class="item-name"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="lootbox">
                                <img src="" class="lootbox-image">
                                <div class="inventory-item" style="display: none;">
                                    <div class="item-inner-wrapper">
                                        <div class="item-tier"></div>
                                        <img>
                                        <div class="item-name"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="lootbox">
                                <img src="" class="lootbox-image">
                                <div class="inventory-item" style="display: none;">
                                    <div class="item-inner-wrapper">
                                        <div class="item-tier"></div>
                                        <img>
                                        <div class="item-name"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer" style="text-align: center;">
                        <div class="lootbox-prompt" id="unboxButton">
                            <div id="loot-modal-flavor">"Stick em up!"</div>
                            <div id="shoot">▶ Shoot!</div>
                        </div>
                        <div id="closeRewards" style="display: none;">
                            <div data-bs-dismiss="modal">Collect drops</div><br>
                            <div id="neverShowAgain" style="margin-top: 5px;">Don't show this again</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($lootboxTest)
        <div id="lootbox-editor" class="form-horizontal card bg-white text-black">
            @if($lootboxTest->css)
                <form enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $lootboxTest->id }}">
                    <div class="card-header">
                        <div>Editing <em>{{ $lootboxTest->name }}</em></div>
                    </div>
                    <div class="card-body">
                        <div id="info-cssContents-container">
                            <div class="d-flex justify-content-between align-content-center mb-1">
                                <span>CSS</span>
                                <label class="badge bg-secondary align-self-center" id="lootbox-editor-css-status">
                                    <input type="checkbox">
                                    <span class="label">Inactive</span>
                                </label>
                            </div>
                            <div id="lootbox-editor-css-codemirror"></div>
                            <textarea id="lootbox-editor-css-input" class="d-none" name="cssContents">{!! $lootboxTest->css_contents !!}</textarea>
                        </div>

                        <div class="collapse" id="lootbox-editor-collapse">
                            <small class="form-text">When this reward is equipped, the class <code id="code-id">reward-{{ $lootboxTest->slug }}</code> will be added to the root HTML element.</small>
                            <div class="mt-3">
                                <div>Keyboard shortcuts</div>
                                <div style="font-size: smaller;">
                                    <kbd>Esc</kbd> - unfocus input field<br>
                                    <kbd>Ctrl+E</kbd> - toggle [<strong>E</strong>]ditor<br>
                                    <kbd>Ctrl+C</kbd> - toggle [<strong>C</strong>]SS<br>
                                    <kbd>Ctrl+R</kbd> - [<strong>R</strong>]eset all
                                </div>
                                <small class="form-text">Shortcuts can be used without Ctrl when outside an input field.</small>
                            </div>

                            <div class="mt-3">
                                <label class="form-label" for="lootbox-editor-opacity">Editor opacity</label>
                                <input type="range" class="form-range" id="lootbox-editor-opacity" min="0.3" max="1" step="0.1" value="1">
                                <small class="form-text d-flex justify-content-between">
                                    <span>30%</span>
                                    <span>100%</span>
                                </small>
                            </div>
                        </div>

                        <div class="alert alert-dismissible alert-danger" style="display: none;">
                            <span id="dialog-edit-error"></span>
                            <button type="button" class="btn-close"></button>
                        </div>
                    </div>

                    <div class="card-footer d-flex">
                        <button class="btn btn-outline-dark me-auto" type="button" data-bs-toggle="collapse" data-bs-target="#lootbox-editor-collapse" id="lootbox-editor-collapse-toggle"><i class="fa-solid fa-chevrons-down"></i></button>
                        <i class="fa-solid fa-check text-success me-3 align-self-center" style="font-size: 30px; display: none;" id="lootbox-editor-submit-icon"></i>
                        <button class="btn btn-primary" id="dialog-edit-submit" type="submit">Save</button>
                    </div>
                </form>
            @else
                <div class="card-body">
                    <div>Previewing <em>{{ $lootboxTest->name }}</em></div>
                </div>
            @endif
        </div>
    @endif
@endsection
