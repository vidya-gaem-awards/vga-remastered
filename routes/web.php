<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutocompleterController;
use App\Http\Controllers\AwardAdminController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\EditorController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LauncherController;
use App\Http\Controllers\LootboxController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NomineeController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\ReferrerController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\StaticController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\VideoGamesController;
use App\Http\Controllers\VotingController;
use Illuminate\Support\Facades\Route;

#
# INTERNAL SYSTEM STUFF
#

Route::get('/', [StaticController::class, 'index'])->name('index');
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::get('/login/return', [AuthController::class, 'loginReturn'])->name('login.return');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

#
# FRONT PAGE
#

Route::get('/home', [IndexController::class, 'index'])->name('home');
Route::get('/promo', [IndexController::class, 'promo'])->name('promo');

#
# NEWS
#

Route::get('/news', [NewsController::class, 'index'])->name('news');
Route::post('/news/add', [NewsController::class, 'add'])->name('news.add'); # newsAdd
Route::delete('/news/delete/{id}', [NewsController::class, 'delete'])->name('news.delete') # newsDelete
    ->where('id', '[0-9]+');

#
# VIDEO GAMES
#

Route::get('/vidya', [VideoGamesController::class, 'index'])->name('video-games'); # videoGames
Route::post('/vidya/add', [VideoGamesController::class, 'add'])->name('video-games.add'); # addVideoGame
Route::post('/vidya/remove', [VideoGamesController::class, 'remove'])->name('video-games.remove'); # removeVideoGame
Route::post('/vidya/reload', [VideoGamesController::class, 'reloadWikipedia'])->name('video-games.reload.wikipedia'); # reloadVideoGamesWikipedia
Route::post('/vidya/reload/igdb', [VideoGamesController::class, 'reloadIgdb'])->name('video-games.reload.igdb'); # reloadVideoGamesIgdb

#
# CREW
#

Route::get('/team', [PeopleController::class, 'index'])->name('people');
Route::get('/team/permissions', [PeopleController::class, 'permissions'])->name('people.permissions'); # permissions
Route::get('/team/add', [PeopleController::class, 'add'])->name('people.add'); # addPerson
Route::post('/team/add/search', [PeopleController::class, 'search'])->name('people.add.search'); #userSearch
Route::get('/team/{id}', [PeopleController::class, 'view'])->name('people.view') # viewPerson
    ->where('id', '[0-9]+');
Route::get('/team/{id}/edit', [PeopleController::class, 'edit'])->name('people.edit') # editPerson
    ->where('id', '[0-9]+');
Route::post('/team/{id}/edit', [PeopleController::class, 'post'])->name('people.edit.post') # editPersonPost
    ->where('id', '[0-9]+');

#
# AWARDS
#

Route::get('/awards', [AwardController::class, 'index'])->name('awards');
Route::post('/awards', [AwardController::class, 'post'])->name('awards.post'); # awardFrontendPost

Route::get('/awards/manage', [AwardAdminController::class, 'managerList'])->name('awards.manage'); # awardManager
Route::post('/awards/manage', [AwardAdminController::class, 'managerPost'])->name('awards.manage.post'); # awardManagerPost
Route::post('/awards/manage/ajax', [AwardAdminController::class, 'managerPostAjax'])->name('awards.manage.post.ajax'); # awardManagerPostAjax

Route::get('/nominees/export', [NomineeController::class, 'exportNominees'])->name('nominees.export'); # nomineeExport
Route::get('/nominees/export/user-nominations', [NomineeController::class, 'exportUserNominations'])->name('nominees.export.user-nominations'); #nomineeUserNominationExport
Route::get('/nominees/{id?}', [NomineeController::class, 'index'])->name('nominees.manage'); # nomineeManager
Route::post('/nominees/{id}', [NomineeController::class, 'post'])->name('nominees.manage.post'); # nomineePost
Route::post('/nominees/{id}/group/ignore', [NomineeController::class, 'nominationGroupIgnore'])->name('nominations.group.ignore'); # nominationsIgnoreGroup
Route::post('/nominees/{id}/group/merge', [NomineeController::class, 'nominationGroupMerge'])->name('nominations.group.merge'); # nominationsMergeGroup
Route::post('/nominees/{id}/group/demerge', [NomineeController::class, 'nominationGroupDemerge'])->name('nominations.group.demerge'); # nominationsDemergeGroup
Route::post('/nominees/{id}/group/unlink', [NomineeController::class, 'nominationGroupUnlink'])->name('nominations.group.unlink'); # nominationsUnlinkGroup

Route::get('/awards/autocompleters', [AutocompleterController::class, 'index'])->name('autocompleters');
Route::post('/awards/autocompleters/ajax', [AutocompleterController::class, 'ajax'])->name('autocompleters.ajax'); # autocompleterAjax
Route::get('/awards/autocompleters/ajax/wikipedia', [AutocompleterController::class, 'wikipedia'])->name('autocompleters.wikipedia'); # autocompleterWikipedia
Route::get('/awards/autocompleters/ajax/igdb', [AutocompleterController::class, 'igdb'])->name('autocompleters.igdb'); # autocompleterIgdb

