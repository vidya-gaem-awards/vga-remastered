<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Autocompleter;
use App\Models\GameRelease;
use App\Models\TableHistory;
use App\Services\AuditService;
use App\Services\IgdbService;
use App\Services\WikipediaService;
use App\Settings\AppSettings;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AutocompleterController extends Controller
{
    public function __construct(
        private AppSettings $settings,
        private AuditService $auditService,
    )
    {
    }

    public function index(): View
    {
        $autocompleters = Autocompleter::query()
            ->with('awards')
            ->get();

        $jsonArray = $autocompleters
            ->keyBy('id')
            ->map(fn($autocompleter) => [
                'id' => $autocompleter->id,
                'slug' => $autocompleter->slug,
                'name' => $autocompleter->name,
                'suggestions' => $autocompleter->strings,
            ]);

        $gameReleases = GameRelease::all();

        return view('autocompleters', [
            'autocompleters' => $autocompleters,
            'autocompletersEncodable' => $jsonArray,
            'gameReleases' => count($gameReleases),
        ]);
    }

    public function ajax(Request $request): JsonResponse
    {
        if ($this->settings->read_only) {
            return response()->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        $action = $request->post('action');

        if (!in_array($action, ['new', 'edit', 'delete'])) {
            return response()->json(['error' => 'Invalid action specified.']);
        }

        if ($action !== 'new') {
            $id = strtolower($request->post('id'));

            if (strlen($id) === 0) {
                return response()->json(['error' => 'An ID is required.']);
            }

            $autocompleter = Autocompleter::with('awards')->find($id);
            if (!$autocompleter) {
                return response()->json(['error' => 'Couldn\'t find an autocompleter with that ID.']);
            }
        }

        if ($action === 'delete') {
            if ($autocompleter->awards->isNotEmpty()) {
                return response()->json(['error' => 'Can\'t delete this autocompleter: there are awards still using it (such as the ' . $autocompleter->awards->first()->name . ').']);
            }

            $autocompleter->delete();

            $this->auditService->add(
                Action::makeWith('autocompleter-deleted', $autocompleter->id)
            );

            return response()->json(['success' => true]);
        }

        if ($action === 'new') {
            $autocompleter = new Autocompleter();
        }

        $slug = $request->post('slug');
        $otherAutocompleter = Autocompleter::where('slug', $slug)->first();
        if ($otherAutocompleter && $otherAutocompleter->id !== $autocompleter->id) {
            return response()->json(['error' => 'That slug is already in use. Please enter another slug.']);
        }

        if (strlen($request->post('slug')) === 0) {
            return response()->json(['error' => 'An autocompleter slug is required.']);
        }

        if (strlen($request->post('name')) === 0) {
            return response()->json(['error' => 'An autocompleter name is required.']);
        }

        $autocompleter->name = $request->post('name');
        $autocompleter->slug = $request->post('slug');
        $autocompleter->strings = array_values(array_filter(array_map('trim', explode("\n", $request->post('suggestions')))));
        $autocompleter->save();

        $this->auditService->add(
            Action::makeWith($request->post('action') === 'new' ? 'autocompleter-added' : 'autocompleter-edited', $autocompleter->id),
            TableHistory::makeWith(Autocompleter::class, $autocompleter->id, $request->post()),
        );

        return response()->json(['success' => true]);
    }

    public function wikipedia(WikipediaService $wikipedia, Request $request): JsonResponse
    {
        $year = $request->query('year');

        try {
            $games = $wikipedia->getGames((int)$year);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        $suggestions = $wikipedia->getStringListForAutocompleter($games);

        return response()->json(['success' => true, 'suggestions' => $suggestions]);
    }

    public function igdb(IgdbService $igdb, Request $request): JsonResponse
    {
        $year = $request->query('year');

        $allGames = [];
        $offset = 0;

        try {
            do {
                $games = $igdb->getGames((int)$year, $offset);
                $allGames = [...$allGames, ...$games];
                $offset = count($allGames);
            } while (!empty($games));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        $suggestions = $igdb->getStringListForAutocompleter($allGames);

        return response()->json(['success' => true, 'suggestions' => $suggestions]);
    }
}
