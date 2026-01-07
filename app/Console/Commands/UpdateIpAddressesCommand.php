<?php

namespace App\Console\Commands;

use App\Models\UserNomination;
use App\Services\AbuseIpdbService;
use App\Settings\AppSettings;
use Illuminate\Console\Command;
use RuntimeException;

class UpdateIpAddressesCommand extends Command
{
    protected $signature = 'app:ips';

    protected $description = 'Update IP address data';

    public function handle(
        AppSettings $settings,
        AbuseIpdbService $abuseIpdb
    ): void {
        if ($settings->read_only) {
            throw new RuntimeException('Database is in read-only mode. Read-only mode must be disabled to run this script.');
        }

        $this->info('Updating nomination IPs');

        $ips = UserNomination::query()
            ->select('fuzzy_user_id')
            ->whereLike('fuzzy_user_id', 'ip_%')
            ->get()
            ->pluck('fuzzy_user_id')
            ->unique()
            ->sort()
            ->map(fn ($fuzzy) => substr($fuzzy, 3))
            ->values();

        $this->info($ips->count() . ' IP addresses to update');

        foreach ($ips as $index => $ip) {
            $result = $abuseIpdb->updateIpInformation($ip);
            if ($result) {
                $this->line("$index. Updated IP: $ip");
            } else {
                $this->warn("$index. Did not update IP: $ip");
            }
        }
    }
}
