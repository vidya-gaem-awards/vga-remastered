<?php

namespace App\Http\Controllers;

use App\Models\Access;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class ReferrerController extends Controller
{
    public function index(Request $request): View
    {
        $days = $request->query->get('days', '7');
        if ($days !== 'all' && !ctype_digit($days)) {
            $days = 7;
        }

        $query = DB::table('access')
            ->selectRaw('MAX(created_at) as latest')
            ->selectRaw('COUNT(id) as total')
            ->addSelect('referer')
            ->whereNotLike('referer', '%vidyagaemawards.com%')
            ->whereNotLike('referer', '%.lndo.site%');

        if ($days !== 'all') {
            $query
                ->where('created_at', '>', now()->subDays($days));
        }

        $result = $query
            ->groupBy('referer')
            ->having('total', '>=', 1)
            ->orderByRaw('total')
            ->orderByRaw('latest')
            ->get()
            ->map(fn (object $row) => (array) $row);

        $referrers = [];

        // Due to the magic of the internet, multiple URLs can resolve to one website.
        // Here we try and combine those URLs as much as possible to get more accurate data.
        foreach ($result as $referer) {
            $referer['latest'] = Date::make($referer['latest']);
            $referer['referer'] = $this->cleanUrlPreDisplay($referer['referer']);
            $referer['type'] = false;
            $key = $this->cleanUrlPreCompare($referer['referer']);

            if (str_starts_with($key, 'android-app://')) {
                $class = 'info';
                $referer['referer'] = str_replace('android-app://', '', $referer['referer']);
                $referer['type'] = 'android';
            } elseif (str_contains($key, '4chan.org') || str_contains($key, '4channel.org')) {
                $class = 'success';
            } elseif (str_starts_with($key, 'reddit.com')) {
                $class = 'danger';
            } else {
                $class = 'warning';
            }

            if (str_starts_with($key, 't.co/')) {
                $referer['type'] = 'twitter';
            } elseif (str_starts_with($key, 'discordapp.com/')) {
                $referer['type'] = 'discord';
            }

            $referer['class'] = $class;

            if (!isset($referrers[$key])) {
                $referrers[$key] = $referer;
            } else {
                $referrers[$key]['total'] += $referer['total'];
                $referrers[$key]['latest'] = max($referer['latest'], $referrers[$key]['latest']);
            }
        }

        // We may have to redo the sort after combining some referers
        usort($referrers, function ($a, $b) {
            $total = $b['total'] <=> $a['total'];
            if ($total !== 0) {
                return $total;
            }
            return $b['latest'] <=> $a['latest'];
        });

        return view('referrers', [
            'referrers' => $referrers,
            'days' => $days,
        ]);
    }

    // @TODO: these functions shouldn't live out of the controller
    private function cleanUrlPreDisplay($referrer): ?string
    {
        // Combine all regional Google domains
        if (preg_match('{^https?://www\.google\.}', $referrer)) {
            $referrer = preg_replace('{www\.google\.[a-z]{2,3}(\.[a-z]{2,3})?/}', 'www.google.com/', $referrer);
        }

        // Remove everything after the search parameter for Google and Bing
        if (preg_match('{search\?q=.+}', $referrer)) {
            $referrer = preg_replace('{search\?q=(.+?)&.+}', 'search?q=$1', $referrer);
        }

        // Strip any parameters off the end of reddit URLs
        if (preg_match('{reddit.com/r/(.+?)/comments}', $referrer)) {
            $referrer = preg_replace('{/comments/(.+)/\?.*}', '/comments/$1/', $referrer);
        }

        // Remove the nonsense from Yandex
        if (preg_match('{yandex\.ru/clck/jsredir}', $referrer)) {
            $referrer = preg_replace('{jsredir\?.*}', 'jsredir', $referrer);
        }

        // Remove the nonsense from Google
        if (preg_match('{www\.google\.(.+)/url?.+}', $referrer)) {
            $referrer = preg_replace('{/url?.+}', '', $referrer);
        }

        // Remove unnecessary URL parameters in SomethingAwful URLs
        if (preg_match('{forums\.somethingawful\.com}', $referrer)) {
            $referrer = preg_replace('{&perpage=\d+}', '', $referrer);
            $referrer = preg_replace('{&userid=\d+}', '', $referrer);
        }

        // Remove the derefer parameter off the end of 4chan URLs
        if (preg_match('{/derefer\?url=.+}', $referrer)) {
            $referrer = preg_replace('{/derefer\?url=.+}', '', $referrer);
        }

        return $referrer;
    }

    private function cleanUrlPreCompare($referrer): string
    {
        // Remove the http and www prefixes, as well as the trailing slash
        $referrer = rtrim(preg_replace('{https?://(www\.)?}', '', $referrer), '/');

        // Replace 4channel.org with 4chan.org
        $referrer = str_replace('4channel.org', '4chan.org', $referrer);

        // Remove the slugs from 4chan threads
        if (preg_match('{boards\.4chan\.org/.+/thread/[0-9]+/.+$}', $referrer)) {
            $referrer = preg_replace('{/([0-9]+)/.+?$}', '/$1', $referrer);
        }

        // Remove the board page number (such as boards.4chan.org/v/3)
        if (preg_match('#boards\.4chan\.org/[^/]+/[0-9]{1,2}#', $referrer)) {
            $referrer = preg_replace('{/[0-9]+$}', '', $referrer);
        }

        // Remove links to individual posts from the end of Knockout URLs
        if (preg_match('{knockout\.chat/thread/}', $referrer)) {
            $referrer = preg_replace('{#post-.+}', '', $referrer);
        }

        return $referrer;
    }
}
