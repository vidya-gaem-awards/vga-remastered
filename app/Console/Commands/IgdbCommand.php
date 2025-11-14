<?php

namespace App\Console\Commands;

use App\Models\Autocompleter;
use App\Services\IgdbService;
use App\Settings\AppSettings;
use Illuminate\Console\Command;
use RuntimeException;

class IgdbCommand extends Command
{
    protected $signature = 'app:igdb {year?} {--no-clear}';

    protected $description = 'Imports a list of video games from IGDB into the autocomplete list.';

    public function handle(
        AppSettings $settings,
        IgdbService $igdb,
    ): void {
        if ($settings->read_only) {
            throw new RuntimeException('Database is in read-only mode. Read-only mode must be disabled to run this script.');
        }

        $year = $this->argument('year') ?: year();

        $allGames = [];
        $offset = 0;

        do {
            $this->info('Fetching games from IGDB, offset ' . $offset);
            $games = $igdb->getGames((int)$year, $offset);

            $allGames = [...$allGames, ...$games];
            $offset = count($allGames);
        } while (!empty($games));

        if ($year !== year()) {
            $autocompleter = Autocompleter::where('slug', 'video-games-' . $year)->first();
            if (!$autocompleter) {
                $autocompleter = new Autocompleter();
                $autocompleter->slug = 'video-games-' . $year;
                $autocompleter->name = 'Video games in ' . $year;
            }

            if (!$this->option('no-clear')) {
                $strings = [];
            } else {
                $strings = $autocompleter->strings;
            }

            foreach ($igdb->getStringListForAutocompleter($allGames) as $string) {
                $strings[] = $string;
            }

            $autocompleter->strings = $strings;
            $autocompleter->save();
        } else {
            $igdb->addGamesToGameReleaseTable($allGames, !$this->option('no-clear'));
        }

        $this->info('Import complete. ' . count($allGames) . ' games added.');
    }
}
