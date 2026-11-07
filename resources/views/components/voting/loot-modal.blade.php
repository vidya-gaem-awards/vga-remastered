<div class="modal fade loot-modal" data-js="loot-modal" role="dialog" data-bs-backdrop="static">
    <div class="modal-dialog loot-modal__dialog" role="document">
        <div class="loot-modal__title">LOOT GET</div>

        <div class="modal-content loot-modal__content">
            <div class="loot-modal__ratio"></div>
            <div class="loot-modal__frame"></div>
            <div class="loot-modal__water"></div>
            <div class="loot-modal__water loot-modal__water--front"></div>

            <div class="loot-modal__stage">
                <div class="loot-modal__body">
                    <div class="loot-modal__slots">
                        @for($i = 0; $i < 3; $i++)
                            <div class="loot-slot" data-js="loot-slot">
                                <img class="loot-slot__sprite" data-js="loot-sprite" alt="">
                                <x-voting.item-card class="item-card--loot" data-js="loot-item" hidden />
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="loot-modal__footer">
                    <button type="button" class="loot-modal__prompt" data-js="unbox">
                        <span class="loot-modal__flavor" data-js="loot-flavor">"Whoa, a big one!"</span>
                        <span class="loot-modal__reel">▶ Reel 'em in!</span>
                    </button>
                    <div class="loot-modal__collect" data-js="loot-collect" hidden>
                        <button type="button" class="loot-modal__link" data-bs-dismiss="modal">Collect drops</button>
                        <button type="button" class="loot-modal__link loot-modal__link--secondary" data-js="never-show-again">Don't show this again</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
