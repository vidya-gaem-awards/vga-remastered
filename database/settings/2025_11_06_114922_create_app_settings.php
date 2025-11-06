<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('app.voting_start');
        $this->migrator->add('app.voting_end');
        $this->migrator->add('app.stream_time');
        $this->migrator->add('app.default_page', 'home');
        $this->migrator->add('app.award_suggestions', true);
        $this->migrator->add('app.public_pages', []);
        $this->migrator->add('app.read_only', false);
        $this->migrator->add('app.navbar_items', ['config' => ['label' => 'Config', 'order' => 1]]);
        $this->migrator->add('app.lootbox_cost', 0);
    }
};
