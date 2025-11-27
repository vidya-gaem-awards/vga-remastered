<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Nominee;
use App\Models\TableHistory;
use App\Services\AuditService;
use App\Services\FileService;
use App\Settings\AppSettings;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TasksController extends Controller
{
    public function __construct(
        private readonly AppSettings $settings,
        private readonly AuditService $auditService,
        private readonly FileService $fileService,
    ) {
    }

    public function index(): View
    {
        $query = Nominee::query()
            ->whereHas('award', function ($query) {
                $query->where('enabled', true);
                if (Gate::denies('awards_secret')) {
                    $query->where('secret', false);
                }
            })
            ->with('award.nominees')
            ->with('award.autocompleter')
            ->with('image');

        $flavourText = (clone $query)
            ->where(function (Builder $query) {
                // @TODO: is this necessary? Empty string check was relevant for Symfony, but I think
                //        for Laravel it should only need to check against null.
                $query->where('flavor_text', '')
                    ->orWhereNull('flavor_text');
            })
            ->get();

        $images = (clone $query)
            ->whereDoesntHave('image')
            ->get();

        $subtitles = (clone $query)
            ->where(function (Builder $query) {
                // @TODO: is this necessary? Empty string check was relevant for Symfony, but I think
                //        for Laravel it should only need to check against null.
                $query->where('nominees.subtitle', '')
                    ->orWhereNull('nominees.subtitle');
            })
            ->get();

        $totalNominees = Nominee::query()
            ->whereHas('award', function ($query) {
                $query->where('enabled', true);
                if (Gate::denies('awards_secret')) {
                    $query->where('secret', false);
                }
            })
            ->count();

        $tasks = [
            'Subtitles' => [$subtitles, $totalNominees],
            'Flavor text' => [$flavourText, $totalNominees],
            'Images' => [$images, $totalNominees],
        ];

        $awards = $nominees = [];

        foreach ($tasks as $name => $raw) {
            $data = [
                'id' => str_replace(' ', '-', strtolower($name)),
                'count' => $raw[1] - count($raw[0]),
                'total' => $raw[1],
            ];
            $data['percent'] = $data['total'] > 0 ? ($data['count'] / $data['total'] * 100) : 100;

            if ($data['percent'] > 90) {
                $data['class'] = 'success';
            } elseif ($data['percent'] > 50) {
                $data['class'] = 'warning';
            } else {
                $data['class'] = 'danger';
            }

            $data['awards'] = [];

            $raw[0] = $raw[0]->sortBy('award.order');

            /** @var Nominee $nominee */
            foreach ($raw[0] as $nominee) {
                $award = $nominee->award;
                if (!isset($data['awards'][$award->id])) {
                    $data['awards'][$award->id] = [
                        'award' => $award,
                        'nominees' => [],
                    ];
                }
                $data['awards'][$award->id]['nominees'][] = $nominee;

                $awards[$award->id] = $award;
                $nominees[$award->id][$nominee->id] = $nominee;
            }

            $tasks[$name] = $data;
        }

        return view('tasks.index', [
            'tasks' => $tasks,
            'awards' => $awards,
            'nominees' => $nominees,
        ]);
    }

    public function imageCheck()
    {
        abort(501);
    }

    public function post(Request $request): JsonResponse
    {
        if ($this->settings->read_only) {
            return response()->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        $action = $request->post('action');
        $fullAccess = Gate::allows('nominations_edit');

        if ($action !== 'nominee') {
            return response()->json(['error' => 'Invalid action specified.']);
        }

        if (Gate::denies('tasks_nominees') && !$fullAccess) {
            return response()->json(['error' => 'You don\'t have permission to edit that nominee.']);
        }

        /** @var Nominee $nominee */
        $nominee = Nominee::find($request->post('nominee'));

        if (!$nominee || ($nominee->award->secret && Gate::denies('awards_secret'))) {
            return response()->json(['error' => 'Invalid award specified.']);
        } elseif (!$nominee->award->enabled) {
            return response()->json(['error' => 'Award isn\'t enabled.']);
        }

        if ($fullAccess) {
            $nominee->name = $request->post('name');
        }

        if (($fullAccess || !$nominee->subtitle) && $request->post('subtitle')) {
            $nominee->subtitle = $request->post('subtitle');
        }

        if (($fullAccess || !$nominee->flavor_text) && $request->post('flavorText')) {
            $nominee->flavor_text = $request->post('flavorText');
        }

        if (($fullAccess || !$nominee->image) && $request->file('image')) {
            try {
                $file = $this->fileService->handleUploadedFile(
                    $request->file('image'),
                    'Nominee.image',
                    'nominees',
                    $nominee->award->id . '--' . $nominee->slug,
                );
            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()]);
            }

            if ($nominee->image) {
                $oldFile = $nominee->image;
                $nominee->image()->dissociate();
                $nominee->save();
                $this->fileService->deleteFile($oldFile);
            }

            $nominee->image()->associate($file);
        }

        $nominee->save();

        $this->auditService->add(
            Action::makeWith('nominee-edit', $nominee->award->id, $nominee->id),
            TableHistory::makeWith(Nominee::class, $nominee->id, $request->post())
        );

        return response()->json(['success' => true, 'nominee' => $nominee]);
    }
}
