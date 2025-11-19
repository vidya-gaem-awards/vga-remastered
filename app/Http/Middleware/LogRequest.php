<?php

namespace App\Http\Middleware;

use App\Models\Access;
use App\Settings\AppSettings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class LogRequest
{
    public function __construct(
        private AppSettings $settings,
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Nothing to do in read-only mode
        if ($this->settings->read_only) {
            return $response;
        }

        // @TODO: this comment is not currently true, because there is no request middleware.

        // If the user didn't have an access cookie when they first loaded the page, one would have been generated
        // in the request middleware. As such, we only need to worry about copying the value from the session
        // into the cookie here.
        $randomIdCookie = $request->cookies->get('access');
        $randomIdSession = $request->session()->get('access');

        if ($randomIdSession && !$randomIdCookie) {
            $cookie = cookie('access', $randomIdSession, minutes: 60 * 24 * 90);
            $response = $response->withCookie($cookie);
        }

        // @TODO: cookie stuff goes here

        if (!$this->shouldLogRequest($request)) {
            return $response;
        }

        $route = Route::getCurrentRoute();

        // Strip out invalid UTF-8
        $headers = $request->headers->all();
        foreach ($headers as $key => $value) {
            $headers[$key] = array_map($this->stripInvalidUtf8(...), $value);
        }

        $userAgent = substr($this->stripInvalidUtf8($request->userAgent() ?? ''), 0, 255);
        $referer = $request->headers->get('referer');
        if ($referer !== null) {
            $referer = substr($this->stripInvalidUtf8($referer), 0, 255);
        }

        Access::create([
            'cookie_id' => '',
            'route' => $route?->getName() ?? '',
            'controller' => $route?->getAction('uses') ?? '',
            'request_string' => $request->path(),
            'request_method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $userAgent,
            // @TODO: This has been pointless for years, all requests go to index.php
            'filename' => $request->server('SCRIPT_FILENAME'),
            'referer' => $referer,
            'headers' => $headers,
            'user_id' => request()->user()?->id,
        ]);

        return $response;
    }

    private function stripInvalidUtf8(string $text): string
    {
        return mb_convert_encoding($text, 'UTF-8', 'UTF-8');
    }

    private function shouldLogRequest(Request $request): bool
    {
        // This is the sub-request that occurs when you load the index page
        if ($request->server('SUB_REQUEST')) {
            return false;
        }

        return true;
    }
}
