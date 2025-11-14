<?php

namespace App\Http\Controllers;

use App\Settings\AppSettings;
use Illuminate\Contracts\View\View;

class StaticController extends Controller
{
    /*
     * @TODO: Need to see if this can be set up to do internal forwarding rather than redirecting
     */
    public function index(AppSettings $settings)
    {
        return redirect()->route($settings->default_page);
    }

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
}
