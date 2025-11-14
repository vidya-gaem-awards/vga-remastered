<?php

namespace App\Console\Commands;

use App\Models\Autocompleter;
use App\Services\WikipediaService;
use App\Settings\AppSettings;
use Illuminate\Console\Command;
use RuntimeException;

class WikipediaCommand extends Command
{
    protected $signature = 'app:wikipedia {year?} {--no-clear}';

    protected $description = 'Imports a list of video games from Wikipedia into the autocomplete list.';

    public function handle(
        AppSettings $settings,
        WikipediaService $wikipedia,
    ): void {
        if ($settings->read_only) {
            throw new RuntimeException('Database is in read-only mode. Read-only mode must be disabled to run this script.');
        }

        $year = $this->argument('year') ?: year();
        $games = $wikipedia->getGames((int)$year);

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

            foreach ($wikipedia->getStringListForAutocompleter($games) as $string) {
                $strings[] = $string;
            }

            $autocompleter->strings = $strings;
            $autocompleter->save();
        } else {
            $wikipedia->addGamesToGameReleaseTable($games, !$this->option('no-clear'));
        }

        foreach ($wikipedia->getOutput() as $message) {
            $this->info($message);
        }

        $this->info('Import complete. ' . count($games) . ' games added.');
    }
}
