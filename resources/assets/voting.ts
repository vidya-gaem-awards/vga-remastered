import './bootstrap.js';
import './styles/voting.scss';

// Importing individual widgets doesn't seem to work with Vite, so we have to include all of jQuery UI.
import 'jquery-ui/dist/jquery-ui';
// import 'jquery-ui/ui/widgets/draggable';
// import 'jquery-ui/ui/widgets/droppable';

import $ from 'jquery';
const jQuery = $;

require('bootstrap');
import Sortable from "sortablejs";

import {EditorView, basicSetup, minimalSetup} from "codemirror";
import {css} from "@codemirror/lang-css"

declare var votingEnabled: boolean;
declare var lastVotes: any;
declare var currentAward: string;
declare var postURL: string;
declare var votingStyle: any;
declare const lootboxSettings: any;
declare const lootboxTiers: any;
declare const rewards: Record<string, Item>;
declare const lootboxTest: null | number;
declare const lootboxItemUpdateUrl: string | undefined;

// Elements used by this script are marked with data-js attributes.
// Classes are for styling only and must not be used as JS selectors.

if (lootboxTest) {
    const updateListenerExtension = EditorView.updateListener.of((update) => {
      if (update.docChanged) {
        $('[data-js="lootbox-css-input"]').val(update.state.doc.toString());
        $('[data-js="lootbox-editor-style"]').text(update.state.doc.toString());
      }
    });

    let editor = new EditorView({
        doc: $('[data-js="lootbox-editor-style"]').text().trim(),
        extensions: [
            basicSetup,
            css(),
            EditorView.lineWrapping,
            updateListenerExtension
        ],
        parent: $('[data-js="lootbox-css-editor"]')[0],
    });
}

interface File {
  fullFilename: string;
  id: number;
  relativePath: string;
  url: string;
}

interface Item {
  absoluteDropChance: any;
  additionalFiles: File[];
  buddie: boolean;
  css: boolean;
  cssContents: string | null;
  dropChance: any;
  extra: any;
  id: number;
  image: File;
  music: boolean;
  musicFile: File | null;
  name: string;
  slug: string;
  tier: number;
  year: string;
}

let previousLockExists = lastVotes.length > 1;
let dragCounter: any;
let toddDialog: any;
let showRewardsOnSubmit: boolean = false;
let music: HTMLAudioElement;
let canPlayAudio: boolean;
let usingWinamp: boolean = false;
let inventory;
let lootboxTestSlug: string | null = null;

function onDumbShit(cb) {
    var input = '';
    var key = '38384040373937396665';
    document.addEventListener('keydown', function (e) {
        input += ("" + e.keyCode);
        if (input === key) {
            return cb();
        }
        if (!key.indexOf(input)) return;
        input = ("" + e.keyCode);
    });
}


// OwO what's this?
onDumbShit(function () {
    var audio = new Audio('/ogg/box-earned.ogg');
    audio.play();

    $('[data-js="cheat-code"]').prop('hidden', false);
});

function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min)) + min; //The maximum is exclusive and the minimum is inclusive
}

function reset() {
    localStorageRemove('ignoreRewards');
}

function resetCSS() {
    const $html = $('html');
    if ($html.attr('data-css')) {
        $html.removeClass('reward-' + $html.attr('data-css'));
        $html.removeAttr('data-css');
    }
    $('[data-js="item-action"][data-type="css"]').attr('aria-pressed', 'false');
    $('[data-js="lootbox-css-status"]').addClass('bg-secondary').removeClass('bg-success');
    $('[data-js="lootbox-css-toggle"]').prop('checked', false);
    $('[data-js="lootbox-css-status-label"]').text('Inactive');

    localStorageRemove('activeCSS');
}

function resetMusic() {
    if (music) {
        music.pause();
        music.currentTime = 0;
    }
    if (localStorage.getItem('activeMusic')) {
        localStorageRemove('activeMusic');
    }

    $('[data-js="item-action"][data-type="music"]').attr('aria-pressed', 'false');
}

function resetBuddie() {
    $('[data-js="reward-buddie"]').prop('hidden', true).removeAttr('src');
    if (localStorage.getItem('activeBuddie')) {
        localStorageRemove('activeBuddie');
    }

    $('[data-js="item-action"][data-type="buddie"]').attr('aria-pressed', 'false');
}

function resetRewards() {
    resetCSS();
    resetMusic();
    resetBuddie();
}

function getTextNodesIn(el) {
    return $(el).find(":not(iframe)").addBack().contents().filter(function() {
        return this.nodeType === 3;
    });
}

declare interface String {
    shuffle() : string;
}

function shuffleString(string) {
    var a = string.split(""),
        n = a.length;

    for(var i = n - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var tmp = a[i];
        a[i] = a[j];
        a[j] = tmp;
    }
    return a.join("");
};

