<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Autocompleter;
use App\Models\Award;
use App\Models\AwardSuggestion;
use App\Models\TableHistory;
use App\Services\AuditService;
use App\Settings\AppSettings;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AwardAdminController extends Controller
{
    public function __construct(
        readonly private AppSettings $settings,
        readonly private AuditService $auditService,
    ) {
    }

    public function managerList(Request $request)
    {
        $query = Award::query()
            ->with('autocompleter')
            ->with('awardFeedback')
            ->orderByRaw('deleted_at IS NOT NULL')
            ->orderBy('order');

        if (!Gate::allows('awards_secret')) {
            $query->notSecret();
        }

        $awards = $query->get()->keyBy('id');

        if ($request->get('sort') === 'percentage') {
            $awards = $awards->sortByDesc(fn ($a) => $a->getFeedbackPercent()['positive']);
        } elseif ($request->get('sort') === 'net') {
            $awards = $awards->sortByDesc(fn ($a) => $a->getGroupedFeedback()['net']);
        }

        $autocompleters = Autocompleter::all();

        $awardSuggestions = AwardSuggestion::whereNull('award_id')
            ->orderBy('suggestion')
            ->get();

        return view('awards.manage', [
            'awards' => $awards,
            'autocompleters' => $autocompleters,
            'awardSuggestions' => $awardSuggestions,
        ]);
    }

    public function managerPost(Request $request): RedirectResponse
    {
        if ($this->settings->read_only) {
            $this->addFlash('formError', 'The site is currently in read-only mode. No changes can be made.');
            return redirect()->back();
        }

        // Open / close all awards
        if ($request->post('action') === 'massChangeNominations') {
            if ($request->post('todo') === 'open') {
                $awards = Award::notSecret()->get();
                foreach ($awards as $award) {
                    $award->nominations_enabled = true;
                    $award->save();
                }

                $this->auditService->add(
                    Action::makeWith('mass-nomination-open')
                );

                $this->addFlash('formSuccess', 'Nominations for all awards are now open.');
            } elseif ($request->post('todo') === 'close') {
                $awards = Award::notSecret()->get();
                foreach ($awards as $award) {
                    $award->nominations_enabled = false;
                    $award->save();
                }

                $this->auditService->add(
                    Action::makeWith('mass-nomination-close')
                );

                $this->addFlash('formSuccess', 'Nominations for all awards are now closed.');
            }
        }

        return redirect()->back();
    }

    public function managerPostAjax(Request $request): JsonResponse
    {
        if ($this->settings->read_only) {
            return response()->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        if ($request->post('action') === 'new') {
            $award = new Award();
        } else {
            $id = strtolower($request->post('id'));
            if (strlen($id) == 0) {
                return response()->json(['error' => 'An ID is required.']);
            }

            $award = Award::find($id);
            if (!$award || ($award->secret && !Gate::allows('awards_secret'))) {
                return response()->json(['error' => 'Couldn\'t find an award with that ID.']);
            }
        }

        if ($request->post('action') === 'delete') {
            if (Gate::allows('awards_delete')) {
                $award->delete();

                $this->auditService->add(
                    Action::makeWith('award-delete', $award->id)
                );

                return response()->json(['success' => true]);
            } else {
                return response()->json(['error' => 'You aren\'t allowed to delete awards.']);
            }
        } elseif ($request->post('action') === 'new' || $request->post('action') === 'edit') {
            if (strlen($request->post('name')) === 0) {
                return response()->json(['error' => 'An award name is required.']);
            } elseif (strlen($request->post('subtitle')) === 0) {
                return response()->json(['error' => 'A subtitle is required.']);
            } elseif (!ctype_digit($request->post('order')) || $request->post('order') > 10000) {
                return response()->json(['error' => 'Position must be between 1 and 10000.']);
            }

            $awardBySlug = Award::query()
                ->where('slug', $request->post('slug'))
                ->where('id', '!=', $award->id ?? 0)
                ->first();

            if ($awardBySlug) {
                return response()->json(['error' => 'Slug is already used by a different award.']);
            }

            if ($request->post('autocompleter')) {
                $autocompleter = Autocompleter::find($request->post('autocompleter'));
                if (!$autocompleter) {
                    $autocompleter = null;
                }
            } else {
                $autocompleter = null;
            }

            if ($request->post('action') === 'new') {
                $award = new Award();
            }

            $award->slug = $request->post('slug');
            $award->name = $request->post('name');
            $award->subtitle = $request->post('subtitle');
            $award->comments = $request->post('comments');
            $award->order = $request->post('order');
            $award->enabled = $request->boolean('enabled');
            $award->nominations_enabled = $request->boolean('nominationsEnabled');
            $award->secret = $request->boolean('secret');
            $award->autocompleter()->associate($autocompleter);
            $award->save();

            $this->auditService->add(
                Action::makeWith($request->post('action') === 'new' ? 'award-added' : 'award-edited', $award->id),
                TableHistory::makeWith(Award::class, $award->id, $request->post())
            );

            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'Invalid action specified.']);
        }
    }
}
