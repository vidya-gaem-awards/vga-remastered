<?php

namespace App\Http\Controllers;

use App\Models\Action;
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

class VideoGamesController extends Controller
{
    public function __construct(
        readonly private AppSettings $settings,
        readonly private AuditService $auditService,
        readonly private IgdbService $igdb,
        readonly private WikipediaService $wikipedia,
    ) {
    }

    public function index(): View
    {
        $games = GameRelease::query()->orderBy('name')->get();

        return view('video-games.index', [
            'games' => $games,
        ]);
    }

    public function add(Request $request): JsonResponse
    {
        if ($this->settings->read_only) {
            return response()->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        $name = trim($request->post('name'));

        if (trim($name) === '') {
            return response()->json(['error' => 'Please enter the name of the game.']);
        }

        $game = new GameRelease();
        $game->name = $name;
        $game->list = 'video-games';
        $game->source = 'manual';
        $game->platforms = collect(GameRelease::PLATFORMS)
            ->keys()
            ->filter(fn ($platform) => $request->boolean($platform))
            ->values()
            ->toArray();

        if (count($game->platforms) === 0) {
            return response()->json(['error' => 'You need to select at least one platform.']);
        }

        $game->save();

        $this->auditService->add(
            Action::makeWith('add-video-game', $game->id),
            TableHistory::makeWith(GameRelease::class, $game->id, $request->post())
        );

        return response()->json(['success' => $game->name]);
    }

    public function remove(Request $request): JsonResponse
    {
        if ($this->settings->read_only) {
            return response()->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        $game = GameRelease::find($request->post('id'));
        if (!$game) {
            return response()->json(['error' => 'Couldn\'t find the selected game. Perhaps it has already been removed?']);
        }

        $game->delete();

        $this->auditService->add(
            Action::makeWith('remove-video-game', $game->id)
        );

        return response()->json(['success' => true]);
    }

    public function reloadWikipedia(): JsonResponse
    {
        if ($this->settings->read_only) {
            return response()->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        try {
            $games = $this->wikipedia->getGames(year());
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

        $this->wikipedia->addGamesToGameReleaseTable($games, true);
        $this->auditService->add(
            Action::makeWith('reload-video-games', 'wikipedia')
        );

        $this->addFlash('success', 'The list of ' . year() . ' video game releases has been successfully imported from Wikipedia.');

        return response()->json(['success' => true]);
    }

    public function reloadIgdb(): JsonResponse
    {
        if ($this->settings->read_only) {
            return response()->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        try {
            $allGames = [];
            $offset = 0;

            do {
                $games = $this->igdb->getGames(year(), $offset);

                $allGames = [...$allGames, ...$games];
                $offset = count($allGames);
            } while (!empty($games));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

        $this->igdb->addGamesToGameReleaseTable($allGames, true);
        $this->auditService->add(
            Action::makeWith('reload-video-games', 'igdb')
        );

        $this->addFlash('success', 'The list of ' . year() . ' video game releases has been successfully imported from IGDB.');

        return response()->json(['success' => true]);
    }
}
