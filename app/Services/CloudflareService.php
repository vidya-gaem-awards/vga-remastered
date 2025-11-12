<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

readonly class CloudflareService
{
    protected ?string $apiKey;
    protected ?string $zoneId;

    public function __construct()
    {
        $this->apiKey = config('services.cloudflare.api_key');
        $this->zoneId = config('services.cloudflare.zone_id');
    }

    public function isServiceAvailable(): bool
    {
        return $this->apiKey && $this->zoneId;
    }

    public function purgeCache(): void
    {
        Http::withToken($this->apiKey)
            ->throw()
            ->post("https://api.cloudflare.com/client/v4/zones/{$this->zoneId}/purge_cache", [
                'purge_everything' => true,
            ]);
    }
}
