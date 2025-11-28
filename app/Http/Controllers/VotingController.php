<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use App\Settings\AppSettings;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriodImmutable;
use DateTimeInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class VotingController extends Controller
{
    public function __construct(
        private readonly AppSettings $settings,
        private readonly AuditService $auditService,
    )
    {
    }

    public function index()
    {
        abort(501);
    }

    public function post()
    {
        abort(501);
    }

    public function codeEntry()
    {
        abort(501);
    }

    public function codeViewer(): View
    {
        $currentDate = Date::make(date('Y-m-d H:00'))->setTimezone('America/New_York');
        $currentCode = $this->getCode($currentDate);

        $url = route('voting.code-entry', $currentCode);
        $url = substr($url, 0, strrpos($url, '/') + 1);

        $logs = DB::table('voting_code_logs as vcl')
            ->select(
                'vcl.code',
                DB::raw('COUNT(DISTINCT vcl.cookie_id) as count'),
                DB::raw('MIN(vcl.created_at) as first_use'),
                DB::raw('MAX(vcl.created_at) as last_use')
            )
            ->join('votes as v', 'vcl.cookie_id', '=', 'v.cookie_id')
            ->groupBy('vcl.code')
            ->orderBy('first_use', 'ASC')
            ->get();

        $votingStart = $this->settings->voting_start;
        if ($votingStart) {
            $votingStart = $votingStart->setTimezone('America/New_York');
        }

        $allValidCodes = [];

        if ($votingStart) {
            $votingStart = $votingStart
                ->modify('-1 day')
                ->setMinute(0)
                ->setSecond(0);

            $votingEnd = $this->settings->voting_end ?: Date::make(date('Y-m-d H:00'))->setTimezone('America/New_York');
            $votingEnd = $votingEnd
                ->modify('+1 day')
                ->setMinute(0)
                ->setSecond(0);

            $interval = new CarbonInterval('PT1H');
            $range = new CarbonPeriodImmutable($votingStart, $interval, $votingEnd);

            foreach ($range as $date) {
                $code = $this->getCode($date);
                $allValidCodes[$code] = $date;
            }
        }

        return view('voting-code', [
            'date' => $currentDate,
            'url' => $url,
            'code' => $currentCode,
            'logs' => $logs,
            'validCodes' => $allValidCodes,
        ]);
    }

    private function getCode(DateTimeInterface $datetime): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $code = '';
        for ($i = 0; $i < 4; $i++) {
            $seedString = config('app.voting_code_key') . $datetime->format(' Y-m-d H:00 ') . $i;
            $code .= $characters[self::randomNumber($seedString, strlen($characters) - 1)];
        }

        return $code;
    }

    /**
     * Normally we would just use random_int, but we want to be able to provide a seed.
     * @param string $seed
     * @param int $max
     * @return int
     */
    private static function randomNumber(string $seed, int $max): int
    {
        //hash the seed to ensure enough random(ish) characters each time
        $hash = sha1($seed);

        //use the first x characters and convert from hex to base 10 (this is where the random number is obtained)
        $rand = base_convert(substr($hash, 0, 6), 16, 10);

        //as a decimal percentage (ensures between 0 and max number)
        return (int)round($rand / 0xFFFFFF * $max);
    }
}
