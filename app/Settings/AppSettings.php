<?php

namespace App\Settings;

use Carbon\CarbonImmutable;
use Spatie\LaravelSettings\Settings;

class AppSettings extends Settings
{
    public ?CarbonImmutable $voting_start;
    public ?CarbonImmutable $voting_end;
    public ?CarbonImmutable $stream_time;
    public string $default_page;
    public bool $award_suggestions;
    public array $public_pages;
    public bool $read_only;
    public array $navbar_items;
    public int $lootbox_cost;

    public static function group(): string
    {
        return 'app';
    }
}
