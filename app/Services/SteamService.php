<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

readonly class SteamService
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.steam.client_secret');
    }

    public function isApiKeySet(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Converts a string to a Steam ID if possible.
     *
     * Adapted from https://github.com/xPaw/SteamID.php under the MIT License.
     * Ideally, we'd use the library directly, but it requires the GMP extension, which is generally
     * not included with PHP and would be mildly annoying to add just for this.
     *
     * @param string $string
     * @return string|null
     */
    public function stringToSteamId(string $string): ?string
    {
        // Plain Steam ID
        if (is_numeric($string)) {
            // Annoyingly, when you link a Steam account with Discord, the steam ID it gives back is incorrect.
            // See https://github.com/discordapp/discord-api-docs/issues/271.
            // Specifically, the 32nd bit (representing the instance of the account) is 0 when it should be 1.
            // If we detect the issue, we flip the bit.
            $binary = str_pad(decbin($string), 64, '0', STR_PAD_LEFT);
            if (isset($binary[31]) && $binary[31] === '0') {
                $binary[31] = '1';
                return (string)bindec($binary);
            }

            return $string;
        }

        // Profile URL with Steam ID
        if (preg_match('/^https?:\/\/(?:my\.steamchina|steamcommunity)\.com\/(?P<type>profiles|gid)\/(?P<id>.+?)(?:\/|$)/', $string, $matches) === 1) {
            return $matches['id'];
        }

        // Profile URL with vanity string, or plain vanity string
        if (preg_match('/^https?:\/\/(?:my\.steamchina|steamcommunity)\.com\/(?P<type>id|groups|games)\/(?P<id>[\w-]+)(?:\/|$)/', $string, $matches) === 1
            || preg_match( '/^(?P<id>[\w-]+)$/', $string, $matches ) === 1 ) {
            $url = "https://api.steampowered.com/ISteamUser/ResolveVanityURL/v0001/?key={$this->apiKey}&vanityurl={$matches['id']}";
            $result = Http::get($url)->json();

            if ($result['response']['success'] === 1) {
                return $result['response']['steamid'];
            }

            return null;
        }

        return null;
    }

    public function getProfile(string $steamId): ?array
    {
        $url = "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$this->apiKey}&steamids={$steamId}";
        $result = Http::get($url)->json();

        if (empty($result['response']['players'])) {
            return null;
        }

        $player = $result['response']['players'][0];

        return [
            'steamId64' => $player['steamid'],
            'nickname' => $player['personaname'],
            'avatar' => $player['avatar'],
        ];
    }
}
