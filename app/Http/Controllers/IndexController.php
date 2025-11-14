<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Contracts\View\View;

class IndexController extends Controller
{
    public function home(): View
    {
        $news = News::where('visible', true)
            ->with('user')
            ->where('created_at', '<', now())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('home', [
            'title' => 'Home',
            'news' => $news,
        ]);
    }

    public function promo(): View
    {
        return view('promo');
    }
}
