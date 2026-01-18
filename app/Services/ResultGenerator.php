<?php

namespace App\Services;

use App\Models\Access;
use App\Models\Award;
use App\Models\IpAddress;
use App\Models\Result;
use App\Models\Vote;
use App\Models\VotingCodeLog;
use App\Settings\AppSettings;
use App\VGA\ResultCalculator\AbstractResultCalculator;
use App\VGA\ResultCalculator\InstantRunoff;
use App\VGA\ResultCalculator\Schulze;
use App\VGA\ResultCalculator\SchulzeLegacy;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriodImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

readonly class ResultGenerator
{
    private const array FILTERS = [
        '01-all' => false, // no filtering
        '04-4chan' => 4,  // 4chan
        '05-4chan-and-voting-code' => 'voting_group & 4 AND voting_group & 1024', // 4chan AND voting code
        '06-4chan-without-voting-code' => 'voting_group & 4 AND NOT voting_group & 1024', // 4chan AND NOT voting code
        '07-4chan-or-null' => 'voting_group & 4 OR voting_group & 2048', // 4chan OR null
        '08-4chan-or-null-with-voting-code' => 'voting_group & 4 > 0 OR (voting_group & 2048 AND voting_group & 1024)', // 4chan OR (null AND voting code)
        '09-null-and-voting-code' => 'voting_group & 2048 AND voting_group & 1024', // null AND voting code
        '10-null-without-voting-code' => 'voting_group & 2048 AND NOT voting_group & 1024',  // null AND NOT voting code
        '11-reddit' => 1,
        '12-twitter' => 2,
        // 4chan: 4
        '13-something-awful' => 8,
        '14-neogaf' => 16,
        '15-knockout' => 32,
        '16-8chan' => 64,
        '17-twitch' => 128,
        '18-facebook' => 256,
        '19-google' => 512,
        '02-voting-code' => 1024,
        '03-null' => 2048,
        '20-yandex' => 4096,
        '21-kiwifarms' => 8192,
        '22-4chan-ads' => 16384,
        '23-youtube' => 32768,
        '24-4chan-no-vpns' => '(voting_group & 4 OR (voting_group & 2048 AND voting_group & 1024)) AND voting_group & 65536'
    ];


    public function __construct(
        private AppSettings $settings,
        private AbuseIpdbService $abuseIpdb,
    ) {
    }

    public function performFullUpdate(): void
    {
        $this->updateIpAddresses();
        $this->updateVotingGroups();
        $this->updateResults();
    }

    public function updateIpAddresses(): void
    {
        if ($this->settings->read_only) {
            throw new RuntimeException('Database is in read-only mode.');
        }

        Log::info('Updating IP address data');

        $ips = DB::query()
            ->selectRaw('DISTINCT (v.ip) as ip')
            ->from('votes')
            ->get();

        Log::info($ips->count() . ' unique IP addresses on record');

        $updated = 0;

        foreach ($ips as $ip) {
            $result = $this->abuseIpdb->updateIpInformation($ip['ip']);
            if ($result) {
                $updated++;
            }
        }

        Log::info($updated . ' IP addresses updated');
    }

    /**
     * Updates the voting group for each vote based on different bits of data,
     * including referrers, voting codes, and AbuseIPDB data.
     *
     * @return void
     */
    public function updateVotingGroups(): void
    {
        if ($this->settings->read_only) {
            throw new RuntimeException('Database is in read-only mode.');
        }

        $this->log('Updating voting groups');

        // Step 1. Get a list of voters
        $ids = DB::table('votes')
            ->selectRaw('DISTINCT cookie_id')
            ->get()
            ->pluck('cookie_id')
            ->toArray();

        $voters = array_fill_keys($ids, [
            'codes' => [],
            'notes' => [],
            'referrers' => [],
            'vpn' => false,
        ]);

        $this->log("Step 1 (create array) complete");

        // Step 2. Check voting codes
        $codeLogs = VotingCodeLog::all();

        foreach ($codeLogs as $log) {
            if (isset($voters[$log->cookie_id])) {
                $voters[$log->cookie_id]['codes'][] = $log->code;
            }
        }

        $this->log('Step 2 (get voting codes) complete');

        // Step 3. Check referrers
        $suspiciousIps = IpAddress::query()
            ->where('usage_type', 'Data Center/Web Hosting/Transit')
            ->orWhere('abuse_score', '>', 0)
            ->orWhere('report_count', '>', 0)
            ->get()
            ->pluck('ip');

        $accessLogs = Access::query()
            ->where(function (Builder $query) {
                $query->whereNotLike('referer', 'https://____.vidyagaemawards.com%')
                    ->orWhereNull('referer');
            })
            ->whereIn('cookie_id', array_keys($voters))
            ->orderBy('created_at')
            ->get();

        foreach ($accessLogs as $log) {
            $referer = preg_replace('{https?://(www\.)?}', '', $log->referer ?: '');
            $voters[$log->cookie_id]['referrers'][] = $referer;
            $voters[$log->cookie_id]['vpn'] = $suspiciousIps->contains($log->ip);
        }

        $this->log('Step 3 (get referrers / VPNs) complete');

        // Step 4. Begin the processing
        $sites = [
            'reddit.com' => 2 ** 0,
            'old.reddit.com' => 2 ** 0,
            't.co' => 2 ** 1,
            'boards.4chan.org' => 2 ** 2,
            'boards.4channel.org' => 2 ** 2,
            'sys.4chan.org' => 2 ** 2,
            'sys.4channel.org' => 2 ** 2,
            'forums.somethingawful.com' => 2 ** 3,
            'neogaf.com' => 2 ** 4,
            'knockout.chat' => 2 ** 5,
            '8ch.net' => 2 ** 6,
            'twitch.tv' => 2 ** 7,
            'facebook.com' => 2 ** 8,
            'm.facebook.com' => 2 ** 8,
            'l.facebook.com' => 2 ** 8,
            'google.' => 2 ** 9,
            // voting code: 2 ** 10
            // no referer: 2 ** 11,
            'yandex.ru' => 2 ** 12,
            'kiwifarms.net' => 2 ** 13,
            // 4chan ads: 2 ** 14
            'youtube.com' => 2 ** 15,
            // VPN: 2 ** 16
        ];

        foreach ($voters as &$info) {
            $number = 0;

            // If user has a voting code
            if (count($info['codes']) > 0) {
                $number += 2 ** 10;
                $info['notes'][] = "Has voting code";
            }

            foreach (['dbMO'] as $ad_voting_code) {
                if (in_array($ad_voting_code, $info['codes'])) {
                    $number += 2 ** 14;
                    break;
                }
            }

            if ($info['vpn']) {
                $number += 2 ** 16;
            }

            $referers = array_unique($info['referrers']);

            // It's possible to have multiple unique referrers for one site.
            // To avoid messing up the bitmask, only count each site once.
            $used_bits = [];

            foreach ($referers as $referer) {
                foreach ($sites as $site => $value) {
                    if (str_starts_with($referer, $site) && !in_array($value, $used_bits, true)) {
                        $info['notes'][] = $site;
                        $used_bits[] = $value;
                        $number += $value;
                    }
                }

                if (empty($referer)) {
                    $number += 2 ** 11;
                }
            }

            $info['number'] = $number;
        }

        unset($info);

        $numberTotals = [];
        foreach ($voters as $info) {
            if (!isset($numberTotals[$info['number']])) {
                $numberTotals[$info['number']] = 0;
            }
            $numberTotals[$info['number']]++;
        }

        $this->log("Step 4 (assign numbers) complete");

        // Step 5. Update the values in the database
        $count = 0;
        foreach ($voters as $id => $info) {
            $count++;

            Vote::where('cookie_id', $id)
                ->update(['voting_group' => $info['number']]);

            if ($count % 1000 === 0) {
                $this->log("Processing record $count...");
            }
        }

        $this->log("Step 5 (update database) complete");
    }

    /**
     * Updates the results for every combination of award, filter, and calculator, unless otherwise
     * specified.
     *
     * @param string|null $filter If specified, only updates that filter instead of all of them.
     * @param Award|null $award If specified, only updates that award instead of all of them.
     * @param CarbonInterface|null $maxDate If specified, only includes votes cast before that date.
     *                                      This will update the results for that date's time key
     *                                      instead of 'latest'.
     *
     * @return void
     */
    public function updateResults(
        ?string $filter = null,
        ?Award $award = null,
        ?CarbonInterface $maxDate = null,
    ): void {
        if ($this->settings->read_only) {
            throw new RuntimeException('Database is in read-only mode.');
        }

        if ($filter && !isset(self::FILTERS[$filter])) {
            throw new RuntimeException("Invalid filter specified: $filter");
        }

        // Timekey
        if ($maxDate) {
            $timeKey = $maxDate->format('Y-m-d H:00:00');
            $this->log("Updating result cache (time key: $timeKey)");
            if (!$filter) {
                $filter = Result::OFFICIAL_FILTER;
            }

            Result::where('time_key', $timeKey)
                ->where('filter', $filter)
                ->delete();
        } else {
            $timeKey = now()->format('Y-m-d H:00:00');
            $this->log("Updating results cache (time key: latest)");

            // Remove existing data (except old timekeys)
            Result::whereIn('time_key', [$timeKey, 'latest'])
                ->whereNot('algorithm', 'schulze')
                ->delete();
        }

        // Start by getting a list of awards and all the nominees.
        if ($award) {
            $awards = collect([$award]);
        } else {
            $awards = Award::query()
                ->with('nominees')
                ->orderBy('order')
                ->get();
        }

        $this->log('Awards loaded.');

        if ($filter) {
            $filters = [$filter => self::FILTERS[$filter]];
        } else {
            $filters = self::FILTERS;
        }

        // Now we can start grabbing votes.
        foreach ($filters as $filterName => $condition) {
            foreach ($awards as $_award) {
                $query = Vote::query()
                    ->where('award_id', $_award->id);

                if (is_int($condition)) {
                    $query->whereRaw('(voting_group & ?)', [$condition]);
                } elseif (is_string($condition)) {
                    // @TODO: this is unpleasant
                    $query->whereRaw('(' . $condition . ')');
                }

                if ($maxDate) {
                    $query->where('created_at', '<=', $maxDate);
                }

                $votes = $query->get()
                    ->pluck('preferences')
                    ->filter();

                $nominees = $_award->nominees
                    ->keyBy('id');

                $calculators = [
                    Schulze::class,
                    SchulzeLegacy::class,
                    InstantRunoff::class,
                ];

                /**
                 * @var class-string<AbstractResultCalculator> $calculatorClass
                 */
                foreach ($calculators as $calculatorClass) {
                    $calculator = new $calculatorClass($nominees->all(), $votes->all());
                    $result = $calculator->calculateResults();

                    $resultObject = new Result();
                    $resultObject->algorithm = $calculator->getAlgorithmId();
                    $resultObject->award_id = $_award->id;
                    $resultObject->filter = $filterName;
                    $resultObject->results = $result;
                    $resultObject->steps = $calculator->getSteps();
                    $resultObject->warnings = $calculator->getWarnings();
                    $resultObject->votes = $votes->count();
                    $resultObject->time_key = 'latest';

                    if (!$maxDate) {
                        $resultObject->save();
                    }

                    // To save space, only create the time-keyed entries for the official results
                    if ($resultObject->algorithm === Result::OFFICIAL_ALGORITHM
                        && $resultObject->filter === Result::OFFICIAL_FILTER) {
                        $resultObject2 = $resultObject->replicate();
                        $resultObject2->time_key = $timeKey;
                        $resultObject2->save();
                    }
                }

                $this->log("[$filterName] Award complete: $_award->slug [$_award->id]");
            }
        }

        $this->log('Done.');
    }

    /**
     * Retroactively updates results for all past time keys, going back to the start of voting.
     *
     * Normally not required unless changes were made to filters or voting algorithms while
     * voting was open.
     *
     * @return void
     */
    public function backfillTimeKeys(): void
    {
        if ($this->settings->read_only) {
            throw new RuntimeException('Database is in read-only mode.');
        }

        $start = $this->settings->voting_start;
        if (!$start) {
            throw new RuntimeException("Can't backfill without a voting start date.");
        }

        $period = CarbonPeriodImmutable::create($start, '1 hour', now());
        foreach ($period as $date) {
            $this->updateResults(maxDate: $date);
        }
    }

    private function log(string $line): void
    {
        $time = microtime(true) - LARAVEL_START;

        $text = sprintf('%5.2f: %s', $time, $line);
        Log::info($text);
        if (App::runningInConsole()) {
            echo $text . "\n";
        }
    }
}
