<section class="inventory">
    <h2 class="inventory__title">Your Treasure</h2>

    <div class="inventory__body">
        <div class="inventory__grid" data-js="inventory-grid">
            <x-voting.item-card class="item-card--gold" data-js="gold-count" :image="asset('2023images/gold-bars.png')" name="-1 gold" hidden />
        </div>

        <template data-js="inventory-item-template">
            <x-voting.item-card data-js="inventory-item" :actions="true" />
        </template>

        <div class="inventory__actions">
            <button type="button" class="btn inventory__action" data-js="buy-lootbox">Force encounter (<span data-js="lootbox-cost"></span> gold)</button>
            <button type="button" class="btn inventory__action" data-js="unequip-all">Unequip all items</button>
            <button type="button" class="btn inventory__action" data-js="restore-drops" disabled>Restore drops</button>
            <button type="button" class="btn inventory__action" data-js="mute-music" hidden>Mute music</button>
        </div>
    </div>
</section>

<form class="cheat-code" data-js="cheat-code" hidden>
    <label class="cheat-code__label" for="cheat-code-input">Enter cheat code</label>
    <div class="cheat-code__row">
        <input type="text" class="form-control" id="cheat-code-input" data-js="cheat-code-input">
        <button type="submit" class="btn cheat-code__submit">Activate</button>
    </div>
</form>
