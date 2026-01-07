<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Illuminate\Console\Command;

class MigratePermissionsCommand extends Command
{
    protected $signature = 'migrate:permissions';

    protected $description = 'Updates the database with permissions from the class constants';

    public function handle(): void
    {
        Permission::unguard();

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

        $this->info('Permissions updated successfully.');
    }
}
