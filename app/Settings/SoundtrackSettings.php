<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SoundtrackSettings extends Settings
{
    public ?string $download_url;
    public ?string $download_size;
    public ?string $preshow_download_url;
    public ?string $preshow_download_size;

    public static function group(): string
    {
        return 'soundtrack';
    }
}
