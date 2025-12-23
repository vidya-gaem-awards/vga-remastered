<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutocompleterController;
use App\Http\Controllers\AwardAdminController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\ConfigController;
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

Route::get('/', [IndexController::class, 'index'])->name('index');
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::get('/login/return', [AuthController::class, 'loginReturn'])->name('login.return');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

#
# FRONT PAGE
#

Route::get('/home', [IndexController::class, 'home'])->name('home');

#
# NEWS
#

Route::get('/news', [NewsController::class, 'index'])->name('news');

Route::can('news_manage')->group(function () {
    Route::post('/news/add', [NewsController::class, 'add'])->name('news.add');
    Route::post('/news/delete/{news}', [NewsController::class, 'delete'])->name('news.delete');
});

#
# VIDEO GAMES
#

Route::can('conditionally_public|add_video_game')->group(function () {
    Route::get('/vidya', [VideoGamesController::class, 'index'])->name('video-games');
});

Route::can('add_video_game')->group(function () {
    Route::post('/vidya/add', [VideoGamesController::class, 'add'])->name('video-games.add');
    Route::post('/vidya/remove', [VideoGamesController::class, 'remove'])->name('video-games.remove');
    Route::post('/vidya/reload', [VideoGamesController::class, 'reloadWikipedia'])->name('video-games.reload.wikipedia');
    Route::post('/vidya/reload/igdb', [VideoGamesController::class, 'reloadIgdb'])->name('video-games.reload.igdb');
});

#
# CREW
#

Route::can('profile_view')->group(function () {
    Route::get('/team', [PeopleController::class, 'index'])->name('people');
    Route::get('/team/permissions', [PeopleController::class, 'permissions'])->name('people.permissions');
    Route::get('/team/{user}', [PeopleController::class, 'view'])->name('people.view'); # viewPerson
});

Route::can('add_user')->group(function () {
    Route::get('/team/add', [PeopleController::class, 'add'])->name('people.add');
    Route::post('/team/add/search', [PeopleController::class, 'search'])->name('people.add.search');
});

Route::can('profile_edit_details')->group(function () {
    Route::get('/team/{user}/edit', [PeopleController::class, 'edit'])->name('people.edit')->can('profile_edit_details');
    Route::post('/team/{user}/edit', [PeopleController::class, 'post'])->name('people.edit.post')->can('profile_edit_details');
});

#
# AWARDS
#

Route::can('conditionally_public|awards_edit')->group(function () {
    Route::get('/awards', [AwardController::class, 'index'])->name('awards');
    Route::post('/awards', [AwardController::class, 'post'])->name('awards.post');
});

Route::can('awards_feedback')->group(function () {
    Route::get('/awards/manage', [AwardAdminController::class, 'managerList'])->name('awards.manage')->can('awards_feedback');
});

Route::can('awards_edit')->group(function () {
    Route::post('/awards/manage', [AwardAdminController::class, 'managerPost'])->name('awards.manage.post');
    Route::post('/awards/manage/ajax', [AwardAdminController::class, 'managerPostAjax'])->name('awards.manage.post.ajax');
});

Route::can('nominations_view')->group(function () {
    Route::get('/nominees/export', [NomineeController::class, 'exportNominees'])->name('nominees.export');
    Route::get('/nominees/export/user-nominations', [NomineeController::class, 'exportUserNominations'])->name('nominees.export.user-nominations');
    Route::get('/nominees/{award?}', [NomineeController::class, 'index'])->name('nominees.manage');
});

Route::can('nominations_edit')->group(function () {
    Route::post('/nominees/{award}', [NomineeController::class, 'post'])->name('nominees.manage.post');
    Route::post('/nominees/{award}/group/ignore', [NomineeController::class, 'nominationGroupIgnore'])->name('nominations.group.ignore');
    Route::post('/nominees/{award}/group/merge', [NomineeController::class, 'nominationGroupMerge'])->name('nominations.group.merge');
    Route::post('/nominees/{award}/group/demerge', [NomineeController::class, 'nominationGroupDemerge'])->name('nominations.group.demerge');
    Route::post('/nominees/{award}/group/unlink', [NomineeController::class, 'nominationGroupUnlink'])->name('nominations.group.unlink');
});

Route::can('autocompleter_edit')->group(function () {
    Route::get('/awards/autocompleters', [AutocompleterController::class, 'index'])->name('autocompleters');
    Route::post('/awards/autocompleters/ajax', [AutocompleterController::class, 'ajax'])->name('autocompleters.ajax');
    Route::get('/awards/autocompleters/ajax/wikipedia', [AutocompleterController::class, 'wikipedia'])->name('autocompleters.wikipedia');
    Route::get('/awards/autocompleters/ajax/igdb', [AutocompleterController::class, 'igdb'])->name('autocompleters.igdb');
});

