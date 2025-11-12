<?php

namespace App\Settings;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
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

    public const array ALLOWED_DEFAULT_PAGES = [
        'home'      => 'Standard landing page',
        'awards'    => 'Awards and Nominations',
        'voting'    => 'Voting page',
        'countdown' => 'Stream countdown',
        'stream'    => 'Stream page',
        'finished'  => 'Post-stream "thank you" page',
        'promo'     => 'Special promo page',
    ];

    public static function group(): string
    {
        return 'app';
    }

    public function isPagePublic(string $page): bool
    {
        if ($page === 'home' || $page === 'promo') {
            return true;
        }

        // Some conditionally public pages have multiple routes with the same permission.
        // For these alternate routes, just check the main route.
        $alternateRoutes = [
            'awards.post' => 'awards',
            'voting.post' => 'voting',
            'voting.code-entry' => 'voting',
            'results.pairwise' => 'results',
            'winners' => 'results',
        ];

        if (isset($alternateRoutes[$page])) {
            $page = $alternateRoutes[$page];
        }

        return in_array($page, $this->public_pages, true);
    }

    public function navbarItems(): Collection
    {
        return collect($this->navbar_items)->sortBy('order');
    }

    /**
     * @return bool Returns true if voting is currently open.
     */
    public function isVotingOpen(): bool
    {
        if (!$this->voting_start || $this->voting_start->isFuture()) {
            return false;
        }

        if ($this->voting_end && $this->voting_end->isPast()) {
            return false;
        }

        return true;
    }
}
