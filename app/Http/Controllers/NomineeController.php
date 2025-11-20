<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Award;
use App\Models\Nominee;
use App\Models\TableHistory;
use App\Models\UserNomination;
use App\Models\UserNominationGroup;
use App\Services\AuditService;
use App\Services\FileService;
use App\Settings\AppSettings;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use League\Csv\Bom;
use League\Csv\Writer;

class NomineeController extends Controller
{
    public function __construct(
        private readonly AppSettings $settings,
        private readonly AuditService $auditService,
        private readonly FileService $fileService,
    ) {
    }

    public function index(Request $request, ?Award $award = null): View
    {
        $awards = Award::query()
            ->hideSecret()
            ->orderBy('order')
            ->get()
            ->keyBy('id');

        if (!$award) {
            return view('nominees', [
                'awards' => $awards,
                'award' => null,
            ]);
        }

        // Laravel will handle the 404 for non-enabled awards (because it's a global scope),
        // but not the secret awards (because it's a local scope)
        if ($awards->doesntContain($award->id)) {
            abort(404);
        }

        $alphabeticalSort = $request->query('sort') === 'alphabetical';

        $nomineesArray = $award->nominees()
            ->with('image')
            ->get()
            ->keyBy('id');

        // Get all userNominationGroups for the Award, sorted by number of nominations that the group has
        $userNominationGroups = $award->userNominationGroups()
            ->with('mergedInto')
            ->with('nominee')
            ->withCount('userNominations')
            ->orderByDesc('user_nominations_count')
            ->orderBy('name')
            ->get();

        $nominationNames = $userNominationGroups
            ->where('user_nominations_count', '>=', 3)
            ->pluck('name')
            ->sort()
            ->values();

        return view('nominees', [
            'awards' => $awards,
            'award' => $award,
            'alphabeticalSort' => $alphabeticalSort,
            'nominationNames' => $nominationNames,
            'nominees' => $nomineesArray,
            'userNominationGroups' => $userNominationGroups,
        ]);
    }

    public function post(?Award $award, Request $request): JsonResponse
    {
        if ($this->settings->read_only) {
            return response()->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        if ($award && $response = $this->permissionCheck($award)) {
            return $response;
        }

        $action = $request->post('action');

        if (!in_array($action, ['new', 'edit', 'delete'], true)) {
            return response()->json(['error' => 'Invalid action specified.']);
        }

        if ($action !== 'new') {
            $nominee = $award->nominees()
                ->with('userNominationGroup')
                ->find($request->post('id'));
            if (!$nominee) {
                return response()->json(['error' => 'Invalid nominee specified.']);
            }
        } else {
            $nominee = new Nominee();
            $nominee->award()->associate($award);
        }

        if ($action === 'delete') {
            if ($nominee->userNominationGroup) {
                $nominee->userNominationGroup->nominee()->dissociate();
                $nominee->userNominationGroup->save();
            }
            $nominee->delete();

            $this->auditService->add(
                Action::makeWith('nominee-delete', $award->id, $nominee->id)
            );

            return response()->json(['success' => true]);
        }

        if (strlen(trim($request->post('name', ''))) === 0) {
            return response()->json(['error' => 'You need to enter a name.']);
        }

        // Re-slugify every name the name changes
        $slug = Str::of($request->post('name'))
            ->slug(' ')
            ->wordWrap(45, "\n", true)
            ->replace(' ', '-')
            ->split("/\n/")[0];

        $nominee->name = $request->post('name');
        $nominee->subtitle = $request->post('subtitle');
        $nominee->flavor_text = $request->post('flavorText');
        $nominee->slug = $slug;
        $nominee->save();

        if ($action === 'new' && !empty($request->post('group'))) {
            /** @var ?UserNominationGroup $group */
            $group = $award->userNominationGroups()->find($request->post('group'));
            if (!$group) {
                return response()->json(['error' => 'Invalid nomination group. Refresh the page and try again.']);
            } elseif ($group->nominee) {
                return response()->json(['error' => 'This nomination group is already linked to a nominee. Refresh the page and try again.']);
            }

            $group->nominee()->associate($nominee);
            $group->save();
        }

        if ($request->file('image')) {
            try {
                $file = $this->fileService->handleUploadedFile(
                    $request->file('image'),
                    'Nominee.image',
                    'nominees',
                    $award->slug . '--' . $nominee->slug,
                );
            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()]);
            }

            if ($nominee->image) {
                $image = $nominee->image;
                $nominee->image()->dissociate();
                $nominee->save();
                $this->fileService->deleteFile($image);
            }

            $nominee->image()->associate($file);
            $nominee->save();
        }

        $this->auditService->add(
            Action::makeWith('nominee-' . $action, $award->id, $nominee->id),
            TableHistory::makeWith(Nominee::class, $nominee->id, $request->post())
        );