#
# TASKS
#

Route::get('/tasks', [TasksController::class, 'index'])->name('tasks');
Route::get('/tasks/check-images', [TasksController::class, 'imageCheck'])->name('tasks.check-images'); # tasksImageCheck
Route::post('/tasks', [TasksController::class, 'post'])->name('tasks.post'); # tasksPost

#
# VOTING
#

Route::get('/vote/code', [VotingController::class, 'codeViewer'])->name('voting.code-viewer'); # viewVotingCode
Route::get('/vote/{id?}', [VotingController::class, 'index'])->name('voting');
Route::post('/vote/{id}', [VotingController::class, 'post'])->name('voting.post'); # votingPost
Route::get('/vote/v/{code}', [VotingController::class, 'codeEntry'])->name('voting.code-entry'); # voteWithCode

#
# RESULTS
#

Route::get('/winners', [ResultController::class, 'simple'])->name('winners');
Route::post('/winners', [ResultController::class, 'winnerImageUpload'])->name('winners.image-upload'); # winnerImageUpload
Route::get('/results', [ResultController::class, 'detailed'])->name('results');
Route::get('/results/pairwise', [ResultController::class, 'pairwise'])->name('results.pairwise'); # pairwiseResults
Route::get('/results/{id}', [ResultController::class, 'awardResults'])->name('results.award'); # resultsAward

#
# REFERRERS
#

Route::get('/referrers', [ReferrerController::class, 'index'])->name('referrers');

#
# AUDIT LOG
#

Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log'); # auditLog

#
# LAUNCHER
#

Route::get('/countdown', [LauncherController::class, 'countdown'])->name('countdown');
Route::get('/stream', [LauncherController::class, 'stream'])->name('stream');
Route::get('/finished', [LauncherController::class, 'finished'])->name('finished');

#
# STATIC PAGES
#

Route::get('/privacy', [StaticController::class, 'privacy'])->name('privacy');
Route::get('/videos', [StaticController::class, 'videos'])->name('videos');
Route::get('/soundtrack', [StaticController::class, 'soundtrack'])->name('soundtrack');
Route::get('/credits', [StaticController::class, 'credits'])->name('credits');
Route::get('/trailers', [StaticController::class, 'trailers'])->name('trailers');
Route::get('/voting/results', [StaticController::class, 'resultRedirect'])->name('result-redirect'); # resultRedirect

#
# CONFIG
#

Route::can('edit_config')->group(function () {
    Route::get('/config', [ConfigController::class, 'index'])->name('config');
    Route::post('/config', [ConfigController::class, 'post'])->name('config.post'); # configPost
    Route::post('/config/purge-cache', [ConfigController::class, 'purgeCache'])->name('config.purge-cache'); # configPurgeCache
    Route::get('/config/cron', [ConfigController::class, 'cron'])->name('config.cron'); # cron
//    Route::post('/config/cron', [ConfigController::class, 'cronPost'])->name('config.cron.post'); # cronPost
});

#
# PAGE EDITOR
#

Route::get('/config/editor', [EditorController::class, 'index'])->name('editor');
Route::post('/config/editor', [EditorController::class, 'post'])->name('editor.post'); # editorPost

#
# LOOTBOXES (ADMIN SECTION)
#

Route::get('/lootboxes', [LootboxController::class, 'lootboxRedirect'])->name('lootbox.redirect'); # lootboxRedirect
Route::get('/lootboxes/items', [LootboxController::class, 'items'])->name('lootbox.items'); # lootboxItems
Route::post('/lootboxes/items', [LootboxController::class, 'itemPost'])->name('lootbox.items.post'); # lootboxItemPost
Route::post('/lootboxes/items/css', [LootboxController::class, 'itemUpdateCss'])->name('lootbox.items.css'); # lootboxItemUpdateCss
Route::post('/lootboxes/items/calculation', [LootboxController::class, 'itemCalculation'])->name('lootbox.items.calculation'); # lootboxItemCalculation
Route::get('/lootboxes/tiers', [LootboxController::class, 'tiers'])->name('lootbox.tiers'); # lootboxTiers
Route::post('/lootboxes/tiers', [LootboxController::class, 'tierPost'])->name('lootbox.tiers.post'); # lootboxTierPost
Route::post('/lootboxes/tiers/calculation', [LootboxController::class, 'tierCalculation'])->name('lootbox.tiers.calculation'); # lootboxTierCalculation
Route::get('/lootboxes/settings', [LootboxController::class, 'settings'])->name('lootbox.settings'); # lootboxSettings
Route::post('/lootboxes/settings', [LootboxController::class, 'settingsSave'])->name('lootbox.settings.save'); # lootboxSettingsSave

#
# INVENTORY (VOTING PAGE)
#

Route::post('/inventory/purchase-lootbox', [InventoryController::class, 'purchaseLootbox'])->name('inventory.purchase-lootbox');