function shuffleTextNodes(el) {
    var nodes = getTextNodesIn(el);
    $.each(nodes, function () {
        this.textContent = shuffleString(this.textContent);
    });
}

function localStorageSet(key: string, value: string) {
  if (lootboxTest) {
    return;
  }

  localStorage.setItem(key, value);
}

function localStorageRemove(key: string) {
  if (lootboxTest) {
    return;
  }

  localStorage.removeItem(key);
}

function migrateInventory() {
  let inventoryJson = lootboxTest ? null : localStorage.getItem('inventory');

  if (inventoryJson) {
    inventory = JSON.parse(inventoryJson);
  }

  if (inventory && inventory.version < 4) {
    localStorage.clear();
    inventory = null;
  }

  if (!inventory) {
    inventory = {
      'version': 4,
      'shekels': 0,
      'unlocks': [],
      'unlockKeys': []
    };

    localStorageSet('inventory', JSON.stringify(inventory));
  }
}

declare global {
    interface Window {
        cheat(code: string): void;
    }
}

jQuery(function () {
    // If there's no award currently selected, none of this code is relevant.
    if (!currentAward) {
        return;
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // used with character name from 2023 show
    /*
    updateCharacterNameDisplay();

    $('#character-name-input')
      .on('keyup', handleCharacterNameChange)
      .on('change', handleCharacterNameChange);

    $('#character-form').on('submit', (event: JQuery.Event) => {
      event.preventDefault();
      let name = $('#character-name-input').val();
      if (name.length === 0) {
        name = 'Anonymous';
      }

      localStorageSet('characterName', name);
      updateCharacterNameDisplay();
      $('#character').modal('hide');

      $.post('/rpg/name', { name });
    });
    */

    canPlayAudio = new Audio().canPlayType('audio/ogg') !== '';

    migrateInventory();

    if (lootboxTest) {
        lootboxTestSlug = Object.values(rewards).find(r => r.id === lootboxTest).slug;
        addRewardToInventory(lootboxTestSlug);
        inventory['shekels'] = 1000;
        // for (const reward of Object.keys(rewards)) {
        //     addRewardToInventory(reward);
        // }
    }

    const lootboxCost = lootboxTest ? 1 : lootboxSettings.cost;

    $('[data-js="lootbox-cost"]').text(lootboxCost);

    function updateInventory() {
        localStorageSet('inventory', JSON.stringify(inventory));
        $('[data-js="gold-count"] [data-js="item-name"]').text(inventory['shekels'] + ' gold');

        $('[data-js="buy-lootbox"]').prop('disabled', !(lootboxTest || inventory['shekels'] >= lootboxCost));

        $('[data-js="inventory-item"]').remove();

        for (var i = 0; i < inventory.unlockKeys.length; i++) {
            var reward = rewards[inventory.unlockKeys[i]];
            if (reward === undefined) {
                console.error('Reward is undefined!', i, inventory.unlockKeys[i]);
                continue;
            }
            const template = $('[data-js="inventory-item-template"]')[0] as HTMLTemplateElement;
            var element = $(template.content.firstElementChild.cloneNode(true) as HTMLElement);
            element.attr('data-id', reward.id);
            element.attr('data-short-name', reward.slug);
            element.find('[data-js="item-image"]').attr('src', reward.image.url);
            element.find('[data-js="item-name"]').text(reward.name);
            element.find('[data-js="item-action"]').attr('data-id', reward.slug);

            if (reward.slug === 'nothing') {
                element.find('[data-js="item-action"][data-type="buddie"]').text('Equip');
            }

            if (!reward.music) {
                element.find('[data-js="item-action"][data-type="music"]').remove();
            }

            if (!reward.css) {
                element.find('[data-js="item-action"][data-type="css"]').remove();
            }

            element.find('[data-js="item-action"][data-type="css"]').attr('aria-pressed', String(localStorage.getItem('activeCSS') === reward.slug));
            element.find('[data-js="item-action"][data-type="music"]').attr('aria-pressed', String(localStorage.getItem('activeMusic') === reward.slug));

            if (lootboxTest) {
                element.find('[data-js="item-action"][data-type="buddie"]').attr('aria-pressed', String(lootboxTest === reward.id));
            } else {
                element.find('[data-js="item-action"][data-type="buddie"]').attr('aria-pressed', String(localStorage.getItem('activeBuddie') === reward.slug));
            }

            if (reward.extra) {
                element.attr('data-encrypted', '');
            }

            element.appendTo($('[data-js="inventory-grid"]'));
        }
    }

    updateInventory();

    $('[data-js="gold-count"]').prop('hidden', false);

    if (!lootboxTest && localStorage.getItem('activeCSS')) {
        activateCss(localStorage.getItem('activeCSS'));
    }

    function tryToPlayMusicAutomatically() {
        playMusic(localStorage.getItem('activeMusic'), true).catch(() => {
            let clickHandler = (event: JQuery.ClickEvent) => {
                $(document).off('click', clickHandler);

                const element = $(event.target);
                if (element.closest('[data-js="item-action"][data-type="music"]').length > 0) {
                    return;
                }

                if (localStorage.getItem('activeMusic')) {
                    playMusic(localStorage.getItem('activeMusic'), true);
                }
            }

            $(document).on('click', clickHandler);
        });

        $(`[data-js="item-action"][data-type="music"][data-id="${localStorage.getItem('activeMusic')}"]`).attr('aria-pressed', 'true');
    }

    if (localStorage.getItem('activeMusic') && !localStorage.getItem('muteMusic') && !lootboxTest) {
        tryToPlayMusicAutomatically();
    }

    if (lootboxTest) {
        activateBuddie(lootboxTestSlug);
    } else if (localStorage.getItem('activeBuddie')) {
        activateBuddie(localStorage.getItem('activeBuddie'));
    }

    $('[data-js="inventory-grid"]').on('click', '[data-js="item-action"]', function () {
        var id = $(this).attr('data-id');

        const button = $(this).attr('data-type');
        const alreadyActive = $(this).attr('aria-pressed') === 'true';

        if (id === undefined) {
            return;
        }

        var reward = rewards[id];

        if (id === 'nothing') {
            resetRewards();
        }

        if (id === 'clamburger') {
            shuffleTextNodes($('div'));
        }

        if (reward.extra) {
            const a = document.createElement("a");
            document.body.appendChild(a);
            a.href = reward.extra;
            a.setAttribute('target', '_blank');
            a.setAttribute("download", "");
            a.click();
            document.body.removeChild(a);

            return;
        }

        if (reward.css && button === 'css') {
            if (alreadyActive) {
                resetCSS();
            } else {
                activateCss(id);
                localStorageSet('activeCSS', id);
            }
        }

        if (id !== 'nothing' && button === 'buddie') {
            if (alreadyActive) {
                resetBuddie();
            } else {
                activateBuddie(id);
                localStorageSet('activeBuddie', id);
            }
        }

        if (reward.music && button === 'music') {
            if (alreadyActive) {
                resetMusic();
            } else if (!canPlayAudio) {
                $('[data-js="no-music-modal"]').modal('show');
            } else {
                activateMusic(id);
                localStorageSet('activeMusic', id);
                localStorageRemove('muteMusic');
            }
        }
    });

    $('[data-js="no-music-modal"]').on('show.bs.modal', function () {
        $('body').attr('data-modal', 'no-music');
    }).on('hide.bs.modal', function () {
        $('body').removeAttr('data-modal');
    });

    $('[data-js="mute-music"]').on('click', function () {
        resetMusic();

        $(this).prop('hidden', true);
    });

    $('[data-js="cheat-code"]').on('submit', function (event) {
        event.preventDefault();
        var code = String($('[data-js="cheat-code-input"]').val());

        processCheat(code);
    });

    function processCheat(code) {
        $('[data-js="cheat-code-input"]').val('');
        if (code === 'rosebud') {
            inventory['shekels'] += 1000;
            playCheatMusic(true);
            $('[data-js="cheat-code-input"]').val('rosebud');
        } else if (code === 'motherlode') {
            inventory['shekels'] += 50000;
            playCheatMusic(true);
        } else if (code === 'idkfa') {
            addRewardToInventory('cacodemon');
            playCheatMusic(true);
        } else if (code === 'impulse 101') {
            for (const reward of Object.keys(rewards)) {
                addRewardToInventory(reward);
            }
            playCheatMusic(true);
        } else if (code.substring(0, 4) === 'gib ' && rewards[code.substring(4)]) {
            addRewardToInventory(code.substring(4));
            playCheatMusic(true);
        }
        updateInventory();
    }

    window.cheat = processCheat;

    function playCheatMusic(markAsCheater) {
        var sound = new Audio("/ogg/box-earned.ogg");
        sound.volume = 0.25;
        sound.play();

        if (markAsCheater) {
            localStorageSet('casual', '1');
            // $('#inventory').find('h1').text('Filthy casual detected');
        }
    }

    function playMusic(id, showButton): Promise<void> {
        var reward = rewards[id];
        if (!reward.musicFile) {
            return;
        }

        if (music) {
            music.pause();
            music.currentTime = 0;
        }

        if (showButton) {
            $('[data-js="mute-music"]').prop('hidden', false);
        }

        music = new Audio(reward.musicFile.url);
        music.volume = 0.5;

        return music.play();
    }

    function activateBuddie(slug: string) {
        $('[data-js="reward-buddie"]').attr('src', rewards[slug].image.url).prop('hidden', false);

        $('[data-js="item-action"][data-type="buddie"]').attr('aria-pressed', 'false');
        $(`[data-js="item-action"][data-type="buddie"][data-id="${slug}"]`).attr('aria-pressed', 'true');
    }

    function activateMusic(slug: string) {
        playMusic(slug, true).then(() => {
            $('[data-js="item-action"][data-type="music"]').attr('aria-pressed', 'false');
            $(`[data-js="item-action"][data-type="music"][data-id="${slug}"]`).attr('aria-pressed', 'true');
        }).catch(e => {
            alert('Failed to play music: ' + e.name);
            console.log('Failed to play music', e.name);
        });
    }

    function activateCss(slug: string) {
        const $html = $('html');
        if ($html.attr('data-css')) {
            $html.removeClass('reward-' + $html.attr('data-css'));
        }
        $html.attr('data-css', slug);
        $html.addClass('reward-' + slug);

        $('[data-js="item-action"][data-type="css"]').attr('aria-pressed', 'false');
        $(`[data-js="item-action"][data-type="css"][data-id="${slug}"]`).attr('aria-pressed', 'true');

        if (slug === lootboxTestSlug) {
            $('[data-js="lootbox-css-status"]').addClass('bg-success').removeClass('bg-secondary');
            $('[data-js="lootbox-css-toggle"]').prop('checked', true);
            $('[data-js="lootbox-css-status-label"]').text('Active');
        }
    }

    $('[data-js="buy-lootbox"]').on('click', function () {
        if (inventory['shekels'] < lootboxCost) {
            return;
        }

        openLootboxRewards(true);
        inventory['shekels'] -= lootboxCost;
        updateInventory();
    });

    if (localStorage.getItem('ignoreRewards') && !lootboxTest) {
        $('[data-js="restore-drops"]').prop('disabled', false);
    }

    if ((!localStorage.getItem('ignoreRewards') && lastVotes.length === 0) || lootboxTest) {
        showRewardsOnSubmit = true;
    }

    $('[data-js="unequip-all"]').on('click', function (event) {
        resetRewards();
    });

    $('[data-js="restore-drops"]').on('click', function (event) {
        event.preventDefault();
        $(this).prop('disabled', true);
        localStorageRemove('ignoreRewards');
        if (lastVotes.length === 0) {
            showRewardsOnSubmit = true;
        }
        alert('Your drops have been restored.');
    });

    var lootboxSound = new Audio("/ogg/box-opened.ogg");

    var showNewLoot = function showNewLoot(i) {
        var lootbox = $('[data-js="loot-slot"]').eq(i);
        lootbox.attr('data-animation', 'in');
        lootbox.find('[data-js="loot-sprite"]').prop('hidden', true);

        const $item = lootbox.find('[data-js="loot-item"]');
        $item.prop('hidden', false);

        var title = $item.find('[data-js="item-name"]');
        var image = $item.find('[data-js="item-image"]');

        var reward = pendingItems.pop();

        $item.removeAttr('data-encrypted');

        if (reward.type === 'item') {
            var item = rewards[reward.item.slug];

            if (item.extra) {
                $item.attr('data-encrypted', '');
            }

            addRewardToInventory(item.slug);
            image.attr('src', item.image.url);
            title.text(item.name);
        } else if (reward.type === 'shekels') {
            var shekelCount = reward.amount;
            inventory['shekels'] += shekelCount;
            image.attr('src', '/2023images/gold-bars.png');
            title.text(shekelCount + " gold");
        }

        updateInventory();
    };

    function addRewardToInventory(choice) {
        var reward = rewards[choice];
        if ($.inArray(reward.slug, inventory['unlockKeys']) === -1) {
            inventory['unlocks'][reward.slug] = 1;
            inventory['unlockKeys'].push(reward.slug);
        } else {
            inventory['unlocks'][reward.slug]++;
        }
    }

    $('[data-js="unbox"]').on('click', function () {
        if (lootboxRevealAudio) {
            lootboxRevealAudio.pause();
            lootboxRevealAudio = null;
        }
        lootboxSound.volume = 0.25;
        lootboxSound.play();

        $('[data-js="loot-slot"]').attr('data-animation', 'out');

        $(this).prop('hidden', true);

        setTimeout(function () {
            $('[data-js="loot-slot"]').find('img').removeAttr('src');
        }, 1100);

        for (var i = 0; i < 3; i++) {
            setTimeout(showNewLoot, 2000 + i * 300, i);
        }

        setTimeout(function () {
            $('[data-js="loot-collect"]').prop('hidden', false);
        }, 3600);
    });

    $('[data-js="never-show-again"]').on('click', function () {
        localStorageSet('ignoreRewards', 'true');
        $('[data-js="loot-modal"]').modal('hide');
        $('[data-js="restore-drops"]').prop('disabled', false);
    });

    var pendingItems = [];

    let lootboxRevealAudio: HTMLAudioElement;

    function openLootboxRewards(force) {
        if (!force && !showRewardsOnSubmit) {
            return;
        }

        $('[data-js="unbox"]').prop('hidden', false);
        $('[data-js="loot-collect"]').prop('hidden', true);
        $('[data-js="loot-slot"]').removeAttr('data-animation');
        $('[data-js="loot-item"]').prop('hidden', true);
        $('[data-js="loot-sprite"]').prop('hidden', false);

        var lootboxes = [
          ['mario.webp', 0, 2],
          ['dopefish.webp', 15, 2],
          ['ecco.gif', 10, 2],
          ['cavestory.webp', 20, 2],
          ['darkstalkers.webp', -20, 1],
          ['disgaea.webp', 20, 1],
          ['yugi.webp', 0, 1],
          ['taiko.webp', -38, 0.5],
          ['megaman.webp', 15, 1],
          ['pokemon.webp', 10, 1],
          ['darius.webp', -38, 1],
          ['superblackbass.webp', 28, 2],
          ['kirby.webp', 0, 2],
          ['ffl2.webp', 0, 2],
          ['coalecanth.webp', 0, 1],
          ['zelda.webp', -28, 0.5],
        ];

        $('[data-js="loot-sprite"]').each(function () {
            let lootbox = lootboxes[getRandomInt(0, lootboxes.length)];
            $(this).attr('src', '/2025images/lootbox_sprites/' + lootbox[0]);
            $(this).css('translate', lootbox[1]);
            $(this).css('scale', lootbox[2]);
        });

        var prompts = [
            `"Whoa, a big one!"`,
            `Something's on the line`,
            `A bite!`,
            `"Nothing like a good day on the water" *siiiip*`,
            `"They fear me."`
          ];
        $('[data-js="loot-flavor"]').text(prompts[getRandomInt(0, prompts.length)]);

        $('[data-js="loot-modal"]').modal('show');

        lootboxRevealAudio = new Audio('/ogg/box-earned.ogg');
        lootboxRevealAudio.volume = 0.10;
        lootboxRevealAudio.play();

        showRewardsOnSubmit = false;

        $('[data-js="unbox"]').prop('disabled', true);

        const url = lootboxTest ? '/inventory/purchase-lootbox?test=1' : '/inventory/purchase-lootbox';

        $.post(url).then(data => {
            pendingItems = data.rewards;
            for (const reward of pendingItems) {
                if (reward.type !== 'item') {
                    continue;
                }
                if (!rewards[reward.item.slug]) {
                    rewards[reward.item.slug] = reward.item;
                }
            }
        }).always(() => {
            $('[data-js="unbox"]').prop('disabled', false);
        });
    }

    var votesChanged = false;

    var resetButton = $('[data-js="vote-reset"]');
    var submitButton = $('[data-js="vote-submit"]');

    // Only used in the "drag from left to right" (legacy) layout
    var legacyBallot = $('[data-js="legacy-ballot"]');
    var nomineeColumn = $('[data-js="legacy-pool"]');
    var voteColumn = $('[data-js="legacy-votes"]');
    var voteColumnBoxes = voteColumn.find('[data-js="legacy-slot"]');
    var dragged;
    var draggedFrom;

    // Only used in the "drag from top to bottom" layout
    var topArea = $('[data-js="pool"]');
    var bottomArea = $('[data-js="ballot"]');
    var bottomAreaContainer = $('[data-js="ballot-inner"]');

    function nomineeCard(id): JQuery<HTMLElement> {
        return $('[data-js="nominee"]').filter(`[data-nominee="${id}"]`);
    }

    function legacyNominee(id): JQuery<HTMLElement> {
        return $('[data-js="legacy-nominee"]').filter(`[data-nominee="${id}"]`);
    }

    function legacyVoteSlot(slot): JQuery<HTMLElement> {
        return voteColumnBoxes.filter(`[data-slot="${slot}"]`);
    }

    var sortableOptions = {
        group: {
            name: 'omega',
            pull: false
        },
        draggable: '[data-js="nominee"]',
        handle: '[data-js="drag-handle"]',
        animation: 0,
        dataIdAttr: 'data-order',
        onStart: function (event) {
            bottomArea.attr('data-dragging', '');
            $(event.item).attr('data-dragging', '');
        },
        onEnd: function (event) {
            bottomArea.removeAttr('data-dragging');
            $(event.item).removeAttr('data-dragging');
            updateNumbers();
            unlockVotes();
        },
        scroll: true,
        scrollSensitivity: 100,
        scrollSpeed: 20
    };

    if (votingEnabled) {
        if (topArea.length > 0) {
            new Sortable(bottomArea[0], sortableOptions);
        }

        $('[data-js="nominee"]').on('click', function (event) {

            let target = event.originalEvent.target as HTMLElement;

            if (target.localName === 'a') {
                return;
            }

            if (!$.contains(topArea[0], this)) {
                $(this).detach().appendTo(topArea);
            } else {
                $(this).detach().appendTo(bottomArea);
            }

            updateNumbers();
            unlockVotes();
        });
    }

    // Legacy
    voteColumnBoxes.each(function () {
        $(this).html("");
    });

    nomineeColumn.height(voteColumn.height());

    $('[data-js="legacy-nominee"]').draggable({
        containment: '[data-js="legacy-ballot"]',
        distance: 20,
        opacity: 0.75,
        zIndex: 100,
        revert: "invalid",
        revertDuration: 200,
    })
        //when you start dragging, it puts the elements in variables
        .bind('dragstart', function (event) {
            dragged = $(this);
            draggedFrom = $(this).parent();
        });

    //be able to drop nominees in voteBoxes
    voteColumnBoxes.droppable({
        drop: function (event, ui) {
            var dropped = ui.draggable;
            var droppedOn = $(this);

            //if you're dropping the nominee exactly where you took it from, it cancels the drop
            if (droppedOn.attr("data-slot") === draggedFrom.attr("data-slot")) {
                $(dragged).draggable("option", "revert", true);
                return
            }

            unlockVotes();

            //put the content of the box you're voting over in a variable (.detach keeps the draggable)
            var stuffDeleted = droppedOn.contents().detach();

            //add your dragged vote to the box
            $(dropped).detach().css({top: 0, left: 0}).appendTo(droppedOn);

            //put what you deleted back where your vote came from
            draggedFrom.append(stuffDeleted);

            updateNumbers();
        }
    });

    //be able to drop nominees back in the original container
    nomineeColumn.find('[data-js="legacy-slot"]').droppable({
        drop: function (event, ui) {
            var dropped = ui.draggable;
            var droppedOn = $(this);

            //if you're dropping the nominee exactly where you took it from, it cancels the drop
            if (droppedOn.attr("data-slot") === draggedFrom.attr("data-slot")) {
                $(dragged).draggable("option", "revert", true);
                return;
            }

            unlockVotes();

            //add your dragged vote to the container
            $(dropped).detach().css({top: 0, left: 0}).appendTo(droppedOn);

            //empty the number
            dropped.find('[data-js="nominee-rank"]').text("");
        }
    });

    moveNomineesBack(false);

    // Update interface to indicate that the votes have been succesfully submitted and changed.
    function lockVotes() {
        bottomAreaContainer.add(legacyBallot).attr('data-locked', '');
        $('[data-js="ballot-title"]').text("Your Votes");
        submitButton.prop('hidden', true).attr('title', 'Saved!');

        previousLockExists = true;
        votesChanged = false;
    }

    // Update interface to indicate that there have been changes since the last submitted vote.
    function unlockVotes() {
        bottomAreaContainer.add(legacyBallot).removeAttr('data-locked');
        $('[data-js="ballot-title"]').text("Your Votes (unsaved)");
        submitButton.prop('hidden', false).attr('title', 'Submit Votes');
        votesChanged = true;
    }

    function getOrdinal(number) {
        return number + (['st', 'nd', 'rd'][((number+90) % 100 - 10) % 10 - 1] || 'th');
    }

    // Syncs each nominee card's data-zone with the area it's in, and updates the preference numbers.
    function updateNumbers() {
        topArea.find('[data-js="nominee"]').attr('data-zone', 'pool');

        const ballotCards = bottomArea.find('[data-js="nominee"]');
        ballotCards.each(function (index) {
            $(this).attr('data-zone', 'ballot');
            $(this).find('[data-js="nominee-rank"]').text('#' + (index + 1));
        });

        $('[data-js="next-preference"]').text(getOrdinal(ballotCards.length + 1));

        voteColumnBoxes.each(function () {
            $(this).find('[data-js="nominee-rank"]').text("#" + $(this).attr("data-slot"));
        });
    }

    // Resets all nominees back to the user's last submitted vote.
    // If there is no last submitted vote, or if resetAll is set to true, will move all nominees back into the top pane.
    function moveNomineesBack(resetAll) {
        if (votingStyle === 'legacy') {
            var voteBoxes = nomineeColumn.find('[data-js="legacy-slot"]');
            if (resetAll || lastVotes.length === 0) {
                var allNominees = [];
                $('[data-js="legacy-nominee"]').each(function () {
                    allNominees.push($(this).detach());
                });

                for (i = 0; i < allNominees.length; i++) {
                    $(voteBoxes[i]).append(allNominees[i]);
                }
            } else {
                var haveVotedFor = [];
                for (i = 1; i < lastVotes.length; i++) {
                    haveVotedFor.push(legacyNominee(lastVotes[i]).detach());
                }
                var theRest = $('[data-js="legacy-nominee"]').detach();
                for (i = 0; i < lastVotes.length; i++) {
                    legacyVoteSlot(i + 1).append(haveVotedFor[i]);
                }
                for (i = 0; i < theRest.length; i++) {
                    $(voteBoxes[i + lastVotes.length - 1]).append(theRest[i]);
                }
            }
            resetLeftSide();

        } else {
            bottomArea.find('[data-js="nominee"]').each(function () {
                var element = $(this);
                element.detach().appendTo(topArea);
            });

            if (!resetAll) {
                for (var i = 1; i < lastVotes.length; i++) {
                    var element = nomineeCard(lastVotes[i]);
                    element.detach().appendTo(bottomArea);
                }
            }
            resetTopArea();
        }

        updateNumbers();

        if (previousLockExists) {
            lockVotes();
        }
    }

    function resetLeftSide() {
        var muhNominees = nomineeColumn.find('[data-js="legacy-nominee"]').detach();

        muhNominees = ($(muhNominees) as any).sort(function (a, b) {
            var contentA = parseInt($(a).attr('data-order'));
            var contentB = parseInt($(b).attr('data-order'));
            return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
        });

        var voteBoxes = nomineeColumn.find('[data-js="legacy-slot"]');

        for (var i = 0; i < muhNominees.length; i++) {
            $(voteBoxes[i]).append(muhNominees[i]);
        }
    }

    function sortRightSide() {
        //variable that I'm using to know which voteBox the loop is at
        var currentVoteBox = 0;
        var listVoteBox = [];

        //pass through every voteBox, empty them while placing the vote in the array, ignoring the empty voteBoxes
        voteColumnBoxes.each(function () {
            currentVoteBox++;

            if ($(this).find('[data-js="legacy-nominee"]').length > 0) {
                listVoteBox.push($(this).contents().detach());
            }
        });

        //put the votes back in the voteBoxes
        for (var i = 0; i < currentVoteBox; i++) {
            if (listVoteBox[i]) { //if it exists
                listVoteBox[i].appendTo(legacyVoteSlot(i + 1));
            }
        }
    }

    // Resets the top nominee pane to its original configuration.
    // Only used in the "drag and drop" layout.
    function resetTopArea() {
        var nominees = topArea.find('[data-js="nominee"]').detach();

        nominees = ($(nominees) as any).sort(function (a, b) {
            var contentA = parseInt($(a).attr('data-order'));
            var contentB = parseInt($(b).attr('data-order'));
            return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
        });

        topArea.append(nominees);
    }

    // Reset Votes
    resetButton.on('click', function () {
        moveNomineesBack(true);
        resetTopArea();
        resetLeftSide();
        unlockVotes();

        const voteAreas = topArea.add(bottomArea);
        voteAreas.attr('data-flash', '');
        setTimeout(function () {
            voteAreas.removeAttr('data-flash');
        }, 200);
    });

    function markCurrentAwardVoted() {
        $('[data-js="award-link"]').filter(`[data-award="${currentAward}"]`).attr('data-voted', '');
    }

    // Submit Votes
    submitButton.on('click', function () {
        if (!votesChanged) {
            return;
        }

        lockVotes();
        if (votingStyle === 'legacy') {
            sortRightSide();
            updateNumbers();
        }

        var preferences = [null];

        if (votingStyle === 'legacy') {
            voteColumnBoxes.each(function () {
                var slot = $(this).attr("data-slot");
                var nomineeID = $(this).find('[data-js="legacy-nominee"]').attr("data-nominee");
                if (nomineeID !== undefined) {
                    preferences[slot] = nomineeID;
                }
            });
        } else {
            bottomArea.find('[data-js="nominee"]').each(function (index) {
                preferences[index + 1] = $(this).attr('data-nominee');
            });
        }

        lastVotes = preferences;

        if (lootboxTest) {
          markCurrentAwardVoted();
        } else {
          $.post(postURL, {preferences: preferences}, function (data) {
            if (data.error) {
              alert("An error occurred:\n" + data.error + "\nYour vote has not been saved.");
            } else {
              markCurrentAwardVoted();
            }
          }, 'json');
        }

        openLootboxRewards(false);
    });

    if (lootboxTest) {
      const lootboxEditor = $('[data-js="lootbox-editor"]');

      $('[data-js="lootbox-editor-opacity"]').on('change', function () {
        lootboxEditor.css('opacity', $(this).val());
      });

      $('[data-js="lootbox-editor-details"]').on('show.bs.collapse', function () {
        $('[data-js="lootbox-editor-details-toggle"]').find('i').removeClass('fa-chevrons-down').addClass('fa-chevrons-up');
      });

      $('[data-js="lootbox-editor-details"]').on('hide.bs.collapse', function () {
        $('[data-js="lootbox-editor-details-toggle"]').find('i').removeClass('fa-chevrons-up').addClass('fa-chevrons-down');
      });

      lootboxEditor.draggable({
          handle: '[data-js="lootbox-editor-handle"]',
      });

      $('[data-js="lootbox-css-toggle"]').on('change', function () {
          if (!$(this).is(':checked')) {
              resetCSS();
          } else {
              activateCss(lootboxTestSlug);
          }
      });

      let currentlySubmitting = false;

      $('[data-js="lootbox-editor-form"]').on('submit', function (event) {
        event.preventDefault();

        if (currentlySubmitting) {
          return;
        }
        currentlySubmitting = true;

        // Show the "please wait" message and disable the submit button
        lootboxEditor.find("button[type=submit]").prop("disabled", true);
        // $("#dialog-edit-error").parent().slideUp()

        var formData = new FormData(this);
        $.ajax({
          url: lootboxItemUpdateUrl,
          type: 'POST',
          data: formData,
          contentType: false,
          processData: false
        }).done(function (response) {
          if (response.success) {
            $('[data-js="lootbox-editor-saved"]').prop('hidden', false).stop(true, true).show().fadeOut(5000);
          } else {
            alert('Error: ' + response.error);
          }
        }, "json").always(() => {
          lootboxEditor.find("button[type=submit]").prop("disabled", false);
          currentlySubmitting = false;
        });
      })

      document.addEventListener('keydown', function (e) {
        if (e.code === 'Escape') {
          if (e.target instanceof HTMLElement) {
            e.target.blur();
            return;
          }
        }

        if ((e.target instanceof HTMLInputElement || e.target instanceof HTMLTextAreaElement || $(e.target).attr('role') === 'textbox') && !e.ctrlKey) {
          return;
        }

        // Toggle buddie
        if (e.code === 'KeyB') {
          if ($('[data-js="reward-buddie"]').attr('src')) {
            resetBuddie();
          } else {
            activateBuddie(lootboxTestSlug);
          }
        }

        // Toggle music
        if (e.code === 'KeyM') {
          if (music && !music.paused) {
            resetMusic();
          } else if (rewards[lootboxTestSlug].music) {
            activateMusic(lootboxTestSlug);
          }
          return;
        }

        // Toggle CSS
        if (e.code === 'KeyC') {
          if ($('html').attr('data-css')) {
            resetCSS();
          } else {
            activateCss(lootboxTestSlug);
          }
          return;
        }

        // Toggle editor
        if (e.code === 'KeyE') {
            lootboxEditor.prop('hidden', !lootboxEditor.prop('hidden'));
            return;
        }

        // Reset all
        if (e.code === 'KeyR') {
          resetRewards();
          lootboxEditor
              .prop('hidden', false)
              .css('opacity', 1)
              .css('left', '5px')
              .css('top', '5px');
          return;
        }
      }, false);
    }

    // apply random text for the award title decoration
    var titleDeco = [
        "You're lucky to miss the",
        'Drink up before the',
        'I was beaten behind the',
        'Who the fuck asked for the',
        'Feel the cromulence,',
        'What a beautiful',
        '▲&nbsp;&nbsp;<br/>▲▲',
        'Jump the shark with the',
        '/v/GA regrettably presents',
    ];
    $('[data-js="title-tagline"]').html(titleDeco[getRandomInt(0, titleDeco.length)]);

    // prompt user if they leave without saving
    {
        window.addEventListener('beforeunload', (e: BeforeUnloadEvent) => {
            if(votesChanged){
                e.preventDefault();
                e.returnValue = '';
            }
        });
        
        // The leave modal is only rendered while voting is open.
        const leaveModal = document.querySelector<HTMLElement>('[data-js="leave-modal"]');
        const continueEl = leaveModal?.querySelector('[data-js="leave-continue"]') as HTMLAnchorElement;
        const cancelEl = leaveModal?.querySelector('[data-js="leave-cancel"]') as HTMLElement;

        function hideLeaveDialog() {
            leaveModal.hidden = true;
        }

        if (leaveModal) {
            cancelEl.onclick = hideLeaveDialog;
            leaveModal.onclick = hideLeaveDialog;
        }

        // intercept page leaves via anchor tags, show confirmation modal
        document.addEventListener('click', (e: MouseEvent) => {
            if(!leaveModal) return;
            if(!votesChanged) return;

            const target = e.target as Element; 
            if(target == continueEl) { // user has manually requested to leave
                votesChanged = false;
                return;
            }

            // show leave modal
            const link = target.closest('a');
            if (link && link.href) {
                continueEl.href = link.href;
                leaveModal.hidden = false;
                e.preventDefault(); 
            }
        });
    }
});
