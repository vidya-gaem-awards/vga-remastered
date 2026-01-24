<?php

namespace App\Services;

use App\Models\IpAddress;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class AbuseIpdbService
{
    private const string BASE_URI = 'https://api.abuseipdb.com/api/v2/';

    private PendingRequest $client;

    public function __construct()
    {
        $this->client = Http::baseUrl(self::BASE_URI)
            ->withHeader('Key', config('services.abuse_ipdb.api_key'))
            ->acceptJson();
    }

    public function getIpInformation(string $ipAddress): array
    {
        return $this->client->get('check', [
            'ipAddress' => $ipAddress,
        ])->throw()->json('data');
    }

    public function updateIpInformation(string $ipAddress, bool $force = false): bool
    {
        $ip = IpAddress::where('ip', $ipAddress)->first();

        if ($ip && $ip->updated_at->isAfter('-28 days') && !$force) {
            return false;
        }

        if (!$ip) {
            $ip = new IpAddress();
            $ip->ip = $ipAddress;
        }

        $info = $this->getIpInformation($ipAddress);

        $ip->whitelisted = $info['isWhitelisted'];
        $ip->abuse_score = $info['abuseConfidenceScore'];
        $ip->country_code = $info['countryCode'];
        $ip->usage_type = $info['usageType'];
        $ip->isp = $info['isp'];
        $ip->report_count = $info['totalReports'];
        $ip->domain = $info['domain'];
        $ip->save();
        // Always update the timestamp, even if nothing actually changed, since we use it as a
        // 'last checked' date rather than 'last updated'
        $ip->touch();

        return true;
    }
}
