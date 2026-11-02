<?php

namespace App\Http\Controllers;

use App\Enums\SoundtrackSection;
use App\Models\Action;
use App\Models\File;
use App\Models\SoundtrackSong;
use App\Http\Requests\SoundtrackSettingsRequest;
use App\Models\TableHistory;
use App\Services\AuditService;
use App\Services\FileService;
use App\Settings\AppSettings;
use App\Settings\SoundtrackSettings;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SoundtrackController extends Controller
{
    public function __construct(
        readonly private SoundtrackSettings $settings,
        readonly private AppSettings $appSettings,
        readonly private AuditService $auditService,
        readonly private FileService $fileService,
    ) {
    }

    public function show(): View
    {
        return view('soundtrack', [
            'soundtrack' => $this->settings,
            'albumArt' => File::where('entity', 'Soundtrack.albumArt')->first(),
            'sections' => SoundtrackSong::allBySection(),
        ]);
    }

    public function manage(): View
    {
        return view('soundtrack-manage', [
            'soundtrack' => $this->settings,
            'albumArt' => File::where('entity', 'Soundtrack.albumArt')->first(),
            'sections' => SoundtrackSong::allBySection(),
        ]);
    }

    public function settingsSave(SoundtrackSettingsRequest $request): RedirectResponse
    {
        if ($this->appSettings->read_only) {
            $this->addFlash('error', 'The site is currently in read-only mode. No changes can be made.');
            return redirect()->back();
        }

        $values = $request->validated();

        $this->settings->fill($values);
        $this->settings->save();

        if ($request->file('album_art')) {
            try {
                $this->fileService->handleUploadedFile(
                    $request->file('album_art'),
                    'Soundtrack.albumArt',
                    'soundtrack',
                    'album-art',
                );
            } catch (Exception $e) {
                $this->addFlash('error', 'The album art could not be uploaded: ' . $e->getMessage());
                return redirect()->route('soundtrack.manage');
            }

            // Popping the last one ensures we keep the one we just uploaded
            $existingFiles = File::where('entity', 'Soundtrack.albumArt')->get();
            $existingFiles->pop();
            foreach ($existingFiles as $oldFile) {
                $this->fileService->deleteFile($oldFile);
            }
        }

        $this->auditService->add(
            Action::makeWith('soundtrack-settings-update'),
            TableHistory::makeWith(SoundtrackSettings::class, 1, $values),
        );

        $this->addFlash('success', 'Soundtrack settings saved.');
        return redirect()->route('soundtrack.manage');
    }

    public function songsSave(Request $request): RedirectResponse
    {
        if ($this->appSettings->read_only) {
            $this->addFlash('error', 'The site is currently in read-only mode. No changes can be made.');
            return redirect()->back();
        }

        $songs = [];
        $errors = [];

        foreach (array_values($request->input('songs', [])) as $index => $row) {
            $song = [
                'section' => SoundtrackSection::tryFrom($row['section']),
                'title' => $row['title'],
                'artist' => $row['artist'],
                'context' => $row['context'],
                'link' => $row['link'],
            ];

            $error = $this->validateSong($song);

            if ($error) {
                $errors[] = 'Song ' . ($index + 1) . ': ' . $error;
            } else {
                $songs[] = $song;
            }
        }

        if ($errors) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
            $this->addFlash('error', 'No changes were made to the tracklist.');
            return redirect()->route('soundtrack.manage');
        }

        $this->replaceAll($songs);

        // The whole tracklist is replaced on each save, so there's no single entry to point to
        $this->auditService->add(
            Action::makeWith('soundtrack-songs-update', count($songs)),
            TableHistory::makeWith(SoundtrackSong::class, 0, $songs),
        );

        $this->addFlash('success', 'Tracklist saved.');
        return redirect()->route('soundtrack.manage');
    }

    /**
     * @param array{section: ?SoundtrackSection, title: ?string, artist: ?string, context: ?string, link: ?string} $song
     *
     * @return string|null The problem with the song, or null if it's valid.
     */
    private function validateSong(array $song): ?string
    {
        if (!$song['section']) {
            return 'Invalid section.';
        }

        if (!$song['title']) {
            return 'A title is required.';
        }

        foreach (['title', 'artist', 'context', 'link'] as $field) {
            if ($song[$field] && mb_strlen($song[$field]) > 255) {
                return ucfirst($field) . ' is too long (maximum 255 characters).';
            }
        }

        if ($song['link'] && !str_starts_with($song['link'], 'https://')) {
            return 'The link "' . $song['link'] . '" isn\'t a valid URL. It must start with "https://".';
        }

        return null;
    }

    /**
     * Replaces the entire tracklist in the database.
     *
     * @param array<array{section: SoundtrackSection, title: string, artist: ?string, context: ?string, link: ?string}> $songs
     */
    private function replaceAll(array $songs): void
    {
        $songs = collect($songs)->groupBy(fn (array $song) => $song['section']->value);

        SoundtrackSong::query()->delete();

        $order = 0;
        $number = 0;

        foreach (SoundtrackSection::cases() as $section) {
            foreach ($songs->get($section->value, []) as $song) {
                SoundtrackSong::create([
                    'section' => $section,
                    'order' => $order++,
                    'number' => $section === SoundtrackSection::Main ? ++$number : null,
                    'title' => $song['title'],
                    'artist' => $song['artist'],
                    'context' => $song['context'],
                    'link' => $song['link'],
                ]);
            }
        }
    }
}
