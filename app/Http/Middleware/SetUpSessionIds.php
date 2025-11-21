<?php

namespace App\Http\Middleware;

use App\Facade\FuzzyUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetUpSessionIds
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /*
         * @TODO: The code in this function is comically old (some version of it dates back to 2012!).
         *        In all that time we've never once used the HMAC functionality of the ID, and I'm
         *        not 100% sure the whole 'cookie or session' logic is necessary either.
         *        For now, I guess it's not doing any harm... which means it'll probably be here at
         *        least another decade.
         */

        // Generate a random ID to keep in the cookie if one doesn't already exist.
        // We use this cookie as part of the voting identification process.
        $randomIdCookie = $request->cookie('access');
        $randomIdSession = $request->session()->get('access');

        if ($randomIdCookie && $randomIdSession) {
            $randomId = $randomIdCookie;
        } elseif ($randomIdCookie && !$randomIdSession) {
            $request->session()->put('access', $randomIdCookie);
            $randomId = $randomIdCookie;
        } elseif (!$randomIdSession) {
            // HMAC no longer necessary, because Laravel encrypts cookies automatically.
            $randomId = substr(hash('sha256', random_bytes(64)), 0, 8);
            $request->session()->put('access', $randomId);
        } else {
            $randomId = $randomIdSession;
        }

        // If the user has a votingCode cookie set, use that, otherwise, use the votingCode session.
        // This helps guard against users with cookies turned off.
        $votingCodeSession = $request->session()->get('votingCode');
        $votingCodeCookie = $request->cookie('votingCode');

        if ($votingCodeCookie) {
            $request->session()->put('votingCode', $votingCodeCookie);
            $votingCode = $votingCodeCookie;
        } else {
            $votingCode = $votingCodeSession;
        }

        // @TODO: This feels janky.
        FuzzyUser::setRandomId($randomId);
        FuzzyUser::setVotingCode($votingCode);

        return $next($request);
    }
}