        return response()->json(['success' => true]);
    }

    public function nominationGroupIgnore(Award $award, Request $request): JsonResponse
    {
        if ($response = $this->permissionCheck($award)) {
            return $response;
        }

        $groupId = $request->post('group');
        /** @var ?UserNominationGroup $group */
        $group = $award->userNominationGroups()->find($groupId);

        if (!$group) {
            return response()->json(['error' => 'Invalid nomination group specified.']);
        }

        if ($group->mergedInto) {
            return response()->json(['error' => 'Cannot change the ignored status of a nomination group that has previously been merged.']);
        }

        if ($group->nominee) {
            return response()->json(['error' => 'Cannot change the ignored status of a nomination group that is linked to a nominee.']);
        }

        $ignore = $request->request->get('ignore') === 'true';
        $group->ignored = $ignore;
        $group->save();

        $this->auditService->add(
            Action::makeWith($ignore ? 'nomination-group-ignored' : 'nomination-group-unignored', $award->id, $group->id),
        );


        return response()->json(['success' => true]);
    }

    public function nominationGroupMerge(Award $award, Request $request): JsonResponse
    {
        if ($response = $this->permissionCheck($award)) {
            return $response;
        }

        $fromId = $request->request->get('from');
        /** @var ?UserNominationGroup $fromGroup */
        $fromGroup = $award->userNominationGroups()->find($fromId);

        $toId = $request->request->get('to');
        /** @var ?UserNominationGroup $toGroup */
        $toGroup = $award->userNominationGroups()->find($toId);

        if (!$fromGroup || !$toGroup) {
            return response()->json(['error' => 'Invalid nomination group specified.']);
        }

        if ($fromGroup->mergedInto) {
            return response()->json(['error' => 'This nomination group has already been merged.']);
        }

        if ($toGroup->mergedInto) {
            return response()->json(['error' => 'You cannot select a nomination group that has already been merged.']);
        }

        if ($fromGroup->nominee) {
            return response()->json(['error' => 'Cannot merge from a nomination group that is linked to a nominee. (You can still merge into it.)']);
        }

        $fromGroup->ignored = false;
        $fromGroup->mergedInto()->associate($toGroup);
        $fromGroup->save();

        foreach ($fromGroup->userNominations as $nomination) {
            $nomination->userNominationGroup()->associate($toGroup);
            $nomination->originalGroup()->associate($fromGroup);
            $nomination->save();
        }

        $this->auditService->add(
            Action::makeWith('nomination-group-merged', $fromGroup->id, $toGroup->id),
        );

        return response()->json(['success' => true]);
    }

    public function nominationGroupDemerge(Award $award, Request $request): JsonResponse
    {
        if ($response = $this->permissionCheck($award)) {
            return $response;
        }

        $groupId = $request->post('group');
        /** @var UserNominationGroup $group */
        $group = $award->userNominationGroups()->find($groupId);

        if (!$group) {
            return response()->json(['error' => 'Invalid nomination group specified.']);
        }

        if (!$group->mergedInto) {
            return response()->json(['error' => 'This nomination group has not been merged.']);
        }

        $mergedInto = $group->mergedInto;
        $group->mergedInto()->dissociate();
        $group->save();

        $nominations = UserNomination::where('original_group_id', $groupId)->get();

        foreach ($nominations as $nomination) {
            $nomination->userNominationGroup()->associate($group);
            $nomination->originalGroup()->dissociate();
            $nomination->save();
        }

        $this->auditService->add(
            Action::makeWith('nomination-group-demerged', $group->id, $mergedInto->id)
        );

        return response()->json(['success' => true]);
    }

    public function nominationGroupUnlink(Award $award, Request $request): JsonResponse
    {
        if ($response = $this->permissionCheck($award)) {
            return $response;
        }

        $groupId = $request->post('group');
        $group = $award->userNominationGroups()->find($groupId);

        if (!$group) {
            return response()->json(['error' => 'Invalid nomination group specified.']);
        }

        $groupId = $request->request->get('group');
        /** @var UserNominationGroup $group */
        $group = $award->userNominationGroups()->find($groupId);

        if (!$group) {
            return response()->json(['error' => 'Invalid nomination group specified.']);
        }

        $group->nominee()->dissociate();
        $group->save();

        $this->auditService->add(
            Action::makeWith('nomination-group-updated', $group->id),
            TableHistory::makeWith(UserNominationGroup::class, $group->id, ['action' => 'unlink'])
        );

        return response()->json(['success' => true]);
    }

    public function exportNominees(): Response
    {
        $awards = Award::query()
            ->with('nominees')
            ->hideSecret()
            ->orderBy('order')
            ->get();

        $csv = Writer::fromString();
        $csv->insertOne([
            'Award Name',
            'Award Subtitle',
            'Nominee Name',
            'Nominee Subtitle',
            'Flavor Text',
        ]);

        foreach ($awards as $award) {
            foreach ($award->nominees as $nominee) {
                $csv->insertOne([
                    $award->name,
                    $award->subtitle,
                    $nominee->name,
                    $nominee->subtitle,
                    $nominee->flavor_text,
                ]);
            }
        }

        return response($csv->toString(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vga-' . year() . '-nominees.csv"',
        ]);
    }

    public function exportUserNominations(): Response
    {
        $awards = Award::query()
            ->with('userNominationGroups')
            ->hideSecret()
            ->orderBy('order')
            ->get();

        $csv = Writer::fromString();
        $csv->setOutputBOM(Bom::Utf8);
        $csv->insertOne([
            'Award Name',
            'Award Subtitle',
            'Nomination',
            'Count',
        ]);

        foreach ($awards as $award) {
            /** @var Collection<UserNominationGroup> $groups */
            $groups = $award->userNominationGroups()
                ->withCount('userNominations')
                ->where('ignored', false)
                ->whereDoesntHave('mergedInto')
                ->get();

            foreach ($groups as $group) {
                $csv->insertOne([
                    $award->name,
                    $award->subtitle,
                    $group->name,
                    $group->user_nominations_count,
                ]);
            }
        }

        return response($csv->toString(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vga-' . year() . '-user-nominations.csv"',
        ]);
    }

    private function permissionCheck(Award $award): ?JsonResponse
    {
        if ($this->settings->read_only) {
            return response()->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        if ($award->secret && Gate::denies('awards_secret')) {
            abort(404);
        }

        return null;
    }
}
