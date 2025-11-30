<?php

namespace App\Http\Controllers;

use App\Facade\FuzzyUser;
use App\Models\Award;
use App\Models\LootboxItem;
use App\Models\LootboxTier;
use App\Models\Nominee;
use App\Models\Vote;
use App\Models\VotingCodeLog;
use App\Settings\AppSettings;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriodImmutable;
use DateTimeInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class VotingController extends Controller
{
    public function __construct(
        private readonly AppSettings $settings,
    ) {
    }

    public function index(Request $request, ?Award $award = null): View|RedirectResponse
    {
        /** @var Award[] $awards */
        $awards = Award::query()
            ->orderBy('order')
            ->get();

        if ($award) {
            $award->load([
                'nominees',
                'nominees.image',
            ]);
        }

        $prevAward = null;
        $nextAward = null;
        $voteJSON = [null];

        $start = $this->settings->voting_start;
        $end = $this->settings->voting_end;

        $votingNotYetOpen = $this->settings->isVotingNotYetOpen();
        $votingClosed = $this->settings->hasVotingClosed();
        $votingOpen = $this->settings->isVotingOpen();

        $lootboxTest = null;

        if ($votingNotYetOpen) {
            if (!$start) {
                $voteText = 'Voting will open soon.';
            } else {
                $voteText = 'Voting opens in ' . AppSettings::getRelativeTimeString($start) . '.';
            }
        } elseif ($votingOpen) {
            if (!$end) {
                $voteText = 'Voting is now open!';
            } else {
                $voteText = 'Voting closes in ' . AppSettings::getRelativeTimeString($end);
            }
        } else {
            $voteText = 'Voting is now closed.';
        }

        // Users with special access to the voting page can change the current vote status for testing purposes
        if (Gate::allows('voting_view')) {
            $time = $request->query('time');
            if ($time === 'before') {
                $votingNotYetOpen = true;
                $votingOpen = $votingClosed = false;
                $voteText = 'Voting will open soon.';
            } elseif ($time === 'after') {
                $votingClosed = true;
                $votingNotYetOpen = $votingOpen = false;
                $voteText = 'Voting is now closed.';
            } elseif ($time === 'during') {
                $votingOpen = true;
                $votingNotYetOpen = $votingClosed = false;
                $voteText = 'Voting is now open!';
            }
        }

        if ($request->query->getInt('lootbox') && Gate::allows('items_manage')) {
            $lootboxTest = LootboxItem::find($request->query->getInt('lootbox'));
            if (!$lootboxTest) {
                $this->addFlash('error', 'Invalid lootbox item specified.');
                return redirect()->route('lootbox.items');
            }

            if (empty($time)) {
                $votingOpen = true;
                $votingNotYetOpen = $votingClosed = false;
                $voteText = 'Voting is now open!';
            }
        }

        $votes = Vote::where('cookie_id', FuzzyUser::cookieId())->get();

        $simpleVotes = [];
        foreach ($awards as $a) {
            $simpleVotes[$a->id] = [];
        }
        foreach ($votes as $vote) {
            $preferences = $vote->preferences;
            array_unshift($preferences, null);
            $simpleVotes[$vote->award_id] = $preferences;
        }

        // Fetch the active award (if given)
        if ($award) {
            $awardArray = $awards->all();
            // Iterate through the awards list to get the previous and next awards
            $iterAward = reset($awardArray);
            while ($iterAward->id !== $award->id) {
                $prevAward = $iterAward;
                $iterAward = next($awardArray);
            }

            $nextAward = next($awardArray);
            if (!$nextAward) {
                $nextAward = reset($awardArray);
            }

            if (!$prevAward) {
                $prevAward = end($awardArray);
            }

            if (isset($simpleVotes[$award->id])) {
                $voteJSON = $simpleVotes[$award->id];
            }
        }

        $voteDialogMapping = [
            6 => 'height2 width3',
            7 => 'height2 width4',
            8 => 'height2 width4',
            9 => 'height2 width5',
            10 => 'height2 width5',
            11 => 'height3 width4',
            12 => 'height3 width4',
            13 => 'height3 width5',
            14 => 'height3 width5',
            15 => 'height3 width5',
        ];

        // @TODO: Probably remove support for the old voting page.
        if ($request->query->get('legacy') === '1') {
            $request->session()->put('legacyVotingPage', true);
        } elseif ($request->query->get('legacy') === '0') {
            $request->session()->put('legacyVotingPage', false);
        }

        // Fake ads
        // @TODO: Advertisements and related functionality are not currently implemented.
        $adverts = [];
//        $adverts = $em->getRepository(Advertisement::class)->findBy(['special' => 0]);

        $decorations = [];

        if (!empty($adverts)) {
            $adCount = count($adverts);
            $iterations = ($adCount > 6) ? 6 : $adCount;
            for ($i = 0; $i < $iterations; $i++) {
                $index = array_rand($adverts);
                $decoration = array_splice($adverts, $index, 1)[0];

                $direction = $i % 2 === 0 ? 'left' : 'right';
                $angle = random_int(-5, 5);
                $x = random_int(-30, 0);
                $y = 300 + floor($i / 2) * 600 + random_int(-200, 200);

                $decorations[] = [
                    'decoration' => $decoration,
                    'direction' => $direction,
                    'angle' => $angle,
                    'x' => $x,
                    'y' => $y,
                ];
            }
        }

        // Lootbox items
        $items = LootboxItem::query()
            ->with(['image', 'musicFile', 'additionalFiles'])
            ->get()
            ->keyBy('slug');

        $itemsWithCss = $items->filter(fn (LootboxItem $item) => $item->css && $item->css_contents);

        $customCss = '';
        foreach ($itemsWithCss as $item) {
            if ($lootboxTest === $item) {
                continue;
            }
            $customCss .= "/* Start CSS for {$item->slug} */\n";
            $customCss .= $item->css_contents . "\n";
            $customCss .= "/* End CSS for {$item->slug} */\n\n";
        }

        $lootboxSettings = [
            'cost' => $this->settings->lootbox_cost,
        ];

        $lootboxTiers = LootboxTier::all()->keyBy('id');

        return view('voting', [
            'title' => 'Voting',
            'awards' => $awards,
            'award' => $award,
            'votingNotYetOpen' => $votingNotYetOpen,
            'votingClosed' => $votingClosed,
            'votingOpen' => $votingOpen,
            'voteText' => $voteText,
            'prevAward' => $prevAward,
            'nextAward' => $nextAward,
            'votes' => $voteJSON,
            'allVotes' => $simpleVotes,
            'voteButtonSizeMap' => $voteDialogMapping,
            'votingStyle' => $request->session()->get('legacyVotingPage', false) ? 'legacy' : 'new',
            'decorations' => $decorations,
            'items' => $items,
            'lootboxSettings' => $lootboxSettings,
            'lootboxTiers' => $lootboxTiers,
            'lootboxTest' => $lootboxTest,
            'rewardCSS' => $customCss,
        ]);
    }

    public function post(Award $award, Request $request): JsonResponse
    {
        if ($this->settings->read_only) {
            return response()->json(['error' => 'Voting has closed.']);
        }

        if (!Gate::allows('voting_view')) {
            if ($this->settings->isVotingNotYetOpen()) {
                return response()->json(['error' => 'Voting hasn\'t started yet.']);
            } elseif ($this->settings->hasVotingClosed()) {
                return response()->json(['error' => 'Voting has closed.']);
            }
        }

        $preferences = $request->request->all('preferences') ?: [''];

        // Remove blank preferences and recreate the key ordering.
        $preferences = array_values(array_filter($preferences));
        // By adding an element to the front and then removing it, we shift the keys from 0 to n to 1 to n+1.
        array_unshift($preferences, '');
        unset($preferences[0]);

        if (count($preferences) !== count(array_unique($preferences))) {
            return response()->json(['error' => 'Duplicate nominees are not allowed.']);
        }

        $nomineeIDs = $award->nominees->pluck('id');
        $invalidNominees = array_diff($preferences, $nomineeIDs->toArray());

        if (count($invalidNominees) > 0) {
            return response()->json(
                ['error' => 'Some of the nominees you\'ve voted for are invalid: ' . implode(', ', $invalidNominees)]
            );
        }

        $vote = Vote::query()
            ->where('award_id', $award->id)
            ->where('cookie_id', FuzzyUser::cookieId())
            ->first();

        if (count($preferences) === 0) {
            if ($vote) {
                $vote->delete();
            }
            return response()->json(['success' => true]);
        }

        if (!$vote) {
            $vote = new Vote();
            $vote->award_id = $award->id;
            $vote->cookie_id = FuzzyUser::cookieId();
        }

        $vote->preferences = $preferences;
        $vote->user_id = Auth::id();
        $vote->ip = $request->ip();
        $vote->voting_code = FuzzyUser::votingCode();
        $vote->save();

        return response()->json(['success' => true]);
    }

    public function codeEntry(string $code, Request $request): RedirectResponse
    {
        if ($this->settings->read_only) {
            return redirect()->route('voting');
        }

        $code = substr($code, 0, 20);

        $request->session()->put('votingCode', $code);

        $log = new VotingCodeLog();
        $log->code = $code;
        $log->ip = $request->ip();
        $log->cookie_id = FuzzyUser::cookieId();
        $log->referer = $request->server('HTTP_REFERER');
        if (Auth::user()) {
            $log->user_id = Auth::user()->id;
        }
        $log->save();

        $response = redirect()->route('voting');
        $response->cookie(cookie('votingCode', $code, 60 * 24 * 90));

        return $response;
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
