<?php

namespace App\Http\Controllers;

use App\Facade\FuzzyUser;
use App\Models\Action;
use App\Models\Autocompleter;
use App\Models\Award;
use App\Models\AwardFeedback;
use App\Models\AwardSuggestion;
use App\Models\File;
use App\Models\GameRelease;
use App\Models\UserNomination;
use App\Models\UserNominationGroup;
use App\Services\AuditService;
use App\Settings\AppSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AwardController extends Controller
{
    public function __construct(
        private AppSettings $settings,
        private AuditService $auditService,
    ) {
    }

    public function index(): View
    {
        $awards = Award::query()
            ->with('autocompleter')
            ->with('userNominations')
            ->where('secret', false)
            ->orderBy('order')
            ->get()
            ->keyBy('id');

        $awardIds = $awards->keys()->toArray();

        $userNominations = UserNomination::query()
            ->where('fuzzy_user_id', FuzzyUser::id())
            ->get();

        $nominations = array_fill_keys($awardIds, []);

        /** @var UserNomination $un */
        foreach ($userNominations as $un) {
            $nominations[$un->award_id][] = [
                'id' => $un->id,
                'nomination' => $un->nomination,
            ];
        }

        $feedback = AwardFeedback::query()
            ->where('fuzzy_user_id', FuzzyUser::id())
            ->get();

        $opinions = array_fill_keys($awardIds, 0);

        /** @var AwardFeedback $cf */
        foreach ($feedback as $cf) {
            $opinions[$cf->award_id] = (int) $cf->opinion;
        }

        $userSuggestions = AwardSuggestion::query()
            ->where('fuzzy_user_id', FuzzyUser::id())
            ->get();

        $suggestions = array_fill_keys($awardIds, []);
        $suggestions['new-award'] = [];

        /** @var AwardSuggestion $suggestion */
        foreach ($userSuggestions as $suggestion) {
            if ($suggestion->award_id) {
                $key = $suggestion->award_id;
            } else {
                $key = 'new-award';
            }
            $suggestions[$key][] = $suggestion->suggestion;
        }

        $result = Autocompleter::all();
        $autocompleters = array_fill_keys(array_map(fn ($id) => 'award_' . $id, $awardIds), []);

        /** @var Autocompleter $autocompleter */
        foreach ($result as $autocompleter) {
            $strings = $autocompleter->strings;
            // The video-game autocompleter is a special case: its values are stored in another table
            if ($autocompleter->slug === 'video-games') {
                $games = GameRelease::all();
                foreach ($games as $game) {
                    $platforms = array_map(fn ($p) => GameRelease::PLATFORMS[$p], $game->platforms);
                    $strings[] = [
                        'value' => $game->name,
                        'label' => $game->name . ' (' . implode(', ', $platforms) . ')',
                    ];
                }
            }
            $autocompleters['auto_' . $autocompleter->id] = $strings;
        }

        foreach ($awards as $award) {
            // Don't bother populating the autocompleter for this award if it already has a different one defined
            if ($award->autocompleter) {
                continue;
            }

            $allNominations = $award->userNominations->map(fn ($un) => $un->nomination);

            $nominationCount = array_fill_keys($allNominations->values()->toArray(), 0);
            foreach ($allNominations as $nomination) {
                $nominationCount[$nomination]++;
            }

            $nominationCount = array_filter($nominationCount, function ($count) {
                return $count >= 2;
            }, ARRAY_FILTER_USE_BOTH);

            $autocompleters['award_' . $award->id] = array_keys($nominationCount);
        }

        $vidyaLinkImage = File::where('entity', 'Misc.vidyaLink')->first();

        return view('awards', [
            'awards' => $awards,
            'userNominations' => $nominations,
            'userOpinions' => $opinions,
            'userSuggestions' => $suggestions,
            'autocompleters' => $autocompleters,
            'vidyaLinkImage' => $vidyaLinkImage,
        ]);
    }

    public function post(Request $request): JsonResponse
    {
        $awardSuggestion = $request->post('awardSuggestion');
        if ($awardSuggestion !== null) {
            if ($this->settings->read_only || !$this->settings->award_suggestions) {
                return response()->json(['error' => 'New awards can no longer be suggested.']);
            }

            $awardSuggestion = trim($awardSuggestion);
            if ($awardSuggestion === '') {
                return response()->json(['error' => 'Your award idea cannot be blank.']);
            }

            $result = AwardSuggestion::query()
                ->whereNull('award_id')
                ->where('fuzzy_user_id', FuzzyUser::id())
                ->where('suggestion', $awardSuggestion)
                ->first();

            if ($result) {
                return response()->json(['error' => 'You\'ve already suggested that award.']);
            }

            $suggestion = new AwardSuggestion();
            $suggestion->suggestion = $awardSuggestion;
            $suggestion->fuzzy_user_id = FuzzyUser::id();
            $suggestion->save();

            $this->auditService->add(
                Action::makeWith('new-award-suggested', $suggestion->id)
            );

            return response()->json(['success' => true]);
        }

        /** @var Award $award */
        $award = Award::find($request->post('id'));

        if (!$award || $award->secret) {
            return response()->json(['error' => 'Invalid award provided.']);
        }

        $opinion = $request->post('opinion');
        if ($opinion !== null) {
            if ($this->settings->read_only) {
                return response()->json(['error' => 'Feedback can no longer be given on awards.']);
            }

            if (!in_array($opinion, ['-1', '1', '0'], true)) {
                return response()->json(['error' => 'Invalid opinion provided.']);
            }

            $feedback = AwardFeedback::query()
                ->where('award_id', $award->id)
                ->where('fuzzy_user_id', FuzzyUser::id())
                ->first();

            if (!$feedback) {
                $feedback = new AwardFeedback();
                $feedback->award()->associate($award);
                $feedback->fuzzy_user_id = FuzzyUser::id();
            }
            $feedback->opinion = $opinion;
            $feedback->save();

            $this->auditService->add(
                Action::makeWith('opinion-given', $award->id, $opinion)
            );

            return response()->json(['success' => true]);
        }

        $suggestedName = $request->post('suggestedName');
        if ($suggestedName !== null) {
            if ($this->settings->read_only) {
                return response()->json(['error' => 'Name suggestions can no longer be made for this award.']);
            }

            $suggestedName = trim($suggestedName);
            if ($suggestedName === '') {
                return response()->json(['error' => 'Suggested award name cannot be blank.']);
            }

            $result = AwardSuggestion::query()
                ->where('fuzzy_user_id', FuzzyUser::id())
                ->where('award_id', $award->id)
                ->where('suggestion', $suggestedName)
                ->first();

            if ($result) {
                return response()->json(['error' => 'You\'ve already suggested that name.']);
            }

            $suggestion = new AwardSuggestion();
            $suggestion->award()->associate($award);
            $suggestion->suggestion = $suggestedName;
            $suggestion->fuzzy_user_id = FuzzyUser::id();
            $suggestion->save();

            $this->auditService->add(
                Action::makeWith('award-name-suggested', $award->id, $suggestedName)
            );

            return response()->json(['success' => true]);
        }

        $nomination = $request->post('nomination');
        if ($nomination !== null) {
            if ($this->settings->read_only) {
                return response()->json(['error' => 'Nominations can no longer be made for this award.']);
            }

            if (!$award->nominations_enabled) {
                return response()->json(['error' => 'Nominations aren\'t currently open for this award.']);
            }

            $nomination = trim($nomination);
            if ($nomination === '') {
                return response()->json(['error' => 'Nomination cannot be blank.']);
            }

            $result = UserNomination::query()
                ->where('fuzzy_user_id', FuzzyUser::id())
                ->where('award_id', $award->id)
                ->where('nomination', $nomination)
                ->first();

            if ($result) {
                return response()->json(['error' => 'You\'ve already nominated that.']);
            }

            // Find existing nomination group
            $result = UserNominationGroup::query()
                ->where('name', $nomination)
                ->where('award_id', $award->id)
                ->first();

            if (!$result) {
                $nominationGroup = new UserNominationGroup();
                $nominationGroup->award()->associate($award);
                $nominationGroup->name = $nomination;
                $nominationGroup->save();
            } else {
                $nominationGroup = $result;
            }

            $userNomination = new UserNomination();
            $userNomination->award()->associate($award);
            $userNomination->fuzzy_user_id = FuzzyUser::id();
            $userNomination->nomination = $nomination;
            $userNomination->userNominationGroup()->associate($nominationGroup->mergedInto ?: $nominationGroup);
            $userNomination->save();

            $this->auditService->add(
                Action::makeWith('nomination-made', $award->id, $nomination)
            );

            return response()->json(['success' => true, 'id' => $userNomination->id]);
        }

        $removeNomination = $request->post('removeNomination');
        if ($removeNomination !== null) {
            if ($this->settings->read_only || !$award->nominations_enabled) {
                return response()->json(['error' => 'Nominations can no longer be changed for this award.']);
            }

            $result = UserNomination::query()
                ->where('fuzzy_user_id', FuzzyUser::id())
                ->where('award_id', $award->id)
                ->where('id', $removeNomination)
                ->first();

            if (!$result) {
                return response()->json(['error' => 'Unable to find nomination. Perhaps it was already removed?']);
            }

            $result->delete();

            $this->auditService->add(
                Action::makeWith('nomination-removed', $award->id, $result->nomination)
            );

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'An unexpected error occurred.']);
    }
}