#
# TASKS
#

Route::can('tasks_view')->group(function () {
    Route::get('/tasks', [TasksController::class, 'index'])->name('tasks');
    Route::post('/tasks', [TasksController::class, 'post'])->name('tasks.post'); # tasksPost
});

#
# VOTING
#

Route::can('voting_view')->group(function () {
    Route::get('/vote/code', [VotingController::class, 'codeViewer'])->name('voting.code-viewer');
});

Route::can('conditionally_public|voting_view')->group(function () {
    Route::get('/vote/{award?}', [VotingController::class, 'index'])->name('voting');
    Route::post('/vote/{award}', [VotingController::class, 'post'])->name('voting.post');
    Route::get('/vote/v/{code}', [VotingController::class, 'codeEntry'])->name('voting.code-entry');
});

#
# RESULTS
#

Route::can('conditionally_public|voting_results')->group(function () {
    Route::get('/winners', [ResultController::class, 'simple'])->name('winners');
    Route::get('/results', [ResultController::class, 'detailed'])->name('results');
    Route::get('/results/pairwise', [ResultController::class, 'pairwise'])->name('results.pairwise');
});

Route::can('awards_edit')->group(function () {
    Route::post('/winners', [ResultController::class, 'winnerImageUpload'])->name('winners.image-upload');
});

Route::can('voting_results')->group(function () {
    Route::get('/results/{award}', [ResultController::class, 'awardResults'])->name('results.award');
});

#
# REFERRERS
#

Route::can('referrers_view')->group(function () {
    Route::get('/referrers', [ReferrerController::class, 'index'])->name('referrers');
});

#
# AUDIT LOG
#

Route::can('audit_log_view')->group(function () {
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log');
});

#
# LAUNCHER
#

Route::can('conditionally_public|view_unfinished_pages')->group(function () {
    Route::get('/countdown', [LauncherController::class, 'countdown'])->name('countdown');
    Route::get('/stream', [LauncherController::class, 'stream'])->name('stream');
    Route::get('/finished', [LauncherController::class, 'finished'])->name('finished');
});

#
# STATIC PAGES
#

Route::get('/promo', [StaticController::class, 'promo'])->name('promo');
Route::get('/privacy', [StaticController::class, 'privacy'])->name('privacy');
Route::get('/voting/results', [StaticController::class, 'resultRedirect'])->name('result-redirect');
Route::get('/version', [StaticController::class, 'version'])->name('version');

Route::can('conditionally_public|view_unfinished_pages')->group(function () {
    Route::get('/soundtrack', [StaticController::class, 'soundtrack'])->name('soundtrack');
    Route::get('/credits', [StaticController::class, 'credits'])->name('credits');
    Route::get('/trailers', [StaticController::class, 'trailers'])->name('trailers');
});

#
# CONFIG
#

Route::can('edit_config')->group(function () {
    Route::get('/config', [ConfigController::class, 'index'])->name('config');
    Route::post('/config', [ConfigController::class, 'post'])->name('config.post');
    Route::post('/config/purge-cache', [ConfigController::class, 'purgeCache'])->name('config.purge-cache');
    Route::get('/config/cron', [ConfigController::class, 'cron'])->name('config.cron');
});

#
# LOOTBOXES (ADMIN SECTION)
#

Route::can('items_manage')->group(function () {
    Route::get('/lootboxes', [LootboxController::class, 'lootboxRedirect'])->name('lootbox.redirect');
    Route::get('/lootboxes/items', [LootboxController::class, 'items'])->name('lootbox.items');
    Route::post('/lootboxes/items', [LootboxController::class, 'itemPost'])->name('lootbox.items.post');
    Route::post('/lootboxes/items/css', [LootboxController::class, 'itemUpdateCss'])->name('lootbox.items.css');
    Route::post('/lootboxes/items/calculation', [LootboxController::class, 'itemCalculation'])->name('lootbox.items.calculation');
    Route::get('/lootboxes/tiers', [LootboxController::class, 'tiers'])->name('lootbox.tiers');
    Route::post('/lootboxes/tiers', [LootboxController::class, 'tierPost'])->name('lootbox.tiers.post');
    Route::post('/lootboxes/tiers/calculation', [LootboxController::class, 'tierCalculation'])->name('lootbox.tiers.calculation');
    Route::get('/lootboxes/settings', [LootboxController::class, 'settings'])->name('lootbox.settings');
    Route::post('/lootboxes/settings', [LootboxController::class, 'settingsSave'])->name('lootbox.settings.save');
});

#
# INVENTORY (VOTING PAGE)
#

Route::post('/inventory/purchase-lootbox', [InventoryController::class, 'purchaseLootbox'])->name('inventory.purchase-lootbox');
