<?php

namespace App\Console\Commands;

use App\Models\Autocompleter;
use App\Models\Permission;
use App\Models\User;
use App\Services\SteamService;
use App\Settings\AppSettings;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

class InitialiseDatabaseCommand extends Command
{
    protected $signature = 'app:init-db';

    protected $description = 'Initialises the database with the required data.';

    private SteamService $steam;

    public function handle(
        SteamService $steam,
        AppSettings $settings,
    ): void {
        $this->steam = $steam;

        Model::unguard();

        Artisan::call('migrate', ['--force' => true]);

        $teamMember = User::where('team_member', true)->first();
        if ($teamMember) {
            $response = $this->confirm('A team member account already exists. Are you sure you want to re-initialise the database?', false);
            if (!$response) {
                $this->info('Cancelled.');
                return;
            }
        }

        // Add the default config
        // (Most of the initial settings are now done via a settings migration)
        $settings->setDefaultNavbarItems();

        // Add the special autocompleter
        $autocompleter = Autocompleter::where('slug', 'video-games')->first();
        if (!$autocompleter) {
            Autocompleter::create([
                'slug' => 'video-games',
                'name' => 'Video games in ' . year(),
                'strings' => [],
            ]);
        }

        // Add the standard permissions
        foreach (Permission::STANDARD_PERMISSIONS as $id => $description) {
            Permission::firstOrCreate([
                'id' => $id,
            ], [
                'description' => $description,
            ]);
        }

        // Add the default permission inheritance
        foreach (Permission::STANDARD_PERMISSION_INHERITANCE as $parent => $children) {
            Permission::find($parent)->children()->sync($children);
        }

        // Add available templates
        // @TODO: not yet supported

        // Add the first user account
        do {
            $account = $this->ask('Enter a Steam ID or profile URL to give that user level 5 access');
            $profile = $this->validateSteamAccount($account);
            if (!$profile) {
                $this->error('Invalid account. Please enter a valid Steam ID or profile URL.');
            }
        } while (!$profile);

        $user = User::firstOrCreate([
            'steam_id' => $profile['steamId64'],
        ], [
            'name' => $profile['nickname'],
            'avatar_url' => $profile['avatar'],
            'team_member' => true,
        ]);

        $user->permissions()->syncWithoutDetaching('LEVEL_5');

        $this->info('Setup complete.');
    }

    private function validateSteamAccount(string $account): ?array
    {
        $steamId = $this->steam->stringToSteamId($account);
        if (!$steamId) {
            return null;
        }

        return $this->steam->getProfile($steamId);
    }
}
