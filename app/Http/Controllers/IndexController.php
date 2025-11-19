<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Settings\AppSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    /**
     * This route clones the request, updates the URI to the default_page route and then
     * re-dispatches it.
     *
     * This allows us to make different pages the root page without needing to update routes
     * or manually call controllers.
     *
     * I would rate this as only mildly hacky.
     */
    public function index(AppSettings $settings, Request $request): Response
    {
        $routeUrl = route($settings->default_page, absolute: false);

        $routeClone = $request->duplicate(server: [
            ...$request->server(),
            'REQUEST_URI' => $routeUrl,
            'SUB_REQUEST' => true,
        ]);

        return Route::dispatch($routeClone);
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
