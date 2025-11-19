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
}
