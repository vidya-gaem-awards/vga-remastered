<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Award;
use App\Models\Result;
use App\Models\TableHistory;
use App\Services\AuditService;
use App\Services\FileService;
use App\Settings\AppSettings;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ResultController extends Controller
{
    public function __construct(
        private readonly AppSettings $settings,
        private readonly AuditService $auditService,
        private readonly FileService $fileService,
    ) {
    }

    public function simple(): View
    {
        $awards = Award::query()
            ->with('results')
            ->with('winnerImage')
            ->orderBy('order')
            ->get();

        $results = $winners = [];

        foreach ($awards as $award) {
            $rankings = array_values($award->officialResults() ? $award->officialResults()->results : []);

            if (empty($rankings)) {
                $results[$award->id] = null;
                continue;
            }

            // @TODO: slug/ID
            $winners[$award->id] = $award->nominees()->where('slug', $rankings[0])->first();

            foreach ($rankings as $key => &$value) {
                // @TODO: slug/ID
                $nominee = $award->nominees()->where('slug', $value)->first();
                $output = '<span class="rank">#' . ($key + 1) . '</span>&nbsp;';

                if ($nominee) {
                    $output .= str_replace(' ', '&nbsp;', $nominee->name);
                } else {
                    $output .= '<span style="color: white; background: red;">' . $value . '</span>';
                }
                $value = $output;
            }

            $results[$award->id] = $rankings;
        }

        return view('winners', [
            'awards' => $awards,
            'results' => $results,
            'winners' => $winners,
        ]);
    }

    public function winnerImageUpload(Request $request): JsonResponse
    {
        if ($this->settings->read_only) {
            return response()->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        $id = $request->post('id', false);

        $award = Award::find($id);
        if (!$award) {
            return response()->json(['error' => 'Invalid award specified.']);
        }

        try {
            $file = $this->fileService->handleUploadedFile(
                $request->file('file'),
                'Award.winnerImage',
                'winners',
                $award->id
            );
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

        if ($award->winnerImage) {
            $oldFile = $award->winnerImage;
            $award->winnerImage()->dissociate();
            $award->save();
            $this->fileService->deleteFile($oldFile);
        }

        $award->winnerImage()->associate($file);
        $award->save();

        $this->auditService->add(
            Action::makeWith('winner-image-updated', $award->id),
            TableHistory::makeWith(Award::class, $award->id, ['image' => $file->id])
        );

        return response()->json(['success' => true, 'filePath' => $file->getURL()]);
    }

    public function detailed(Request $request): View
    {
        $awards = Award::query()
            ->with('nominees')
            ->with('results')
            ->orderBy('order')
            ->get();

        $results = [];

        $filters = [
            '01-all' => 'No filtering',
//                '03-null' => 'No referrer',
            '08-4chan-or-null-with-voting-code' => '4chan',
            '19-google' => 'Google',
            '16-8chan' => '8chan',
//            '02-voting-code' => 'Voting code',
//            '04-4chan' => '4chan',
//            '05-4chan-and-voting-code' => '4chan with code',
//            '06-4chan-without-voting-code' => '4chan without code',
//            '07-4chan-or-null' => '4chan + NULL',
//            '09-null-and-voting-code' => 'NULL with code',
//            '10-null-without-voting-code' => 'NULL without code',
            '15-knockout' => 'Knockout',
            '18-facebook' => 'Facebook',
            '12-twitter' => 'Twitter',
            '17-twitch' => 'Twitch',
            '11-reddit' => 'Reddit',
            '21-kiwifarms' => 'Kiwifarms',
            '14-neogaf' => 'NeoGAF',
            '13-something-awful' => 'Something Awful',
            '20-yandex' => 'Yandex',
            '23-youtube' => 'YouTube',
        ];

        if (Gate::allows('voting_code')) {
            $filters['22-4chan-ads'] = '4chan Ads';
        }

        if (Gate::allows('voting_results')) {
            $filters['24-4chan-no-vpns'] = '4chan (VPNs excluded)';
        }

        $nominees = [];

        /** @var Award $award */
        foreach ($awards as $award) {
            foreach ($award->nominees as $nominee) {
                // @TODO: backwards compatibility measure. Need to pick one and only one to use
                $nominees[$award->id][$nominee->id] = $nominee;
                $nominees[$award->id][$nominee->slug] = $nominee;
            }
            foreach ($award->results as $result) {
                if ($result->time_key !== 'latest') {
                    continue;
                }
                if ($result->algorithm !== Result::OFFICIAL_ALGORITHM) {
                    continue;
                }
                if (isset($filters[$result->filter]) && $result->votes >= 5) {
                    $results[$award->id][$result->filter] = $result;
                }
            }

            if (isset($results[$award->id])) {
                // Display the filter with the most votes first (this will invariably put No Filtering and 4chan on top)
                uasort($results[$award->id], function (Result $a, Result $b) {
                    return $b->votes <=> $a->votes;
                });
            }

            // If the result cache is empty, results haven't been generated yet.
            if ($award->results()->count() === 0 || !isset($results[$award->id])) {
                $results[$award->id] = null;
            }
        }

        return view('results', [
            'awards' => $awards,
            'nominees' => $nominees,
            'results' => $results,
            'filters' => $filters,
            'sweepPoints' => $request->query->getBoolean('sweepPoints'),
        ]);
    }

    public function pairwise(): View
    {
        $awards = Award::query()
            ->with('nominees')
            ->with('results')
            ->orderBy('order')
            ->get();

        $pairwise = [];

        foreach ($awards as $award) {
            $pairwise[$award->id] = $award->officialResults() ? $award->officialResults()->steps['pairwise'] : null;
        }

        return view('results-pairwise', [
            'awards' => $awards,
            'pairwise' => $pairwise,
        ]);
    }

    public function awardResults(Award $award): View
    {
        $awards = Award::query()
            ->with('results')
            ->orderBy('order')
            ->get();

        $resultHistory = $award->results()
            ->where('filter', Result::OFFICIAL_FILTER)
            ->where('algorithm', Result::OFFICIAL_ALGORITHM)
            ->whereNot('time_key', 'latest')
            ->where('votes', '>', 0)
            ->orderByDesc('time_key')
            ->get();

        $colours = [
            '#00008b', // dark blue
            '#008000', // green
            '#7f0000', // maroon
            '#ff8c00', // orange
            '#8b008b', // dark magenta
            '#556b2f', // dark olive green
            '#008b8b', // dark cyan
        ];

        $firstHistory = $resultHistory->first();

        $nomineeColours = [];

        $index = 0;
        foreach ($firstHistory->results as $nominee) {
            $nomineeColours[$nominee] = $colours[$index] ?? '#000000';
            $index++;
        }

        return view('results-award', [
            'awards' => $awards,
            'award' => $award,
            'resultHistory' => $resultHistory,
            'nomineeColours' => $nomineeColours,
            // @TODO: slug vs ID issue again
            'nominees' => $award->nominees->keyBy('slug'),
        ]);
    }
}
