<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Contracts\View\View;

class IndexController extends Controller
{
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

    public function promo(): View
    {
        return view('promo');
    }
}
