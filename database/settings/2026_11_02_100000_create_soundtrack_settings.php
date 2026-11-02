<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('soundtrack.download_url');
        $this->migrator->add('soundtrack.download_size');
        $this->migrator->add('soundtrack.preshow_download_url');
        $this->migrator->add('soundtrack.preshow_download_size');
    }
};
