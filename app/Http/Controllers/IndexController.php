<?php

namespace App\Http\Controllers;

use App\Models\News;

class IndexController extends Controller
{
    public function index()
    {
        $news = News::where('visible', true)
            ->with('user')
            ->where('created_at', '<', now())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('index', [
            'title' => 'Home',
            'news' => $news,
        ]);
    }

    public function promo()
    {
        abort(501);
    }
}
