<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\File;
use App\Models\TableHistory;
use App\Services\AuditService;
use App\Services\CloudflareService;
use App\Services\FileService;
use App\Settings\AppSettings;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ConfigController extends Controller
{
    public function __construct(
        readonly private CloudflareService $cloudflare,
        readonly private AppSettings $settings,
        readonly private AuditService $auditService,
        readonly private FileService $fileService,
    ) {
    }

    public function index(): View
    {
        $navbarConfig = [];
        foreach ($this->settings->navbarItems() as $routeName => $details) {
            $navbarConfig[] = "$routeName: {$details['label']}";
        }

        // Ultra alerts are very important and appear with a blinding red background to encourage you to fix the issue ASAP
        $ultraAlerts = [];
        if ($this->settings->stream_time) {
            if ($this->settings->stream_time->isFuture() && $this->settings->isPagePublic('results')) {
                $ultraAlerts[] = 'The results page is public, but the stream date hasn\'t passed yet.';
            }
            if ( ! $this->settings->isPagePublic($this->settings->default_page)) {
                $ultraAlerts[] = 'The default page doesn\'t have public access turned on.';
            }
        }

        $images = File::whereLike('entity', 'Misc.%')->get()->keyBy('entity');

        return view('config', [
            'navigationBarConfig' => implode("\n", $navbarConfig),
            'routes' => $this->getValidNavbarRoutes(),
            'cloudflareAvailable' => $this->cloudflare->isServiceAvailable(),
            'ultraAlerts' => $ultraAlerts,
            'images' => $images,
        ]);
    }

    public function post(Request $request): RedirectResponse
    {
        if ($this->settings->read_only) {
            return redirect()->back()->with(
                'error',
                'The site is currently in read-only mode. No changes can be made. To disable read-only mode, you will need to edit the database directly.'
            );
        }

        $error = false;

        if ($request->input('readOnly')) {
            $this->settings->award_suggestions = false;
            $this->settings->read_only = true;
            $this->settings->save();

            $this->auditService->add(
                 Action::makeWith('config-readonly-enabled')
            );

            return redirect()->back()->with('success', 'Read-only mode has been successfully enabled.');
        }

        $dates = ['voting_start', 'voting_end', 'stream_time'];

        foreach ($dates as $date) {
            if (!$request->input($date)) {
                $this->settings->$date = null;
            } else {
                try {
                    $this->settings->$date = Date::parse($request->input($date))->shiftTimezone('America/New_York');
                } catch (Exception) {
                    $this->addFlash('error', "Invalid date provided for " . Str::of($date)->headline()->lower() . ".");
                    $error = true;
                }
            }
        }

        $this->settings->default_page = $request->input('defaultPage', 'home');
        $this->settings->public_pages = array_keys($request->input('publicPages') ?: []);

        $navbarItems = explode("\n", $request->input('navigationMenu'));
        $navbarItems = array_map(function ($line) {
            $elements = explode(":", trim($line));
            return array_map('trim', $elements);
        }, $navbarItems);

        $navbarItemsOrdered = [];
        foreach ($navbarItems as $index => $details) {
            $navbarItemsOrdered[$details[0]] = [
                'label' => $details[1],
                'order' => $index
            ];
        }

        $navbarError = false;
        $validRoutes = $this->getValidNavbarRoutes();
        foreach ($navbarItemsOrdered as $routeName => $details) {
            if (str_starts_with($routeName, 'dropdown')) {
                continue;
            }
            if (!isset($validRoutes[$routeName])) {
                $this->addFlash('error', 'Invalid route specified in the navigation menu config (' . $routeName . ').');
                $navbarError = $error = true;
            }
            if (empty($details['label'])) {
                $this->addFlash('error', 'No label provided for route ' . $routeName . ' in the navigation menu config.');
                $navbarError = $error = true;
            }

            $labelData = explode('/', $details['label'], 2);
            if (count($labelData) === 2) {
                if (!isset($navbarItemsOrdered[$labelData[0]])) {
                    $this->addFlash('error', 'Invalid navbar configuration: dropdown not found (' . $details['label'] . ').');
                    $navbarError = $error = true;
                }
            }
        }

        if (!$navbarError) {
            $this->settings->navbar_items = $navbarItemsOrdered;
        }

        $this->settings->award_suggestions = $request->boolean('awardSuggestions');
        $this->settings->save();

        if ($request->file('vidya_link')) {
            try {
                $this->fileService->handleUploadedFile(
                    $request->file('vidya_link'),
                    'Misc.vidyaLink',
                    'misc',
                    'vidya-link',
                );
            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()]);
            }

            // Popping the last one ensures we keep the one we just uploaded
            $existingFiles = File::where('entity', 'Misc.vidyaLink')->get();
            $existingFiles->pop();
            foreach ($existingFiles as $oldFile) {
                $this->fileService->deleteFile($oldFile);
            }
        }

        $this->auditService->add(
            Action::makeWith('config-updated', 1),
            TableHistory::makeWith(AppSettings::class, 1, $request->all())
        );

        if (!$error) {
            return redirect()->back()->with('success', 'Config successfully saved.');
        }

        return redirect()->back();
    }

    public function purgeCache(Request $request): RedirectResponse
    {
        $type = $request->input('type');

        if ($this->settings->read_only) {
            return redirect()->back()->with(
                'error',
                'The site is currently in read-only mode. Please clear the cache the old fashioned way.'
            );
        }

        if ($type === 'cloudflare') {
            if ( ! $this->cloudflare->isServiceAvailable()) {
                return redirect()->back()->with('error', 'Cloudflare service is not available.');
            }

            $this->cloudflare->purgeCache();

            $this->auditService->add(
                Action::makeWith('config-cache-cleared', 1),
                TableHistory::makeWith(AppSettings::class, 1, $request->all())
            );

            return redirect()->back()->with('success', 'Cloudflare cache has been purged.');
        }

        if ($type === 'laravel') {
            // Must run before the cache is cleared or Doctrine loses the reference to the user
            $this->auditService->add(
                Action::makeWith('config-cache-cleared', 1),
                TableHistory::makeWith(AppSettings::class, 1, $request->all())
            );

            try {
                $exitCode = Artisan::call('optimize:clear');

                if ($exitCode === 0) {
                    return redirect()->back()->with('success', 'Laravel cache has been purged.');
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
            }

            return redirect()->back()->with('error', 'An error occurred (exit code ' . $exitCode . ').');
        }

        return redirect()->back()->with('error', 'Invalid cache type specified.');
    }

    public function cron(): View
    {
        return view('cron');
    }

    /**
     * Gets an array of routes that can be used in the top navigation bar.
     * @return \Illuminate\Routing\Route[] An array of routes, indexed by the route name.
     */
    private function getValidNavbarRoutes(): array
    {
        return collect(Route::getRoutes())->filter(function (\Illuminate\Routing\Route $route) {
            $name = $route->getName();

            // Ignore internal routes
            if ( ! $name || str_contains($name, 'generated::')) {
                return false;
            }

            // Ignore POST-only routes
            if ( ! in_array('GET', $route->methods(), true)) {
                return false;
            }

            // Ignore any routes with required URL parameters
            if (count($route->parameterNames()) > count($route->getOptionalParameterNames())) {
                return false;
            }

            return true;
        })
            ->keyBy(fn($route) => $route->getName())
            ->sortKeys()
            ->toArray();
    }
}
