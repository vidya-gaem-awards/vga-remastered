<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class StaticController extends Controller
{
    public function privacy(): View
    {
        return view('privacy');
    }

    public function credits(): View
    {
        return view('credits');
    }

    public function trailers(): View
    {
        return view('trailers');
    }

    public function resultRedirect(): RedirectResponse
    {
        return redirect()->route('results');
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
