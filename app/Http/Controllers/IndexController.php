<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Settings\AppSettings;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    public function __construct(
        private readonly AppSettings $settings,
        private readonly Kernel $kernel,
    ) {
    }

    /**
     * This route clones the request, updates the URI to the default_page route and then
     * re-dispatches it.
     *
     * This allows us to make different pages the root page without needing to update routes
     * or manually call controllers.
     *
     * I would rate this as only mildly hacky.
     */
    public function index(): Response
    {
        $routeUrl = route($this->settings->default_page, absolute: false);

        // We need a fresh Request where the middleware hasn't already run, because some middleware
        // (such as EncryptCookies) handles being run twice very badly.
        $request = Request::capture();

        $newRequest = $request->duplicate(server: [
            ...$request->server(),
            'REQUEST_URI' => $routeUrl,
            'SUB_REQUEST' => true,
        ]);

        return $this->kernel->handle($newRequest);
    }

    public function home(): View
    {
        $news = News::query()
            ->with('user')
            ->where('show_at', '<', now())
            ->orderBy('show_at', 'desc')
            ->get();

        return view('home', [
            'title' => 'Home',
            'news' => $news,
        ]);
    }
}
