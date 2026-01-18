<?php

use App\Settings\AppSettings;
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:results')->everyFifteenMinutes()->when(function () {
    $appSettings = resolve(AppSettings::class);

    $votingOpen = $appSettings->isVotingOpen();
    $votingJustClosed = $appSettings->hasVotingClosed() && $appSettings->voting_end->isAfter('-1 hour');

    return $votingOpen || $votingJustClosed;
})->withoutOverlapping();
