<?php

namespace App\Http\Controllers;

use App\Settings\AppSettings;
use Illuminate\Contracts\View\View;

class StaticController extends Controller
{
    public function privacy(): View
    {
        return view('privacy');
    }

    public function videos()
    {
        abort(501);
    }

    public function soundtrack()
    {
        abort(501);
    }

    public function credits()
    {
        abort(501);
    }

    public function trailers()
    {
        abort(501);
    }

    public function resultRedirect()
    {
        abort(501);
    }

    public function promo(): View
    {
        return view('promo');
    }

    public function version(): View
    {
        $path = base_path('.git/');

        if (config('app.commit')) {
            $commit = config('app.commit');
            $commitSource = 'build';
        } else {
            if (file_exists($path)) {
                $head = trim(substr(file_get_contents($path . 'HEAD'), 4));
                $commit = trim(file_get_contents(sprintf($path . $head)));
                $commitSource = 'file';
            } else {
                $commit = $commitSource = null;
            }
        }

        return view('version', [
            'commit' => $commit,
            'commitSource' => $commitSource,
        ]);
    }
}
